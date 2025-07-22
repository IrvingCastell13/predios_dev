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
    Title,
    Tooltip,
    Legend,
    CategoryScale,
    LinearScale,
    BarElement
  } from 'chart.js';
  import axios from 'axios';
  
  ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);
  
  export default {
    name: 'BaseGroupedBar',
    components: { Bar },
    props: {
      apiUrl: { type: String, required: true },
      chartTitle: { type: String, required: true },
      startDate: { type: String, required: true },
      endDate: { type: String, required: true }
    },
    data() {
      return {
        chartData: null,
        chartOptions: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom'
            },
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
  
          this.chartData = {
            labels: response.data.labels, // Ej: ["Mar", "Abr", "May", ...]
            datasets: [
              {
                label: 'Atrasados',
                backgroundColor: '#e74c3c',
                data: response.data.atrasados
              },
              {
                label: 'En tiempo',
                backgroundColor: '#27ae60',
                data: response.data.enTiempo
              }
            ]
          };
        } catch (error) {
          console.error('Error al obtener datos de la gráfica:', error);
        }
      }
    }
  };
  </script>
  