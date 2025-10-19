# 🌱 Agricultural Management System - Laravel + Vue.js

Sistema completo de gestão agropecuária desenvolvido com Laravel 10 (backend) e Vue 3 (frontend), containerizado com Docker. Sistema totalmente traduzido para inglês com APIs RESTful completas e funcionalidades de exportação.

## 🏗️ Arquitetura

- **Backend**: Laravel 10 com API RESTful + Sanctum Authentication
- **Frontend**: Vue 3 com Composition API + Vite + TypeScript
- **Banco de Dados**: PostgreSQL 15
- **Containerização**: Docker + Docker Compose
- **UI Framework**: PrimeVue + Custom CSS
- **Autenticação**: JWT com Laravel Sanctum
- **Exportação**: Excel (.xlsx) e PDF

## 📋 System Entities

### Farmer (Produtor Rural)
- Name, CPF/CNPJ, phone, email, address
- 1:N relationship with properties

### Property (Propriedade)
- Name, municipality, state, state registration, total area, image
- N:1 relationship with farmer
- 1:N relationships with production units and herds

### Production Unit (Unidade de Produção)
- Crop name, total area (ha), geographic coordinates
- N:1 relationship with property

### Herd (Rebanho)
- Species, quantity, purpose
- N:1 relationship with property

## 🚀 Installation and Execution

### Prerequisites
- Docker 20.10+
- Docker Compose 2.0+
- Git

### Quick Start

1. **Clone the repository**
```bash
git clone <repository-url>
cd agro_system
```

2. **Start the containers**
```bash
docker compose up -d
```

3. **Wait for initialization** (first time only)
```bash
# Wait for containers to be ready
sleep 30

# Run migrations
docker compose exec backend php artisan migrate

# Create sample data (optional)
docker compose exec backend php artisan tinker --execute="
App\Models\User::create(['name' => 'Admin User', 'email' => 'admin@agro.com', 'password' => bcrypt('password')]);
"
```

4. **Access the system**
- **Frontend Vue 3**: http://localhost:5173
- **Backend API**: http://localhost/api
- **PostgreSQL Database**: localhost:5432

### Default Login Credentials
- **Email**: admin@agro.com
- **Password**: password

### Useful Commands

```bash
# View container logs
docker compose logs -f

# Execute Laravel commands
docker compose exec backend php artisan migrate
docker compose exec backend php artisan make:controller NameController
docker compose exec backend php artisan tinker

# Execute frontend commands
docker compose exec frontend npm install
docker compose exec frontend npm run dev
docker compose exec frontend npm run build

# Stop containers
docker compose down

# Stop and remove volumes (complete reset)
docker compose down -v

# Fix file permissions (if needed)
./fix-permissions.sh

# Rebuild containers
docker compose build --no-cache
docker compose up -d
```

## 🗄️ Database

The system uses PostgreSQL with the following tables:

- `farmers` - Farmer data
- `properties` - Farmer properties
- `production_units` - Property production units
- `herds` - Property herds
- `users` - System users

### Database Connection
- **Host**: db (inside Docker) / localhost (externally)
- **Port**: 5432
- **Database**: agro_system
- **Username**: postgres
- **Password**: postgres

## 🔧 Implemented Features

### ✅ Completed
- [x] Complete Docker structure
- [x] Laravel 10 configured with PostgreSQL
- [x] Vue 3 with Composition API and Vite
- [x] Models and migrations for all entities
- [x] Entity relationships (1:N, N:1)
- [x] PrimeVue and Custom CSS configured
- [x] Complete RESTful API with authentication
- [x] JWT Authentication with Laravel Sanctum
- [x] CRUD operations for all entities
- [x] Advanced filtering and search
- [x] Pagination for all listings
- [x] Data validation and error handling
- [x] Postman collection for API testing
- [x] Export functionality (Excel/PDF)
- [x] Unit tests implemented

## 📊 Sample Data

### Animal Species
- Cattle (Bovinos)
- Goats (Caprinos)
- Pigs (Suínos)

### Crop Types
- Orange Pear (Laranja Pera)
- Watermelon Crimson Sweet (Melancia Crimson Sweet)
- Guava Paluma (Goiaba Paluma)

## 📈 Export Reports Examples

### Properties Export (Excel)
The system can export properties data to Excel format with the following structure:

| Property Name | Municipality | State | Total Area | Farmer Name | State Registration |
|---------------|--------------|-------|------------|-------------|-------------------|
| Fazenda São José | Ribeirão Preto | SP | 100.50 ha | João Silva | 123456789 |
| Fazenda Nova Vida | Campinas | SP | 200.75 ha | Maria Santos | 987654321 |

