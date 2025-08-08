<template>
    <div class="space-y-6">
      <!-- Filtros -->
      <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <select v-model="filters.predio" class="border rounded px-3 py-2">
          <option value="">Predio</option>
          <option v-for="item in catalogos.predios" :key="item.id" :value="item.id">{{ item.nombre }}</option>
        </select>
  
        <select v-model="filters.edificio" class="border rounded px-3 py-2">
          <option value="">Edificio</option>
          <option v-for="item in catalogos.edificios" :key="item.id" :value="item.id">{{ item.nombre }}</option>
        </select>
  
        <select v-model="filters.nivel" class="border rounded px-3 py-2">
          <option value="">Nivel</option>
          <option v-for="item in catalogos.niveles" :key="item.id" :value="item.id">{{ item.nombre }}</option>
        </select>
  
        <select v-model="filters.zona" class="border rounded px-3 py-2">
          <option value="">Zona</option>
          <option v-for="item in catalogos.zonas" :key="item.id" :value="item.id">{{ item.nombre }}</option>
        </select>
  
        <select v-model="filters.tipoEquipo" class="border rounded px-3 py-2">
          <option value="">Tipo de equipo</option>
          <option v-for="item in catalogos.tiposEquipo" :key="item.id" :value="item.id">{{ item.nombre }}</option>
        </select>
  
        <button @click="aplicarFiltros" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded">
          Aplicar filtros
        </button>
      </div>
  
      <!-- Gráficas -->
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="border border-gray-300 rounded p-4 shadow">
          <BaseDonutChart
            ref="donut"
            apiUrl="/api/v1/tickets-por-equipo"
            chartTitle="Tickets por Equipo"
            v-bind="filters"
          />
        </div>
        <div class="border border-gray-300 rounded p-4 shadow">
          <BaseHorizontalStackedBar
            ref="tickets"
            apiUrl="/api/v1/tickets-equipo-detalle"
            chartTitle="Tickets por Equipo"
            v-bind="filters"
          />
        </div>
      </div>  
    
    <div class="border border-gray-300 rounded p-4 shadow">
        <BaseHorizontalStackedBar
        ref="incidencias"
        apiUrl="/api/v1/incidencias-por-equipo"
        chartTitle="Incidencias por Equipo y nivel de cumplimiento"
        v-bind="filters"
        />
    </div>

    <div class="border border-gray-300 rounded p-4 shadow">
        <BaseHorizontalStackedBar
        ref="rechazo"
        apiUrl="/api/v1/rechazo-por-equipo"
        chartTitle="% Rechazo por Equipo"
        v-bind="filters"
        />
    </div>
      
    </div>
  </template>
  
  <script>
  import BaseHorizontalStackedBar from '../BaseHorizontalStackedBar.vue';
  import BaseDonutChart from '../BaseDonutChart.vue';
  import axios from 'axios';
  
  export default {
    name: 'SeccionEquipos',
    components: {
      BaseHorizontalStackedBar,
      BaseDonutChart
    },
    props: {
      startDate: { type: String, required: true },
      endDate: { type: String, required: true }
    },
    data() {
      return {
        filters: {
          predio: '',
          edificio: '',
          nivel: '',
          zona: '',
          tipoEquipo: '',
          startDate: this.startDate,
          endDate: this.endDate
        },
        catalogos: {
          predios: [],
          edificios: [],
          niveles: [],
          zonas: [],
          tiposEquipo: []
        }
      };
    },
    watch: {
      startDate(newVal) {
        this.filters.startDate = newVal;
      },
      endDate(newVal) {
        this.filters.endDate = newVal;
      }
    },
    mounted() {
      this.cargarCatalogos();
    },
    methods: {
      async cargarCatalogos() {
        try {
          const response = await axios.get('/api/v1/catalogos/equipos-filtros');
          this.catalogos = response.data;
        } catch (error) {
          console.error('Error al cargar catálogos:', error);
        }
      },
      aplicarFiltros() {
        this.$refs.donut?.fetchChartData?.();
        this.$refs.tickets?.fetchChartData?.();
        this.$refs.incidencias?.fetchChartData?.();
        this.$refs.rechazo?.fetchChartData?.();
      }
    }
  };
  </script>
  <style scoped>
  button {
    transition: all 0.2s;
  }
  button:hover {
    color: black;
  }
  .grid-cols-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  </style>
  