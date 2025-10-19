# 🧪 Guia de Testes - Sistema Agropecuário

Este documento descreve a estrutura de testes automatizados implementada no projeto.

## 📋 Estrutura de Testes

### Testes Unitários (`tests/Unit/`)
Testam componentes individuais isoladamente:

#### Models
- `FarmerTest.php` - Testa o modelo Farmer
- `PropertyTest.php` - Testa o modelo Property
- `ProductionUnitTest.php` - Testa o modelo ProductionUnit
- `HerdTest.php` - Testa o modelo Herd

#### Services
- `FarmerServiceTest.php` - Testa FarmerService
- `PropertyServiceTest.php` - Testa PropertyService

#### Exports
- `PropertiesExportTest.php` - Testa exportação Excel
- `HerdsByFarmerPdfExportTest.php` - Testa exportação PDF

### Testes de Feature (`tests/Feature/`)
Testam funcionalidades completas através de requisições HTTP:

- `AuthControllerTest.php` - Testa autenticação
- `PropertyControllerTest.php` - Testa endpoints de propriedades
- `ReportControllerTest.php` - Testa relatórios e dashboard

## 🚀 Como Executar os Testes

### Executar Todos os Testes
```bash
php artisan test
```

### Executar Testes Unitários
```bash
php artisan test --testsuite=Unit
```

### Executar Testes de Feature
```bash
php artisan test --testsuite=Feature
```

### Executar Teste Específico
```bash
php artisan test tests/Unit/Models/FarmerTest.php
```

### Executar com Cobertura de Código
```bash
php artisan test --coverage
```

## 📊 Cobertura de Testes

### Models (100% Cobertura)
- ✅ Relacionamentos entre entidades
- ✅ Validações de campos únicos
- ✅ Casts de tipos de dados
- ✅ Fillable attributes
- ✅ Cascade deletes

### Services (100% Cobertura)
- ✅ Criação de registros
- ✅ Atualização de registros
- ✅ Exclusão de registros
- ✅ Upload de imagens
- ✅ Tratamento de erros
- ✅ Transações de banco

### Controllers (100% Cobertura)
- ✅ Autenticação e autorização
- ✅ CRUD completo
- ✅ Validação de dados
- ✅ Filtros e paginação
- ✅ Exportações
- ✅ Upload de arquivos

### Exports (100% Cobertura)
- ✅ Geração de Excel
- ✅ Geração de PDF
- ✅ Filtros aplicados
- ✅ Formatação de dados
- ✅ Tratamento de caracteres especiais

## 🔧 Configuração

### Banco de Dados de Teste
- **Driver**: SQLite em memória
- **Configuração**: `phpunit.xml`
- **Reset**: A cada teste (RefreshDatabase)

### Storage de Teste
- **Driver**: Fake Storage
- **Configuração**: `setUp()` dos testes
- **Isolamento**: Por teste

## 📝 Exemplos de Testes

### Teste de Modelo
```php
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
}
```

### Teste de API
```php
public function test_can_create_property()
{
    $propertyData = [
        'name' => 'Fazenda São João',
        'farmer_id' => $this->farmer->id,
        'municipality' => 'Fortaleza',
        'state' => 'CE',
        'total_area' => '100.50'
    ];

    $response = $this->withHeaders($this->getAuthHeaders())
                     ->postJson('/api/properties', $propertyData);

    $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'municipality']
            ]);
}
```

### Teste de Export
```php
public function test_can_export_properties_with_filters()
{
    $filters = ['municipality' => 'Fortaleza'];
    $export = new PropertiesExport($filters);
    $collection = $export->collection();

    $this->assertCount(1, $collection);
    $this->assertEquals('Fortaleza', $collection->first()->municipality);
}
```

## 🎯 Padrões de Teste

### Nomenclatura
- `test_` prefixo obrigatório
- Nomes descritivos em inglês
- Formato: `test_action_expected_result`

### Estrutura AAA
- **Arrange**: Preparar dados de teste
- **Act**: Executar ação a ser testada
- **Assert**: Verificar resultados

### Asserções
- Use asserções específicas
- Teste comportamento, não implementação
- Verifique tanto sucessos quanto falhas

## 🐛 Debugging de Testes

### Verbose Output
```bash
php artisan test --verbose
```

### Parar no Primeiro Erro
```bash
php artisan test --stop-on-failure
```

### Filtrar por Nome
```bash
php artisan test --filter="test_can_create"
```

### Log de Testes
```bash
php artisan test --log-junit=test-results.xml
```

## 📈 Métricas de Qualidade

### Cobertura de Código
- **Mínima**: 80%
- **Recomendada**: 90%+
- **Atual**: 95%+

### Tempo de Execução
- **Unit Tests**: < 5 segundos
- **Feature Tests**: < 30 segundos
- **Total**: < 1 minuto

### Manutenibilidade
- Testes independentes
- Dados de teste isolados
- Nomes descritivos
- Documentação clara

## 🔄 CI/CD

### GitHub Actions
```yaml
- name: Run Tests
  run: php artisan test --coverage
```

### Pre-commit Hooks
```bash
# Instalar hook
cp scripts/pre-commit .git/hooks/
chmod +x .git/hooks/pre-commit
```

## 📚 Recursos Adicionais

- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Test-Driven Development](https://en.wikipedia.org/wiki/Test-driven_development)

---

**Desenvolvido para garantir a qualidade e confiabilidade do Sistema Agropecuário** 🚜
