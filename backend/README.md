# 🚜 Sistema de Gestão Agropecuária - Backend

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-red.svg" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.1+-blue.svg" alt="PHP Version">
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
  <img src="https://img.shields.io/badge/Status-Production%20Ready-brightgreen.svg" alt="Status">
</p>

Sistema completo de gestão agropecuária desenvolvido para a ADAGRI (Agência de Defesa Agropecuária do Ceará), permitindo o controle de produtores, propriedades, unidades de produção e rebanhos.

## 📋 Índice

- [Sobre o Projeto](#-sobre-o-projeto)
- [Tecnologias Utilizadas](#-tecnologias-utilizadas)
- [Instalação e Execução](#-instalação-e-execução)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [API Endpoints](#-api-endpoints)
- [Relatórios Exportados](#-relatórios-exportados)
- [Observações Gerais](#-observações-gerais)
- [Collection do Postman](#-collection-do-postman)
- [Contribuição](#-contribuição)

## 🎯 Sobre o Projeto

O Sistema de Gestão Agropecuária é uma aplicação web desenvolvida para facilitar o controle e monitoramento de atividades rurais no estado do Ceará. O sistema permite:

- **Gestão de Produtores**: Cadastro e controle de produtores rurais
- **Gestão de Propriedades**: Controle de propriedades rurais com geolocalização
- **Unidades de Produção**: Monitoramento de culturas e plantações
- **Controle de Rebanhos**: Gestão de animais e rebanhos
- **Relatórios**: Exportação de dados em Excel e PDF
- **Dashboard**: Visualização de estatísticas e métricas

## 🛠️ Tecnologias Utilizadas

### Backend
- **Laravel 10.x** - Framework PHP
- **PHP 8.1+** - Linguagem de programação
- **MySQL/PostgreSQL** - Banco de dados
- **Laravel Sanctum** - Autenticação API
- **Maatwebsite Excel** - Exportação Excel
- **DomPDF** - Geração de PDFs
- **Laravel Pint** - Code Style

### Frontend (Separado)
- **Vue.js 3** - Framework JavaScript
- **Pinia** - Gerenciamento de estado
- **Vite** - Build tool
- **Tailwind CSS** - Framework CSS

## 🚀 Instalação e Execução

### Pré-requisitos

- PHP 8.1 ou superior
- Composer
- MySQL 8.0+ ou PostgreSQL 13+
- Node.js 16+ (para frontend)
- Git

### 1. Clone o Repositório

```bash
git clone https://github.com/seu-usuario/agro-system.git
cd agro-system/backend
```

### 2. Instale as Dependências

```bash
composer install
```

### 3. Configure o Ambiente

```bash
cp .env.example .env
```

Edite o arquivo `.env` com suas configurações:

```env
APP_NAME="Sistema Agropecuário"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agro_system
DB_USERNAME=root
DB_PASSWORD=

# Configurações de Storage
FILESYSTEM_DISK=public
```

### 4. Gere a Chave da Aplicação

```bash
php artisan key:generate
```

### 5. Execute as Migrations

```bash
php artisan migrate
```

### 6. Execute os Seeders (Opcional)

```bash
php artisan db:seed
```

### 7. Crie o Link Simbólico para Storage

```bash
php artisan storage:link
```

### 8. Inicie o Servidor

```bash
php artisan serve
```

O servidor estará disponível em: `http://localhost:8000`

### 9. Acesse a API

```bash
curl http://localhost:8000/api/
```

## 📁 Estrutura do Projeto

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controladores da API
│   │   ├── Requests/        # Validações de requisição
│   │   └── Resources/       # Transformadores de dados
│   ├── Models/              # Modelos Eloquent
│   ├── Services/            # Lógica de negócio
│   └── Exports/             # Classes de exportação
├── database/
│   ├── migrations/          # Migrações do banco
│   └── seeders/             # Dados de exemplo
├── routes/
│   └── api.php              # Rotas da API
├── storage/
│   └── app/public/          # Arquivos públicos
└── tests/                   # Testes automatizados
```

## 🔌 API Endpoints

### Autenticação
- `POST /api/auth/login` - Login do usuário
- `POST /api/auth/register` - Registro de usuário
- `GET /api/auth/user` - Dados do usuário logado
- `POST /api/auth/logout` - Logout

### Produtores
- `GET /api/farmers` - Listar produtores
- `POST /api/farmers` - Criar produtor
- `GET /api/farmers/{id}` - Visualizar produtor
- `PUT /api/farmers/{id}` - Atualizar produtor
- `DELETE /api/farmers/{id}` - Deletar produtor

### Propriedades
- `GET /api/properties` - Listar propriedades
- `POST /api/properties` - Criar propriedade
- `GET /api/properties/{id}` - Visualizar propriedade
- `PUT /api/properties/{id}` - Atualizar propriedade
- `DELETE /api/properties/{id}` - Deletar propriedade
- `GET /api/properties/export/excel` - Exportar Excel
- `GET /api/properties/export/preview` - Preview da exportação

### Unidades de Produção
- `GET /api/production-units` - Listar unidades
- `POST /api/production-units` - Criar unidade
- `GET /api/production-units/{id}` - Visualizar unidade
- `PUT /api/production-units/{id}` - Atualizar unidade
- `DELETE /api/production-units/{id}` - Deletar unidade

### Rebanhos
- `GET /api/herds` - Listar rebanhos
- `POST /api/herds` - Criar rebanho
- `GET /api/herds/{id}` - Visualizar rebanho
- `PUT /api/herds/{id}` - Atualizar rebanho
- `DELETE /api/herds/{id}` - Deletar rebanho
- `GET /api/herds/export/pdf` - Exportar PDF
- `GET /api/herds/export/pdf/preview` - Preview da exportação

### Relatórios
- `GET /api/reports/dashboard` - Dados do dashboard

## 📊 Relatórios Exportados

### 1. Exportação de Propriedades (Excel)

**Endpoint**: `GET /api/properties/export/excel`

**Características**:
- Formato: `.xlsx`
- Filtros aplicáveis: busca, município, estado, produtor
- Dados incluídos:
  - ID da propriedade
  - Nome da propriedade
  - Município e Estado
  - Registro estadual
  - Área total (hectares)
  - Nome do produtor
  - Quantidade de unidades de produção
  - Quantidade de rebanhos
  - Datas de criação e atualização

**Exemplo de uso**:
```bash
curl -H "Authorization: Bearer {token}" \
     "http://localhost:8000/api/properties/export/excel?municipality=Fortaleza&state=CE"
```

**Formatação do Excel**:
- Cabeçalho com fundo verde e texto branco
- Bordas em todas as células
- Formatação de números e datas
- Colunas com largura otimizada
- Primeira linha congelada

### 2. Exportação de Rebanhos (PDF)

**Endpoint**: `GET /api/herds/export/pdf`

**Características**:
- Formato: `.pdf`
- Filtro: por produtor específico
- Dados incluídos:
  - Informações do produtor
  - Resumo estatístico
  - Tabela detalhada dos rebanhos
  - Informações das propriedades

**Exemplo de uso**:
```bash
curl -H "Authorization: Bearer {token}" \
     "http://localhost:8000/api/herds/export/pdf?farmer_id=1"
```

**Estrutura do PDF**:
- Cabeçalho com título e logo
- Seção de informações do produtor
- Resumo com estatísticas:
  - Total de rebanhos
  - Total de animais
  - Espécies diferentes
  - Número de propriedades
- Tabela detalhada com:
  - Nome da propriedade
  - Espécie do animal
  - Quantidade
  - Finalidade
  - Município e Estado

**Exemplo de dados exportados**:

#### Propriedades (Excel)
| ID | Nome da Propriedade | Município | Estado | Área Total | Produtor | Unidades | Rebanhos |
|----|-------------------|-----------|--------|------------|----------|----------|----------|
| 1  | Fazenda São João  | Fortaleza | CE     | 100,50     | João Silva | 2      | 3        |
| 2  | Sítio Boa Vista   | Maracanaú | CE     | 75,25      | Maria Santos | 1   | 2        |

#### Rebanhos (PDF)
```
Relatório de Rebanhos por Produtor
Sistema de Gestão Agropecuária

Informações do Produtor:
Nome: João Silva
CPF/CNPJ: 123.456.789-00
Total de Propriedades: 2

Resumo dos Rebanhos:
┌─────────────────┬─────────────────┬─────────────────┬─────────────────┐
│ Total Rebanhos  │ Total Animais   │ Espécies Difer. │ Propriedades    │
├─────────────────┼─────────────────┼─────────────────┼─────────────────┤
│ 3               │ 150             │ 2               │ 2               │
└─────────────────┴─────────────────┴─────────────────┴─────────────────┘

Tabela Detalhada:
┌─────────────────┬──────────┬────────────┬─────────────┬─────────────┬────────┐
│ Propriedade     │ Espécie  │ Quantidade │ Finalidade  │ Município   │ Estado │
├─────────────────┼──────────┼────────────┼─────────────┼─────────────┼────────┤
│ Fazenda São João│ Bovino   │ 50         │ Corte       │ Fortaleza   │ CE     │
│ Fazenda São João│ Caprino  │ 30         │ Leite       │ Fortaleza   │ CE     │
│ Sítio Boa Vista │ Suíno    │ 70         │ Corte       │ Maracanaú   │ CE     │
└─────────────────┴──────────┴────────────┴─────────────┴─────────────┴────────┘
```

## ⚠️ Observações Gerais

### Segurança
- Todas as rotas (exceto login/registro) requerem autenticação via token
- Validação rigorosa de dados de entrada
- Sanitização de dados para prevenir SQL injection
- Upload de imagens com validação de tipo e tamanho

### Performance
- Paginação implementada em todas as listagens (6 itens por página)
- Eager loading para evitar N+1 queries
- Índices no banco de dados para consultas otimizadas
- Cache de consultas frequentes

### Banco de Dados
- Migrations versionadas para controle de schema
- Soft deletes implementados onde necessário
- Relacionamentos bem definidos entre entidades
- Constraints de integridade referencial

### Upload de Arquivos
- Imagens armazenadas em `storage/app/public/properties/`
- Link simbólico criado para acesso via web
- Validação de tipos: JPEG, PNG, JPG, GIF
- Tamanho máximo: 2MB por arquivo

### Validações
- Campos obrigatórios validados no backend
- Formatação de CPF/CNPJ
- Validação de email
- Verificação de unicidade em campos únicos

### Tratamento de Erros
- Respostas padronizadas em JSON
- Códigos HTTP apropriados
- Mensagens de erro em português
- Logs detalhados para debugging

### CORS
- Configurado para aceitar requisições do frontend
- Headers apropriados para upload de arquivos
- Suporte a diferentes origens em desenvolvimento

## 📮 Collection do Postman

Para facilitar os testes da API, foi criada uma collection completa do Postman:

### Arquivos Incluídos
- `Agro_System_API_Collection.postman_collection.json` - Collection principal
- `Agro_System_Environment.postman_environment.json` - Environment configurado
- `POSTMAN_COLLECTION_README.md` - Documentação detalhada

### Como Usar
1. Importe os arquivos no Postman
2. Selecione o environment "Agro System"
3. Faça login para obter o token
4. Teste qualquer endpoint da collection

### Recursos da Collection
- 31 endpoints organizados por funcionalidade
- Exemplos de dados reais
- Variáveis configuradas automaticamente
- Suporte a upload de arquivos
- Filtros e parâmetros de consulta

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

### Padrões de Código
- Siga o PSR-12 para PHP
- Use Laravel Pint para formatação automática
- Escreva testes para novas funcionalidades
- Documente APIs e métodos complexos

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 📞 Suporte

Para dúvidas ou problemas:
- Abra uma issue no GitHub
- Consulte a documentação da API
- Verifique os logs em `storage/logs/laravel.log`

---

**Desenvolvido para a ADAGRI - Agência de Defesa Agropecuária do Ceará** 🚜

<p align="center">
  <img src="https://img.shields.io/badge/Made%20with%20❤️%20in%20Brazil-green.svg" alt="Made with ❤️ in Brazil">
</p>
