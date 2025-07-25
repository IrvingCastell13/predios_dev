<template>
  <div class="chart-container" style="position: relative; height: 400px; overflow-y: scroll;">
    <Bar v-if="chartData.datasets.length > 0 && !loading" :data="chartData" :options="chartOptions" />
    <div v-if="loading" class="text-center text-gray-500 pt-8">Cargando datos de vigencia...</div>
    <div v-if="!loading && chartData.datasets.length === 0" class="text-center text-gray-500 pt-8">No hay datos para mostrar con los filtros seleccionados.</div>
  </div>
</template>

<script>
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import axios from 'axios';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

export default {
  name: 'HorizontalVigenciaPorPredio',
  components: { Bar },
  props: {
    // Props para recibir los IDs de los filtros seleccionados
    idPredios: { type: Array, required: true },
    idGrupos: { type: Array, required: true },
    idCategorias: { type: Array, required: true },
    idTiposDocumento: { type: Array, required: true },
    idTiposInmueble: { type: Array, required: true }
  },
  data() {
    return {
      loading: false,
      chartData: {
        labels: [],
        datasets: []
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
          title: {
            display: true,
            text: 'Nivel de Vigencia de Documentos por Predio',
            font: { size: 16 }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) { label += ': '; }
                if (context.parsed.x !== null) {
                  label += context.parsed.x.toFixed(2) + '%';
                }
                return label;
              }
            }
          },
          legend: { position: 'bottom' }
        },
        scales: {
          x: {
            stacked: true,
            title: { display: true, text: 'Porcentaje (%)' },
            max: 100,
            ticks: {
              callback: function(value) { return value + "%" }
            }
          },
          y: { stacked: true }
        }
      },
      cancelTokenSource: null
    };
  },
  computed: {
    // Creamos un objeto computado con todos los filtros.
    // El 'watcher' observará este objeto para recargar la gráfica.
    apiParams() {
      return {
        id_predios: this.idPredios,
        id_grupos_doc: this.idGrupos,
        id_categorias_doc: this.idCategorias,
        id_tipos_documento: this.idTiposDocumento,
        id_tipos_inmueble: this.idTiposInmueble
      };
    }
  },
  watch: {
    // Cuando cualquiera de los filtros cambie, este watcher se activará
    apiParams: {
      handler() {
        this.fetchData();
      },
      deep: true
    }
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      this.loading = true;
      if (this.cancelTokenSource) {
        this.cancelTokenSource.cancel('Operación cancelada por una nueva solicitud.');
      }
      this.cancelTokenSource = axios.CancelToken.source();

      try {
        const response = await axios.get('/api/bi/porcentaje-vigencia-por-predio', {
          params: this.apiParams, // Usamos el objeto computado que contiene todos los filtros
          cancelToken: this.cancelTokenSource.token
        });
        
        this.chartData = response.data;

      } catch (error) {
        if (axios.isCancel(error)) {
          console.log('Solicitud cancelada:', error.message);
        } else {
          console.error('Error al obtener los datos de vigencia por predio:', error);
          this.chartData = { labels: [], datasets: [] };
        }
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.chart-container {
  padding: 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  background-color: #ffffff;
}
</style>
