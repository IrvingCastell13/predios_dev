<template>
    <div class="space-y-6">

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


      <!-- Gráficas -->
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="border border-gray-300 rounded p-4 shadow">
          <BaseDonutChart
            ref="donut"
            apiUrl="/api/v1/tickets-por-categoria"
            chartTitle="Tickets por Categoría"
            v-bind="filters"
          />
        </div>
        <div class="border border-gray-300 rounded p-4 shadow">
          <BaseHorizontalStackedBar
            ref="tickets"
            apiUrl="/api/v1/tickets-categoria-detalle"
            chartTitle="Tickets por Categoría"
            v-bind="filters"
            id="TicketsCategoriaDetalle"
          />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div class="border border-gray-300 rounded p-4 shadow">
          <BaseHorizontalStackedBar
            ref="incidencias"
            apiUrl="/api/v1/incidencias-por-categoria"
            chartTitle="Incidencias por Categoría y nivel de cumplimiento"
            v-bind="filters"
            id="IncidenciasCategoria"
          />
        </div>

        <div class="border border-gray-300 rounded p-4 shadow">
          <BaseHorizontalBar
            ref="rechazo"
            apiUrl="/api/v1/rechazo-por-categoria"
            chartTitle="Rechazo por Categoría"
            v-bind="filters"
          />
        </div>
      </div>
    </div>
  </template>

  <script>
  import BaseHorizontalBar from '../BaseHorizontalBar.vue';
  import BaseHorizontalStackedBar from '../BaseHorizontalStackedBar.vue';
  import BaseDonutChart from '../BaseDonutChart.vue';
  import axios from 'axios';
import { useStoreStore } from '../js/Store';

  export default {
    name: 'SeccionCategorias',
    components: {
      BaseHorizontalStackedBar,
      BaseDonutChart,
      BaseHorizontalBar
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
          startDate: this.startDate,
          endDate: this.endDate
        },
        catalogos: {
          predios: [],
          edificios: [],
          niveles: [],
          zonas: []
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
        this.$refs.donut?.fetchChartData?.();
        this.$refs.tickets?.fetchChartData?.();
        this.$refs.incidencias?.fetchChartData?.();
        this.$refs.rechazo?.fetchChartData?.();
      }
    }
  };
  </script>
