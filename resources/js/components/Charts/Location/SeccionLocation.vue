<template>
    <div class="space-y-6">
      <!-- Filtros de catálogo -->
      <div class="row">

        <div class="col-6">
            <label class="form-label fs-sm">Predio</label>
            <v-select id="predio" class=" mb-3 w-100" v-model="filters.predio"  :multiple="true" :options="store.catalogos.predios"  label="NombrePredio" placeholder="Predio" :reduce="predio => predio.IDPredio" @update:modelValue="cambiarPredio"  >
            </v-select>
        </div>

        <div class="col-6">
            <label class="form-label fs-sm">Edificio</label>
            <v-select id="edificio" class=" mb-3 " v-model="filters.edificio":multiple="true" :options="store.catalogos.edificios"  label="NombreEdificio" placeholder="Edificio" :reduce="edificio => edificio.IDEdificio" @update:modelValue="cambiarEdificio"  ></v-select>
        </div>

        <div class="col-6">
            <label class="form-label fs-sm">Nivel</label>
            <v-select id="nivel" class=" mb-3 " v-model="filters.nivel" :multiple="true" :options="store.catalogos.niveles"  label="NombreNivel" placeholder="Nivel" :reduce="nivel => nivel.IDNivel" @update:modelValue="cambiarNivel" ></v-select>
        </div>

        <div class="col-6">
            <label class="form-label fs-sm">Zona</label>
            <v-select id="zona" class=" mb-3 " v-model="filters.zona" :multiple="true" :options="store.catalogos.zonas"  label="NombreZona" placeholder="Zona" :reduce="zona => zona.IDZona" @update:modelValue="updateZona"  ></v-select>
        </div>


      </div>


        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="border border-gray-300 rounded p-4 shadow col-span-2">
                <SummaryTickets :start-date="startDate" :end-date="endDate"  v-bind="filters" ref="SummaryTickets"/>
            </div>
            <div class="border border-gray-300 rounded p-4 shadow">
                <TicketsByBuilding :start-date="startDate" :end-date="endDate"  v-bind="filters" ref="TicketsByBuilding" id="UbicacionTicketsByBuilding"/>
            </div>
            <div class="border border-gray-300 rounded p-4 shadow">
                <IncidentsByBuilding :start-date="startDate" :end-date="endDate"  v-bind="filters" ref="IncidentsByBuilding" id="UbicacionIncidentsByBuilding"/>
            </div>
            <div class="border border-gray-300 rounded p-4 shadow">
                <TicketsByLevel :start-date="startDate" :end-date="endDate"  v-bind="filters" ref="TicketsByLevel" id="UbicacionTicketsByLevel"/>
            </div>
            <div class="border border-gray-300 rounded p-4 shadow">
                <IncidentsByLevel :start-date="startDate" :end-date="endDate"  v-bind="filters" ref="IncidentsByLevel" id="UbicacionIncidentsByLevel"/>
            </div>
            <div class="border border-gray-300 rounded p-4 shadow">
                <TicketsByZone :start-date="startDate" :end-date="endDate"  v-bind="filters" ref="TicketsByZone" id="UbicacionTicketsByZone"/>
            </div>
            <div class="border border-gray-300 rounded p-4 shadow">
                <IncidentsByZone :start-date="startDate" :end-date="endDate"  v-bind="filters" ref="IncidentsByZone" id="UbicacionIncidentsByZone"/>
            </div>
        </div>
  >
    </div>
  </template>

  <script>
    import BaseHorizontalStackedBar from '../BaseHorizontalStackedBar.vue';
    import BaseDonutChart from '../BaseDonutChart.vue';
    import axios from 'axios';
    import SummaryTickets from './SummaryTickets.vue';
    import TicketsByBuilding from './TicketsByBuilding.vue';
    import IncidentsByBuilding from './IncidentsByBuilding.vue';
    import TicketsByLevel from './TicketsByLevel.vue';
    import IncidentsByLevel from './IncidentsByLevel.vue';
    import TicketsByZone from './TicketsByZone.vue';
    import IncidentsByZone from './IncidentsByZone.vue';
import { useStoreStore } from '../js/Store';

  export default {
    name: 'SeccionLocation',
    components: {
        BaseHorizontalStackedBar,
        BaseDonutChart,
        SummaryTickets,
        TicketsByBuilding,
        IncidentsByBuilding,
        TicketsByLevel,
        IncidentsByLevel,
        TicketsByZone,
        IncidentsByZone,
    },
    props: {
      startDate: { type: String, required: true },
      endDate: { type: String, required: true }
    },
    data() {
      return {
        store: useStoreStore(),


        filters: {
          predio: '',
          edificio: '',
          nivel: '',
          zona: '',
          persona: '',
          startDate: this.startDate,
          endDate: this.endDate
        },
        catalogos: {
          predios: [],
          edificios: [],
          niveles: [],
          zonas: [],
          personas: []
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


    methods: {

        cambiarPredio() {

            if(!this.filters.predio){
                this.filters.predio = '';
                this.store.filters.predio = ''
            }else{
                this.store.filters.predio = this.filters.predio
            }


            this.filters.edificio = ''
            this.filters.nivel = ''
            this.filters.zona = ''
            this.store.obtenerEdificios()
        },

        cambiarEdificio() {

            if(!this.filters.edificio){
                this.filters.edificio = '';
                this.store.filters.edificio = ''
            }else{
                this.store.filters.edificio = this.filters.edificio
            }


            this.filters.nivel = ''
            this.filters.zona = ''
            this.store.obtenerNiveles()
        },

        cambiarNivel() {

            if(!this.filters.nivel){
                this.filters.nivel = '';
                this.store.filters.nivel = ''
            }else{
                this.store.filters.nivel = this.filters.nivel
            }


            this.filters.zona = ''
            this.store.obtenerZonas()
        },

        updateZona() {

            if(!this.filters.zona){
                this.filters.zona = '';
                this.store.filters.zona = ''
            }else{
                this.store.filters.zona = this.filters.zona
            }

        },

        aplicarFiltros() {

            this.$refs.SummaryTickets?.fetchChartData?.();
            this.$refs.TicketsByBuilding?.fetchChartData?.();
            this.$refs.IncidentsByBuilding?.fetchChartData?.();
            this.$refs.TicketsByLevel?.fetchChartData?.();
            this.$refs.IncidentsByLevel?.fetchChartData?.();
            this.$refs.TicketsByZone?.fetchChartData?.();
            this.$refs.IncidentsByZone?.fetchChartData?.();
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

    .vs__clear {
  cursor: pointer !important;
  pointer-events: auto !important;
  z-index: 10 !important;
}

</style>
