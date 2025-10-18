# Instruções para Corrigir Tipos de Dados

## Problema Identificado

Os campos `total_area` (tabela `properties`) e `total_area_ha` (tabela `production_units`) estão definidos como `string` nas migrations, mas precisamos fazer operações matemáticas (SUM) com eles.

## Solução

### 1. Executar a Migration de Correção

Execute o comando para rodar a migration que corrige os tipos de dados:

```bash
php artisan migrate
```

### 2. Verificar se a Migration Foi Aplicada

A migration `2025_01_15_143000_fix_numeric_fields_types.php` irá:

- Alterar `total_area` na tabela `properties` de `string` para `decimal(10,2)`
- Alterar `total_area_ha` na tabela `production_units` de `string` para `decimal(10,2)`

### 3. Testar a API

Após executar a migration, teste a API:

```bash
curl -H "Authorization: Bearer {token}" \
     -H "Accept: application/json" \
     "http://localhost:8000/api/reports/dashboard"
```

## Alternativa Temporária (Se Não Puder Executar Migration)

Se não conseguir executar a migration imediatamente, o código atual já está preparado para funcionar com os campos como string, fazendo CAST para decimal nas consultas SQL.

## Verificação dos Dados

Após a migration, verifique se os dados foram convertidos corretamente:

```sql
-- Verificar tipos das colunas
SELECT column_name, data_type 
FROM information_schema.columns 
WHERE table_name = 'properties' AND column_name = 'total_area';

SELECT column_name, data_type 
FROM information_schema.columns 
WHERE table_name = 'production_units' AND column_name = 'total_area_ha';
```

## Rollback (Se Necessário)

Se precisar reverter a migration:

```bash
php artisan migrate:rollback --step=1
```

Isso reverterá os campos para o tipo `string` original.
