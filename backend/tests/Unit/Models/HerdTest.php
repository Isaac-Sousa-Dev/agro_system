<?php

namespace Tests\Unit\Models;

use App\Models\Farmer;
use App\Models\Property;
use App\Models\Herd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HerdTest extends TestCase
{
    use RefreshDatabase;

    public function test_herd_can_be_created()
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

        $herd = Herd::create([
            'species' => 'Bovino',
            'quantity' => 25,
            'property_id' => $property->id,
            'purpose' => 'Corte'
        ]);

        $this->assertInstanceOf(Herd::class, $herd);
        $this->assertEquals('Bovino', $herd->species);
        $this->assertEquals(25, $herd->quantity);
        $this->assertEquals($property->id, $herd->property_id);
        $this->assertEquals('Corte', $herd->purpose);
    }

    public function test_herd_belongs_to_property()
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

        $herd = Herd::create([
            'species' => 'Bovino',
            'quantity' => 25,
            'property_id' => $property->id,
            'purpose' => 'Corte'
        ]);

        $this->assertInstanceOf(Property::class, $herd->property);
        $this->assertEquals($property->id, $herd->property->id);
        $this->assertEquals('Fazenda São João', $herd->property->name);
    }

    public function test_herd_fillable_attributes()
    {
        $herd = new Herd();
        $fillable = $herd->getFillable();

        $expected = ['species', 'quantity', 'property_id', 'purpose'];

        foreach ($expected as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_herd_quantity_cast()
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

        $herd = Herd::create([
            'species' => 'Bovino',
            'quantity' => 25,
            'property_id' => $property->id,
            'purpose' => 'Corte'
        ]);

        $this->assertIsInt($herd->quantity);
        $this->assertEquals(25, $herd->quantity);
    }

    public function test_herd_cascade_delete()
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

        $herd = Herd::create([
            'species' => 'Bovino',
            'quantity' => 25,
            'property_id' => $property->id,
            'purpose' => 'Corte'
        ]);

        // Deletar a propriedade deve deletar o rebanho
        $property->delete();

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
        $this->assertDatabaseMissing('herds', ['id' => $herd->id]);
    }

    public function test_herd_species_validation()
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

        // Teste com espécies válidas
        $validSpecies = ['Bovino', 'Caprino', 'Suíno', 'Ovino', 'Equino'];

        foreach ($validSpecies as $species) {
            $herd = Herd::create([
                'species' => $species,
                'quantity' => 10,
                'property_id' => $property->id,
                'purpose' => 'Corte'
            ]);

            $this->assertEquals($species, $herd->species);
        }
    }

    public function test_herd_purpose_validation()
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

        // Teste com finalidades válidas
        $validPurposes = ['Corte', 'Leite', 'Reprodução', 'Trabalho', 'Lazer'];

        foreach ($validPurposes as $purpose) {
            $herd = Herd::create([
                'species' => 'Bovino',
                'quantity' => 10,
                'property_id' => $property->id,
                'purpose' => $purpose
            ]);

            $this->assertEquals($purpose, $herd->purpose);
        }
    }
}
