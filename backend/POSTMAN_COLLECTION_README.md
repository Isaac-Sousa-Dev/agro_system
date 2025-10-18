# 🚀 Collection do Postman - Sistema Agropecuário

Esta collection contém todos os endpoints da API do Sistema de Gestão Agropecuária, organizados de forma clara e com exemplos práticos.

## 📁 Arquivos Incluídos

- `Agro_System_API_Collection.postman_collection.json` - Collection principal com todos os endpoints
- `Agro_System_Environment.postman_environment.json` - Environment com variáveis configuradas
- `POSTMAN_COLLECTION_README.md` - Este arquivo de instruções

## 🛠️ Como Importar no Postman

### 1. Importar a Collection
1. Abra o Postman
2. Clique em "Import" no canto superior esquerdo
3. Selecione o arquivo `Agro_System_API_Collection.postman_collection.json`
4. Clique em "Import"

### 2. Importar o Environment
1. No Postman, vá em "Environments" no menu lateral
2. Clique em "Import"
3. Selecione o arquivo `Agro_System_Environment.postman_environment.json`
4. Clique em "Import"
5. Selecione o environment "Agro System - Environment" no dropdown superior direito

## 🔧 Configuração Inicial

### Variáveis do Environment
- `base_url`: URL base da API (padrão: http://localhost:8000)
- `auth_token`: Token de autenticação (será preenchido automaticamente após login)
- `user_id`: ID do usuário logado
- `farmer_id`: ID do produtor para testes
- `property_id`: ID da propriedade para testes
- `production_unit_id`: ID da unidade de produção para testes
- `herd_id`: ID do rebanho para testes

## 📋 Estrutura da Collection

### 🔐 Autenticação
- **Login**: Faz login e obtém o token de autenticação
- **Registro**: Registra um novo usuário
- **Dados do Usuário**: Retorna informações do usuário logado
- **Logout**: Invalida o token de autenticação

### 👨‍🌾 Produtores
- **Listar**: Lista todos os produtores com paginação
- **Criar**: Cria um novo produtor
- **Visualizar**: Mostra dados de um produtor específico
- **Atualizar**: Atualiza dados de um produtor
- **Deletar**: Remove um produtor

### 🏡 Propriedades
- **Listar**: Lista propriedades com filtros (busca, município, estado, produtor)
- **Criar**: Cria nova propriedade (com ou sem imagem)
- **Visualizar**: Mostra dados de uma propriedade
- **Atualizar**: Atualiza dados de uma propriedade
- **Deletar**: Remove uma propriedade
- **Exportar Excel**: Exporta propriedades para Excel
- **Preview Exportação**: Visualiza dados antes da exportação

### 🌾 Unidades de Produção
- **Listar**: Lista unidades de produção
- **Criar**: Cria nova unidade de produção
- **Visualizar**: Mostra dados de uma unidade
- **Atualizar**: Atualiza dados de uma unidade
- **Deletar**: Remove uma unidade

### 🐄 Rebanhos
- **Listar**: Lista rebanhos
- **Criar**: Cria novo rebanho
- **Visualizar**: Mostra dados de um rebanho
- **Atualizar**: Atualiza dados de um rebanho
- **Deletar**: Remove um rebanho
- **Exportar PDF**: Exporta rebanhos para PDF por produtor
- **Preview Exportação**: Visualiza dados antes da exportação

### 📊 Relatórios
- **Dashboard**: Retorna estatísticas gerais do sistema

### ℹ️ Informações da API
- **Informações**: Lista todos os endpoints disponíveis

## 🚀 Como Usar

### 1. Primeiro, faça login
1. Vá para a pasta "🔐 Autenticação"
2. Execute a requisição "Login"
3. Copie o token retornado
4. Cole o token na variável `auth_token` do environment

### 2. Teste os endpoints
1. Todas as requisições protegidas já estão configuradas com o token
2. Use os IDs de exemplo ou crie novos registros
3. Os IDs são salvos automaticamente nas variáveis do environment

### 3. Exemplos de Uso

#### Criar um Produtor
```json
{
    "name": "João Silva",
    "cpf_cnpj": "123.456.789-00",
    "phone": "(85) 99999-9999",
    "email": "joao@email.com",
    "address": "Rua das Flores, 123 - Fortaleza/CE"
}
```

#### Criar uma Propriedade
```json
{
    "name": "Fazenda São João",
    "farmer_id": 1,
    "municipality": "Fortaleza",
    "state": "CE",
    "state_registration": "123456789",
    "total_area": "100.50",
    "productionUnits": [
        {
            "crop_name": "Laranja Pera",
            "total_area_ha": "50.25",
            "geographic_coordinates": "-3.7172,-38.5434"
        }
    ],
    "herds": [
        {
            "species": "Bovino",
            "quantity": 25,
            "purpose": "Corte"
        }
    ]
}
```

#### Criar uma Unidade de Produção
```json
{
    "crop_name": "Laranja Pera",
    "total_area_ha": "50.25",
    "geographic_coordinates": "-3.7172,-38.5434",
    "property_id": 1
}
```

#### Criar um Rebanho
```json
{
    "species": "Bovino",
    "quantity": 25,
    "property_id": 1,
    "purpose": "Corte"
}
```

## 🔍 Filtros Disponíveis

### Propriedades
- `search`: Busca por nome da propriedade ou nome do produtor
- `municipality`: Filtra por município
- `state`: Filtra por estado (UF)
- `farmer_id`: Filtra por produtor específico

### Exportações
- **Excel**: Usa os mesmos filtros das propriedades
- **PDF**: Filtra rebanhos por produtor (`farmer_id`)

## 📝 Notas Importantes

1. **Autenticação**: Todas as requisições (exceto login e registro) precisam do token de autenticação
2. **Paginação**: As listagens usam paginação com 6 itens por página
3. **Validação**: Todos os campos obrigatórios estão documentados nos exemplos
4. **Imagens**: Para upload de imagens, use `multipart/form-data` em vez de `application/json`
5. **IDs**: Os IDs são sequenciais e começam em 1

## 🐛 Troubleshooting

### Erro 401 (Unauthorized)
- Verifique se o token está correto na variável `auth_token`
- Faça login novamente se o token expirou

### Erro 422 (Validation Error)
- Verifique se todos os campos obrigatórios estão preenchidos
- Confirme se os tipos de dados estão corretos

### Erro 500 (Server Error)
- Verifique se o servidor está rodando
- Confirme se o banco de dados está configurado

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique os logs do Laravel em `storage/logs/laravel.log`
2. Confirme se todas as dependências estão instaladas
3. Verifique se as migrations foram executadas

---

**Desenvolvido para o Sistema de Gestão Agropecuária - ADAGRI** 🚜
