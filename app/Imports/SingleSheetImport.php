<?php

namespace App\Imports;

use App\Models\Estado;
use App\Models\Expediente;
use App\Models\Juzgado;
use App\Models\Materia;
use App\Services\FuzzyMatcherService;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SingleSheetImport implements ToCollection, WithHeadingRow
{
    private array $juzgados;
    private array $materias;
    private array $estados;
    public array  $errors        = [];
    public array  $unmatched     = [];
    public array  $notImported   = [];
    public array  $sheetHeadings = [];   // raw column keys of this sheet (diagnostic)
    public int    $imported      = 0;
    public int    $created       = 0;
    public int    $updated       = 0;
    public int    $skipped       = 0;
    private int   $observacionMaxLength = 255;

    public function __construct()
    {
        $this->juzgados = Juzgado::all(['id_juzgado', 'nombre_juzgado'])->toArray();
        $this->materias = Materia::all(['id_materia', 'nombre_materia'])->toArray();
        $this->estados  = Estado::all(['id_estado', 'nombre_estado'])->toArray();

        // Detect actual DB length in case schema differs from migration file.
        try {
            $col = DB::selectOne("SELECT CHARACTER_MAXIMUM_LENGTH as l FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='expedientes' AND COLUMN_NAME='observacion'");
            if (!empty($col?->l)) {
                $this->observacionMaxLength = (int) $col->l;
            }
        } catch (\Throwable $e) {
            // Keep safe default when metadata query is unavailable.
            $this->observacionMaxLength = 255;
        }
    }

    /**
     * Try multiple possible heading keys and return the first non-empty value.
     * Falls back to scanning ALL column keys for any meaningful stem from ANY of the
     * provided key variants (skips generic words like 'nombre', 'numero', 'tipo').
     */
    private function col($row, string ...$keys): string
    {
        $arr = is_array($row) ? $row : $row->toArray();

        // 1. Exact key match
        foreach ($keys as $k) {
            $val = FuzzyMatcherService::normalizeRaw((string) ($arr[$k] ?? ''));
            if ($val !== '') return mb_strtoupper($val);
        }

        // 2. Fallback: extract meaningful stems from ALL provided keys,
        //    then find any column whose heading contains one of those stems.
        $generic = ['nombre', 'numero', 'nro', 'num', 'del', 'los', 'las', 'tipo', 'fecha', 'datos'];
        $stems   = [];
        foreach ($keys as $k) {
            foreach (explode('_', strtolower($k)) as $part) {
                if (strlen($part) >= 4 && !in_array($part, $generic)) {
                    $stems[] = $part;
                }
            }
        }
        $stems = array_unique($stems);

        foreach ($arr as $colKey => $v) {
            $colLower = strtolower((string) $colKey);
            foreach ($stems as $stem) {
                if (str_contains($colLower, $stem)) {
                    $val = FuzzyMatcherService::normalizeRaw((string) $v);
                    if ($val !== '') return mb_strtoupper($val);
                }
            }
        }

        return '';
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            // On first row, capture heading keys for diagnostics
            if ($index === 0) {
                $this->sheetHeadings = array_keys($row->toArray());
            }

            // Try many possible heading variants for the expediente number
            $numeroExpediente = $this->col($row,
                'expediente', 'numero_expediente', 'n_expediente',
                'num_expediente', 'nro_expediente', 'numero'
            );

            // Absolute fallback: read column B by position (index 1) in case heading key differs
            if ($numeroExpediente === '') {
                $vals = array_values(is_array($row) ? $row : $row->toArray());
                $byPos = FuzzyMatcherService::normalizeRaw((string) ($vals[1] ?? ''));
                $numeroExpediente = mb_strtoupper($byPos);
            }

            // Skip only truly empty expediente cells. If the whole row is visually/actually empty,
            // don't count it as skipped (Excel sheets can have many formatted empty rows).
            if ($numeroExpediente === '') {
                if ($this->rowHasAnyData($row)) {
                    $this->skipped++;
                    $this->notImported[] = [
                        'fila'       => $index + 2,
                        'expediente' => null,
                        'motivo'     => 'Número de expediente vacío (columna B).',
                    ];
                }
                continue;
            }

            // Read catalog fields — try multiple heading name variants
            $juzgadoRaw = $this->col($row,
                // standard names
                'juzgado', 'juzgados', 'nombre_juzgado', 'nombre_del_juzgado',
                'juzgado_nombre', 'organo_jurisdiccional', 'organo', 'sala',
                'tribunal', 'juzgado_de_origen', 'juzgado_destino',
                // typo variant present in EXP. ARCHIVO DEFINITIVO sheet
                'juzgago'
            );

            // Diagnostic: on first data row, warn if juzgado column was not detected
            if ($index === 0 && empty($juzgadoRaw)) {
                $this->errors[] = 'DIAGNÓSTICO (Hoja): No se detectó columna de Juzgado. '
                    . 'Columnas disponibles: ' . implode(', ', $this->sheetHeadings);
            }
            $materiaRaw = $this->col($row,
                'materia', 'materias', 'nombre_materia', 'materia_procesal',
                'tipo_materia', 'especialidad'
            );
            $estadoRaw = $this->col($row,
                'estado', 'estados', 'nombre_estado', 'estado_procesal',
                'estado_del_expediente', 'situacion'
            );

            // Use a lower threshold (40) for juzgado to handle minor variants (º vs °, spacing, etc.)
            $idJuzgado = FuzzyMatcherService::findBestMatch($juzgadoRaw, $this->juzgados, 'nombre_juzgado', 'id_juzgado', 40);
            $idMateria = FuzzyMatcherService::findBestMatch($materiaRaw, $this->materias, 'nombre_materia', 'id_materia');
            $idEstado  = FuzzyMatcherService::findBestMatch($estadoRaw, $this->estados, 'nombre_estado', 'id_estado');

            // Build warnings for unmatched fields, with scored suggestions
            $warnings  = [];
            $obsPartes = [];

            if (!empty($juzgadoRaw) && $idJuzgado === null) {
                $suggestions = FuzzyMatcherService::getSuggestions($juzgadoRaw, $this->juzgados, 'nombre_juzgado', 'id_juzgado', 5);
                $warnings[]  = [
                    'campo'       => 'Juzgado',
                    'valor_excel' => $juzgadoRaw,
                    'sugerencias' => $suggestions,
                ];
                $obsPartes[] = "Juzgado Excel: {$juzgadoRaw}";
            }
            if (!empty($estadoRaw) && $idEstado === null) {
                $suggestions = FuzzyMatcherService::getSuggestions($estadoRaw, $this->estados, 'nombre_estado', 'id_estado', 5);
                $warnings[]  = [
                    'campo'       => 'Estado',
                    'valor_excel' => $estadoRaw,
                    'sugerencias' => $suggestions,
                ];
                $obsPartes[] = "Estado Excel: {$estadoRaw}";
            }
            if (!empty($materiaRaw) && $idMateria === null) {
                $suggestions = FuzzyMatcherService::getSuggestions($materiaRaw, $this->materias, 'nombre_materia', 'id_materia', 5);
                $warnings[]  = [
                    'campo'       => 'Materia',
                    'valor_excel' => $materiaRaw,
                    'sugerencias' => $suggestions,
                ];
                $obsPartes[] = "Materia Excel: {$materiaRaw}";
            }
            if (!empty($warnings)) {
                $this->unmatched[] = [
                    'fila'         => $index + 2,
                    'expediente'   => $numeroExpediente,
                    'advertencias' => $warnings,
                ];
            }
            $observacion = !empty($obsPartes) ? implode(' | ', $obsPartes) : null;

            // COMENTARIOS column (present in EXP. ARCHIVO DEFINITIVO) — prepend to observacion
            $comentariosRaw = $this->col($row, 'comentarios', 'comentario', 'notas', 'nota', 'observaciones');
            if (!empty($comentariosRaw) && $comentariosRaw !== mb_strtoupper($observacion ?? '')) {
                $observacion = !empty($observacion)
                    ? $observacion . ' | Comentario: ' . $comentariosRaw
                    : 'Comentario: ' . $comentariosRaw;
            }

            // DB safeguard: trim to actual observacion column length.
            if ($observacion !== null && $this->observacionMaxLength > 0 && mb_strlen($observacion) > $this->observacionMaxLength) {
                $observacion = mb_substr($observacion, 0, $this->observacionMaxLength);
            }

            // Parse date
            $fechaRaw = $row['fecha_de_resolucion_judicial'] ?? $row['fecha_resolucion'] ?? null;
            $fecha    = null;
            if ($fechaRaw) {
                $fecha = $this->parseDate((string) $fechaRaw);
            }

            try {
                // Build update payload: only include fields that have a real value
                // so that re-importing doesn't wipe existing data with nulls/empty strings
                $demandante  = trim((string) ($row['demandante'] ?? ''));
                $demandado   = trim((string) ($row['demandado'] ?? ''));
                $contenido   = trim((string) ($row['contenido_de_la_resolucion_judicial'] ?? $row['contenido_resolucion'] ?? ''));
                $antecedentes = trim((string) ($row['antecedentes'] ?? ''));

                $updateData = [];
                if ($idJuzgado   !== null) $updateData['id_juzgado']           = $idJuzgado;
                if ($idMateria   !== null) $updateData['id_materia']            = $idMateria;
                if ($idEstado    !== null) $updateData['id_estado']             = $idEstado;
                if ($fecha       !== null) $updateData['fecha_resolucion']      = $fecha;
                if ($demandante  !== '')   $updateData['demandante']            = $demandante;
                if ($demandado   !== '')   $updateData['demandado']             = $demandado;
                if ($contenido   !== '')   $updateData['contenido_resolucion']  = $contenido;
                if ($antecedentes !== '')  $updateData['antecedentes']          = $antecedentes;
                if ($observacion !== null) $updateData['observacion']           = $observacion;

                $expediente = Expediente::updateOrCreate(
                    ['numero_expediente' => $numeroExpediente],
                    $updateData
                );
                $this->imported++;
                if ($expediente->wasRecentlyCreated) {
                    $this->created++;
                } else {
                    $this->updated++;
                }
            } catch (\Throwable $e) {
                $this->errors[] = "Fila " . ($index + 2) . " (Exp: {$numeroExpediente}): " . $e->getMessage();
                $this->skipped++;
                $this->notImported[] = [
                    'fila'       => $index + 2,
                    'expediente' => $numeroExpediente,
                    'motivo'     => $e->getMessage(),
                ];
            }
        }
    }

    private function rowHasAnyData($row): bool
    {
        $arr = is_array($row) ? $row : $row->toArray();
        foreach ($arr as $v) {
            if (FuzzyMatcherService::normalizeRaw((string) $v) !== '') {
                return true;
            }
        }
        return false;
    }

    private function parseDate(string $value): ?string
    {
        $value = trim($value);
        if (empty($value)) {
            return null;
        }
        // Try common formats
        $formats = ['d.m.Y', 'd/m/Y', 'Y-m-d', 'd-m-Y', 'd.m.y'];
        foreach ($formats as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $value);
            if ($dt !== false) {
                return $dt->format('Y-m-d');
            }
        }
        // Excel serial number
        if (is_numeric($value)) {
            try {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value);
                return $dt->format('Y-m-d');
            } catch (\Throwable $e) {
            }
        }
        return null;
    }
}
