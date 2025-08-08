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


        <div class="col-6">
            <label class="form-label fs-sm">Persona</label>
            <v-select id="persona" class=" mb-3 " v-model="filters.persona" :options="catalogos.personas"  label="full_name" placeholder="Usuario" :multiple="true" :reduce="persona => persona.IDPersona" @update:modelValue="updatePersona"></v-select>
        </div>


      </div>

      <!-- Gráficas -->
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="border border-gray-300 rounded p-4 shadow">
            <BaseDonutChart
                ref="donutChart"
                apiUrl="/api/v1/tickets-distribucion"
                chartTitle="Distribución de Tickets por Persona"
                v-bind="filters"
            />
        </div>

        <div class="border border-gray-300 rounded p-4 shadow">
            <BaseHorizontalStackedBar
            ref="TicketsPersonaEstado"
            apiUrl="/api/v1/tickets-by-persona"
            chartTitle="Tickets por persona"
            v-bind="filters"
            id="TicketsPersonaResponsable"
            />
        </div>


    </div>
      <div class="grid grid-cols-2 gap-4 mb-4">


        <div class="border border-gray-300 rounded p-4 shadow">
            <BaseHorizontalStackedBar
            ref="incidencias"
            apiUrl="/api/v1/incidencias-por-persona"
            chartTitle="Incidencias por persona y nivel de cumplimiento"
            v-bind="filters"
            id="IncidenciasPersonaNivel"
            />
        </div>

        <div class="border border-gray-300 rounded p-4 shadow">
        <BaseHorizontalBar
          ref="rechazo"
          apiUrl="/api/v1/rechazo-por-persona"
          chartTitle="Rechazo por persona"
          v-bind="filters"

        />
      </div>
    </div>

    </div>
  </template>

  <script>
    import BaseHorizontalStackedBar from '../BaseHorizontalStackedBar.vue';
    import BaseDonutChart from '../BaseDonutChart.vue';
    import axios from 'axios';
    import { useStoreStore } from '../js/Store';
    import BaseHorizontalBar from '../BaseHorizontalBar.vue';

  export default {
    name: 'SeccionPersonas',
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
          persona: '',
          startDate: this.startDate,
          endDate: this.endDate
        },
        catalogos: {

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

    mounted() {
      this.getPersonas();
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
            this.filters.persona = ''
            this.store.obtenerEdificios()
            this.getPersonas()
        },

        cambiarEdificio() {

            if(!this.filters.edificio){
                this.filters.edificio = '';
                this.store.filters.edificio = ''
                this.filters.persona = ''
            }else{
                this.store.filters.edificio = this.filters.edificio
            }


            this.filters.nivel = ''
            this.filters.zona = ''
            this.filters.persona = ''
            this.store.obtenerNiveles()
            this.getPersonas()
        },

        cambiarNivel() {

            if(!this.filters.nivel){
                this.filters.nivel = '';
                this.store.filters.nivel = ''
            }else{
                this.store.filters.nivel = this.filters.nivel
            }


            this.filters.persona = ''
            this.filters.zona = ''
            this.store.obtenerZonas()
            this.getPersonas()
        },

        updateZona() {

            if(!this.filters.zona){
                this.filters.zona = '';
                this.store.filters.zona = ''
            }else{
                this.store.filters.zona = this.filters.zona
            }

            this.filters.persona = ''
              this.getPersonas()
        },





        updatePersona() {

            if(!this.filters.persona){
                this.filters.persona = '';
                this.store.filters.persona = ''
            }else{
                this.store.filters.persona = this.filters.persona
            }

            this.getPersonas()


        },

        async getPersonas(){

            try {
                const { data: { data } } = await axios.get(route('graficas.getPersonasByPredio'), {
                    params: {
                        ...this.filters,
                    }
                });
                this.catalogos.personas = data;


            } catch (error) {

                console.error('Error al obtener los predios:', error);

            }
        },

        aplicarFiltros() {
            this.$refs.donutChart?.fetchChartData?.();
            this.$refs.incidencias?.fetchChartData?.();
            this.$refs.rechazo?.fetchChartData?.();
            this.$refs.categoria?.fetchChartData?.();
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
