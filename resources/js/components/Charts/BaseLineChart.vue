<template>
    <div style="height: 400px;">
      <Line v-if="chartData" :data="chartData" :options="chartOptions" />
      <div v-else class="text-center text-gray-400 py-4">Cargando gráfica...</div>
    </div>
  </template>

  <script>
  import { Line } from 'vue-chartjs';
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale
  } from 'chart.js';
  import axios from 'axios';

  ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale);

  export default {
    name: 'BaseLineChart',
    components: { Line },
    props: {
        apiUrl: { type: String, required: true },
        chartTitle: { type: String, required: true },
        startDate: { type: String, required: true },
        endDate: { type: String, required: true },
        predio: { type: String, default: '' },
        edificio: { type: String, default: '' },
        zona: { type: String, default: '' },
        nivel: { type: String, default: '' },
        persona: { type: String, default: '' }
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
            }
          },
          scales: {
            x: {
              title: {
                display: true,
                text: 'Meses'
              }
            },
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Número de Tickets'
              }
            }
          }
        }
      };
    },
    watch: {
        startDate: 'fetchChartData',
        endDate: 'fetchChartData',
        predio: 'fetchChartData',
        edificio: 'fetchChartData',
        zona: 'fetchChartData',
        nivel: 'fetchChartData',
        persona: 'fetchChartData'
    },
    mounted() {
      this.fetchChartData();
    },
    methods: {
        async fetchChartData() {

            try {
                const params = {
                    start: this.startDate,
                    end: this.endDate
                };

                if (this.predio) params.predio = this.predio;
                if (this.edificio) params.edificio = this.edificio;
                if (this.zona) params.zona = this.zona;
                if (this.nivel) params.nivel = this.nivel;
                if (this.persona) params.persona = this.persona;

                const response = await axios.get(this.apiUrl, { params });
                this.chartData = response.data;
            } catch (error) {
                console.error('Error al obtener datos del API:', error);
            }
        }
    }
  };
  </script>
