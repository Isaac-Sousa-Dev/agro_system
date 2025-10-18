<template>
  <div class="dashboard">
    <!-- Teste com classes Tailwind -->
    <div class="page-header">
      <h1 class="text-3xl font-bold mb-2">Dashboard</h1>
      <p class="text-gray-500">Gerencie sua propriedade rural de forma eficiente e organizada</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="pi pi-users"></i>
        </div>
        <div class="stat-content">
          <h3>Produtores</h3>
          <p class="stat-number">{{ quantityProducers }}</p>
          <p class="stat-label">Cadastrados</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="pi pi-home"></i>
        </div>
        <div class="stat-content">
          <h3>Propriedades</h3>
          <p class="stat-number">{{ quantityProperties }}</p>
          <p class="stat-label">Registradas</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="pi pi-sort-amount-up"></i>
        </div>
        <div class="stat-content">
          <h3>Unid. de Produção</h3>
          <p class="stat-number">{{ quantityProductionUnits }}</p>
          <p class="stat-label">Cadastradas</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="pi pi-circle"></i>
        </div>
        <div class="stat-content">
          <h3>Rebanhos</h3>
          <p class="stat-number">{{ quantityHerds }}</p>
          <p class="stat-label">Animais</p>
        </div>
      </div>
    </div>

    <div class="charts-section">
      <div class="chart-card">
        <h3>Propriedades por Município</h3>
        <div id="propertiesChart" class="chart-container"></div>
      </div>
      <div class="charts-grid mt-4">

        <div class="chart-card">
          <h3>Animais por Espécie</h3>
          <div id="animalsChart" class="chart-container"></div>
        </div>

        <div class="chart-card">
          <h3>Hectares por Cultura</h3>
          <div id="cropsChart" class="chart-container"></div>
        </div>
      </div>
    </div>

    <div class="info-section">
      <h2>Funcionalidades do Sistema</h2>
      <div class="features-grid">
        <div class="feature">
          <div class="feature-icon">
            <i class="pi pi-chart-bar"></i>
          </div>
          <h4>Relatórios Avançados</h4>
          <p>Total de propriedades por município, animais por espécie e hectares por cultura</p>
        </div>
        <div class="feature">
          <div class="feature-icon">
            <i class="pi pi-download"></i>
          </div>
          <h4>Exportação</h4>
          <p>Exporte dados em Excel (.xlsx) e PDF para análise e compartilhamento</p>
        </div>
        <div class="feature">
          <div class="feature-icon">
            <i class="pi pi-search"></i>
          </div>
          <h4>Filtros e Busca</h4>
          <p>Filtros avançados por município, espécie, produtor e muito mais</p>
        </div>
        <div class="feature">
          <div class="feature-icon">
            <i class="pi pi-mobile"></i>
          </div>
          <h4>Interface Responsiva</h4>
          <p>Acesse o sistema de qualquer dispositivo com design moderno</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useReportStore } from '@/stores/report';
import ApexCharts from 'apexcharts'

const { getDashboardData} = useReportStore()

// Configuração do gráfico de propriedades por município
const createPropertiesChart = () => {
  const options = {
    chart: {
      type: 'bar',
      height: 350,
      toolbar: {
        show: false
      }
    },
    series: [{
      name: 'Propriedades',
      data: propertiesByMunicipality.value.map((item: { municipality: string; count: number }) => item.count)
    }],
    xaxis: {
      categories: propertiesByMunicipality.value.map((item: { municipality: string; count: number }) => item.municipality),
      labels: {
        style: {
          fontSize: '12px'
        }
      }
    },
    yaxis: {
      title: {
        text: 'Quantidade de Propriedades'
      }
    },
    colors: ['#059669'],
    dataLabels: {
      enabled: true,
      formatter: function (val: number) {
        return val.toString()
      }
    },
    plotOptions: {
      bar: {
        borderRadius: 4,
        horizontal: false,
      }
    }
  }

  const chart = new ApexCharts(document.querySelector("#propertiesChart"), options);
  chart.render();
}

