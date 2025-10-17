# Exportação de Propriedades para Excel

## Visão Geral

A funcionalidade de exportação de propriedades permite gerar arquivos Excel (.xlsx) com dados das propriedades cadastradas no sistema. A exportação suporta filtros e formatação avançada.

## Endpoints Disponíveis

### 1. Exportar Propriedades para Excel
```
GET /api/properties/export/excel
```

**Parâmetros de Query (opcionais):**
- `search` - Busca por nome da propriedade ou nome do produtor
- `municipality` - Filtrar por município
- `state` - Filtrar por estado
- `farmer_id` - Filtrar por ID do produtor

**Exemplo de uso:**
```
GET /api/properties/export/excel?search=fazenda&state=CE&municipality=Fortaleza
```

**Resposta:**
- Arquivo Excel (.xlsx) para download
- Nome do arquivo: `propriedades_YYYY-MM-DD_HH-mm-ss.xlsx`

### 2. Preview da Exportação
```
GET /api/properties/export/preview
```

**Parâmetros de Query (opcionais):**
- `search` - Busca por nome da propriedade ou nome do produtor
- `municipality` - Filtrar por município
- `state` - Filtrar por estado
- `farmer_id` - Filtrar por ID do produtor

**Exemplo de uso:**
```
GET /api/properties/export/preview?search=fazenda&state=CE
```

**Resposta JSON:**
```json
{
    "message": "Preview da exportação gerado com sucesso",
    "data": {
        "total_records": 150,
        "preview_records": [
            {
                "id": 1,
                "name": "Fazenda Exemplo",
                "municipality": "Fortaleza",
                "state": "CE",
                "state_registration": "123456789",
                "total_area": "100,50",
                "farmer_name": "João Silva",
                "farmer_cpf": "123.456.789-00",
                "production_units_count": 3,
                "herds_count": 2,
                "created_at": "15/10/2025 10:30:00",
                "updated_at": "15/10/2025 10:30:00"
            }
        ],
        "filters_applied": {
            "search": "fazenda",
            "state": "CE"
        }
    }
}
```

## Estrutura do Arquivo Excel

O arquivo Excel exportado contém as seguintes colunas:

| Coluna | Descrição |
|--------|-----------|
| ID | Identificador único da propriedade |
| Nome da Propriedade | Nome da propriedade |
| Município | Município onde está localizada |
| Estado | Estado onde está localizada |
| Registro Estadual | Número do registro estadual |
| Área Total (hectares) | Área total em hectares (formatada) |
| Nome do Produtor | Nome do produtor responsável |
| CPF do Produtor | CPF do produtor |
| Unidades de Produção | Quantidade de unidades de produção |
| Rebanhos | Quantidade de rebanhos |
| Data de Criação | Data e hora de criação do registro |
| Última Atualização | Data e hora da última atualização |

## Características do Arquivo Excel

### Formatação
- **Cabeçalho**: Fundo verde escuro com texto branco em negrito
- **Bordas**: Todas as células possuem bordas finas
- **Primeira linha congelada**: Facilita a navegação em arquivos grandes
- **Largura das colunas**: Ajustada automaticamente para melhor visualização

### Formatação de Dados
- **Área Total**: Formato numérico com separador de milhares e 2 casas decimais
- **Datas**: Formato brasileiro (dd/mm/yyyy hh:mm:ss)
- **Valores nulos**: Exibidos como "N/A"

## Autenticação

Todos os endpoints de exportação requerem autenticação via Sanctum. Inclua o token de autenticação no header:

```
Authorization: Bearer {seu_token}
```

## Tratamento de Erros

Em caso de erro, a API retorna uma resposta JSON com:

```json
{
    "message": "Erro ao exportar propriedades",
    "error": "Descrição detalhada do erro"
}
```

## Exemplos de Uso

### Exportar todas as propriedades
```bash
curl -H "Authorization: Bearer {token}" \
     -H "Accept: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" \
     "https://api.exemplo.com/api/properties/export/excel" \
     --output propriedades.xlsx
```

### Exportar com filtros
```bash
curl -H "Authorization: Bearer {token}" \
     -H "Accept: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" \
     "https://api.exemplo.com/api/properties/export/excel?state=CE&municipality=Fortaleza" \
     --output propriedades_ce_fortaleza.xlsx
```

### Preview da exportação
```bash
curl -H "Authorization: Bearer {token}" \
     -H "Accept: application/json" \
     "https://api.exemplo.com/api/properties/export/preview?search=fazenda"
```

## Limitações

- O preview retorna apenas os primeiros 10 registros
- A exportação completa não possui limite de registros
- Arquivos grandes podem demorar mais para serem gerados
- Recomenda-se usar filtros para exportações muito grandes

## Dependências

- Laravel 10+
- maatwebsite/excel 3.1+
- PhpSpreadsheet (instalado automaticamente com maatwebsite/excel)
