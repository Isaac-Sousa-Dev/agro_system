<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Farmer;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $farmer;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@email.com',
            'password' => Hash::make('password123')
        ]);

        $this->farmer = Farmer::create([
            'name' => 'João Silva',
            'cpf_cnpj' => '123.456.789-00',
            'phone' => '(85) 99999-9999',
            'email' => 'joao@email.com',
            'address' => 'Rua das Flores, 123'
        ]);
    }

    protected function getAuthHeaders()
    {
        $token = $this->user->createToken('auth_token')->plainTextToken;
        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_can_list_properties()
    {
        Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $this->farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->getJson('/api/properties');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'image',
                            'municipality',
                            'state',
                            'state_registration',
                            'total_area',
                            'farmer_id',
                            'farmer',
                            'productionUnits',
                            'herds',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'links',
                    'meta'
                ]);
    }

    public function test_can_list_properties_with_filters()
    {
        Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $this->farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->getJson('/api/properties?municipality=Fortaleza&state=CE');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_create_property()
    {
        $propertyData = [
            'name' => 'Fazenda São João',
            'farmer_id' => $this->farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50',
            'productionUnits' => [
                [
                    'crop_name' => 'Laranja Pera',
                    'total_area_ha' => '50.25',
                    'geographic_coordinates' => '-3.7172,-38.5434'
                ]
            ],
            'herds' => [
                [
                    'species' => 'Bovino',
                    'quantity' => 25,
                    'purpose' => 'Corte'
                ]
            ]
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->postJson('/api/properties', $propertyData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'municipality',
                        'state',
                        'state_registration',
                        'total_area',
                        'farmer_id'
                    ]
                ]);

        $this->assertDatabaseHas('properties', [
            'name' => 'Fazenda São João',
            'farmer_id' => $this->farmer->id
        ]);
    }

    public function test_cannot_create_property_without_authentication()
    {
        $propertyData = [
            'name' => 'Fazenda São João',
            'farmer_id' => $this->farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ];

        $response = $this->postJson('/api/properties', $propertyData);

        $response->assertStatus(401);
    }

    public function test_cannot_create_property_with_invalid_data()
    {
        $propertyData = [
            'name' => '', // Nome vazio
            'farmer_id' => $this->farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->postJson('/api/properties', $propertyData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    public function test_can_show_property()
    {
        $property = Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $this->farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->getJson("/api/properties/{$property->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'municipality',
                        'state',
                        'state_registration',
                        'total_area',
                        'farmer_id',
                        'farmer',
                        'production_units',
                        'herds'
                    ]
                ]);
    }

    public function test_can_update_property()
    {
        $property = Property::create([
            'name' => 'Fazenda Antiga',
            'farmer_id' => $this->farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        $updateData = [
            'name' => 'Fazenda São João Atualizada',
            'farmer_id' => $this->farmer->id,
            'municipality' => 'Maracanaú',
            'state' => 'CE',
            'state_registration' => '987654321',
            'total_area' => '150.75'
        ];

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->putJson("/api/properties/{$property->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'municipality',
                        'state',
                        'state_registration',
                        'total_area',
                        'farmer_id'
                    ]
                ]);

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'name' => 'Fazenda São João Atualizada',
            'municipality' => 'Maracanaú'
        ]);
    }

    public function test_can_delete_property()
    {
        $property = Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $this->farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->deleteJson("/api/properties/{$property->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Property deleted successfully'
                ]);

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }

    public function test_can_export_properties_to_excel()
    {
        Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $this->farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->getJson('/api/properties/export/excel');

        $response->assertStatus(200)
                ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_preview_properties_export()
    {
        Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $this->farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->getJson('/api/properties/export/preview');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'total_records',
                        'preview_records' => [
                            '*' => [
                                'id',
                                'name',
                                'municipality',
                                'state',
                                'state_registration',
                                'total_area',
                                'farmer_name',
                                'farmer_cpf',
                                'production_units_count',
                                'herds_count',
                                'created_at',
                                'updated_at'
                            ]
                        ],
                        'filters_applied'
                    ]
                ]);
    }

    public function test_can_export_properties_with_filters()
    {
        Property::create([
            'name' => 'Fazenda São João',
            'farmer_id' => $this->farmer->id,
            'municipality' => 'Fortaleza',
            'state' => 'CE',
            'state_registration' => '123456789',
            'total_area' => '100.50'
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())
                         ->getJson('/api/properties/export/excel?municipality=Fortaleza&state=CE');

        $response->assertStatus(200);
    }
}
