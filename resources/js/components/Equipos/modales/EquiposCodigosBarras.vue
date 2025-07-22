<template>
    <Modal modalId="EquiposCodigosBarras" customClass="modal-lg" clasesBody="overflow-y-auto">
        <template #title>
            Imprimir etiqueta de equipo(s)
        </template>
        <div class="row g-3 flex-fill">

            <div class="col-md d-flex">
                <div class="border p-2 border-radius-6 flex-fill">
                    <div>
                        <div class="row g-2">
                            <div class="col-xxl">
                             <input type="text" class="form-control form-control-height search"
                                    placeholder="Buscar" v-model="search">
                            </div>
                            <div class="col-xxl">
                                <v-select class=" mb-3" v-model="equiposStore.filtros.IDPredio" :options="equiposStore.predios"  label="NombrePredio" placeholder="Predio" :reduce="predio => predio.IDPredio" @update:modelValue="cambiarPredio"></v-select>
                            </div>
                            <div class="col-auto">
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

                        </div>
                    </div>
                    <div v-if="showFilters" class="mt-2">
                        <div class="row g-2">
                            <div class="col-xxl-6 col-xl-6">
                                <v-select class=" mb-3 " v-model="equiposStore.filtros.IDEdificio" :options="equiposStore.edificios"  label="NombreEdificio" placeholder="Edificio" :reduce="edificio => edificio.IDEdificio" @update:modelValue="equiposStore.obtenerNiveles"></v-select>
                            </div>
                            <div class="col-xxl-6 col-xl-6">
                                <v-select class=" mb-3 " v-model="equiposStore.filtros.IDNivel" :options="equiposStore.niveles"  label="NombreNivel" placeholder="Nivel" :reduce="nivel => nivel.IDNivel" @update:modelValue="equiposStore.obtenerZonas"></v-select>
                            </div>
                            <div class="col-xxl-6 col-xl-6">
                                <v-select class=" mb-3 " v-model="equiposStore.filtros.IDZona" :options="equiposStore.zonas"  label="NombreZona" placeholder="Zona" :reduce="zona => zona.IDZona" @update:modelValue="equiposStore.obtenerContratos"></v-select>
                            </div>

                             <div class="col-xxl-4 col-xl-6">
                                <v-select placeholder="Sistema"  class=" " style="height: 60px !important;"  v-model="equiposStore.filtros.IDSistema" :options="equiposStore.sistemas"   label="label" :reduce="sis => sis.IDSistema"  @update:modelValue="cambiarSistema">
                                </v-select>
                            </div>
                            <div class="col-xxl-4 col-xl-6">
                                <v-select placeholder="Subsistema"  class=" " style="height: 60px !important;"  v-model="equiposStore.filtros.IDSubsistema" :options="equiposStore.subsistemas"  label="label"   :reduce="sub => sub.IDSubsistema" @update:modelValue="equiposStore.obtenerEquipos"></v-select>
                            </div>
                            <div class="col-xxl-4 col-xl-6">
                                <v-select placeholder="Tipo Equipo"  class=" " style="height: 60px !important;"  v-model="equiposStore.filtros.IDTipoEquipo" :options="equiposStore.tiposEquipos"  label="label"   :reduce="tipo => tipo.IDTipoEquipo" @update:modelValue="equiposStore.obtenerEquipos"></v-select >
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="checkbox">
                            <input type="checkbox" id="selectAll" @change="toggleSeleccionarTodos">
                            <label for="selectAll" class="primary fw-medium">Seleccionar
                                todos</label>
                        </div>
                    </div>

                    <ul class="mt-3 select-team">

                        <li v-for="equipo in equiposStore.equipos">

                            <div class="checkbox">
                                <input
                                type="checkbox"
                                :id="'checkbox-' + equipo.IDEquipo"
                                @change="toggleEquipo(equipo, $event)"
                                :checked="isSelected(equipo.IDEquipo)"
                                >
                                <label :for="'checkbox-' + equipo.IDEquipo" class="d-block">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-xl">
                                            <p class="fw-semibold primary">{{ equipo.DescripcionEquipo }}</p>
                                            <p class="fs-xs mt-1 text-color-4 select-team-item">
                                                {{ equipo.tipo.NombreTipoEquipo }} - {{ equipo.tipo.MarcaTipoEquipo }} - {{ equipo.tipo.ModeloTipoEquipo }}
                                            </p>
                                            <p class="fs-xs mt-1 text-color-4 select-team-item">
                                                Vencimiento:Pendiente ver que fecha es</p>
                                        </div>
                                        <div class="col-xl-auto">
                                            <p class="fs-xs">{{ equipo.ubicacion ?? '' }}</p>

                                        </div>
                                        <div class="col-xl-auto">
                                            <img src="images/info-icon-primary.svg" alt="">
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </li>


                    </ul>
                </div>
            </div>
              <div class="col-md-auto d-flex">
                <div class="select-team-sidebar flex-fill d-flex flex-column">
                    <p class="fw-bold primary">Seleccionados: {{ equiposStore.equiposAgregados.length }}</p>
                    <div class="mt-2">
                        <div class="row g-2">
                            <div class="col">
                                <input type="text"
                                    class="form-control form-control-height search right"
                                    placeholder="Buscar" v-model="equiposStore.searchSeleccionados">
                            </div>

                        </div>
                    </div>
                    <div class="mt-2 flex-fill">
                        <ul class="search-module-list">

                            <li v-for="equipo in equiposStore.equiposAgregadosFiltrados">
                                <p class="flex-fill">{{ equipo.DescripcionEquipo }}</p>
                                <span>
                                    <svg @click="mostrarPreviewequipo(equipo)"  width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10 7H10.01M9 10H10V14H11M1 10C1 11.1819 1.23279 12.3522 1.68508 13.4442C2.13738 14.5361 2.80031 15.5282 3.63604 16.364C4.47177 17.1997 5.46392 17.8626 6.55585 18.3149C7.64778 18.7672 8.8181 19 10 19C11.1819 19 12.3522 18.7672 13.4442 18.3149C14.5361 17.8626 15.5282 17.1997 16.364 16.364C17.1997 15.5282 17.8626 14.5361 18.3149 13.4442C18.7672 12.3522 19 11.1819 19 10C19 7.61305 18.0518 5.32387 16.364 3.63604C14.6761 1.94821 12.3869 1 10 1C7.61305 1 5.32387 1.94821 3.63604 3.63604C1.94821 5.32387 1 7.61305 1 10Z"
                                            stroke="#4C6272" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                    </svg>
                                </span>
                                <button @click="eliminarEquipo(equipo)">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13 1L1 13M1 1L13 13" stroke="#BA4A4A" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </li>


                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <template #footer>
            <div class="mt-2 d-flex justify-content-end">
                <ButtonLoading text="Descargar PDF" clases="btn btn-outline-primary" id="download" @click="generarPDF"/>
            </div>
        </template>

         <Modal modalId="modalPreview" >
            <template #title>
                Vista Previa
            </template>
            <div id="fileContainer"></div>
       </Modal>



    </Modal>
