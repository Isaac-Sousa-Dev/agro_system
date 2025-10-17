# Exportação de Rebanhos por Produtor em PDF

## Visão Geral

A funcionalidade de exportação de rebanhos permite gerar relatórios em PDF com dados dos rebanhos agrupados por produtor. O sistema suporta exportação de rebanhos de um produtor específico ou de todos os produtores.

## Endpoints Disponíveis

### 1. Exportar Rebanhos para PDF
```
GET /api/herds/export/pdf
```

**Parâmetros de Query (opcionais):**
- `farmer_id` - ID do produtor específico (se não fornecido, exporta todos os produtores)

**Exemplo de uso:**
```
GET /api/herds/export/pdf?farmer_id=1
```

**Resposta:**
- Arquivo PDF para download
- Nome do arquivo: `rebanhos_produtor_{id}_{data}.pdf` ou `rebanhos_todos_produtores_{data}.pdf`

### 2. Preview da Exportação PDF
```
GET /api/herds/export/pdf/preview
```

**Parâmetros de Query (opcionais):**
- `farmer_id` - ID do produtor específico (se não fornecido, mostra todos os produtores)

**Exemplo de uso:**
```
GET /api/herds/export/pdf/preview?farmer_id=1
```

**Resposta JSON:**
```json
{
    "message": "Preview do PDF gerado com sucesso",
    "data": {
        "total_herds": 25,
        "total_animals": 1250,
        "farmers_count": 3,
        "preview_data": {
            "João Silva": {
                "farmer_name": "João Silva",
                "herds_count": 8,
                "total_animals": 400,
                "properties": {
                    "Fazenda São José": {
                        "property_name": "Fazenda São José",
                        "herds": [
                            {
                                "species": "Bovino",
                                "quantity": 150,
                                "purpose": "Corte",
                                "municipality": "Fortaleza",
                                "state": "CE"
                            }
                        ]
                    }
                }
            }
        },
        "farmer_id": 1
    }
}
```

## Estrutura do Relatório PDF

### Para Produtor Específico

O relatório contém:

1. **Cabeçalho**
   - Título: "Relatório de Rebanhos por Produtor"
   - Sistema: "Sistema de Gestão Agropecuária"

2. **Informações do Produtor**
   - Nome, CPF/CNPJ, Telefone, Email, Endereço
   - Total de Propriedades

3. **Resumo dos Rebanhos**
   - Total de Rebanhos
   - Total de Animais
   - Espécies Diferentes
   - Número de Propriedades

4. **Tabela de Rebanhos**
   - Propriedade, Espécie, Quantidade, Finalidade, Município, Estado

### Para Todos os Produtores

O relatório contém:

1. **Cabeçalho**
   - Título: "Relatório de Rebanhos por Produtor"
   - Sistema: "Sistema de Gestão Agropecuária"

2. **Resumo Geral**
   - Total de Rebanhos
   - Total de Animais
   - Espécies Diferentes
   - Número de Produtores

3. **Tabela de Rebanhos Agrupados**
   - Organizada por Produtor e Propriedade
   - Propriedade, Espécie, Quantidade, Finalidade, Município, Estado

## Características do PDF

### Formatação Visual
- **Cabeçalho**: Verde escuro (#2E8B57) com título centralizado
- **Informações do Produtor**: Fundo cinza claro com borda verde
- **Resumo**: Fundo verde claro com estatísticas centralizadas
- **Tabela**: Cabeçalho verde, linhas alternadas, bordas definidas
- **Fonte**: Arial, tamanhos variados para hierarquia visual

### Layout Responsivo
- **Orientação**: Retrato (A4)
- **Margens**: 20px em todos os lados
- **Quebras de página**: Automáticas para tabelas grandes
- **Cabeçalho congelado**: Título sempre visível

### Dados Formatados
- **Números**: Separadores de milhares (1.000, 2.500)
- **Datas**: Formato brasileiro (dd/mm/yyyy)
- **Texto**: Escape de caracteres especiais para segurança

## Autenticação

Todos os endpoints de exportação requerem autenticação via Sanctum. Inclua o token de autenticação no header:

```
Authorization: Bearer {seu_token}
```

## Tratamento de Erros

### Produtor Não Encontrado (404)
```json
{
    "message": "Produtor não encontrado",
    "error": "Farmer not found"
}
```

### Erro de Geração (500)
```json
{
    "message": "Erro ao gerar PDF de rebanhos",
    "error": "Descrição detalhada do erro"
}
```

## Exemplos de Uso

### Exportar rebanhos de um produtor específico
```bash
curl -H "Authorization: Bearer {token}" \
     -H "Accept: application/pdf" \
     "https://api.exemplo.com/api/herds/export/pdf?farmer_id=1" \
     --output rebanhos_produtor_1.pdf
```

### Exportar rebanhos de todos os produtores
```bash
curl -H "Authorization: Bearer {token}" \
     -H "Accept: application/pdf" \
     "https://api.exemplo.com/api/herds/export/pdf" \
     --output rebanhos_todos_produtores.pdf
```

### Preview da exportação
```bash
curl -H "Authorization: Bearer {token}" \
     -H "Accept: application/json" \
     "https://api.exemplo.com/api/herds/export/pdf/preview?farmer_id=1"
```

## Uso no Frontend

### Função para Download do PDF
```javascript
const exportPdf = async (farmerId = null) => {
  loading.value = true;
  error.value = null;
  
  try {
    const params = farmerId ? { farmer_id: farmerId } : {};
    const response = await api.get('/herds/export/pdf', {
      params,
      responseType: 'blob',
      headers: {
        Accept: 'application/pdf',
      },
    });

    // Obter nome do arquivo do header
    const disposition = response.headers['content-disposition'];
    const fileNameMatch = disposition?.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/i);
    const fileName = fileNameMatch ? fileNameMatch[1].replace(/['"]/g, '') : 'rebanhos.pdf';

    const url = window.URL.createObjectURL(response.data);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', fileName);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
    
    return true;
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Falha ao exportar PDF';
    throw err;
  } finally {
    loading.value = false;
  }
};
```

### Função para Preview
```javascript
const previewPdf = async (farmerId = null) => {
  try {
    const params = farmerId ? { farmer_id: farmerId } : {};
    const response = await api.get('/herds/export/pdf/preview', { params });
    return response.data;
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Falha ao gerar preview';
    throw err;
  }
};
```

## Limitações

- **Tamanho do arquivo**: PDFs muito grandes podem demorar para gerar
- **Memória**: Relatórios com muitos dados podem consumir mais memória
- **Timeout**: Para relatórios muito grandes, considere implementar processamento em background

## Dependências

- Laravel 10+
- dompdf/dompdf 3.1+
- PhpSpreadsheet (para formatação de dados)

## Estrutura de Dados

### Relacionamentos Utilizados
- `Herd` → `Property` → `Farmer`
- Dados agrupados por produtor e propriedade
- Ordenação por nome do produtor, propriedade e espécie

### Campos Incluídos
- **Herd**: species, quantity, purpose
- **Property**: name, municipality, state
- **Farmer**: name, cpf_cnpj, phone, email, address
