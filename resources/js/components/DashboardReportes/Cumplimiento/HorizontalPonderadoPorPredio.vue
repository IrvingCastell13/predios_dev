<template>
  <div style="position: relative; height: 400px; overflow-y: scroll;">
    <div v-if="isLoading" class="d-flex justify-content-center align-items-center h-100">
        <span class="text-secondary">Cargando gráfica...</span>
    </div>
    <Bar v-else-if="chartData" :data="chartData" :options="chartOptions" />
    <div v-else class="d-flex justify-content-center align-items-center h-100">
        <span class="text-secondary">Seleccione uno o más predios para ver los datos.</span>
    </div>
  </div>
</template>

<script>
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js';
import axios from 'axios';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

export default {
  name: 'HorizontalPonderadoPorPredio',
  components: { Bar },
  props: {
    apiUrl: { type: String, required: true },
    chartTitle: { type: String, required: true },
    idPredios: { type: Array, required: true },
    idGrupos: { type: Array, required: true },
    idCategorias: { type: Array, required: true },
    idTiposDocumento: { type: Array, required: true },
    idTiposInmueble: { type: Array, required: true }
  },
  data() {
    return {
      chartData: null,
      isLoading: true,
    };
  },
  computed: {
    chartOptions() {
        return {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: this.chartTitle }
            },
            scales: {
                x: { min: 0, max: 100, ticks: { stepSize: 20 }, title: { display: true, text: 'Porcentaje (%)' }},
                y: { stacked: false }
            }
        }
    },
    filtros() {
        return {
            predios: this.idPredios,
            grupos: this.idGrupos,
            categorias: this.idCategorias,
              // --- INICIO: Añadir a filtros ---
            tiposDocumento: this.idTiposDocumento,
            tiposInmueble: this.idTiposInmueble
            // --- FIN: Añadir a filtros ---
        };
    }
  },
  watch: {
    filtros: {
        handler() {
            this.fetchData();
        },
        deep: true,
        immediate: true
    }
  },
  methods: {
    async fetchData() {
        this.isLoading = true;
        this.chartData = null;

        try {
            const response = await axios.get(this.apiUrl, {
                params: {
                    predio_ids: this.idPredios,
                    grupo_ids: this.idGrupos,
                    categoria_ids: this.idCategorias,
                    tipo_doc_ids: this.idTiposDocumento,
                    tipo_inmueble_ids: this.idTiposInmueble
                }
            });
            this.chartData = response.data;
        } catch (error) {
            console.error('Error al obtener datos para la gráfica de barras:', error);
        } finally {
            this.isLoading = false;
        }
    }
  }
};
</script>