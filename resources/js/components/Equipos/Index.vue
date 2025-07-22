<template>

        <AppLayout clases="d-flex flex-fill">

             <div
                    class="white-box d-flex flex-column p-20 pb-0 border-bottom-left-radius-0 border-bottom-right-radius-0 w-full">

                    <div class="dashboard-page-header">
                        <div class="row align-items-center g-3">
                            <div class="col-md-3">
                                <h5 class="fw-semibold primary">Equipos</h5>
                                <div class="d-flex align-items-center gap-5px mt-1">
                                    <p class="fs-sm fw-semibold text-color-4">Ver como</p>
                                    <span class="fs-sm fw-semibold success d-flex align-items-center gap-5px">Tabla <img src="/images/angle-down.svg" alt="" class="w-10px"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row flex-nowrap g-2" style="display: flex;">
                                    <div class="flex-fill" style="min-width: 0;">
                                        <label for=""></label>
                                        <input type="text" class="form-control search" v-model="search" placeholder="Buscar">
                                    </div>
                                    <div class="flex-fill" style="min-width: 0;">
                                        <label for=""></label>
                                        <v-select class=" mb-3" v-model="store.filters.IDPredio" :options="store.prediosCrear"  label="NombrePredio" placeholder="Predio" :reduce="predio => predio.IDPredio" @update:modelValue="cambiarPredio"></v-select>
                                    </div>
                                    <div class="flex-fill" style="min-width: 0;">
                                        <label for=""></label>
                                        <v-select placeholder="Sistema" style="height: 60px !important;"  v-model="store.filters.IDSistema" :options="store.sistemas"  label="NombreSistema"   :reduce="sistema => sistema.IDSistema"  @update:modelValue="store.obtenerSubsistemasIndex"></v-select>
                                    </div>
                                    <div class="flex-fill" style="min-width: 0;">
                                        <label for=""></label>
                                       <v-select placeholder="Subsistema"  class=" " style="height: 60px !important;"  v-model="store.filters.IDSubsistema" :options="store.subsistemasIndex"  label="NombreSubsistema"   :reduce="subsistema => subsistema.IDSubsistema"></v-select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex justify-content-end align-content-end">
                                    <div class="mx-1" >
                                        <svg class="cursor-pointer" @click="abrirModalEquiposCodigoBarras" width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.5" y="0.5" width="47" height="47" rx="5.5" fill="none"/>
                                            <rect x="0.5" y="0.5" width="47" height="47" rx="5.5" stroke="#0F6491"/>
                                            <mask id="mask0_3892_86544" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="12" y="12" width="24" height="24">
                                            <rect x="12" y="12" width="24" height="24" fill="#D9D9D9"/>
                                            </mask>
                                            <g mask="url(#mask0_3892_86544)">
                                            <path d="M18 32C18 32.2833 17.9042 32.5208 17.7125 32.7125C17.5208 32.9042 17.2833 33 17 33H14C13.7167 33 13.4792 32.9042 13.2875 32.7125C13.0958 32.5208 13 32.2833 13 32V29C13 28.7167 13.0958 28.4792 13.2875 28.2875C13.4792 28.0958 13.7167 28 14 28C14.2833 28 14.5208 28.0958 14.7125 28.2875C14.9042 28.4792 15 28.7167 15 29V31H17C17.2833 31 17.5208 31.0958 17.7125 31.2875C17.9042 31.4792 18 31.7167 18 32ZM34 28C34.2833 28 34.5208 28.0958 34.7125 28.2875C34.9042 28.4792 35 28.7167 35 29V32C35 32.2833 34.9042 32.5208 34.7125 32.7125C34.5208 32.9042 34.2833 33 34 33H31C30.7167 33 30.4792 32.9042 30.2875 32.7125C30.0958 32.5208 30 32.2833 30 32C30 31.7167 30.0958 31.4792 30.2875 31.2875C30.4792 31.0958 30.7167 31 31 31H33V29C33 28.7167 33.0958 28.4792 33.2875 28.2875C33.4792 28.0958 33.7167 28 34 28ZM16.5 30C16.3667 30 16.25 29.95 16.15 29.85C16.05 29.75 16 29.6333 16 29.5V18.5C16 18.3667 16.05 18.25 16.15 18.15C16.25 18.05 16.3667 18 16.5 18H17.5C17.6333 18 17.75 18.05 17.85 18.15C17.95 18.25 18 18.3667 18 18.5V29.5C18 29.6333 17.95 29.75 17.85 29.85C17.75 29.95 17.6333 30 17.5 30H16.5ZM19.5 30C19.3667 30 19.25 29.95 19.15 29.85C19.05 29.75 19 29.6333 19 29.5V18.5C19 18.3667 19.05 18.25 19.15 18.15C19.25 18.05 19.3667 18 19.5 18C19.6333 18 19.75 18.05 19.85 18.15C19.95 18.25 20 18.3667 20 18.5V29.5C20 29.6333 19.95 29.75 19.85 29.85C19.75 29.95 19.6333 30 19.5 30ZM22.5 30C22.3667 30 22.25 29.95 22.15 29.85C22.05 29.75 22 29.6333 22 29.5V18.5C22 18.3667 22.05 18.25 22.15 18.15C22.25 18.05 22.3667 18 22.5 18H23.5C23.6333 18 23.75 18.05 23.85 18.15C23.95 18.25 24 18.3667 24 18.5V29.5C24 29.6333 23.95 29.75 23.85 29.85C23.75 29.95 23.6333 30 23.5 30H22.5ZM25.5 30C25.3667 30 25.25 29.95 25.15 29.85C25.05 29.75 25 29.6333 25 29.5V18.5C25 18.3667 25.05 18.25 25.15 18.15C25.25 18.05 25.3667 18 25.5 18H27.5C27.6333 18 27.75 18.05 27.85 18.15C27.95 18.25 28 18.3667 28 18.5V29.5C28 29.6333 27.95 29.75 27.85 29.85C27.75 29.95 27.6333 30 27.5 30H25.5ZM29.5 30C29.3667 30 29.25 29.95 29.15 29.85C29.05 29.75 29 29.6333 29 29.5V18.5C29 18.3667 29.05 18.25 29.15 18.15C29.25 18.05 29.3667 18 29.5 18C29.6333 18 29.75 18.05 29.85 18.15C29.95 18.25 30 18.3667 30 18.5V29.5C30 29.6333 29.95 29.75 29.85 29.85C29.75 29.95 29.6333 30 29.5 30ZM31.5 30C31.3667 30 31.25 29.95 31.15 29.85C31.05 29.75 31 29.6333 31 29.5V18.5C31 18.3667 31.05 18.25 31.15 18.15C31.25 18.05 31.3667 18 31.5 18C31.6333 18 31.75 18.05 31.85 18.15C31.95 18.25 32 18.3667 32 18.5V29.5C32 29.6333 31.95 29.75 31.85 29.85C31.75 29.95 31.6333 30 31.5 30ZM18 16C18 16.2833 17.9042 16.5208 17.7125 16.7125C17.5208 16.9042 17.2833 17 17 17H15V19C15 19.2833 14.9042 19.5208 14.7125 19.7125C14.5208 19.9042 14.2833 20 14 20C13.7167 20 13.4792 19.9042 13.2875 19.7125C13.0958 19.5208 13 19.2833 13 19V16C13 15.7167 13.0958 15.4792 13.2875 15.2875C13.4792 15.0958 13.7167 15 14 15H17C17.2833 15 17.5208 15.0958 17.7125 15.2875C17.9042 15.4792 18 15.7167 18 16ZM30 16C30 15.7167 30.0958 15.4792 30.2875 15.2875C30.4792 15.0958 30.7167 15 31 15H34C34.2833 15 34.5208 15.0958 34.7125 15.2875C34.9042 15.4792 35 15.7167 35 16V19C35 19.2833 34.9042 19.5208 34.7125 19.7125C34.5208 19.9042 34.2833 20 34 20C33.7167 20 33.4792 19.9042 33.2875 19.7125C33.0958 19.5208 33 19.2833 33 19V17H31C30.7167 17 30.4792 16.9042 30.2875 16.7125C30.0958 16.5208 30 16.2833 30 16Z" fill="#0F6491"/>
                                            </g>
                                        </svg>

                                    </div>
                                    <div class="mx-1">

                                        <button v-if="showFilters" class="page-header-icon page-header-icon-primary cursor-pointer active" @click="showFilters = !showFilters">
                                            <svg width="18" height="19" viewBox="0 0 18 19" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M1 1H17V3.172C16.9999 3.70239 16.7891 4.21101 16.414 4.586L12 9V16L6 18V9.5L1.52 4.572C1.18545 4.20393 1.00005 3.7244 1 3.227V1Z"
                                                    stroke="#0F6491" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>

                                        </button>

                                        <button v-else class="page-header-icon page-header-icon-primary" @click="showFilters = !showFilters">
                                            <svg width="18" height="19" viewBox="0 0 18 19" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M1 1H17V3.172C16.9999 3.70239 16.7891 4.21101 16.414 4.586L12 9V16L6 18V9.5L1.52 4.572C1.18545 4.20393 1.00005 3.7244 1 3.227V1Z"
                                                    stroke="#0F6491" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="mx-1">
                                        <div class="dropdown">
                                            <button @click="crearNuevoEquipo" class="page-header-icon page-header-icon-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 1V15M1 8H15" stroke="white" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- //End Dashboard Page Header -->


                      <div v-if="showFilters" class="mt-2">
                            <div class="row flex-nowrap g-2" style="display: flex;">

                                <div class="flex-fill" style="min-width: 0;">
                                        <label class="form-label fs-sm">Estado</label>
                                        <v-select v-model="store.filters.estados" :options="store.estadosInstancia" label="NombreEstado" placeholder="estados" :reduce="estado => estado.IDEstado" :multiple="true" @update:modelValue="aplicarFiltros"
                                        />
                                </div>

                                <div class="flex-fill" style="min-width: 0;">
                                    <label class="form-label fs-sm">Edificio</label>
                                    <v-select class=" mb-3 " v-model="store.filters.IDEdificio" :options="store.edificiosIndex"  label="NombreEdificio" placeholder="Edificio" :reduce="edificio => edificio.IDEdificio" @update:modelValue="cambiarEdificio"></v-select>
                                </div>
                                <div class="flex-fill" style="min-width: 0;">
                                    <label class="form-label fs-sm">Nivel</label>
                                    <v-select class=" mb-3 " v-model="store.filters.IDNivel" :options="store.nivelesIndex"  label="NombreNivel" placeholder="Nivel" :reduce="nivel => nivel.IDNivel" @update:modelValue="cambiarNivel"></v-select>
                                </div>
                                <div class="flex-fill" style="min-width: 0;">
                                    <label class="form-label fs-sm">Zona</label>
                                    <v-select class=" mb-3 " v-model="store.filters.IDZona" :options="store.zonasIndex"  label="NombreZona" placeholder="Zona" :reduce="zona => zona.IDZona" @update:modelValue="aplicarFiltros"></v-select>
                                </div>

                                 <div class="flex-fill" style="min-width: 0;">
                                        <label class="form-label fs-sm">Plan asociado </label>
                                        <v-select v-model="store.filters.estados" :options="store.estadosInstancia" label="NombreEstado" placeholder="Plan" :reduce="estado => estado.IDEstado" :multiple="true" @update:modelValue="aplicarFiltros"
                                        />
                                </div>

                            </div>

                        </div>

                    <div class="dashboard-page-content overflow-auto">
                        <div>
                            <table class="table" id="equipos">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="table-header-count">{{ store.totalEquipos }}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div>Sistema/ Subsistema/Tipo</div>
                                        </th>
                                        <th>
                                            <div>Clave / Nombre / Nro. Serie</div>
                                        </th>
                                        <th>
                                            <div>Marca
                                            </div>
                                        </th>
                                        <th>
                                            <div>Estado</div>
                                        </th>
                                        <th>
                                            <div>Próximo mantenimiento</div>
                                        </th>
                                        <th>
                                            <div>Ubicación</div>
                                        </th>
                                        <th>
                                            <div>Planes mantenimiento asociados</div>
                                        </th>
                                       <th class="acciones-columna"></th>

                                    </tr>
                                </thead>

                            </table>
                        </div>
                    </div>
                    <!-- //End Dashboard Page Content -->

                </div>


                <CrearEquipo/>
                <EquiposCodigosBarras/>
                <Detalle/>
        </AppLayout>

  </template>

