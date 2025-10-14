# 🧪 Guia de Testes da API - Agricultural Management System

## 📋 **Configuração Inicial**

### 1. **Importar Collection no Postman**
1. Abra o Postman
2. Clique em **Import**
3. Selecione o arquivo: `Agricultural_Management_System_API.postman_collection.json`
4. A collection será importada com todas as rotas configuradas

### 2. **Configurar Variáveis de Ambiente**
1. Clique no ícone de **ambiente** (olho) no canto superior direito
2. Configure as variáveis:
   - `base_url`: `http://localhost`
   - `auth_token`: (deixe vazio inicialmente)

## 🔐 **Testando Autenticação**

### **Passo 1: Login**
1. Execute a requisição **Authentication > Login**
2. Use as credenciais:
   ```json
   {
       "email": "admin@agro.com",
       "password": "password"
   }
   ```
3. **Copie o token** da resposta e cole na variável `auth_token`

### **Passo 2: Verificar Usuário**
1. Execute **Authentication > Get User**
2. Deve retornar os dados do usuário autenticado

## 👨‍🌾 **Testando Farmers (Produtores)**

### **1. Listar Farmers**
- **GET** `/api/farmers`
- **Parâmetros opcionais**:
  - `page`: Número da página (padrão: 1)
  - `search`: Buscar por nome ou CPF/CNPJ
  - `municipality`: Filtrar por município

**Exemplo**: `/api/farmers?search=João&municipality=Ribeirão Preto`

### **2. Criar Farmer**
- **POST** `/api/farmers`
- **Body**:
```json
{
    "name": "Maria Santos",
    "cpf_cnpj": "98765432100",
    "phone": "(11) 88888-8888",
    "email": "maria@email.com",
    "address": "Rua dos Campos, 456",
    "registration_date": "2024-10-13"
}
```

### **3. Visualizar Farmer**
- **GET** `/api/farmers/{id}`
- Retorna farmer com propriedades, unidades de produção e rebanhos

### **4. Atualizar Farmer**
- **PUT** `/api/farmers/{id}`
- Mesmo formato do body de criação

### **5. Deletar Farmer**
- **DELETE** `/api/farmers/{id}`
- ⚠️ **Atenção**: Deleta em cascata (propriedades, unidades, rebanhos)

## 🏡 **Testando Properties (Propriedades)**

### **1. Listar Properties**
- **GET** `/api/properties`
- **Parâmetros opcionais**:
  - `page`: Número da página
  - `search`: Buscar por nome da propriedade ou nome do farmer
  - `municipality`: Filtrar por município
  - `state`: Filtrar por estado (UF)
  - `farmer_id`: Filtrar por farmer específico

### **2. Criar Property**
- **POST** `/api/properties`
- **Body**:
```json
{
    "name": "Fazenda Nova Vida",
    "municipality": "Campinas",
    "state": "SP",
    "state_registration": "987654321",
    "total_area": 200.75,
    "farmer_id": 1
}
```

### **3. Exportar Properties**
- **GET** `/api/properties/export/excel`
- Retorna arquivo Excel com todas as propriedades

## 🌾 **Testando Production Units (Unidades de Produção)**

### **1. Listar Production Units**
- **GET** `/api/production-units`
- **Parâmetros opcionais**:
  - `property_id`: Filtrar por propriedade
  - `crop_name`: Filtrar por tipo de cultura

### **2. Criar Production Unit**
- **POST** `/api/production-units`
- **Body**:
```json
{
    "crop_name": "Watermelon Crimson Sweet",
    "total_area_ha": 25.75,
    "geographic_coordinates": "-22.9068,-47.0633",
    "property_id": 1
}
```

## 🐄 **Testando Herds (Rebanhos)**

### **1. Listar Herds**
- **GET** `/api/herds`
- **Parâmetros opcionais**:
  - `property_id`: Filtrar por propriedade
  - `species`: Filtrar por espécie (Cattle, Goats, Pigs)
  - `purpose`: Filtrar por finalidade

### **2. Criar Herd**
- **POST** `/api/herds`
- **Body**:
```json
{
    "species": "Goats",
    "quantity": 15,
    "purpose": "Milk Production",
    "update_date": "2024-10-13",
    "property_id": 1
}
```

## 📊 **Códigos de Status HTTP**

| Código | Significado | Quando Ocorre |
|--------|-------------|---------------|
| 200 | OK | Requisição bem-sucedida |
| 201 | Created | Recurso criado com sucesso |
| 401 | Unauthorized | Token inválido ou ausente |
| 403 | Forbidden | Sem permissão para acessar |
| 404 | Not Found | Recurso não encontrado |
| 422 | Validation Error | Dados de entrada inválidos |
| 500 | Server Error | Erro interno do servidor |

## 🔍 **Exemplos de Filtros e Buscas**

### **Buscar Farmers por Município**
```
GET /api/farmers?municipality=Ribeirão Preto
```

### **Buscar Properties por Estado**
```
GET /api/properties?state=SP
```

### **Buscar Production Units por Cultura**
```
GET /api/production-units?crop_name=Orange
```

### **Buscar Herds por Espécie**
```
GET /api/herds?species=Cattle
```

## ⚠️ **Observações Importantes**

1. **Autenticação**: Todas as rotas (exceto login/register) precisam do header `Authorization: Bearer {token}`

2. **Relacionamentos**: 
   - Farmer → Properties (1:N)
   - Property → Production Units (1:N)
   - Property → Herds (1:N)

3. **Cascade Delete**: 
   - Deletar Farmer deleta todas suas Properties
   - Deletar Property deleta suas Production Units e Herds

4. **Validações**:
   - CPF/CNPJ deve ser único
   - Email deve ser válido
   - Áreas devem ser números positivos
   - Datas devem estar no formato YYYY-MM-DD

## 🎯 **Fluxo de Teste Recomendado**

1. **Login** → Obter token
2. **Criar Farmer** → Obter ID do farmer
3. **Criar Property** → Usar ID do farmer
4. **Criar Production Unit** → Usar ID da property
5. **Criar Herd** → Usar ID da property
6. **Listar todos** → Verificar relacionamentos
7. **Testar filtros** → Verificar funcionalidades de busca
8. **Exportar** → Testar funcionalidade de exportação

## 📝 **Dados de Exemplo Pré-cadastrados**

O sistema já possui dados de exemplo:
- **Farmer**: João Silva (ID: 1)
- **Property**: Fazenda São José (ID: 1)
- **Production Unit**: Orange Pear (ID: 1)
- **Herd**: Cattle - 25 animals (ID: 1)

Use esses IDs para testar as operações de visualização, atualização e exclusão.
