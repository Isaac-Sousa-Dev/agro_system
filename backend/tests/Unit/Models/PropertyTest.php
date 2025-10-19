<?php

namespace Tests\Unit\Models;

use App\Models\Farmer;
use App\Models\Property;
use App\Models\ProductionUnit;
use App\Models\Herd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_can_be_created()
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
            'total_area' => '100.50',
            'image' => 'properties/test-image.jpg'
        ]);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('Fazenda São João', $property->name);
        $this->assertEquals($farmer->id, $property->farmer_id);
        $this->assertEquals('Fortaleza', $property->municipality);
        $this->assertEquals('CE', $property->state);
        $this->assertEquals('123456789', $property->state_registration);
        $this->assertEquals('100.50', $property->total_area);
        $this->assertEquals('properties/test-image.jpg', $property->image);
    }

    public function test_property_belongs_to_farmer()
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

        $this->assertInstanceOf(Farmer::class, $property->farmer);
        $this->assertEquals($farmer->id, $property->farmer->id);
        $this->assertEquals('João Silva', $property->farmer->name);
    }

    public function test_property_has_many_production_units()
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

        $productionUnit1 = ProductionUnit::create([
            'crop_name' => 'Laranja Pera',
            'total_area_ha' => '50.25',
            'geographic_coordinates' => '-3.7172,-38.5434',
            'property_id' => $property->id
        ]);

        $productionUnit2 = ProductionUnit::create([
            'crop_name' => 'Melancia Crimson Sweet',
            'total_area_ha' => '30.00',
            'geographic_coordinates' => '-3.7200,-38.5400',
            'property_id' => $property->id
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $property->productionUnits);
        $this->assertCount(2, $property->productionUnits);
        $this->assertTrue($property->productionUnits->contains($productionUnit1));
        $this->assertTrue($property->productionUnits->contains($productionUnit2));
    }

    public function test_property_has_many_herds()
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

        $herd1 = Herd::create([
            'species' => 'Bovino',
            'quantity' => 25,
            'property_id' => $property->id,
            'purpose' => 'Corte'
        ]);

        $herd2 = Herd::create([
            'species' => 'Caprino',
            'quantity' => 15,
            'property_id' => $property->id,
            'purpose' => 'Leite'
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $property->herds);
        $this->assertCount(2, $property->herds);
        $this->assertTrue($property->herds->contains($herd1));
        $this->assertTrue($property->herds->contains($herd2));
    }

    public function test_property_fillable_attributes()
    {
        $property = new Property();
        $fillable = $property->getFillable();

        $expected = [
            'name', 'municipality', 'state', 'image',
            'state_registration', 'total_area', 'farmer_id'
        ];

        foreach ($expected as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_property_total_area_cast()
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

        $this->assertEquals(100.50, $property->total_area);
    }

    public function test_property_cascade_delete()
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

        // Deletar o produtor deve deletar a propriedade e seus relacionamentos
        $farmer->delete();

        $this->assertDatabaseMissing('farmers', ['id' => $farmer->id]);
        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
        $this->assertDatabaseMissing('production_units', ['id' => $productionUnit->id]);
        $this->assertDatabaseMissing('herds', ['id' => $herd->id]);
    }
}
