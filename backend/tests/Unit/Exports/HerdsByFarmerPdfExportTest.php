<?php

namespace Tests\Unit\Exports;

use App\Models\Farmer;
use App\Models\Property;
use App\Models\Herd;
use App\Exports\HerdsByFarmerPdfExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HerdsByFarmerPdfExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_pdf_for_specific_farmer()
    {
        $farmer = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $property1 = Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        $property2 = Property::create([
            'name' => 'Sítio Boa Vista',
            'farmer_id' => $farmer->id,
            'municipality' => 'Maracanaú',
            'state' => 'CE',
            'state_registration' => '987654321',
            'total_area' => '75.25'
        ]);

        Herd::create([
            'species' => 'Bovino',
            'quantity' => 25,
            'property_id' => $property1->id,
            'purpose' => 'Corte'
        ]);

        Herd::create([
            'species' => 'Caprino',
            'quantity' => 15,
            'property_id' => $property2->id,
            'purpose' => 'Leite'
        ]);

        $export = new HerdsByFarmerPdfExport($farmer->id);
        $pdf = $export->generate();

        $this->assertInstanceOf(\Dompdf\Dompdf::class, $pdf);
        $this->assertNotEmpty($pdf->output());
    }

    public function test_can_generate_pdf_with_empty_herds()
    {
        $farmer = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $export = new HerdsByFarmerPdfExport($farmer->id);
        $pdf = $export->generate();

        $this->assertInstanceOf(\Dompdf\Dompdf::class, $pdf);
        $this->assertNotEmpty($pdf->output());
    }

}
