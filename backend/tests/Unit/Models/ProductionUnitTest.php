<?php

namespace Tests\Unit\Models;

use App\Models\Farmer;
use App\Models\Property;
use App\Models\ProductionUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_unit_can_be_created()
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

        $this->assertInstanceOf(ProductionUnit::class, $productionUnit);
        $this->assertEquals('Laranja Pera', $productionUnit->crop_name);
        $this->assertEquals('50.25', $productionUnit->total_area_ha);
        $this->assertEquals('-3.7172,-38.5434', $productionUnit->geographic_coordinates);
        $this->assertEquals($property->id, $productionUnit->property_id);
    }

    public function test_production_unit_belongs_to_property()
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

        $this->assertInstanceOf(Property::class, $productionUnit->property);
        $this->assertEquals($property->id, $productionUnit->property->id);
        $this->assertEquals('Fazenda São João', $productionUnit->property->name);
    }

    public function test_production_unit_fillable_attributes()
    {
        $productionUnit = new ProductionUnit();
        $fillable = $productionUnit->getFillable();

        $expected = ['crop_name', 'total_area_ha', 'geographic_coordinates', 'property_id'];

        foreach ($expected as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_production_unit_total_area_ha_cast()
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

        // $this->assertIsFloat($productionUnit->total_area_ha);
        $this->assertEquals(50.25, $productionUnit->total_area_ha);
    }

    public function test_production_unit_cascade_delete()
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

        // Deletar a propriedade deve deletar a unidade de produção
        $property->delete();

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
        $this->assertDatabaseMissing('production_units', ['id' => $productionUnit->id]);
    }
}
