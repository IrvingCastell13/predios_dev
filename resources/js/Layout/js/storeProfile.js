import { defineStore } from 'pinia';
import { ref, reactive } from 'vue';
import { abrirModal, cerrarModal } from '../../Utilities';
import { useAuthStore } from '../../stores/auth/Auth';
import { useLoadingStore } from '../../stores/loading';

export const usestoreProfileStore = defineStore('storeProfile', () => {

    const loadingStore = useLoadingStore();
    const auth = useAuthStore()
    const erroresBack = ref({});
    const archivos = ref([]);
    const UrlLogotipo = ref('/images/avatar_default.jpg')
    const user = reactive({
        NombrePersona: '',
        ApellidoPaternoPersona: '',
        ApellidoMaternoPersona: '',
        Usuario: '',
        EmailPersona: '',
        TelefonoPersona: '',
        ActivoPersona: 1,
        password: '',
        documentos: '',
        comentarios: '',
        rol: '',
        logo: null,
        documentos: [],

    });
    const IDSEliminarArchivos = ref([])


    const getPersona = async () => {

        try {

            const { data: { data } }  = await axios.get(route('obtenerPersona', auth.user.IDPersona))

            user.IDPersona = data.IDPersona,
            user.NombrePersona = data.NombrePersona,
            user.ApellidoPaternoPersona = data.ApellidoPaternoPersona,
            user.ApellidoMaternoPersona = data.ApellidoMaternoPersona,
            user.Usuario = data.Usuario,
            user.EmailPersona = data.EmailPersona,
            user.TelefonoPersona = data.TelefonoPersona,
            user.ActivoPersona = data.ActivoPersona,


            user.comentarios = data.comentarios,
            user.rol = data.rol,
            user.logo = data.logo,
            user.documentos = data.documentos,
            archivos.value = data.documentos
            UrlLogotipo.value = data.image

        } catch (error) {
            console.log(error)
        }

        abrirModal("MiPerfilModal")
    }


    const enviarDatosUsuario = async (modalCarrar = true) => {

        try {

        if(loadingStore.buttonLoading['guardarCambios']) return

        loadingStore.startButtonLoading('guardarCambios')

        const formData = new FormData();
        formData.append('logo', user.logo);
        user.documentos.forEach((documento, index) => {
            formData.append(`documentos[${index}]`, documento);
        });

        // Agregar los demás campos del objeto user al FormData
        Object.keys(user).forEach(key => {
            if (key !== 'logo' && key !== 'documentos') {
                formData.append(key, user[key]);
            }
        });


        formData.append('IDSEliminarArchivos', IDSEliminarArchivos.value);


            const response = await axios.post(route('actualizarPerfil'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            clearForm()


            cerrarModal('MiPerfilModal')

            IDSEliminarArchivos.value = []
            erroresBack.value = {}

            // cerrarModal('modalUsuarios')
            // abrirModal('modalRolesUsuarios')
            // obtenerRoles();

            archivos.value = []
            auth.user.image = response.data.data.image
            auth.user.NombrePersona = response.data.data.NombrePersona
            // localStorage.setItem('user', JSON.stringify(response.data.data));
            loadingStore.stopButtonLoading('guardarCambios')



        } catch (error) {

                loadingStore.stopButtonLoading('guardarCambios')

            erroresBack.value = {}
            console.error('Error al enviar los datos del usuario:', error);
            if (error.response && error.response.data && error.response.data.errors) {
                Object.keys(error.response.data.errors).forEach(key => {
                    erroresBack.value[key] = error.response.data.errors[key][0];
                });
            }
        }
    };

    const clearForm = () => {
        Object.keys(user).forEach(key => {
            if (typeof user[key] === 'string') {
                user[key] = '';
            } else if (Array.isArray(user[key])) {
                user[key] = [];
            } else {
                user[key] = null;
            }
        });

        UrlLogotipo.value = '/images/avatar_default.jpg'

    }


    return {
        erroresBack,
        archivos,
        UrlLogotipo,
        user,
        getPersona,
        IDSEliminarArchivos,
        enviarDatosUsuario
    }
});