// Configuração do gráfico de animais por espécie
const createAnimalsChart = () => {
  const options = {
    chart: {
      type: 'pie',
      height: 350,
      toolbar: {
        show: false
      }
    },
    series: animalsBySpecies.value.map((item: { species: string; count: number }) => item.count),
    labels: animalsBySpecies.value.map((item: { species: string; count: number }) => item.species),
    colors: ['#059669', '#0ea5e9', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#84cc16', '#f97316'],
    dataLabels: {
      enabled: true,
      formatter: function (val: number) {
        return val.toFixed(1) + '%'
      }
    },
    legend: {
      position: 'bottom',
      fontSize: '12px'
    },
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 200
        },
        legend: {
          position: 'bottom'
        }
      }
    }]
  }

  const chart = new ApexCharts(document.querySelector("#animalsChart"), options);
  chart.render();
}

// Configuração do gráfico de hectares por cultura
const createCropsChart = () => {
  const options = {
    chart: {
      type: 'area',
      height: 350,
      toolbar: {
        show: false
      }
    },
    series: [{
      name: 'Hectares',
      data: hectaresByCrop.value.map((item: { crop: string; hectares: number }) => item.hectares)
    }],
    xaxis: {
      categories: hectaresByCrop.value.map((item: { crop: string; hectares: number }) => item.crop),
      labels: {
        style: {
          fontSize: '12px'
        }
      }
    },
    yaxis: {
      title: {
        text: 'Hectares'
      }
    },
    colors: ['#059669'],
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'light',
        type: 'vertical',
        shadeIntensity: 0.5,
        gradientToColors: ['#10b981'],
        inverseColors: false,
        opacityFrom: 0.8,
        opacityTo: 0.1,
        stops: [0, 100]
      }
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'smooth',
      width: 2
    }
  }

  const chart = new ApexCharts(document.querySelector("#cropsChart"), options);
  chart.render();
}


const quantityProducers = ref<number>(0)
const quantityProperties = ref<number>(0)
const quantityProductionUnits = ref<number>(0)
const quantityHerds = ref<number>(0)

const propertiesByMunicipality = ref([])
const animalsBySpecies = ref([])
const hectaresByCrop = ref([])



onMounted(async () => {
  const data = await getDashboardData();
  if (data.success) {
    const dataResponse = data.data
    quantityProducers.value = dataResponse.quantityProducers
    quantityProperties.value = dataResponse.quantityProperties
    quantityProductionUnits.value = dataResponse.quantityProductionUnits
    quantityHerds.value = dataResponse.quantityHerds
    propertiesByMunicipality.value = dataResponse.propertiesByMunicipality
    animalsBySpecies.value = dataResponse.animalsBySpecies
    hectaresByCrop.value = dataResponse.hectaresByCrop
  }
  console.log(data, 'data');
  setTimeout(() => {
    createPropertiesChart();
    createAnimalsChart();
    createCropsChart();
  }, 100);
})

</script>

<style scoped>
.dashboard {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  font-size: 2.5rem;
  width: 4rem;
  height: 4rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  border-radius: 1rem;
  color: white;
}

.stat-content h3 {
  margin: 0 0 0.5rem 0;
  color: #374151;
  font-size: 1rem;
  font-weight: 600;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: #059669;
  margin: 0;
  line-height: 1;
}

.stat-label {
  margin: 0;
  color: #6b7280;
  font-size: 0.875rem;
}

.charts-section {
  margin-bottom: 3rem;
}

.charts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.chart-card {
  background: white;
  padding: 1.5rem;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
}

.chart-card h3 {
  margin: 0 0 1rem 0;
  color: #374151;
  font-size: 1.25rem;
  font-weight: 600;
  text-align: center;
}

.chart-container {
  width: 100%;
  height: 350px;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.action-card {
  background: white;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
  text-align: center;
}

.action-card h3 {
  margin: 0 0 1rem 0;
  color: #374151;
  font-size: 1.25rem;
  font-weight: 600;
}

.action-card p {
  margin: 0 0 1.5rem 0;
  color: #6b7280;
  line-height: 1.6;
}

.info-section {
  background: white;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
}

.info-section h2 {
  margin: 0 0 2rem 0;
  color: #374151;
  font-size: 1.5rem;
  font-weight: 600;
  text-align: center;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.feature {
  text-align: center;
  padding: 1rem;
}

.feature-icon {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.feature h4 {
  margin: 0 0 0.5rem 0;
  color: #374151;
  font-size: 1.1rem;
  font-weight: 600;
}

.feature p {
  margin: 0;
  color: #6b7280;
  font-size: 0.9rem;
  line-height: 1.5;
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .charts-grid {
    grid-template-columns: 1fr;
  }

  .actions-grid {
    grid-template-columns: 1fr;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

  .chart-container {
    height: 300px;
  }
}
</style>
