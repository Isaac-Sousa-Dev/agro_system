<?php

namespace Tests\Unit\Services;

use App\Models\Farmer;
use App\Models\Property;
use App\Services\FarmerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $farmerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->farmerService = new FarmerService(new Farmer());
    }

    public function test_can_create_farmer_without_properties()
    {
        $data = [
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ];

        $farmer = $this->farmerService->create($data);

        $this->assertInstanceOf(Farmer::class, $farmer);
        $this->assertEquals('João Silva', $farmer->name);
        $this->assertEquals('123.456.789-00', $farmer->cpf_cnpj);
        $this->assertEquals('(85) 99999-9999', $farmer->phone);
        $this->assertEquals('joao@email.com', $farmer->email);
        $this->assertEquals('Rua das Flores, 123', $farmer->address);
    }

    public function test_can_create_farmer_with_properties()
    {
        $data = [
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ];

        $properties = [
            [
                'name' => 'Fazenda São João',
                'municipality' => 'Fortaleza',
                'state' => 'CE',
                'state_registration' => '123456789',
                'total_area' => '100.50'
            ],
            [
                'name' => 'Sítio Boa Vista',
                'municipality' => 'Maracanaú',
                'state' => 'CE',
                'state_registration' => '987654321',
                'total_area' => '75.25'
            ]
        ];

        $farmer = $this->farmerService->create($data, $properties);

        $this->assertInstanceOf(Farmer::class, $farmer);
        $this->assertCount(2, $farmer->properties);
        $this->assertEquals('Fazenda São João', $farmer->properties->first()->name);
        $this->assertEquals('Sítio Boa Vista', $farmer->properties->last()->name);
    }

    public function test_can_update_farmer()
    {
        $farmer = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $updateData = [
            'name' => 'João Silva Santos',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 98888-8888',
            'email' => 'joao.santos@email.com',
            'address' => 'Rua das Flores, 456'
        ];

        $updatedFarmer = $this->farmerService->update($updateData, $farmer->id);

        $this->assertEquals('João Silva Santos', $updatedFarmer->name);
        $this->assertEquals('(85) 98888-8888', $updatedFarmer->phone);
        $this->assertEquals('joao.santos@email.com', $updatedFarmer->email);
        $this->assertEquals('Rua das Flores, 456', $updatedFarmer->address);
    }

    public function test_can_update_farmer_with_properties()
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
            'name' => 'João Silva Santos',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 98888-8888',
            'email' => 'joao.santos@email.com',
            'address' => 'Rua das Flores, 456'
        ];

        $newProperties = [
            [
                'name' => 'Fazenda Nova',
                'municipality' => 'Maracanaú',
                'state' => 'CE',
                'state_registration' => '987654321',
                'total_area' => '150.75'
            ]
        ];

        $updatedFarmer = $this->farmerService->update($updateData, $farmer->id, $newProperties);

        $this->assertEquals('João Silva Santos', $updatedFarmer->name);
        $this->assertCount(1, $updatedFarmer->properties);
        $this->assertEquals('Fazenda Nova', $updatedFarmer->properties->first()->name);

        // A propriedade antiga deve ter sido deletada
        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }

    public function test_can_delete_farmer()
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

        $farmer->delete();

        $this->assertDatabaseMissing('farmers', ['id' => $farmer->id]);
        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }
}
