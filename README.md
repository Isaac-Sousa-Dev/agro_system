# Sistema Agropecuário - Laravel + Vue.js

Sistema completo de gestão agropecuária desenvolvido com Laravel 10 (backend) e Vue 3 (frontend), containerizado com Docker.

## 🏗️ Arquitetura

- **Backend**: Laravel 10 com API RESTful
- **Frontend**: Vue 3 com Composition API + Vite
- **Banco de Dados**: PostgreSQL 15
- **Containerização**: Docker + Docker Compose
- **UI Framework**: PrimeVue + CSS Custom

## 📋 Entidades do Sistema

### Produtor Rural
- Nome, CPF/CNPJ, telefone, email, endereço, data de cadastro
- Relacionamento 1:N com propriedades

### Propriedade
- Nome, município, UF, inscrição estadual, área total
- Relacionamento N:1 com produtor
- Relacionamentos 1:N com unidades de produção e rebanhos

### Unidade de Produção
- Nome da cultura, área total (ha), coordenadas geográficas
- Relacionamento N:1 com propriedade

### Rebanho
- Espécie, quantidade, finalidade, data de atualização
- Relacionamento N:1 com propriedade

## 🚀 Como Executar

### Pré-requisitos
- Docker
- Docker Compose

### Instalação e Execução

1. **Clone o repositório**
```bash
git clone <url-do-repositorio>
cd agro_system
```

2. **Inicie os containers**
```bash
docker compose up -d
```

3. **Acesse o sistema**
- Frontend Vue 3: http://localhost:5173
- Backend Laravel: http://localhost (via Nginx)
- Banco PostgreSQL: localhost:5432

### Comandos Úteis

```bash
# Ver logs dos containers
docker compose logs -f

# Executar comandos no backend Laravel
docker compose exec backend php artisan migrate
docker compose exec backend php artisan make:controller NomeController

# Executar comandos no frontend
docker compose exec frontend npm install
docker compose exec frontend npm run dev

# Parar os containers
docker compose down

# Parar e remover volumes (reset completo)
docker compose down -v
```

## 🗄️ Banco de Dados

O sistema usa PostgreSQL com as seguintes tabelas:

- `produtores` - Dados dos produtores rurais
- `propriedades` - Propriedades dos produtores
- `unidades_producao` - Unidades de produção das propriedades
- `rebanhos` - Rebanhos das propriedades

### Conexão com o Banco
- Host: db (dentro do Docker) / localhost (externamente)
- Porta: 5432
- Database: agro_system
- Usuário: postgres
- Senha: postgres

## 📁 Estrutura do Projeto

```
agro_system/
├── backend/                 # Laravel 10
│   ├── app/
│   │   ├── Models/         # Models do sistema
│   │   └── Http/Controllers/ # Controllers da API
│   ├── database/migrations/ # Migrations do banco
│   └── routes/api.php      # Rotas da API
├── frontend/               # Vue 3
│   ├── src/
│   │   ├── components/     # Componentes Vue
│   │   ├── views/         # Páginas
│   │   └── stores/        # Pinia stores
├── docker/                # Configurações Docker
│   ├── php/Dockerfile     # Container PHP/Laravel
│   └── nginx/             # Configuração Nginx
└── docker-compose.yml     # Orquestração dos containers
```

## 🔧 Funcionalidades Implementadas

### ✅ Concluído
- [x] Estrutura Docker completa
- [x] Laravel 10 configurado com PostgreSQL
- [x] Vue 3 com Composition API e Vite
- [x] Models e migrations das entidades
- [x] Relacionamentos entre entidades
- [x] PrimeVue e Tailwind CSS configurados

### 🚧 Em Desenvolvimento
- [ ] Controllers e rotas da API
- [ ] Componentes Vue para CRUD
- [ ] Sistema de relatórios
- [ ] Exportação Excel/PDF
- [ ] Autenticação com Sanctum
- [ ] Testes automatizados

## 📊 Exemplos de Dados

### Espécies de Animais
- Suínos
- Caprinos  
- Bovinos

### Culturas
- Laranja Pera
- Melancia Crimson Sweet
- Goiaba Paluma

## 🛠️ Desenvolvimento

### Adicionando Novas Funcionalidades

1. **Backend (Laravel)**
```bash
# Criar migration
docker compose exec backend php artisan make:migration nome_da_migration

# Criar model
docker compose exec backend php artisan make:model NomeModel

# Criar controller
docker compose exec backend php artisan make:controller NomeController --api
```

2. **Frontend (Vue)**
```bash
# Instalar dependências
docker compose exec frontend npm install nome-do-pacote

# Executar em modo desenvolvimento
docker compose exec frontend npm run dev
```

## 📝 Próximos Passos

1. Implementar controllers da API
2. Criar componentes Vue para CRUD
3. Implementar sistema de relatórios
4. Adicionar exportação Excel/PDF
5. Implementar autenticação
6. Criar testes automatizados
7. Adicionar documentação da API (Postman)

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.
