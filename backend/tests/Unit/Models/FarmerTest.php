<?php

namespace Tests\Unit\Models;

use App\Models\Farmer;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmerTest extends TestCase
{
    use RefreshDatabase;

    public function test_farmer_can_be_created()
    {
        $farmer = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $this->assertInstanceOf(Farmer::class, $farmer);
        $this->assertEquals('João Silva', $farmer->name);
        $this->assertEquals('123.456.789-00', $farmer->cpf_cnpj);
        $this->assertEquals('(85) 99999-9999', $farmer->phone);
        $this->assertEquals('joao@email.com', $farmer->email);
        $this->assertEquals('Rua das Flores, 123', $farmer->address);
    }

    public function test_farmer_has_many_properties()
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

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $farmer->properties);
        $this->assertCount(2, $farmer->properties);
        $this->assertTrue($farmer->properties->contains($property1));
        $this->assertTrue($farmer->properties->contains($property2));
    }

    public function test_farmer_fillable_attributes()
    {
        $farmer = new Farmer();
        $fillable = $farmer->getFillable();

        $expected = ['name', 'cpf_cnpj', 'phone', 'email', 'address'];

        foreach ($expected as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_farmer_cpf_cnpj_is_unique()
    {
        Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Farmer::create([
            'name' => 'Maria Santos',
            'cpf_cnpj' => '123.456.789-00', // Mesmo CPF
            'phone' => '(85) 88888-8888',
            'email' => 'maria@email.com',
            'address' => 'Rua das Palmeiras, 456'
        ]);
    }

    public function test_farmer_phone_is_unique()
    {
        Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'teste@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Farmer::create([
            'name' => 'Maria Santos',
            'cpf_cnpj' => '987.654.321-00',
            'phone' => '(85) 99999-9999', // Mesmo telefone
            'email' => 'maria@email.com',
            'address' => 'Rua das Palmeiras, 456'
        ]);
    }

    public function test_farmer_email_is_unique()
    {
        Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Farmer::create([
            'name' => 'Maria Santos',
            'cpf_cnpj' => '987.654.321-00',
            'phone' => '(85) 88888-8888',
            'email' => 'joao@email.com', // Mesmo email
            'address' => 'Rua das Palmeiras, 456'
        ]);
    }
}
