<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;
class OrderReportsExportByRange implements FromCollection, WithHeadings
{
    use Exportable;

    public function __construct($title, $excelData)
    {
        $this->title = $title;
        $this->excelData = $excelData;
    }

    public function collection()
    {
        return collect($this->excelData);
    }

    public function headings(): array
    {
        return $this->title;
    }
}
