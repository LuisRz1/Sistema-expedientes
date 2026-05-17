<?php

namespace App\Http\Controllers;

use App\Imports\MultiSheetImport;
use App\Imports\SingleSheetImport;
use App\Models\Estado;
use App\Models\Expediente;
use App\Models\Juzgado;
use App\Models\Materia;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    /**
     * Step 1: Upload the file and return a list of sheets for the user to select.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        $file = $request->file('excel_file');
        $path = $file->storeAs('imports', 'temp_' . time() . '.' . $file->getClientOriginalExtension(), 'local');

        // Read sheet names
        $fullPath    = storage_path('app/private/' . $path);
        $spreadsheet = IOFactory::load($fullPath);
        $sheetNames  = $spreadsheet->getSheetNames();

        // Store path in session for next step
        session(['import_file_path' => $path]);

        if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['sheets' => $sheetNames]);
        }
        return view('import.select_sheets', compact('sheetNames'));
    }

    /**
     * Step 2: Import selected sheets.
     */
    public function process(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'sheets' => 'required|array|min:1',
        ]);

        $path = session('import_file_path');
        if (!$path) {
            return redirect()->route('import.index')->withErrors(['Sesión expirada, vuelve a subir el archivo.']);
        }

        $fullPath = storage_path('app/private/' . $path);

        $multiImport = new MultiSheetImport($request->sheets);
        Excel::import($multiImport, $fullPath);

        $totalImported = $multiImport->totalImported();
        $totalCreated  = $multiImport->totalCreated();
        $totalUpdated  = $multiImport->totalUpdated();
        $totalSkipped  = $multiImport->totalSkipped();
        $allErrors     = $multiImport->allErrors();
        $allUnmatched  = $multiImport->allUnmatched();
        $allNotImported = $multiImport->allNotImported();
        $generalTotal = Expediente::count();

        // Collect per-sheet heading info for diagnostics (helps debug column detection)
        $sheetHeadingInfo = [];
        foreach ($multiImport->importers as $sheetName => $importer) {
            if (!empty($importer->sheetHeadings)) {
                $sheetHeadingInfo[] = 'Hoja "' . $sheetName . '": ' . implode(', ', $importer->sheetHeadings);
            }
        }

        // Clean up
        @unlink($fullPath);
        session()->forget('import_file_path');

        $msg = "Importación completada: {$totalImported} fila(s) procesada(s). "
            . "Nuevos: {$totalCreated}, actualizados: {$totalUpdated}. "
            . "Total general actual: {$generalTotal}.";
        if ($totalUpdated > 0) {
            $msg .= " {$totalUpdated} expediente(s) ya existían o estaban repetidos y se actualizaron.";
        }
        if (count($allNotImported)) {
            $msg .= " No importados: " . count($allNotImported) . ".";
        }
        if (count($allErrors)) $msg .= " Error(es) técnicos: " . count($allErrors) . ".";
        if (count($allUnmatched)) $msg .= " " . count($allUnmatched) . " fila(s) importadas sin estado/materia/juzgado reconocido — revisa advertencias.";

        return redirect()->route('expedientes.index')
            ->with('success', $msg)
            ->with('import_unmatched', $allUnmatched)
            ->with('import_errors', $allErrors)
            ->with('import_not_imported', $allNotImported)
            ->with('import_sheet_headings', $sheetHeadingInfo);
    }

    /**
     * AJAX: save manual fixes for unmatched rows (estado, materia, juzgado).
     * Expects JSON body: { fixes: [{ expediente: "...", id_estado: 1, id_materia: 2, id_juzgado: 3 }, ...] }
     */
    public function fixUnmatched(Request $request)
    {
        $fixes = $request->input('fixes', []);
        $updated = 0;
        foreach ($fixes as $fix) {
            $exp = Expediente::where('numero_expediente', $fix['expediente'] ?? '')->first();
            if (!$exp) continue;
            $data = [];
            if (!empty($fix['id_estado']))   $data['id_estado']   = (int) $fix['id_estado'];
            if (!empty($fix['id_materia']))  $data['id_materia']  = (int) $fix['id_materia'];
            if (!empty($fix['id_juzgado']))  $data['id_juzgado']  = (int) $fix['id_juzgado'];
            if (!empty($data)) {
                $exp->update($data);
                $updated++;
            }
        }
        return response()->json(['updated' => $updated]);
    }

    /**
     * AJAX: create a new Estado, Materia, or Juzgado from the unmatched modal.
     * Body: { tipo: "estado"|"materia"|"juzgado", nombre: "...", expediente: "..." (optional) }
     * Returns { id: int, nombre: string } of the new record.
     * Optionally assigns it to the expediente if `expediente` is provided.
     */
    public function createCatalogItem(Request $request)
    {
        $tipo     = $request->input('tipo');
        $nombre   = mb_strtoupper(trim($request->input('nombre', '')));
        $expediente = $request->input('expediente');

        if (empty($nombre)) {
            return response()->json(['error' => 'Nombre requerido'], 422);
        }

        switch ($tipo) {
            case 'estado':
                $item = Estado::firstOrCreate(['nombre_estado' => $nombre]);
                $field = 'id_estado';
                $id    = $item->id_estado;
                break;
            case 'materia':
                $item = Materia::firstOrCreate(['nombre_materia' => $nombre]);
                $field = 'id_materia';
                $id    = $item->id_materia;
                break;
            case 'juzgado':
                $item = Juzgado::firstOrCreate(['nombre_juzgado' => $nombre]);
                $field = 'id_juzgado';
                $id    = $item->id_juzgado;
                break;
            default:
                return response()->json(['error' => 'Tipo inválido'], 422);
        }

        if ($expediente) {
            Expediente::where('numero_expediente', $expediente)->update([$field => $id]);
        }

        return response()->json(['id' => $id, 'nombre' => $nombre, 'tipo' => $tipo]);
    }
}
