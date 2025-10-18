# Exemplo de Store Pinia Atualizada para Dashboard

## Store Atualizada

```typescript
import { defineStore } from "pinia"
import { ref } from "vue"
import api from '@/services/api'

export const useReportStore = defineStore('report', () => {
  const loading = ref(false)
  const error = ref<string | null>(null)
  const reports = ref<[]>([])

  // Contadores básicos
  const quantityProducers = ref<number>(0)
  const quantityProperties = ref<number>(0)
  const quantityProductionUnits = ref<number>(0)
  const quantityHerds = ref<number>(0)

  // Dados para gráficos
  const propertiesByMunicipality = ref<Array<{municipality: string, count: number}>>([])
  const animalsBySpecies = ref<Array<{species: string, count: number}>>([])
  const hectaresByCrop = ref<Array<{crop: string, hectares: number}>>([])

  // Dados adicionais
  const totalAnimals = ref<number>(0)
  const totalHectares = ref<number>(0)
  const averageAnimalsPerProperty = ref<number>(0)
  const averageHectaresPerProperty = ref<number>(0)

  // Rankings
  const topProducersByAnimals = ref<Array<{name: string, total_animals: number}>>([])
  const topProducersByArea = ref<Array<{name: string, total_area: number}>>([])
  const propertiesByState = ref<Array<{state: string, count: number}>>([])

  // Metadados
  const generatedAt = ref<string>('')
  const dataRange = ref<{from: string | null, to: string | null}>({from: null, to: null})

  const getDashboardData = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.get('/reports/dashboard')
      
      if (response.data.success) {
        const data = response.data.data
        
        // Atualizar contadores básicos
        quantityProducers.value = data.quantityProducers
        quantityProperties.value = data.quantityProperties
        quantityProductionUnits.value = data.quantityProductionUnits
        quantityHerds.value = data.quantityHerds
        
        // Atualizar dados para gráficos
        propertiesByMunicipality.value = data.propertiesByMunicipality
        animalsBySpecies.value = data.animalsBySpecies
        hectaresByCrop.value = data.hectaresByCrop
        
        // Atualizar dados adicionais
        totalAnimals.value = data.totalAnimals
        totalHectares.value = data.totalHectares
        averageAnimalsPerProperty.value = data.averageAnimalsPerProperty
        averageHectaresPerProperty.value = data.averageHectaresPerProperty
        
        // Atualizar rankings
        topProducersByAnimals.value = data.topProducersByAnimals
        topProducersByArea.value = data.topProducersByArea
        propertiesByState.value = data.propertiesByState
        
        // Atualizar metadados
        generatedAt.value = data.generatedAt
        dataRange.value = data.dataRange
        
        return data
      } else {
        throw new Error(response.data.message || 'Erro ao carregar dados do dashboard')
      }
    } catch (err: any) {
      error.value = err.message || 'Erro ao carregar dados do dashboard'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    // Estado
    loading,
    error,
    reports,
    
    // Contadores básicos
    quantityProducers,
    quantityProperties,
    quantityProductionUnits,
    quantityHerds,
    
    // Dados para gráficos
    propertiesByMunicipality,
    animalsBySpecies,
    hectaresByCrop,
    
    // Dados adicionais
    totalAnimals,
    totalHectares,
    averageAnimalsPerProperty,
    averageHectaresPerProperty,
    
    // Rankings
    topProducersByAnimals,
    topProducersByArea,
    propertiesByState,
    
    // Metadados
    generatedAt,
    dataRange,
    
    // Ações
    getDashboardData
  }
})
```

## Exemplo de Uso no Componente Vue

