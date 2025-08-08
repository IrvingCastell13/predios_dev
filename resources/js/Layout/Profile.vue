<template>
      <ModalRigth modalId="MiPerfilModal">

        <template #title>
            Mi Perfil
        </template>

        <div class="mb-4">
            <div class="row g-3">
                <div class="col-xxl-auto">
                    <div class="avatar avatar-xxl mb-3">
                        <img :src="storeProfile.UrlLogotipo" alt="">
                        <button class="avatar-edit"><img src="/images/avatar-edit-icon.svg" alt="" @click="openFileBrowser"></button>
                        <input hidden
                            type="file"
                            ref="fileInput"
                            accept="image/*,application/pdf"

                            @change="handleFileChange"
                        />
                    </div>
                </div>
                <div class="col">
                    <div class="row g-3">

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre(s) <span class="text-danger">*</span></label>
                            <input disabled type="text" class="form-control" v-model="storeProfile.user.NombrePersona" :class="{'border-danger': storeProfile.erroresBack.NombrePersona}"
                                placeholder="Indique el nombre de la empresa/marca del cliente">
                                <div v-if="storeProfile.erroresBack.NombrePersona" class="text-danger">{{ storeProfile.erroresBack.NombrePersona }}</div>
                        </div>

                        <div class="mb-3 col-6">
                            <label for="apelleido_paterno" class="form-label">Apellido Paterno <span class="text-danger">*</span></label>
                            <input disabled type="text" id="apelleido_paterno" class="form-control" v-model="storeProfile.user.ApellidoPaternoPersona" :class="{'border-danger': storeProfile.erroresBack.ApellidoPaternoPersona}"
                                placeholder="Indique el nombre de la empresa/marca del cliente">
                                <div v-if="storeProfile.erroresBack.ApellidoPaternoPersona" class="text-danger">{{ storeProfile.erroresBack.ApellidoPaternoPersona }}</div>
                        </div>

                        <div class="mb-3 col-6">
                            <label for="apelleido_materno" class="form-label">Apellido Materno</label>
                            <input disabled type="text" id="apelleido_materno" class="form-control" v-model="storeProfile.user.ApellidoMaternoPersona" :class="{'border-danger': storeProfile.erroresBack.ApellidoMaternoPersona}"
                                placeholder="Indique el nombre de la empresa/marca del cliente">
                                <div v-if="storeProfile.erroresBack.ApellidoMaternoPersona" class="text-danger">{{ storeProfile.erroresBack.ApellidoMaternoPersona }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="user" class="form-label">Nombre de usuario <span class="text-danger">*</span></label>
                            <input disabled type="text" id="user" class="form-control" v-model="storeProfile.user.Usuario" :class="{'border-danger': storeProfile.erroresBack.Usuario}"
                                placeholder="Indique el nombre y apellido del usuario">
                                <div v-if="storeProfile.erroresBack.Usuario" class="text-danger">{{ storeProfile.erroresBack.Usuario }}</div>
                        </div>

                        <div class="mb-3 col-6">
                            <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                            <input disabled type="text" id="email" class="form-control" v-model="storeProfile.user.EmailPersona" :class="{'border-danger': storeProfile.erroresBack.EmailPersona}"
                                placeholder="Ej. example@mail.com">
                                <div v-if="storeProfile.erroresBack.EmailPersona" class="text-danger">{{ storeProfile.erroresBack.EmailPersona }}</div>
                        </div>

                        <div class="mb-3 col-6">
                            <label for="numero" class="form-label">Número telefónico</label>
                            <input type="text" id="numero" class="form-control" v-model="storeProfile.user.TelefonoPersona"
                                placeholder="Ej. 123 45 67 89">
                        </div>

                        <!-- <div v-if="!storeProfile.is_edit" class="mb-3 col-12">
                            <label for="numero" class="form-label">Predios</label>
                            <v-select class=" mb-3" v-model="storeProfile.user.IDPredio" :options="predios"  label="NombrePredio" placeholder="Predio" :reduce="predio => predio.IDPredio"></v-select>
                            <div v-if="storeProfile.erroresBack.IDPredio" class="text-danger">{{ storeProfile.erroresBack.IDPredio }}</div>
                        </div>
                        <div v-if="!storeProfile.is_edit" class="mb-3 col-12">
                            <label for="numero" class="form-label">Roles</label>
                            <v-select class=" mb-3 " v-model="storeProfile.user.rol" :options="roles"  label="name" placeholder="Selecciona los roles" :reduce="rol => rol.id" :multiple="true"></v-select>
                            <div v-if="storeProfile.erroresBack.rol" class="text-danger">{{ storeProfile.erroresBack.rol }}</div>
                        </div> -->


                    </div>
                </div>
            </div>
        </div>





        <div  class="row g-2 d-flex justify-content-center">
            <p class="fs-sm fw-medium mb-1">Documentos del usuario</p>
            <p class="fs-sm fw-medium text-color-4 mb-2">Carga archivos asociados al usuario en formatos de imagen (JPG, PNG) o documento (PDF).</p>

            <div class="col-auto">
                <button @click="abrirSelectorArchivos" class="attachment-item"><img src="/images/plus-primary-icon.svg" alt=""></button>
                <input type="file" ref="refInputArchivos" multiple @change="seleccionarArchivos" style="display: none;" accept="image/*,application/pdf">
            </div>
            <!-- {{ storeProfile.archivos }} -->
            <div  v-for="archivo in storeProfile.archivos" :key="archivo.IDArchivo" class="col-auto">
                <div class="attachment-item">
                    <PreviewDocumentosImgPdf :extension="archivo.ExtensionArchivo" :url="archivo.url"/>
                </div>
            </div>
        </div>


            <div class="attach-file mt-4">
                <div class="mb-3">
                    <button @click="asignarPassword = true" class="col-12">
                        <svg width="17" height="20" viewBox="0 0 17 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.0625 18.25H3.125C2.66087 18.25 2.21575 18.0568 1.88756 17.713C1.55937 17.3692 1.375 16.9029 1.375 16.4167V10.9167C1.375 10.4304 1.55937 9.96412 1.88756 9.6203C2.21575 9.27649 2.66087 9.08333 3.125 9.08333H11.875C12.3391 9.08333 12.7842 9.27649 13.1124 9.6203C13.4406 9.96412 13.625 10.4304 13.625 10.9167V11.375M4 9.08333V5.41667C4 4.44421 4.36875 3.51158 5.02513 2.82394C5.6815 2.13631 6.57174 1.75 7.5 1.75C8.42826 1.75 9.3185 2.13631 9.97487 2.82394C10.6313 3.51158 11 4.44421 11 5.41667V9.08333M10.125 16.4167L11.875 18.25L15.375 14.5833M6.625 13.6667C6.625 13.9098 6.71719 14.1429 6.88128 14.3148C7.04538 14.4868 7.26794 14.5833 7.5 14.5833C7.73206 14.5833 7.95462 14.4868 8.11872 14.3148C8.28281 14.1429 8.375 13.9098 8.375 13.6667C8.375 13.4236 8.28281 13.1904 8.11872 13.0185C7.95462 12.8466 7.73206 12.75 7.5 12.75C7.26794 12.75 7.04538 12.8466 6.88128 13.0185C6.71719 13.1904 6.625 13.4236 6.625 13.6667Z" stroke="#0F6491" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Cambiar Contraseña
                    </button>
                </div>
            </div>


        <div v-if="asignarPassword" class="form-control-wrap position-relative">
            <input v-model="storeProfile.user.password" :type="showPassword ? 'text' : 'password'" class="form-control password-field" placeholder="***************" id="password1" required>

            <button type="button" class="password-toggle-btn" @click="togglePassword"></button>
        </div>

        <div>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fs-sm">Comentarios</label>
                    <textarea disabled type="text" v-model="storeProfile.user.comentarios" class="form-control" placeholder="Escribe tus comentarios..."></textarea>
                </div>
            </div>
        </div>

        <template #footer>



            <div  class="row g-2 align-items-center justify-content-end">
                <div class="col-auto">
                    <ButtonLoading text="Guardar cambios" clases="btn btn-primary" id="guardarCambios"  @click="storeProfile.enviarDatosUsuario()"/>
                </div>
            </div>

        </template>


        </ModalRigth>
</template>

<script setup>


    import {onMounted, ref } from 'vue';

    const asignarPassword = ref(false)
    const storeProfile = usestoreProfileStore()

    const fileInput = ref(null);

    import { v4 as uuidv4 } from 'uuid'; // Asegúrate de instalar la librería uuid

    import { usestoreProfileStore } from './js/storeProfile';
    import ButtonLoading from '../Components/Bootstrap/ButtonLoading.vue';
    import PreviewDocumentosImgPdf from '../Components/preview/PreviewDocumentosImgPdf.vue';
    import ModalRigth from '../Components/ModalRigth.vue';
    const refInputArchivos = ref(null);
    const emit = defineEmits(['peticionCompletada']);




    const abrirSelectorArchivos = () => {
        refInputArchivos.value.click();
    };


    const deleteArchivo = (archivo) => {

        if(archivo.IDCliente){

            swal.fire({
            title: "Estas seguro?",
            text: "No podras revertir esto!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, eliminar!",
            cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    storeProfile.archivos = storeProfile.archivos.filter(item => item.IDArchivo !== archivo.IDArchivo)
                    storeProfile.IDSEliminarArchivos.push(archivo.IDArchivo)
                }
            });
        }else{
            storeProfile.archivos = storeProfile.archivos.filter(item => item.IDArchivo !== archivo.IDArchivo)
        }
    }


    const seleccionarArchivos = (event) => {
        const files = Array.from(event.target.files);
        storeProfile.user.documentos.push(...Array.from(event.target.files))
        files.forEach(file => {
            const archivoConId = {
                IDArchivo: uuidv4(),
                file: file,
                url: URL.createObjectURL(file)
            };
            storeProfile.archivos.push(archivoConId);
        });

    };

    const openFileBrowser = () => {
        fileInput.value.click();
    }

    const handleFileChange = (event) => {
        const file = event.target.files[0];
        if(file){

            if (file.type.startsWith('image/')) {
                storeProfile.UrlLogotipo = URL.createObjectURL(file);
                storeProfile.user.logo = file
            } else {
                // Mostrar error si no es una imagen
                alert('Por favor, selecciona un archivo de imagen válido.');
            }
        }
    };


    onMounted(() => {

        const togglePassword = document.querySelectorAll(".password-toggle-btn");
            togglePassword.forEach((button) => {
            button.addEventListener("click", function () {
                const passwordInput = document.getElementById(button.dataset.target);
                if (passwordInput.type === "password") {
                this.classList.add("active");
                passwordInput.type = "text";
                } else {
                this.classList.remove("active");
                passwordInput.type = "password";
                }
            });
        });

    })

</script>

<style lang="scss" scoped>

</style>