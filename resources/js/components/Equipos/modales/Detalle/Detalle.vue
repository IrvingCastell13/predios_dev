<template>
    <ModalRigth modalId="detalleEquipó" customClass="modal-lg" clasesBody="overflow-y-auto">
         <template #title>
            Detalle del equipo
        </template>

         <div>
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="d-flex align-items-center gap-10">
                    <figure class="Sport-fit-image">
                        <img :src="store.equipo.image" alt="">
                    </figure>




                    <div>
                        <p class="fw-bold primary">{{ store.equipo.DescripcionEquipo }}</p>
                        <div class="d-flex flex-wrap align-items-centercenter gap-5px mt-2">
                            <span class="table-badge primary">{{ store.equipo.subsistema?.sistema?.NombreSistema }}</span>
                            <span class="table-badge success">{{ store.equipo.subsistema?.NombreSubsistema }}</span>
                            <span class="table-badge info">
                                <svg class="cursor-pointer" @click="store.equipo = null" width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4 3C2.78639 4.02477 1.91697 5.39771 1.50943 6.93294C1.10189 8.46817 1.17592 10.0915 1.7215 11.5833C2.26708 13.0751 3.25786 14.3632 4.55971 15.2732C5.86156 16.1833 7.4116 16.6714 9 16.6714C10.5884 16.6714 12.1384 16.1833 13.4403 15.2732C14.7421 14.3632 15.7329 13.0751 16.2785 11.5833C16.8241 10.0915 16.8981 8.46817 16.4906 6.93294C16.083 5.39771 15.2136 4.02477 14 3M9 1V9"
                                        stroke="#B38F00" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                {{ store.equipo.tipo?.NombreTipoEquipo }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <a href="#"
                            class="page-header-icon page-header-icon-success has-counter">
                            <span class="page-header-icon-counter bg-danger">12</span>
                            <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M6 6H14M6 10H12M10.01 15.594L6 18V15H4C3.20435 15 2.44129 14.6839 1.87868 14.1213C1.31607 13.5587 1 12.7956 1 12V4C1 3.20435 1.31607 2.44129 1.87868 1.87868C2.44129 1.31607 3.20435 1 4 1H16C16.7956 1 17.5587 1.31607 18.1213 1.87868C18.6839 2.44129 19 3.20435 19 4V9.5M14 16H20M17 13V19"
                                    stroke="#00A488" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="col-auto">
                        <div class="success">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4 5.00012H3C2.46957 5.00012 1.96086 5.21084 1.58579 5.58591C1.21071 5.96098 1 6.46969 1 7.00012V16.0001C1 16.5306 1.21071 17.0393 1.58579 17.4143C1.96086 17.7894 2.46957 18.0001 3 18.0001H12C12.5304 18.0001 13.0391 17.7894 13.4142 17.4143C13.7893 17.0393 14 16.5306 14 16.0001V15.0001M13 3.00012L16 6.00012M17.385 4.58511C17.7788 4.19126 18.0001 3.65709 18.0001 3.10011C18.0001 2.54312 17.7788 2.00895 17.385 1.61511C16.9912 1.22126 16.457 1 15.9 1C15.343 1 14.8088 1.22126 14.415 1.61511L6 10.0001V13.0001H9L17.385 4.58511Z"
                                    stroke="#0F6491" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <div class="row g-2">
            <div class="col-6">
                <p class="fw-medium fs-sm">Modelo</p>
                <p class="fw-medium text-color-4">{{ store.equipo.tipo?.ModeloTipoEquipo }}</p>
            </div>
            <div class="col-6">
                <p class="fw-medium fs-sm">Marca</p>
                <p class="fw-medium text-color-4">{{ store.equipo.tipo?.MarcaTipoEquipo }}</p>
            </div>
            <div class="col-6">
                <p class="fw-medium fs-sm">Nro Serie</p>
                <p class="fw-medium text-color-4">{{ store.equipo.NoSerieEquipo }}</p>
            </div>
            <div class="col-6">
                <p class="fw-medium fs-sm">Identificación de la unidad</p>
                <p class="fw-medium text-color-4">{{ store.equipo.ClaveEquipo }}</p>
            </div>



            <div class="col-6">
                <p class="fw-medium fs-sm">Frecuencia de mantenimiento</p>
                <p class="fw-medium text-color-4">{{ store.equipo.frecuenciaMantenimiento }}</p>
            </div>
            <div class="col-6">
                <p class="fw-medium fs-sm">Descripción</p>
                <p class="fs-xs fw-medium text-color-4"> {{ store.equipo.DescripcionEquipo }}</p>
            </div>
        </div>
    </div>
        <div class="mt-3">
            <p class="fs-sm fw-medium">Documentos cargados:</p>
            <div class="mt-2">
                <div class="row g-2">
                    <div class="col-auto">
                        <input type="file" ref="refInputArchivos" multiple @change="seleccionarArchivos" style="display: none;" accept="image/*,application/pdf">
                        <div class="attachment-item cursor-pointer" @click="abrirSelectorArchivos" >
                            <svg width="33" height="36" viewBox="0 0 33 36" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M21.2502 9.24994L9.87521 20.6249C9.17902 21.3211 8.7879 22.2654 8.7879 23.2499C8.7879 24.2345 9.17902 25.1787 9.87521 25.8749C10.5714 26.5711 11.5156 26.9622 12.5002 26.9622C13.4848 26.9622 14.429 26.5711 15.1252 25.8749L26.5002 14.4999C27.8926 13.1076 28.6748 11.2191 28.6748 9.24994C28.6748 7.28081 27.8926 5.39233 26.5002 3.99994C25.1078 2.60755 23.2193 1.82532 21.2502 1.82532C19.2811 1.82532 17.3926 2.60755 16.0002 3.99994L4.62521 15.3749C2.53663 17.4635 1.36328 20.2962 1.36328 23.2499C1.36328 26.2036 2.53663 29.0364 4.62521 31.1249C6.71379 33.2135 9.54651 34.3869 12.5002 34.3869C15.4539 34.3869 18.2866 33.2135 20.3752 31.1249L31.7502 19.7499"
                                    stroke="#4C6272" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div v-for="archivo in store.equipo.documentos" :key="archivo.IDArchivo" class="col-auto">
                        <div class="attachment-item">
                            <PreviewDocumentosImgPdf :extension="archivo.ExtensionArchivo" :url="archivo.url"/>
                        </div>
                    </div>
                    <div v-for="archivo in archivos" :key="archivo.IDArchivo" class="col-auto">
                        <div class="attachment-item">
                            <PreviewDocumentosImgPdf :extension="archivo.ExtensionArchivo" :url="archivo.url"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <pre>
            {{  store.equipo }}
        </pre> -->
    </ModalRigth>
</template>

<script setup>
import { inject, ref } from 'vue';
import ModalRigth from '../../../Components/ModalRigth.vue';
import PreviewDocumentosImgPdf from '../../../Components/preview/PreviewDocumentosImgPdf.vue';
import { usestoreStore } from '../../js/store';
import { v4 as uuidv4 } from 'uuid'; // Asegúrate de instalar la librería uuid

    const store = usestoreStore()

    const swal = inject('swal');

    const refInputArchivos = ref(null);

    const documentos = ref([]);
    const archivos = ref([]);

    const abrirSelectorArchivos = () => {
        refInputArchivos.value.click();
    };

    const seleccionarArchivos = (event) => {
    const files = Array.from(event.target.files);
    documentos.value.push(...Array.from(event.target.files))
    files.forEach(file => {
        const extension = file.name.split('.').pop().toLowerCase();
        const archivoConId = {
            IDArchivo: uuidv4(),
            file: file,
            url: URL.createObjectURL(file),
            ExtensionArchivo: extension
        };
        archivos.value.push(archivoConId);

        CargarArchivosNuevos()
    });

};



   const CargarArchivosNuevos = async () => {

        try {


            const formData = new FormData();

            documentos.value.forEach((documento, index) => {
                formData.append(`documentos[${index}]`, documento);
            });


                // if(is_edit.value){
                //     formData.append('IDSEliminarArchivos', IDSEliminarArchivos.value);
                // }
                formData.append('EquipoID', store.equipo.IDEquipo);

                const response = await axios.post(route('equipo.updateArchivos'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                documentos.value = [];
                archivos.value = [];
                refInputArchivos.value.value = null; // Limpiar el input de archivos

        } catch (error) {

            swal.fire({
                icon: 'error',
                title: 'Error al cargar archivos',
                text: 'Hubo un problema al cargar los archivos. Por favor, inténtalo de nuevo.'
            });

            documentos.value = [];
            archivos.value = [];
            refInputArchivos.value.value = null; // Limpiar el input de archivos
           console.log(error)
        }
    };

</script>

