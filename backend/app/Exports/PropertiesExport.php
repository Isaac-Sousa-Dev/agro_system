<?php

namespace App\Exports;

use App\Models\Property;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PropertiesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome da Propriedade',
            'Município',
            'Estado',
            'Registro Estadual',
            'Área Total (hectares)',
            'Nome do Produtor',
            'Unidades de Produção',
            'Rebanhos',
            'Data de Criação',
            'Última Atualização'
        ];
    }

    public function map($property): array
    {
        return [
            $property->id,
            $property->name,
            $property->municipality,
            $property->state,
            $property->state_registration,
            number_format($property->total_area, 2, ',', '.'),
            $property->farmer->name ?? 'N/A',
            $property->productionUnits->count(),
            $property->herds->count(),
            $property->created_at->format('d/m/Y H:i:s'),
            $property->updated_at->format('d/m/Y H:i:s'),
        ];
    }

    public function collection()
    {
        $query = Property::with(['farmer', 'productionUnits', 'herds']);

        // Aplicar filtros se fornecidos
        if (isset($this->filters['search']) && !empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhereHas('farmer', function($farmerQuery) use ($search) {
                      $farmerQuery->where('name', 'ilike', "%{$search}%");
                  });
            });
        }

        if (isset($this->filters['municipality']) && !empty($this->filters['municipality'])) {
            $query->where('municipality', $this->filters['municipality']);
        }

        if (isset($this->filters['state']) && !empty($this->filters['state'])) {
            $query->where('state', $this->filters['state']);
        }

        if (isset($this->filters['farmer_id']) && !empty($this->filters['farmer_id'])) {
            $query->where('farmer_id', $this->filters['farmer_id']);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 25,  // Nome da Propriedade
            'C' => 20,  // Município
            'D' => 15,  // Estado
            'E' => 20,  // Registro Estadual
            'F' => 18,  // Área Total
            'G' => 25,  // Nome do Produtor
            'H' => 20,  // Unidades de Produção
            'J' => 15,  // Rebanhos
            'K' => 20,  // Data de Criação
            'L' => 20,  // Última Atualização
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo do cabeçalho
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2E8B57']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Congelar primeira linha
                $sheet->freezePane('A2');

                // Aplicar bordas em todas as células com dados
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                $sheet->getStyle('A1:' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // Aplicar formatação de data nas colunas de data
                $sheet->getStyle('K2:L' . $lastRow)->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm:ss');

                // Aplicar formatação de número na coluna de área
                $sheet->getStyle('F2:F' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
            },
        ];
    }
}