```vue
<template>
  <div class="dashboard">
    <!-- Loading -->
    <div v-if="loading" class="loading">
      Carregando dados do dashboard...
    </div>
    
    <!-- Error -->
    <div v-if="error" class="error">
      {{ error }}
    </div>
    
    <!-- Dashboard Content -->
    <div v-else class="dashboard-content">
      <!-- Cards de Resumo -->
      <div class="summary-cards">
        <div class="card">
          <h3>Produtores</h3>
          <p class="number">{{ quantityProducers }}</p>
        </div>
        <div class="card">
          <h3>Propriedades</h3>
          <p class="number">{{ quantityProperties }}</p>
        </div>
        <div class="card">
          <h3>Unidades de Produção</h3>
          <p class="number">{{ quantityProductionUnits }}</p>
        </div>
        <div class="card">
          <h3>Rebanhos</h3>
          <p class="number">{{ quantityHerds }}</p>
        </div>
      </div>
      
      <!-- Gráficos -->
      <div class="charts">
        <div class="chart-container">
          <h3>Propriedades por Município</h3>
          <BarChart :data="propertiesByMunicipality" />
        </div>
        
        <div class="chart-container">
          <h3>Animais por Espécie</h3>
          <PieChart :data="animalsBySpecies" />
        </div>
        
        <div class="chart-container">
          <h3>Hectares por Cultura</h3>
          <BarChart :data="hectaresByCrop" />
        </div>
      </div>
      
      <!-- Rankings -->
      <div class="rankings">
        <div class="ranking-card">
          <h3>Top 5 Produtores por Animais</h3>
          <ul>
            <li v-for="producer in topProducersByAnimals" :key="producer.name">
              {{ producer.name }}: {{ producer.total_animals }} animais
            </li>
          </ul>
        </div>
        
        <div class="ranking-card">
          <h3>Top 5 Produtores por Área</h3>
          <ul>
            <li v-for="producer in topProducersByArea" :key="producer.name">
              {{ producer.name }}: {{ producer.total_area }} hectares
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useReportStore } from '@/stores/report'

const reportStore = useReportStore()

// Destructuring para facilitar o uso
const {
  loading,
  error,
  quantityProducers,
  quantityProperties,
  quantityProductionUnits,
  quantityHerds,
  propertiesByMunicipality,
  animalsBySpecies,
  hectaresByCrop,
  topProducersByAnimals,
  topProducersByArea,
  getDashboardData
} = reportStore

onMounted(async () => {
  try {
    await getDashboardData()
  } catch (err) {
    console.error('Erro ao carregar dashboard:', err)
  }
})
</script>
```

## Estrutura da Resposta da API

A API `/reports/dashboard` retorna:

```json
{
  "success": true,
  "data": {
    "quantityProducers": 25,
    "quantityProperties": 45,
    "quantityProductionUnits": 120,
    "quantityHerds": 78,
    "propertiesByMunicipality": [
      {"municipality": "Fortaleza", "count": 15},
      {"municipality": "Caucaia", "count": 12},
      {"municipality": "Maracanaú", "count": 8}
    ],
    "animalsBySpecies": [
      {"species": "Bovino", "count": 1250},
      {"species": "Caprino", "count": 890},
      {"species": "Suíno", "count": 420}
    ],
    "hectaresByCrop": [
      {"crop": "Laranja Pera", "hectares": 1250.5},
      {"crop": "Melancia Crimson Sweet", "hectares": 890.2},
      {"crop": "Goiaba Paluma", "hectares": 650.8}
    ],
    "totalAnimals": 2560,
    "totalHectares": 2791.5,
    "averageAnimalsPerProperty": 56.89,
    "averageHectaresPerProperty": 62.03,
    "topProducersByAnimals": [
      {"name": "João Silva", "total_animals": 450},
      {"name": "Maria Santos", "total_animals": 380}
    ],
    "topProducersByArea": [
      {"name": "João Silva", "total_area": 250.5},
      {"name": "Maria Santos", "total_area": 180.2}
    ],
    "propertiesByState": [
      {"state": "CE", "count": 35},
      {"state": "RN", "count": 10}
    ],
    "generatedAt": "2025-01-15 14:30:00",
    "dataRange": {
      "from": "2025-01-01 00:00:00",
      "to": "2025-01-15 14:30:00"
    }
  }
}
```

## Características da Implementação

1. **Dados Reais**: Todos os dados vêm do banco de dados
2. **Agrupamento**: Municípios são agrupados para evitar duplicação
3. **Filtros Específicos**: Apenas as 3 espécies e 3 culturas especificadas
4. **Performance**: Consultas otimizadas com agregações no banco
5. **Tratamento de Erros**: Respostas padronizadas com tratamento de exceções
6. **Metadados**: Informações sobre quando os dados foram gerados
7. **Rankings**: Top produtores por diferentes métricas
