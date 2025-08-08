<template>
    <ModalRigth  modalId="creatOrden" customClass="modal-lg">

        <template #title>
            Orden de trabajo
        </template>


        <div class="mb-3">
            <div class="bg-light-grey-2 p-2 border-radius">
                <div class="d-flex align-items-center gap-10">
                    <figure class="Sport-fit-image">
                        <img src="images/sport-fit.jpg" alt="">
                    </figure>
                    <div>
                        <p class="fw-bold primary">{{ store?.accionDetalle?.documento?.DescripcionDocumento }}</p>
                        <!-- <p class="fw-semibold text-color-4">Acción de renovación Example...</p> -->
                        <p class="fs-sm fw-semibold success">{{ store?.plan?.NombrePlan }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fs-sm">Fecha de inicio <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" v-model="store.form.FechaIniOrdenTrabajo" />
                    <div v-if="store.formErrors.FechaIniOrdenTrabajo" class="text-danger">Este campo es requerido.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-sm">Fecha de inicio <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" v-model="store.form.FechaFinOT" />
                    <div v-if="store.formErrors.FechaFinOT" class="text-danger">Este campo es requerido.</div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="row g-3">
                <div class="col-md-auto">

                    <label class="form-label fs-sm">Responsable</label>
                    <div class="radio radio-success">
                        <input type="radio" v-model="store.responsable_interno" value="0" id="radio1" name="option1" :checked="store.responsable_interno == 0">
                        <label for="radio1" class="fs-sm">Externo</label>
                    </div>
                    <div class="radio radio-success">
                        <input type="radio" v-model="store.responsable_interno" value="1" id="radio2" name="option1" :checked="store.responsable_interno == 1">
                        <label for="radio2" class="fs-sm">Interno</label>
                    </div>
                </div>
                <div class="col-md" v-if="store.responsable_interno == 1">
                    <label for="titulo" class="form-label">Nombre del responsable</label>
                    <v-select class=" mb-3" v-model="store.form.IDPersona" :options="store.personas" label="full_name" :reduce="persona => persona.IDPersona"></v-select>
                </div>

                <div v-if="store.responsable_interno == 0" class="col-6">
                    <label for="titulo" class="form-label">Nombre del proveedor</label>
                    <v-select class=" mb-3" v-model="store.form.IDProveedor" :options="store.proveedores" label="NombreProveedor" :reduce="proveedor => proveedor.IDProveedor"></v-select>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fs-sm">Orden de compra <span class="text-danger">*</span></label>
                    <input class="form-control" v-model="store.form.orderComptraOt" />
                     <div v-if="store.formErrors.orderComptraOt" class="text-danger">Este campo es requerido.</div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fs-sm">Comentarios <span class="text-danger">*</span></label>
                    <textarea class="form-control" v-model="store.form.comentariosOt">Escribe aquí...</textarea>
                     <div v-if="store.formErrors.comentariosOt" class="text-danger">Este campo es requerido.</div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fs-sm">Descripción <span class="text-danger">*</span></label>
                    <textarea class="form-control" v-model="store.form.DescripcionOt">Escribe aquí...</textarea>
                     <div v-if="store.formErrors.DescripcionOt" class="text-danger">Este campo es requerido.</div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fs-sm">Tareas</label>

                    <div v-for="element in store.form.tareas" :key="element.id" class="d-flex mt-3">

                        <input type="text" disabled class="mx-5 form-control" v-model="element.DescripcionTareaAccion" placeholder="Descripcion de la tarea">


                    </div>

                </div>
            </div>
        </div>

        <div v-if="store.archivos.length == 0" class="mb-3">
            <p class="fs-sm fw-medium text-color-4 mb-2">Documentos</p>
            <input type="file" ref="refInputArchivos" multiple @change="seleccionarArchivos" style="display: none;" accept="image/*,application/pdf">
            <div class="attach-file" @click="abrirSelectorArchivos" >
            <label for="attachFile"><img src="/images/upload-icon.svg" alt=""> Adjuntar archivo(s)</label>
            </div>
        </div>
        <div v-else class="row g-2 d-flex justify-content-center">
            <p class="fs-sm fw-medium text-color-4 mb-2">Documentos</p>
            <div class="col-auto">
                <button @click="abrirSelectorArchivos" class="attachment-item"><img src="/images/plus-primary-icon.svg" alt=""></button>
                <input type="file" ref="refInputArchivos" multiple @change="seleccionarArchivos" style="display: none;" accept="image/*,application/pdf">
            </div>
            <div  v-for="archivo in store.archivos" :key="archivo.IDArchivo" class="col-auto">
                <div class="attachment-item position-relative">
                    <div @click="deleteArchivo(archivo.IDArchivo)" style="background-color: red; border-radius: 100%;" class=" position-absolute top-0 end-0">
                        <svg class="cursor-pointer" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 8L15 16M15 8L9 16M12 21C14.3869 21 16.6761 20.0518 18.364 18.364C20.0518 16.6761 21 14.3869 21 12C21 9.61305 20.0518 7.32387 18.364 5.63604C16.6761 3.94821 14.3869 3 12 3C9.61305 3 7.32387 3.94821 5.63604 5.63604C3.94821 7.32387 3 9.61305 3 12C3 14.3869 3.94821 16.6761 5.63604 18.364C7.32387 20.0518 9.61305 21 12 21Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <PreviewDocumentosImgPdf :extension="archivo.ExtensionArchivo" :url="archivo.url"/>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="row g-2 align-items-center justify-content-between">
                <div class="col-auto">
                    <button type="submit" class="text-color-4">Cancelar</button>
                </div>
                <div class="col-auto">
                    <ButtonLoading text="Guardar" clases="btn btn-primary" id="crear" @click="store.submitForm()"/>
                </div>
            </div>
        </template>
    </ModalRigth>
</template>

<script setup>
import { ref } from 'vue';
import ModalRigth from '../../../../../../Components/ModalRigth.vue';

import PreviewDocumentosImgPdf from '../../../../../../Components/preview/PreviewDocumentosImgPdf.vue';
import { v4 as uuidv4 } from 'uuid';
import ButtonLoading from '../../../../../../Components/Bootstrap/ButtonLoading.vue';
import { usestoreTableroStore } from '../js/storeTablero';
const store = usestoreTableroStore()
const abrirSelectorArchivos = () => {
        refInputArchivos.value.click();
    };

    const refInputArchivos = ref(null)

    const seleccionarArchivos = (event) => {
        const files = Array.from(event.target.files);


        store.form.documentos.push(...Array.from(event.target.files))
        files.forEach(file => {
            const archivoConId = {
                IDArchivo: uuidv4(),
                file: file,
                url: URL.createObjectURL(file)
            };
            store.archivos.push(archivoConId);
        });

    };

    const deleteArchivo = (IDArchivo) => {
        store.archivos = store.archivos.filter(item => item.IDArchivo !== IDArchivo)
    }



</script>
