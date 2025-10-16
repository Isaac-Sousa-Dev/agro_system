# Guia de Estilos Reutilizáveis - Sistema Agro

Este documento descreve o sistema de estilos reutilizáveis implementado para evitar repetição de código CSS em todo o projeto.

## 🎯 Objetivo

Padronizar a aparência de todos os componentes (tabelas, modais, botões, formulários) e eliminar a duplicação de código CSS entre as views.

## 📁 Localização

Os estilos globais estão localizados em: `src/assets/main.css`

## 🧩 Componentes Disponíveis

### Layout de Página

```html
<div class="page-container">
  <div class="page-header">
    <h1>Título da Página</h1>
    <p>Descrição da página</p>
  </div>
  <!-- Conteúdo -->
</div>
```

### Toolbar

```html
<div class="toolbar">
  <div class="toolbar-left">
    <button class="btn-primary">
      <i class="pi pi-plus"></i>
      Novo Item
    </button>
  </div>
  <div class="toolbar-right">
    <!-- Filtros e ações -->
  </div>
</div>
```

### Botões

```html
<!-- Botão primário -->
<button class="btn-primary">
  <i class="pi pi-plus"></i>
  Ação Primária
</button>

<!-- Botão secundário -->
<button class="btn-secondary">
  <i class="pi pi-refresh"></i>
  Atualizar
</button>

<!-- Botão de perigo -->
<button class="btn-danger">
  <i class="pi pi-trash"></i>
  Excluir
</button>
```

### Tabelas

```html
<div class="table-card">
  <table class="table">
    <thead>
      <tr>
        <th>Coluna 1</th>
        <th>Coluna 2</th>
        <th style="width: 1%">Ações</th>
      </tr>
    </thead>
    <tbody>
      <tr v-if="loading">
        <td colspan="3" class="muted">Carregando...</td>
      </tr>
      <tr v-else-if="!items.length">
        <td colspan="3" class="muted">Nenhum item encontrado</td>
      </tr>
      <tr v-for="item in items" :key="item.id">
        <td>{{ item.nome }}</td>
        <td>{{ item.descricao }}</td>
        <td>
          <div class="row-actions">
            <button class="icon" title="Ver">
              <i class="pi pi-eye"></i>
            </button>
            <button class="icon" title="Editar">
              <i class="pi pi-pencil"></i>
            </button>
            <button class="icon danger" title="Excluir">
              <i class="pi pi-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  <Paginator
    :first="(currentPage - 1) * perPage"
    :rows="perPage"
    :totalRecords="total"
    @page="onPage"
  />
</div>
```

### Modais

```html
<div v-if="showModal" class="modal-backdrop">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Título do Modal</h3>
      <button class="modal-close" @click="showModal = false">
        <i class="pi pi-times"></i>
      </button>
    </div>
    
    <div class="modal-content">
      <!-- Conteúdo do modal -->
    </div>
    
    <div class="modal-actions">
      <button class="btn-secondary" @click="showModal = false">
        Cancelar
      </button>
      <button class="btn-primary" @click="salvar">
        Salvar
      </button>
    </div>
  </div>
</div>
```

### Formulários

```html
<form class="form-grid">
  <div class="form-group">
    <label class="form-label">Nome</label>
    <input class="form-input" v-model="form.nome" required />
  </div>
  
  <div class="form-group col-span-2">
    <label class="form-label">Descrição</label>
    <textarea class="form-input" v-model="form.descricao"></textarea>
  </div>
</form>
```

### Detalhes/Informações

```html
<div class="details">
  <div class="detail-item">
    <span class="detail-label">Nome:</span>
    <span class="detail-value">{{ item.nome }}</span>
  </div>
  <div class="detail-item">
    <span class="detail-label">Criado em:</span>
    <span class="detail-value">{{ item.created_at }}</span>
  </div>
</div>
```

### Estados Especiais

```html
<!-- Estado de carregamento -->
<div class="loading-state">
  <i class="pi pi-spin pi-spinner"></i>
  Carregando...
</div>

<!-- Estado vazio -->
<div class="empty-state">
  <div class="empty-state-icon">
    <i class="pi pi-inbox"></i>
  </div>
  <h4 class="empty-state-title">Nenhum item encontrado</h4>
  <p class="empty-state-description">
    Não há itens para exibir no momento.
  </p>
</div>
```

## 🎨 Características dos Estilos

### Cores Principais
- **Verde primário**: `#059669` (botões principais, links)
- **Cinza neutro**: `#374151` (texto principal)
- **Cinza claro**: `#6b7280` (texto secundário)
- **Branco**: `#ffffff` (fundo de cards)
- **Cinza de fundo**: `#f9fafb` (fundo de página)

### Efeitos Visuais
- **Sombras suaves**: Para dar profundidade aos cards
- **Hover effects**: Transformações sutis nos botões
- **Transições**: Animações suaves de 0.2s
- **Border radius**: Cantos arredondados (0.375rem)

### Responsividade
- **Mobile-first**: Design adaptado para dispositivos móveis
- **Breakpoint**: 768px para mudanças de layout
- **Grid responsivo**: Colunas que se adaptam ao tamanho da tela

## 📱 Responsividade

Os estilos incluem media queries para dispositivos móveis:

```css
@media (max-width: 768px) {
  .toolbar {
    flex-direction: column;
    gap: 1rem;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .table {
    font-size: 0.875rem;
  }
}
```

## 🔧 Como Usar

1. **Importe os estilos globais** no `main.ts`:
   ```typescript
   import './assets/main.css'
   ```

2. **Use as classes CSS** nos seus componentes Vue
3. **Evite estilos inline** ou CSS duplicado
4. **Mantenha consistência** usando sempre as mesmas classes

## 📋 Checklist para Novas Views

- [ ] Usar `page-container` para o layout principal
- [ ] Usar `page-header` para título e descrição
- [ ] Usar `toolbar` para botões de ação e filtros
- [ ] Usar `table-card` e `table` para tabelas
- [ ] Usar `btn-primary`, `btn-secondary`, `btn-danger` para botões
- [ ] Usar `modal-backdrop` e `modal` para modais
- [ ] Usar `form-grid` e `form-input` para formulários
- [ ] Usar `row-actions` e `icon` para ações da tabela
- [ ] Remover estilos CSS duplicados do componente

## 🚀 Benefícios

- ✅ **Consistência visual** em toda a aplicação
- ✅ **Manutenibilidade** - mudanças em um lugar afetam todo o sistema
- ✅ **Performance** - menos CSS duplicado
- ✅ **Desenvolvimento mais rápido** - componentes prontos para usar
- ✅ **Responsividade automática** - adaptação a diferentes telas

## 🔄 Atualizações

Quando precisar fazer mudanças nos estilos:

1. Edite apenas o arquivo `src/assets/main.css`
2. As mudanças serão aplicadas automaticamente em todos os componentes
3. Teste em diferentes resoluções para garantir responsividade
4. Mantenha este guia atualizado com novas classes adicionadas
