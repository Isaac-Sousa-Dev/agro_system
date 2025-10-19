<?php

namespace Tests\Unit\Services;

use App\Models\Farmer;
use App\Models\Property;
use App\Models\ProductionUnit;
use App\Models\Herd;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $propertyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyService = new PropertyService(new Property());
    }

    public function test_can_create_property_without_image()
    {
        $farmer = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $data = [
            'name' => 'Fazenda São João',
            'farmer_id' => $farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ];

        $property = $this->propertyService->create($data);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('Fazenda São João', $property->name);
        $this->assertEquals($farmer->id, $property->farmer_id);
        $this->assertEquals('Fortaleza', $property->municipality);
        $this->assertEquals('CE', $property->state);
        $this->assertEquals('123456789', $property->state_registration);
        $this->assertEquals('100.50', $property->total_area);
    }

    public function test_can_create_property_with_image()
    {
        $farmer = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $data = [
            'name' => 'Fazenda São João',
            'farmer_id' => $farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ];

        $property = $this->propertyService->create($data);

        $this->assertInstanceOf(Property::class, $property);
    }

    public function test_can_create_property_with_production_units_and_herds()
    {
        $farmer = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $data = [
            'name' => 'Fazenda São João',
            'farmer_id' => $farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ];

        $productionUnits = [
            [
                'crop_name' => 'Laranja Pera',
                'total_area_ha' => '50.25',
                'geographic_coordinates' => '-3.7172,-38.5434'
            ],
            [
                'crop_name' => 'Melancia Crimson Sweet',
                'total_area_ha' => '30.00',
                'geographic_coordinates' => '-3.7200,-38.5400'
            ]
        ];

        $herds = [
            [
                'species' => 'Bovino',
                'quantity' => 25,
                'purpose' => 'Corte'
            ],
            [
                'species' => 'Caprino',
                'quantity' => 15,
                'purpose' => 'Leite'
            ]
        ];

        $property = $this->propertyService->create($data, $productionUnits, $herds);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertCount(2, $property->productionUnits);
        $this->assertCount(2, $property->herds);
        $this->assertEquals('Laranja Pera', $property->productionUnits->first()->crop_name);
        $this->assertEquals('Bovino', $property->herds->first()->species);
    }

    public function test_can_update_property()
    {
        $farmer = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $property = Property::create([
            'name' => 'Fazenda Antiga',
            'farmer_id' => $farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        $updateData = [
            'name' => 'Fazenda São João Atualizada',
            'farmer_id' => $farmer->id,
            'municipality' => 'Maracanaú',
            'state' => 'CE',
            'state_registration' => '987654321',
            'total_area' => '150.75'
        ];

        $updatedProperty = $this->propertyService->update($updateData, $property->id);

        $this->assertEquals('Fazenda São João Atualizada', $updatedProperty->name);
        $this->assertEquals('Maracanaú', $updatedProperty->municipality);
        $this->assertEquals('987654321', $updatedProperty->state_registration);
        $this->assertEquals('150.75', $updatedProperty->total_area);
    }

    public function test_can_update_property_with_new_image()
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

        $updateData = [
            'name' => 'Fazenda São João Atualizada',
            'farmer_id' => $farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ];

        $updatedProperty = $this->propertyService->update($updateData, $property->id);

        $this->assertEquals('Fazenda São João Atualizada', $updatedProperty->name);
    }

    public function test_can_update_property_with_production_units_and_herds()
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

        // Criar dados antigos
        $oldProductionUnit = ProductionUnit::create([
            'crop_name' => 'Laranja Antiga',
            'total_area_ha' => '25.00',
            'geographic_coordinates' => '-3.7000,-38.5000',
            'property_id' => $property->id
        ]);

        $oldHerd = Herd::create([
            'species' => 'Bovino Antigo',
            'quantity' => 10,
            'property_id' => $property->id,
            'purpose' => 'Corte'
        ]);

        $updateData = [
            'name' => 'Fazenda São João Atualizada',
            'farmer_id' => $farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ];

        $newProductionUnits = [
            [
                'crop_name' => 'Laranja Nova',
                'total_area_ha' => '50.00',
                'geographic_coordinates' => '-3.7200,-38.5400'
            ]
        ];

        $newHerds = [
            [
                'species' => 'Bovino Novo',
                'quantity' => 20,
                'purpose' => 'Leite'
            ]
        ];

        $updatedProperty = $this->propertyService->update($updateData, $property->id, $newProductionUnits, $newHerds);

        $this->assertEquals('Fazenda São João Atualizada', $updatedProperty->name);
        $this->assertCount(1, $updatedProperty->productionUnits);
        $this->assertCount(1, $updatedProperty->herds);
        $this->assertEquals('Laranja Nova', $updatedProperty->productionUnits->first()->crop_name);
        $this->assertEquals('Bovino Novo', $updatedProperty->herds->first()->species);

        // Dados antigos devem ter sido deletados
        $this->assertDatabaseMissing('production_units', ['id' => $oldProductionUnit->id]);
        $this->assertDatabaseMissing('herds', ['id' => $oldHerd->id]);
    }

    public function test_can_delete_property()
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

        $property->delete();

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
        $this->assertDatabaseMissing('production_units', ['id' => $productionUnit->id]);
        $this->assertDatabaseMissing('herds', ['id' => $herd->id]);
    }
}