<script setup>

    import { onMounted, watch } from 'vue';
    import AppLayout from '../../Layout/App.vue'
    import { usestoreStore } from './js/store';
    import CrearEquipo from './modales/CrearEquipo.vue';
    import { abrirModal } from '../../Utilities';
    import EquiposCodigosBarras from './modales/EquiposCodigosBarras.vue';
    import { usecodigoBarrasStore } from './js/codigoBarras';
    import { ref } from 'vue';
    import Detalle from './modales/Detalle/Detalle.vue';

    const store = usestoreStore()

    const equiposStore = usecodigoBarrasStore()
    const search = ref(store.filters.search);
    const showFilters = ref(true);

    watch(search, (newVal) => {


        if (timeout) clearTimeout(timeout);

        timeout = setTimeout(() => {
            store.filters.search = newVal; // sincroniza con el store
            // store.obtenerDatatables();
        }, 500); // espera 500ms sin escribir
    });

    let timeout = null;
    const crearNuevoEquipo = () => {
        abrirModal('modalCreatEquipo')
        store.IsEditing = false
        store.limpiarFormulario()

        store.obtenerTipos()
        // store.getPrediosCrear()
    }

    const abrirModalEquiposCodigoBarras = async () => {

            await equiposStore.obtenerPredios()
            equiposStore.obtenerEquipos()
            equiposStore.getSistemas()
            equiposStore.getTiposEquipos()
            equiposStore.equiposAgregados = []

        abrirModal('EquiposCodigosBarras')

    }

    const cambiarPredio = () => {
        store.filters.IDEdificio = ''
        store.filters.IDNivel = ''
        store.filters.IDZona = ''
        store.obtenerEdificiosIndex()
    }

    const cambiarEdificio = () => {
        store.filters.IDNivel = ''
        store.filters.IDZona = ''
        store.obtenerNivelesIndex()
    }

    const cambiarNivel = () => {
        store.filters.IDZona = ''
        store.obtenerZonasIndex()
    }


    onMounted(() => {
        store.getPrediosCrear()
        store.obtenerSistemas()
        store.obtenerDatatables()
    })




</script>

<style  scoped>


  .acciones-columna,
td.acciones-columna {
    min-width: 50px;
    text-align: center;
}
</style>
