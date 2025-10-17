<?php

namespace App\Exports;

use App\Models\Farmer;
use App\Models\Herd;
use Dompdf\Dompdf;
use Dompdf\Options;

class HerdsByFarmerPdfExport
{
    protected $farmerId;
    protected $farmer;

    public function __construct($farmerId = null)
    {
        $this->farmerId = $farmerId;
        if ($farmerId) {
            $this->farmer = Farmer::with(['properties.herds'])->find($farmerId);
        }
    }

    public function generate()
    {
        // Configurar opções do domPDF
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);

        // Gerar HTML
        $html = $this->generateHtml();

        // Carregar HTML no domPDF
        $dompdf->loadHtml($html);

        // Configurar papel e orientação
        $dompdf->setPaper('A4', 'portrait');

        // Renderizar PDF
        $dompdf->render();

        return $dompdf;
    }

    protected function generateHtml()
    {
        $farmer = $this->farmer;
        $herds = $this->getHerdsData();

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Relatório de Rebanhos por Produtor</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.4;
                    color: #333;
                    margin: 0;
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #2E8B57;
                    padding-bottom: 20px;
                }
                .header h1 {
                    color: #2E8B57;
                    margin: 0;
                    font-size: 24px;
                }
                .header h2 {
                    color: #666;
                    margin: 10px 0 0 0;
                    font-size: 18px;
                    font-weight: normal;
                }
                .farmer-info {
                    background-color: #f8f9fa;
                    padding: 15px;
                    border-radius: 5px;
                    margin-bottom: 25px;
                    border-left: 4px solid #2E8B57;
                }
                .farmer-info h3 {
                    color: #2E8B57;
                    margin: 0 0 10px 0;
                    font-size: 16px;
                }
                .farmer-details {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 10px;
                }
                .farmer-details div {
                    margin-bottom: 5px;
                }
                .farmer-details strong {
                    color: #555;
                }
                .summary {
                    background-color: #e8f5e8;
                    padding: 15px;
                    border-radius: 5px;
                    margin-bottom: 25px;
                    text-align: center;
                }
                .summary h3 {
                    color: #2E8B57;
                    margin: 0 0 10px 0;
                    font-size: 16px;
                }
                .summary-stats {
                    display: flex;
                    justify-content: space-around;
                    flex-wrap: wrap;
                }
                .stat-item {
                    text-align: center;
                    margin: 5px;
                }
                .stat-number {
                    font-size: 20px;
                    font-weight: bold;
                    color: #2E8B57;
                }
                .stat-label {
                    font-size: 11px;
                    color: #666;
                }
                .herds-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                .herds-table th {
                    background-color: #2E8B57;
                    color: white;
                    padding: 12px 8px;
                    text-align: left;
                    font-weight: bold;
                    font-size: 11px;
                }
                .herds-table td {
                    padding: 10px 8px;
                    border-bottom: 1px solid #ddd;
                    font-size: 11px;
                }
                .herds-table tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                .herds-table tr:hover {
                    background-color: #f0f8f0;
                }
                .property-name {
                    font-weight: bold;
                    color: #2E8B57;
                }
                .species {
                    font-weight: bold;
                    color: #333;
                }
                .quantity {
                    text-align: center;
                    font-weight: bold;
                    color: #2E8B57;
                }
                .purpose {
                    font-style: italic;
                    color: #666;
                }
                .no-data {
                    text-align: center;
                    color: #666;
                    font-style: italic;
                    padding: 40px;
                    background-color: #f8f9fa;
                    border-radius: 5px;
                }
                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 10px;
                    color: #666;
                    border-top: 1px solid #ddd;
                    padding-top: 10px;
                }
                .page-break {
                    page-break-before: always;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Relatório de Rebanhos por Produtor</h1>
                <h2>Sistema de Gestão Agropecuária</h2>
            </div>';

        if ($farmer) {
            $html .= $this->generateFarmerInfo($farmer);
            $html .= $this->generateSummary($herds);
            $html .= $this->generateHerdsTable($herds);
        } else {
            $html .= $this->generateAllFarmersSummary($herds);
        }

        $html .= '
            <div class="footer">
                <p>Relatório gerado em ' . now()->format('d/m/Y H:i:s') . ' | Sistema de Gestão Agropecuária</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    protected function generateFarmerInfo($farmer)
    {
        return '
        <div class="farmer-info">
            <h3>Informações do Produtor</h3>
            <div class="farmer-details">
                <div><strong>Nome:</strong> ' . htmlspecialchars($farmer->name) . '</div>
                <div><strong>CPF/CNPJ:</strong> ' . htmlspecialchars($farmer->cpf_cnpj ?? 'N/A') . '</div>
                <div><strong>Telefone:</strong> ' . htmlspecialchars($farmer->phone ?? 'N/A') . '</div>
                <div><strong>Email:</strong> ' . htmlspecialchars($farmer->email ?? 'N/A') . '</div>
                <div><strong>Endereço:</strong> ' . htmlspecialchars($farmer->address ?? 'N/A') . '</div>
                <div><strong>Total de Propriedades:</strong> ' . $farmer->properties->count() . '</div>
            </div>
        </div>';
    }

    protected function generateSummary($herds)
    {
        $totalHerds = $herds->count();
        $totalAnimals = $herds->sum('quantity');
        $speciesCount = $herds->groupBy('species')->count();
        $propertiesCount = $herds->groupBy('property_id')->count();

        return '
        <div class="summary">
            <h3>Resumo dos Rebanhos</h3>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-number">' . $totalHerds . '</div>
                    <div class="stat-label">Total de Rebanhos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . number_format($totalAnimals, 0, ',', '.') . '</div>
                    <div class="stat-label">Total de Animais</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . $speciesCount . '</div>
                    <div class="stat-label">Espécies Diferentes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . $propertiesCount . '</div>
                    <div class="stat-label">Propriedades</div>
                </div>
            </div>
        </div>';
    }

    protected function generateAllFarmersSummary($herds)
    {
        $totalHerds = $herds->count();
        $totalAnimals = $herds->sum('quantity');
        $speciesCount = $herds->groupBy('species')->count();
        $farmersCount = $herds->groupBy('property.farmer_id')->count();

        return '
        <div class="summary">
            <h3>Resumo Geral dos Rebanhos</h3>
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-number">' . $totalHerds . '</div>
                    <div class="stat-label">Total de Rebanhos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . number_format($totalAnimals, 0, ',', '.') . '</div>
                    <div class="stat-label">Total de Animais</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . $speciesCount . '</div>
                    <div class="stat-label">Espécies Diferentes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">' . $farmersCount . '</div>
                    <div class="stat-label">Produtores</div>
                </div>
            </div>
        </div>';
    }

    protected function generateHerdsTable($herds)
    {
        if ($herds->isEmpty()) {
            return '<div class="no-data">Nenhum rebanho encontrado para este produtor.</div>';
        }

        $html = '
        <table class="herds-table">
            <thead>
                <tr>
                    <th>Propriedade</th>
                    <th>Espécie</th>
                    <th>Quantidade</th>
                    <th>Finalidade</th>
                    <th>Município</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($herds as $herd) {
            $html .= '
                <tr>
                    <td class="property-name">' . htmlspecialchars($herd->property->name) . '</td>
                    <td class="species">' . htmlspecialchars($herd->species) . '</td>
                    <td class="quantity">' . number_format($herd->quantity, 0, ',', '.') . '</td>
                    <td class="purpose">' . htmlspecialchars($herd->purpose) . '</td>
                    <td>' . htmlspecialchars($herd->property->municipality) . '</td>
                    <td>' . htmlspecialchars($herd->property->state) . '</td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>';

        return $html;
    }

    protected function getHerdsData()
    {
        if ($this->farmerId) {
            // Rebanhos de um produtor específico
            return Herd::with(['property.farmer'])
                ->whereHas('property', function($query) {
                    $query->where('farmer_id', $this->farmerId);
                })
                ->orderBy('property_id')
                ->orderBy('species')
                ->get();
        } else {
            // Todos os rebanhos agrupados por produtor
            return Herd::with(['property.farmer'])
                ->orderBy('property.farmer.name')
                ->orderBy('property.name')
                ->orderBy('species')
                ->get();
        }
    }
}
