# 🔐 Sistema de Autenticação - Frontend

Este documento descreve o sistema de autenticação JWT implementado no frontend Vue 3.

## 📋 Funcionalidades Implementadas

### ✅ Autenticação JWT
- Login com email/senha
- Token JWT armazenado em cookies seguros
- Interceptors para adicionar token automaticamente nas requisições
- Refresh automático de token quando necessário

### ✅ Guards de Rota
- Proteção de rotas que requerem autenticação
- Redirecionamento automático para login
- Prevenção de acesso ao login quando já autenticado

### ✅ Gerenciamento de Estado
- Store Pinia para estado global de autenticação
- Composable `useAuth` para facilitar uso nos componentes
- Persistência de sessão com cookies

### ✅ Interface de Usuário
- Tela de login moderna e responsiva
- Menu do usuário com avatar e logout
- Feedback visual de loading e erros
- Validação de formulários

## 🏗️ Arquitetura

### Estrutura de Arquivos
```
src/
├── stores/
│   └── auth.ts              # Store Pinia para autenticação
├── composables/
│   └── useAuth.ts           # Composable para facilitar uso
├── services/
│   └── api.ts               # Cliente HTTP com interceptors
├── components/
│   └── LoginForm.vue        # Componente de login
├── views/
│   └── LoginView.vue        # View de login
├── types/
│   └── auth.ts              # Tipos TypeScript
└── router/
    └── index.ts             # Configuração de rotas com guards
```

### Fluxo de Autenticação

1. **Login**: Usuário insere credenciais → API valida → Retorna JWT
2. **Armazenamento**: Token salvo em cookie seguro
3. **Interceptors**: Token adicionado automaticamente nas requisições
4. **Guards**: Verificação de autenticação antes de acessar rotas protegidas
5. **Logout**: Token removido e redirecionamento para login

## 🔧 Configuração

### Variáveis de Ambiente
```env
VITE_API_URL=http://localhost/api
```

### Cookies
- **Nome**: `token`
- **Expiração**: 7 dias
- **Segurança**: Secure, SameSite=Strict

## 📱 Componentes

### LoginForm.vue
- Formulário de login com validação
- Feedback visual de loading/erro
- Toggle de visibilidade da senha
- Design responsivo

### App.vue
- Header condicional baseado em autenticação
- Menu do usuário com avatar e logout
- Navegação protegida

## 🛡️ Segurança

### Implementações de Segurança
- ✅ Tokens JWT seguros
- ✅ Cookies com flags de segurança
- ✅ Interceptors para renovação automática
- ✅ Validação de formulários
- ✅ Proteção CSRF
- ✅ Redirecionamento automático em caso de token inválido

### Headers de Segurança
```typescript
{
  'Authorization': 'Bearer <token>',
  'X-CSRF-TOKEN': '<csrf-token>',
  'Content-Type': 'application/json'
}
```

## 🚀 Uso nos Componentes

### Composable useAuth
```typescript
import { useAuth } from '@/composables/useAuth'

const { 
  user, 
  isAuthenticated, 
  login, 
  logout, 
  loading, 
  error 
} = useAuth()
```

### Store Direta
```typescript
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
// Usar diretamente a store se necessário
```

## 🔄 Estados da Aplicação

### Estados de Autenticação
1. **Não autenticado**: Redirecionado para `/login`
2. **Autenticado**: Acesso total ao sistema
3. **Token expirado**: Logout automático e redirecionamento
4. **Erro de rede**: Feedback visual e retry

### Rotas Protegidas
- `/` - Dashboard (requer auth)
- `/produtores` - Gestão de produtores (requer auth)
- `/propriedades` - Gestão de propriedades (requer auth)
- `/relatorios` - Relatórios (requer auth)

### Rotas Públicas
- `/login` - Tela de login (só para não autenticados)

## 🧪 Testes

### Cenários de Teste
1. **Login bem-sucedido**
2. **Login com credenciais inválidas**
3. **Acesso a rota protegida sem autenticação**
4. **Logout e limpeza de dados**
5. **Token expirado**
6. **Erro de rede**

## 🔧 Configuração do Backend

O frontend espera que o backend tenha os seguintes endpoints:

```
POST /api/auth/login
POST /api/auth/logout
GET  /api/auth/me
GET  /api/csrf-cookie
```

## 📝 Próximos Passos

1. **Implementar refresh token**
2. **Adicionar 2FA**
3. **Implementar "Lembrar-me"**
4. **Adicionar recuperação de senha**
5. **Implementar roles e permissões**

## 🐛 Troubleshooting

### Problemas Comuns
1. **Token não enviado**: Verificar interceptors
2. **Redirecionamento infinito**: Verificar guards
3. **Cookie não persistido**: Verificar configurações de segurança
4. **CORS errors**: Verificar configuração do backend

