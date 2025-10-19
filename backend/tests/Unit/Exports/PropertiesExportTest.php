<?php

namespace Tests\Unit\Exports;

use App\Models\Farmer;
use App\Models\Property;
use App\Models\ProductionUnit;
use App\Models\Herd;
use App\Exports\PropertiesExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertiesExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_export_properties_without_filters()
    {
        $farmer = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $property = Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        $productionUnit = ProductionUnit::create([
            'crop_name' => 'Laranja Pera',
            'total_area_ha' => '50.25',
            'geographic_coordinates' => '-3.7172,-38.5434',
            'property_id' => $property->id
        ]);

        $herd = Herd::create([
            'species' => 'Bovino',
            'quantity' => 25,
            'property_id' => $property->id,
            'purpose' => 'Corte'
        ]);

        $export = new PropertiesExport();
        $collection = $export->collection();

        $this->assertCount(1, $collection);
        $this->assertEquals('Fazenda São João', $collection->first()->name);
    }

    public function test_can_export_properties_with_search_filter()
    {
        $farmer1 = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $farmer2 = Farmer::create([
            'name' => 'Maria Santos',
            'cpf_cnpj' => '987.654.321-00',
            'phone' => '(85) 88888-8888',
            'email' => 'maria@email.com',
            'address' => 'Rua das Palmeiras, 456'
        ]);

        Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $farmer1->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        Property::create([
            'name' => 'Sítio Boa Vista',
            'farmer_id' => $farmer2->id,
            'municipality' => 'Maracanaú',
            'state' => 'CE',
            'state_registration' => '987654321',
            'total_area' => '75.25'
        ]);

        $filters = ['search' => 'João'];
        $export = new PropertiesExport($filters);
        $collection = $export->collection();

        $this->assertCount(1, $collection);
        $this->assertEquals('Fazenda São João', $collection->first()->name);
    }

    public function test_can_export_properties_with_municipality_filter()
    {
        $farmer = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        Property::create([
            'name' => 'Sítio Boa Vista',
            'farmer_id' => $farmer->id,
            'municipality' => 'Maracanaú',
            'state' => 'CE',
            'state_registration' => '987654321',
            'total_area' => '75.25'
        ]);

        $filters = ['municipality' => 'Fortaleza'];
        $export = new PropertiesExport($filters);
        $collection = $export->collection();

        $this->assertCount(1, $collection);
        $this->assertEquals('Fortaleza', $collection->first()->municipality);
    }

    public function test_can_export_properties_with_state_filter()
    {
        $farmer = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        Property::create([
            'name' => 'Sítio Boa Vista',
            'farmer_id' => $farmer->id,
            'municipality' => 'Maracanaú',
            'state' => 'PE',
            'state_registration' => '987654321',
            'total_area' => '75.25'
        ]);

        $filters = ['state' => 'CE'];
        $export = new PropertiesExport($filters);
        $collection = $export->collection();

        $this->assertCount(1, $collection);
        $this->assertEquals('CE', $collection->first()->state);
    }

    public function test_can_export_properties_with_farmer_filter()
    {
        $farmer1 = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $farmer2 = Farmer::create([
            'name' => 'Maria Santos',
            'cpf_cnpj' => '987.654.321-00',
            'phone' => '(85) 88888-8888',
            'email' => 'maria@email.com',
            'address' => 'Rua das Palmeiras, 456'
        ]);

        Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $farmer1->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        Property::create([
            'name' => 'Sítio Boa Vista',
            'farmer_id' => $farmer2->id,
            'municipality' => 'Maracanaú',
            'state' => 'CE',
            'state_registration' => '987654321',
            'total_area' => '75.25'
        ]);

        $filters = ['farmer_id' => $farmer1->id];
        $export = new PropertiesExport($filters);
        $collection = $export->collection();

        $this->assertCount(1, $collection);
        $this->assertEquals($farmer1->id, $collection->first()->farmer_id);
    }

    public function test_export_headings_are_correct()
    {
        $export = new PropertiesExport();
        $headings = $export->headings();

        $expectedHeadings = [
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

        $this->assertEquals($expectedHeadings, $headings);
    }

    public function test_export_mapping_is_correct()
    {
        $farmer = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $property = Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        $productionUnit = ProductionUnit::create([
            'crop_name' => 'Laranja Pera',
            'total_area_ha' => '50.25',
            'geographic_coordinates' => '-3.7172,-38.5434',
            'property_id' => $property->id
        ]);

        $herd = Herd::create([
            'species' => 'Bovino',
            'quantity' => 25,
            'property_id' => $property->id,
            'purpose' => 'Corte'
        ]);

        $export = new PropertiesExport();
        $mappedData = $export->map($property->load(['farmer', 'productionUnits', 'herds']));

        $this->assertEquals($property->id, $mappedData[0]);
        $this->assertEquals('Fazenda São João', $mappedData[1]);
        $this->assertEquals('Fortaleza', $mappedData[2]);
        $this->assertEquals('CE', $mappedData[3]);
        $this->assertEquals('123456789', $mappedData[4]);
        $this->assertEquals('100,50', $mappedData[5]);
        $this->assertEquals('João Silva', $mappedData[6]);
        $this->assertEquals(1, $mappedData[7]); // production units count
        $this->assertEquals(1, $mappedData[8]); // herds count
    }

    public function test_export_column_widths_are_set()
    {
        $export = new PropertiesExport();
        $columnWidths = $export->columnWidths();

        $this->assertArrayHasKey('A', $columnWidths);
        $this->assertArrayHasKey('B', $columnWidths);
        $this->assertArrayHasKey('C', $columnWidths);
        $this->assertArrayHasKey('D', $columnWidths);
        $this->assertArrayHasKey('E', $columnWidths);
        $this->assertArrayHasKey('F', $columnWidths);
        $this->assertArrayHasKey('G', $columnWidths);
        $this->assertArrayHasKey('H', $columnWidths);
        $this->assertArrayHasKey('J', $columnWidths);
        $this->assertArrayHasKey('K', $columnWidths);
        $this->assertArrayHasKey('L', $columnWidths);
    }

    public function test_export_handles_empty_collection()
    {
        $export = new PropertiesExport();
        $collection = $export->collection();

        $this->assertCount(0, $collection);
    }
}