</template>


<script setup>
import { ref, watch } from 'vue';
import Modal from '../../../components/Components/Modal.vue';
import { usecodigoBarrasStore } from '../js/codigoBarras';
import { useLoadingStore } from '../../../stores/loading';
import ButtonLoading from '../../../components/Components/Bootstrap/ButtonLoading.vue';
import { abrirModal, cerrarModal } from '../../../Utilities';


const equiposStore = usecodigoBarrasStore()
const showFilters = ref(true);

const search = ref('');
const loadingStore = useLoadingStore();
watch(search, (newVal) => {
    equiposStore.filtros.search = newVal; // sincroniza con el store

    if (timeout) clearTimeout(timeout);

    timeout = setTimeout(() => {
        equiposStore.obtenerEquipos();

    }, 500); // espera 500ms sin escribir
})
let timeout = null;

const cambiarPredio = async () => {

    equiposStore.filtros.IDEdificio = ''
    equiposStore.filtros.IDNivel = ''
    equiposStore.filtros.IDZona = ''
    equiposStore.filtros.IDContrato = ''
    equiposStore.edificios = []
    equiposStore.niveles = []
    equiposStore.zonas = []

    await equiposStore.obtenerEdificios()
    equiposStore.obtenerEquipos()

}


const toggleEquipo = (equipo, event) => {

        const index =  equiposStore.equiposAgregados.findIndex(d => d.IDEquipo === equipo.IDEquipo);
        if (event.target.checked) {
            if (index === -1) {
                    equiposStore.equiposAgregados.push(equipo);
            }
        } else {
            if (index !== -1) {
                    equiposStore.equiposAgregados.splice(index, 1);
            }
        }

}


const toggleSeleccionarTodos = (event) => {
    if (event.target.checked) {
        equiposStore.equiposAgregados = [...equiposStore.equipos];
    } else {
        equiposStore.equiposAgregados = [];
    }
}

const isSelected = (id) => {
    return equiposStore.equiposAgregados.some(d => d.IDEquipo === id);
}



const mostrarPreviewequipo = equipo => {


        let fileContainer = document.getElementById("fileContainer");


        fileContainer.innerHTML = `<img src="${equipo.image}" class="img-fluid" alt="Imagen">`;


    abrirModal('modalPreview')

}

const eliminarEquipo = (equipo) => {
    const index = equiposStore.equiposAgregados.findIndex(e => e.IDEquipo === equipo.IDEquipo);
    if (index > -1) {
        equiposStore.equiposAgregados.splice(index, 1);
    }

    $(`#equipo-${equipo.IDEquipo}`).attr('checked',false)

}


const generarPDF = async () => {

    try {

        if(loadingStore.buttonLoading['download'] ) return

        loadingStore.startButtonLoading('download')
        const IDEquipos = []
        equiposStore.equiposAgregados.forEach(equipo => {
            IDEquipos.push(equipo.IDEquipo)
        })


        const response = await axios.post(route('equiposPDF'), {
            IDEquipos: IDEquipos
        }, {
            responseType: 'blob', // Esto es importante para recibir el PDF como archivo
        });

        equiposStore.equiposAgregados = [] // Limpiar la lista de equipos agregados después de generar el PDF
        loadingStore.stopButtonLoading('download')
        cerrarModal('EquiposCodigosBarras')
        // Crear un enlace para descargar el archivo
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'archivo.pdf'); // Nombre del archivo
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        loadingStore.stopButtonLoading('download')
        console.error('Error descargando el PDF:', error);
    }

}


</script>
