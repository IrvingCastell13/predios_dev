<template>
  <div style="height: 400px;">
    <Bar v-if="chartData" :data="chartData" :options="chartOptions" />
    <div v-else class="text-center text-gray-400 py-4">Cargando gráfica...</div>
  </div>
</template>

<script>
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js';
import axios from 'axios';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

export default {
  name: 'BaseVerticalBar',
  components: {
    Bar
  },
  props: {
    apiUrl: String,
    chartTitle: String,
    startDate: String,
    endDate: String
  },
  data() {
    return {
      chartData: null,
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' },
          title: {
            display: true,
            text: this.chartTitle
          },
          tooltip: { enabled: true }
        },
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Número de Tickets'
            }
          },
          x: {
            title: {
              display: true,
              text: 'Meses'
            }
          }
        }
      }
    };
  },
  watch: {
    startDate: 'fetchChartData',
    endDate: 'fetchChartData'
  },
  mounted() {
    this.fetchChartData();
  },
  methods: {
    async fetchChartData() {
      try {
        const response = await axios.get(this.apiUrl, {
          params: {
            start: this.startDate,
            end: this.endDate
          }
        });
        this.chartData = response.data;
      } catch (error) {
        console.error('Error al obtener datos del API:', error);
      }
    }
  }
};
</script>

