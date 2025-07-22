<template>

    <ModalRigth modalId="modalCreatEquipo" customClass="modal-lg" clasesBody="overflow-y-auto">

        <template #title>
            <span v-if="store.IsEditing">Actualizar Unidad de Equipo</span>
            <span v-if="!store.IsEditing">Registrar Unidad de Equipo</span>

        </template>

         <div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fs-sm">Sistema <span class="text-danger">*</span></label>
                     <v-select placeholder="Sistema" style="height: 60px !important;"  v-model="store.form.IDSistema" :options="store.sistemas"  label="NombreSistema"   :reduce="sistema => sistema.IDSistema"  @update:modelValue="store.obtenerSubsistemas"></v-select>
                     <div v-if="store.formErrors.IDSistema" class="text-danger">Este campo es requerido.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-sm">Subsistema <span class="text-danger">*</span></label>
                      <v-select placeholder="Subsistema"  class=" " style="height: 60px !important;"  v-model="store.form.IDSubsistema" :options="store.subsistemas"  label="NombreSubsistema"   :reduce="subsistema => subsistema.IDSubsistema"  @update:modelValue="store.obtenerSubsistemas"></v-select>
                     <div v-if="store.formErrors.IDSubsistema" class="text-danger">Este campo es requerido.</div>
                </div>
            </div>
        </div>

        <div class="border-radius-6 p-3 bg-grey-3 mt-4">
            <p class="fs-sm fw-medium">Tipo de Equipo <span class="text-danger">*</span></p>
            <div class="mt-2">
                <v-select placeholder="Tipo equipo" style="height: 60px !important;"  v-model="store.form.IDTipoEquipo" :options="store.tipos"  label="NombreTipoEquipo" :reduce="tipo => tipo.IDTipoEquipo" @update:modelValue="store.cambiarTipo"></v-select>
                    <div v-if="store.formErrors.IDTipoEquipo" class="text-danger" >Este campo es requerido.</div>
            </div>
            <div v-if="store.form.IDTipoEquipo" class="mt-3">
                <div class="row g-2">
                    <div class="col-6">
                        <p class="fw-medium fs-sm">Marca</p>
                        <p class="fw-medium text-color-4">{{ store.tipo.marca }}</p>
                    </div>
                    <div class="col-6">
                        <p class="fw-medium fs-sm">Modelo</p>
                        <p class="fw-medium text-color-4">{{ store.tipo.modelo }}</p>
                    </div>
                    <div class="col-6">
                        <p class="fw-medium fs-sm">Clave</p>
                        <p class="fw-medium text-color-4">{{ store.tipo.clave }}</p>
                    </div>
                    <div class="col-6">
                        <p class="fw-medium fs-sm">Frecuencia de mantenimiento</p>
                        <p class="fw-medium text-color-4">Pendiente</p>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <a @click="limpiarTipo()" href="#" class="danger fw-medium">Limpiar selección</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fs-sm">Nro. Serie de la unidad <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="store.form.NoSerieEquipo"  :class="{'border-danger': store.formErrors.NoSerieEquipo}" placeholder="202545645">
                    <div v-if="store.formErrors.NoSerieEquipo" class="text-danger">Este campo es requerido.</div>

                </div>
                <div class="col-md-6">
                    <label class="form-label fs-sm">Identificación de la unidad <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="store.form.IdentificacionEquipo"  :class="{'border-danger': store.formErrors.IdentificacionEquipo}" placeholder="Carrier-36">
                    <div v-if="store.formErrors.IdentificacionEquipo" class="text-danger">Este campo es requerido.</div>

                </div>
                <div class="col-md-6">
                    <label class="form-label fs-sm">Valor de compra de la unidad <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="store.form.ValorCompraEquipo"  :class="{'border-danger': store.formErrors.ValorCompraEquipo}" placeholder="MXN 3.000,00">
                    <div v-if="store.formErrors.ValorCompraEquipo" class="text-danger">Este campo es requerido.</div>

                </div>
                <div class="col-md-6">
                    <label class="form-label fs-sm">Valor residual<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="store.form.ValorResidualEquipo"  :class="{'border-danger': store.formErrors.ValorResidualEquipo}" placeholder="MXN 2.000,00">
                    <div v-if="store.formErrors.ValorResidualEquipo" class="text-danger">Este campo es requerido.</div>
                </div>
                <div class="mb-3">
                    <label for="DescripcionEquipo" class="form-label">Descripción del equipo<span class="text-danger">*</span></label>
                    <textarea type="text" class="form-control" v-model="store.form.DescripcionEquipo"  :class="{'border-danger': store.formErrors.DescripcionEquipo}" placeholder="Escribe aqui..."> </textarea>
                    <div v-if="store.formErrors.DescripcionEquipo" class="text-danger">Este campo es requerido.</div>
                </div>
                <div class="col-6">
                    <label class="form-label fs-sm">Predio<span class="text-danger">*</span></label>
                   <v-select class=" mb-3" v-model="store.form.IDPredio" :options="store.prediosCrear"  label="NombrePredio" placeholder="Predio" :reduce="predio => predio.IDPredio" @update:modelValue="store.cambiarPredio"></v-select>
                    <small v-if="store.formErrors.IDPredio" class="text-danger">Este campo es requerido.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-sm">Edificio<span class="text-danger">*</span></label>
                     <v-select :loading="store.loadingEdificios" class=" mb-3 " v-model="store.form.IDEdificio" :options="store.edificios"  label="NombreEdificio" placeholder="Edificio" :reduce="edificio => edificio.IDEdificio" @update:modelValue="store.obtenerNiveles"></v-select>
                    <small v-if="store.formErrors.IDEdificio" class="text-danger">Este campo es requerido.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-sm">Nivel<span class="text-danger">*</span></label>
                     <v-select :loading="store.loadingNivel" class=" mb-3 " v-model="store.form.IDNivel" :options="store.niveles"  label="NombreNivel" placeholder="Nivel" :reduce="nivel => nivel.IDNivel" @update:modelValue="store.obtenerZonas"></v-select>
                    <small v-if="store.formErrors.IDNivel" class="text-danger">Este campo es requerido.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-sm">Zona<span class="text-danger">*</span></label>
                    <v-select :loading="store.loadingZona" class=" mb-3 " v-model="store.form.IDZona" :options="store.zonas"  label="NombreZona" placeholder="Zona" :reduce="zona => zona.IDZona"></v-select>
                    <small v-if="store.formErrors.IDZona" class="text-danger">Este campo es requerido.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-sm">Frecuencia de mantenimiento<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="store.form.frecuenciaMantenimiento" placeholder="Quincenal"  :class="{'border-danger': store.formErrors.frecuenciaMantenimiento}">
                    <div v-if="store.formErrors.frecuenciaMantenimiento" class="text-danger">Este campo es requerido.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-sm">Último mantenimiento<span class="text-danger">*</span></label>
                    <input type="date" class="form-control" v-model="store.form.FechaUltMantEquipo"  :class="{'border-danger': store.formErrors.FechaUltMantEquipo}">
                    <div v-if="store.formErrors.FechaUltMantEquipo" class="text-danger">Este campo es requerido.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-sm">Fecha de adquisición<span class="text-danger">*</span></label>
                    <input type="date" class="form-control" v-model="store.form.FechaCompraEquipo"  :class="{'border-danger': store.formErrors.FechaCompraEquipo}">
                    <div v-if="store.formErrors.FechaCompraEquipo" class="text-danger">Este campo es requerido.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-sm">Próximo mantenimiento<span class="text-danger">*</span></label>
                    <input type="date" class="form-control" v-model="store.form.FechaProxMantEquipo"  :class="{'border-danger': store.formErrors.FechaProxMantEquipo}">
                    <div v-if="store.formErrors.FechaProxMantEquipo" class="text-danger">Este campo es requerido.</div>
                </div>
                <div class="mb-3">
                    <label for="ComentariosEquipo" class="form-label">Comentarios</label>
                    <textarea type="text" class="form-control" v-model="store.form.ComentariosEquipo"  :class="{'border-danger': store.formErrors.ComentariosEquipo}" placeholder="Escribe tus comentarios..."></textarea>
                    <div v-if="store.formErrors.ComentariosEquipo" class="text-danger">Este campo es requerido.</div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <div class="row g-2">
                <div v-if="store.archivos.length == 0" class="mb-3">
                    <p class="fs-sm fw-medium text-color-4 mb-2">Carga archivos asociados al la acción en formatos de imagen (JPG, PNG) o documento (PDF).</p>
                    <input type="file" ref="refInputArchivos" multiple @change="seleccionarArchivos" style="display: none;" accept="image/*,application/pdf">
                    <div class="attach-file" @click="abrirSelectorArchivos" >
                    <label for="attachFile"><img src="/images/upload-icon.svg" alt=""> Adjuntar archivo(s)</label>
                    </div>
                </div>
                <div v-else class="row g-2 d-flex justify-content-center">
                    <p class="fs-sm fw-medium mb-1">Documentos del predio</p>
                    <p class="fs-sm fw-medium text-color-4 mb-2">Carga archivos asociados al predio en formatos de imagen (JPG, PNG) o documento (PDF).</p>
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

                            <PreviewDocumentosImgPdf :extension="archivo.ExtensionArchivo" :url="store.cleanUrl(archivo.url)"/>
                        </div>
                    </div>
                </div>

            </div>
        </div>


          <template #footer>
             <div class="row g-2 align-items-center justify-content-between">
                 <div class="col-auto">
                     <span class="cursor-pointer" @click="closeModal">Cancelar</span>

                 </div>
                 <div class="col-auto">
                     <ButtonLoading text="Guardar equipo" clases="btn btn-primary" id="crear" @click="store.enviarFormulario()"/>

                 </div>
             </div>
         </template>

    </ModalRigth>
</template>

<script setup>
    import { reactive, ref } from 'vue';
    import ModalRigth from '../../../components/Components/ModalRigth.vue';
    import { usestoreStore } from '../js/store';
    import { v4 as uuidv4 } from 'uuid';
    import ButtonLoading from '../../../components/Components/Bootstrap/ButtonLoading.vue';
    import PreviewDocumentosImgPdf from '../../../components/Components/preview/PreviewDocumentosImgPdf.vue';
    import { cerrarModal } from '../../../Utilities';

    const store = usestoreStore()
    const refInputArchivos = ref(null)




   const closeModal = () => {
      store.limpiarFormulario();
      cerrarModal('modalCreatEquipo');
    };
   const abrirSelectorArchivos = () => {
        refInputArchivos.value.click();
    };


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
