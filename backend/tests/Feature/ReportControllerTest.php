<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Farmer;
use App\Models\Property;
use App\Models\ProductionUnit;
use App\Models\Herd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@email.com',
            'password' => Hash::make('password123')
        ]);
    }

    protected function getAuthHeaders()
    {
        $token = $this->user->createToken('auth_token')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_can_get_dashboard_data()
    {
        // Criar dados de teste
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

        $property1 = Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $farmer1->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        $property2 = Property::create([
            'name' => 'Sítio Boa Vista',
            'farmer_id' => $farmer2->id,
            'municipality' => 'Maracanaú',
            'state' => 'CE',
            'state_registration' => '987654321',
            'total_area' => '75.25'
        ]);

        $productionUnit1 = ProductionUnit::create([
            'crop_name' => 'Laranja Pera',
            'total_area_ha' => '50.25',
            'geographic_coordinates' => '-3.7172,-38.5434',
            'property_id' => $property1->id
        ]);

        $productionUnit2 = ProductionUnit::create([
            'crop_name' => 'Melancia Crimson Sweet',
            'total_area_ha' => '30.00',
            'geographic_coordinates' => '-3.7200,-38.5400',
            'property_id' => $property2->id
        ]);

        $herd1 = Herd::create([
            'species' => 'Bovino',
            'quantity' => 25,
            'property_id' => $property1->id,
            'purpose' => 'Corte'
        ]);

        $herd2 = Herd::create([
            'species' => 'Caprino',
            'quantity' => 15,
            'property_id' => $property2->id,
            'purpose' => 'Leite'
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->getJson('/api/reports/dashboard');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'quantityProducers',
                        'quantityProperties',
                        'quantityProductionUnits',
                        'quantityHerds',
                        'propertiesByMunicipality' => [
                            '*' => [
                                'municipality',
                                'count'
                            ]
                        ],
                        'animalsBySpecies' => [
                            '*' => [
                                'species',
                                'count'
                            ]
                        ],
                        'hectaresByCrop' => [
                            '*' => [
                                'crop',
                                'hectares'
                            ]
                        ]
                    ]
                ]);

        $data = $response->json('data');
        $this->assertEquals(2, $data['quantityProducers']);
        $this->assertEquals(2, $data['quantityProperties']);
        $this->assertEquals(2, $data['quantityProductionUnits']);
        $this->assertEquals(2, $data['quantityHerds']);
    }

    public function test_dashboard_properties_by_municipality()
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
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '987654321',
            'total_area' => '75.25'
        ]);

        Property::create([
            'name' => 'Fazenda Maracanaú',
            'farmer_id' => $farmer->id,
            'municipality' => 'Maracanaú',
            'state' => 'CE',
            'state_registration' => '111222333',
            'total_area' => '50.00'
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->getJson('/api/reports/dashboard');

        $response->assertStatus(200);

        $propertiesByMunicipality = $response->json('data.propertiesByMunicipality');

        // Verificar se Fortaleza tem 2 propriedades
        $fortaleza = collect($propertiesByMunicipality)->firstWhere('municipality', 'Fortaleza');
        $this->assertEquals(2, $fortaleza['count']);

        // Verificar se Maracanaú tem 1 propriedade
        $maracanau = collect($propertiesByMunicipality)->firstWhere('municipality', 'Maracanaú');
        $this->assertEquals(1, $maracanau['count']);
    }

    public function test_dashboard_animals_by_species()
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

        Herd::create([
            'species' => 'Bovino',
            'quantity' => 25,
            'property_id' => $property->id,
            'purpose' => 'Corte'
        ]);

        Herd::create([
            'species' => 'Caprino',
            'quantity' => 15,
            'property_id' => $property->id,
            'purpose' => 'Leite'
        ]);

        Herd::create([
            'species' => 'Suíno',
            'quantity' => 10,
            'property_id' => $property->id,
            'purpose' => 'Corte'
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->getJson('/api/reports/dashboard');

        $response->assertStatus(200);

        $animalsBySpecies = $response->json('data.animalsBySpecies');

        // Verificar se Bovino tem 25 animais
        $bovino = collect($animalsBySpecies)->firstWhere('species', 'Bovino');
        $this->assertEquals(25, $bovino['count']);

        // Verificar se Caprino tem 15 animais
        $caprino = collect($animalsBySpecies)->firstWhere('species', 'Caprino');
        $this->assertEquals(15, $caprino['count']);

        // Verificar se Suíno tem 10 animais
        $suino = collect($animalsBySpecies)->firstWhere('species', 'Suíno');
        $this->assertEquals(10, $suino['count']);
    }

    public function test_dashboard_hectares_by_crop()
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

        ProductionUnit::create([
            'crop_name' => 'Laranja Pera',
            'total_area_ha' => '50.25',
            'geographic_coordinates' => '-3.7172,-38.5434',
            'property_id' => $property1->id
        ]);

        ProductionUnit::create([
            'crop_name' => 'Laranja Pera',
            'total_area_ha' => '30.00',
            'geographic_coordinates' => '-3.7200,-38.5400',
            'property_id' => $property2->id
        ]);

        ProductionUnit::create([
            'crop_name' => 'Melancia Crimson Sweet',
            'total_area_ha' => '25.50',
            'geographic_coordinates' => '-3.7150,-38.5450',
            'property_id' => $property1->id
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->getJson('/api/reports/dashboard');

        $response->assertStatus(200);

        $hectaresByCrop = $response->json('data.hectaresByCrop');

        // Verificar se Laranja Pera tem 80.25 hectares (50.25 + 30.00)
        $laranja = collect($hectaresByCrop)->firstWhere('crop', 'Laranja Pera');
        $this->assertEquals(80.25, $laranja['hectares']);

        // Verificar se Melancia Crimson Sweet tem 25.50 hectares
        $melancia = collect($hectaresByCrop)->firstWhere('crop', 'Melancia Crimson Sweet');
        $this->assertEquals(25.50, $melancia['hectares']);
    }

    public function test_dashboard_handles_empty_data()
    {
        $response = $this->withHeaders($this->getAuthHeaders())
                         ->getJson('/api/reports/dashboard');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals(0, $data['quantityProducers']);
        $this->assertEquals(0, $data['quantityProperties']);
        $this->assertEquals(0, $data['quantityProductionUnits']);
        $this->assertEquals(0, $data['quantityHerds']);
        $this->assertEmpty($data['propertiesByMunicipality']);
        $this->assertEmpty($data['animalsBySpecies']);
        $this->assertEmpty($data['hectaresByCrop']);
    }

    public function test_dashboard_requires_authentication()
    {
        $response = $this->getJson('/api/reports/dashboard');

        $response->assertStatus(401);
    }
}
