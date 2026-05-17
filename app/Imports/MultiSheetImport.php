<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetImport implements WithMultipleSheets
{
    /** @var SingleSheetImport[] */
    public array $importers = [];

    private array $selectedSheets;

    public function __construct(array $selectedSheets)
    {
        $this->selectedSheets = $selectedSheets;
        foreach ($selectedSheets as $name) {
            $this->importers[$name] = new SingleSheetImport();
        }
    }

    public function sheets(): array
    {
        return $this->importers;
    }

    public function totalImported(): int
    {
        return (int) array_sum(array_map(fn ($i) => $i->imported, $this->importers));
    }

    public function totalCreated(): int
    {
        return (int) array_sum(array_map(fn ($i) => $i->created, $this->importers));
    }

    public function totalUpdated(): int
    {
        return (int) array_sum(array_map(fn ($i) => $i->updated, $this->importers));
    }

    public function totalSkipped(): int
    {
        return (int) array_sum(array_map(fn ($i) => $i->skipped, $this->importers));
    }

    public function allErrors(): array
    {
        $all = [];
        foreach ($this->importers as $importer) {
            $all = array_merge($all, $importer->errors);
        }
        return $all;
    }

    public function allUnmatched(): array
    {
        $all = [];
        foreach ($this->importers as $importer) {
            $all = array_merge($all, $importer->unmatched);
        }
        return $all;
    }

    public function allNotImported(): array
    {
        $all = [];
        foreach ($this->importers as $sheetName => $importer) {
            foreach ($importer->notImported as $item) {
                $item['hoja'] = $sheetName;
                $all[] = $item;
            }
        }
        return $all;
    }
}
