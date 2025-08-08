<template>
    <div class="space-y-6">
      <!-- Filtros de catálogo -->
      <div class="row">

        <div class="col-6">
            <label class="form-label fs-sm">Predio</label>
            <v-select id="predio" class=" mb-3 w-100" v-model="filters.predio"   :options="store.catalogos.predios"  label="NombrePredio" placeholder="Predio" :reduce="predio => predio.IDPredio" :multiple="true" @update:modelValue="cambiarPredio"  >
            </v-select>
        </div>
        <div class="col-6">
            <label class="form-label fs-sm">Estado</label>
            <v-select v-model="filters.estado" :options="store.estadosInstancia" label="NombreEstado" placeholder="Estado" :reduce="estado => estado.IDEstado" :multiple="true" @update:modelValue="cambiarEstado"/>
        </div>


      </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="border border-gray-300 rounded p-4 shadow">
                    <TicketsChart :start-date="startDate" :end-date="endDate" v-bind="filters" ref="TicketsChart" id="TicketsChart"/>
                </div>
                <div class="border border-gray-300 rounded p-4 shadow">
                    <IncidentsChart :start-date="startDate" :end-date="endDate" v-bind="filters" ref="IncidentsChart" id="IncidentsChart"/>
                </div>
            </div>
            <div class="mb-4 border border-gray-300 rounded p-4 shadow">
                <RejectionChart :start-date="startDate" :end-date="endDate" v-bind="filters" ref="RejectionChart" id="RejectionChart"/>
            </div>

</div>

  </template>

  <script>
    import TicketsChart from './TicketsChart.vue'
    import IncidentsChart from './IncidentsChart.vue'
    import RejectionChart from './RejectionChart.vue'

import { useStoreStore } from '../js/Store';

  export default {
    name: 'SeccionLocation',
    components: {

        TicketsChart,
        IncidentsChart,
        RejectionChart,
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
          estado: '',
          startDate: this.startDate,
          endDate: this.endDate
        },

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

        },


        cambiarEstado() {

            if(!this.filters.estado){
                this.filters.estado = '';
                this.store.filters.estado = ''
            }else{
                this.store.filters.estado = this.filters.estado
            }

        },




        async obtenerEdificios(){
            try {
                this.loadingE = true

                const { data: { data } } = await axios.get(route('edificios.index'), {
                    params: {
                        IDPredio: this.filters.predio
                    }
                })
                this.catalogos.edificios = data;

                this.loadingE = false
            } catch (error) {
                this.loadingE = false
                console.error('Error al obtener los edificios:', error);
            }
        },

        async obtenerNiveles(){
            try {

                this.loadingN = true

                const { data: { data } } = await axios.get(route('niveles.index'), {
                    params: {
                        IDEdificio: this.filters.edificio
                    }
                })
                this.catalogos.niveles = data;
                this.loadingN = false
            } catch (error) {
                this.loadingN = false
            }
        },

        async obtenerZonas(){

            try {

                this.loadingZ = true

                const { data: { data } } = await axios.get(route('zonas.index'), {
                    params: {
                        IDNivel: this.filters.nivel
                    }
                })
                this.catalogos.zonas = data
                this.loadingZ = false
            } catch (error) {
                this.loadingZ = true
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
