<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class AccountsExport implements FromCollection, WithEvents, ShouldAutoSize
{
    protected $data;
    protected $request;

    public function __construct($data, $request = null)
    {
        $this->data = $data;
        $this->request = $request;
    }

    public function collection()
    {
        $rows = collect();

        // ===== TITLE & FILTER INFO =====
        $rows->push(['Accounts Report']);
        $rows->push(['Date Range: ' . ($this->request->date_range ?? 'All')]);
        $rows->push(['Filter: ' . $this->formatFilterName($this->request->filter ?? null)]);
        $rows->push(['Search: ' . ($this->request->search ?? '-')]);
        $rows->push([]);

        // ===== HEADER =====
        $rows->push(['Date','Type','Category','Amount','Note']);

        // ===== DATA =====
        foreach ($this->data as $row) {
            $rows->push([
                $row['date'],
                $row['type'],
                $row['category'],
                $row['amount'],
                $row['note'],
            ]);
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // ===== ROW SETUP =====
                $headerRow = 5;
                $dataStart = 6;
                $rowCount = $dataStart + count($this->data) - 1;

                // ===== MERGE CELLS =====
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->mergeCells('A3:E3');
                $sheet->mergeCells('A4:E4');

                // ===== ALIGN CENTER =====
                $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal('center');

                // ===== TITLE STYLE =====
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

                // ===== HEADER STYLE =====
                $sheet->getStyle("A{$headerRow}:E{$headerRow}")
                    ->getFont()
                    ->setBold(true);

                // OPTIONAL HEADER BG
                $sheet->getStyle("A{$headerRow}:E{$headerRow}")
                    ->getFill()
                    ->setFillType('solid')
                    ->getStartColor()
                    ->setRGB('D9D9D9');

                // ===== TABLE BORDER =====
                $sheet->getStyle("A{$headerRow}:E{$rowCount}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle('thin');

                // ===== OUTER BORDER =====
                $sheet->getStyle("A1:E{$rowCount}")
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle('medium');
            }
        ];
    }
    private function formatFilterName($filter)
    {
        if (!$filter) return 'All';

        if (in_array($filter, ['income', 'expense'])) {
            return ucfirst($filter);
        }

        if (str_starts_with($filter, 'cat_')) {
            $cat = str_replace('cat_', '', $filter);
            return ucwords(str_replace('_', ' ', $cat));
        }

        return ucfirst($filter);
    }
}