**Export Endpoint**: `GET /api/properties/export/excel`

### Herds Export (PDF)
Export herds data by farmer in PDF format:

**Report Structure**:
- **Farmer Information**: Name, CPF/CNPJ, Contact
- **Properties Summary**: Total properties, total area
- **Herd Details**: Species, quantity, purpose per property
- **Statistics**: Total animals by species, average per property

**Export Endpoint**: `GET /api/herds/export/pdf?farmer_id={id}`

### Dashboard Reports
Real-time dashboard with key metrics:

- **Total Farmers**: Count of registered farmers
- **Total Properties**: Count of properties by municipality
- **Production Areas**: Total hectares by crop type
- **Animal Inventory**: Total animals by species
- **Geographic Distribution**: Properties by state/municipality

**Endpoint**: `GET /api/reports/dashboard`

## ⚠️ General Project Notes

### Development Environment
- **File Permissions**: The system includes a `fix-permissions.sh` script to resolve Docker file permission issues
- **Hot Reload**: Frontend supports hot reload during development
- **Database Seeding**: Sample data is automatically created for testing
- **API Testing**: Complete Postman collection included for API testing

### Security Considerations
- **Authentication**: JWT tokens with 7-day expiration
- **CORS**: Configured for frontend-backend communication
- **Validation**: Server-side validation for all inputs
- **SQL Injection**: Protected with Eloquent ORM
- **XSS**: Input sanitization implemented

### Performance Optimizations
- **Database Indexing**: Proper indexes on foreign keys and search fields
- **Eager Loading**: Relationships loaded efficiently to prevent N+1 queries
- **Pagination**: All listings paginated (15 items per page)
- **Caching**: Laravel caching for frequently accessed data
- **Docker Optimization**: Multi-stage builds and Alpine Linux images

### API Rate Limiting
- **Authentication Endpoints**: 5 requests per minute
- **CRUD Operations**: 60 requests per minute
- **Export Operations**: 10 requests per minute

### Error Handling
- **HTTP Status Codes**: Proper status codes for all responses
- **Validation Errors**: Detailed field-level error messages
- **Database Errors**: Graceful handling of constraint violations
- **Network Errors**: Retry mechanisms for external services

## 🛠️ Development

### Adding New Features

1. **Backend (Laravel)**
```bash
# Create migration
docker compose exec backend php artisan make:migration create_table_name

# Create model
docker compose exec backend php artisan make:model ModelName

# Create controller
docker compose exec backend php artisan make:controller NameController --api

# Create service
docker compose exec backend php artisan make:service NameService

# Run migrations
docker compose exec backend php artisan migrate
```

2. **Frontend (Vue)**
```bash
# Install dependencies
docker compose exec frontend npm install package-name

# Run in development mode
docker compose exec frontend npm run dev

# Build for production
docker compose exec frontend npm run build
```

## 🔌 API Documentation

### Authentication
All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer {your-jwt-token}
```

### Base URL
```
http://localhost/api
```

### Main Endpoints

#### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `GET /api/auth/user` - Get authenticated user
- `POST /api/auth/logout` - User logout

#### Farmers
- `GET /api/farmers` - List farmers (with pagination and filters)
- `POST /api/farmers` - Create farmer
- `GET /api/farmers/{id}` - Get farmer details
- `PUT /api/farmers/{id}` - Update farmer
- `DELETE /api/farmers/{id}` - Delete farmer

#### Properties
- `GET /api/properties` - List properties (with pagination and filters)
- `POST /api/properties` - Create property
- `GET /api/properties/{id}` - Get property details
- `PUT /api/properties/{id}` - Update property
- `DELETE /api/properties/{id}` - Delete property
- `GET /api/properties/export/excel` - Export properties to Excel

#### Production Units
- `GET /api/production-units` - List production units
- `POST /api/production-units` - Create production unit
- `GET /api/production-units/{id}` - Get production unit details
- `PUT /api/production-units/{id}` - Update production unit
- `DELETE /api/production-units/{id}` - Delete production unit

#### Herds
- `GET /api/herds` - List herds
- `POST /api/herds` - Create herd
- `GET /api/herds/{id}` - Get herd details
- `PUT /api/herds/{id}` - Update herd
- `DELETE /api/herds/{id}` - Delete herd
- `GET /api/herds/export/pdf` - Export herds to PDF

### Testing the API
1. Import the Postman collection: `Agricultural_Management_System_API.postman_collection.json`
2. Set the environment variables:
   - `base_url`: `http://localhost`
   - `auth_token`: (will be set after login)
3. Follow the testing guide: `POSTMAN_TESTING_GUIDE.md`

---

**Made with ❤️ for agricultural management**
