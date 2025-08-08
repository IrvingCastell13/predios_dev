import { defineStore } from 'pinia';
import { ref } from 'vue';
import { useLoadingStore } from '../../../../../../stores/loading';
import { reactive } from 'vue';
import { cerrarModal } from '../../../../../../Utilities';

export const usestoreTableroStore = defineStore('store', () => {

    const plan = ref({})
    const html = ref(null)

    const personas = ref([])
    const formErrors = ref([])
    const proveedores = ref([])
    const id = ref(null)
    const filtroVer = ref('')
    const loadingStore = useLoadingStore();
    const responsable_interno = ref(1)
    const form = reactive({
        IDAccionesRenovacion: '',
        IDTipoOT: 5,
        DescripcionOt: '',
        comentariosOt: '',
        IDPersona: '',
        IDProveedor: '',
        FechaIniOrdenTrabajo: '',
        FechaFinOT: '',
        documentos: [],
        tareas: [],
        orderComptraOt: ''
    })

    const formOriginal = JSON.parse(JSON.stringify(form));

    const archivos = ref([])


    const validateForm = () => {
        formErrors.value = {}
            // if (!form.NombreTicket) errors.value.NombreTicket = 'Este campo es requerido'
            if (!form.IDTipoOT) formErrors.value.IDTipoOT = true
            if (!form.DescripcionOt) formErrors.value.DescripcionOt = true
            if (!form.comentariosOt) formErrors.value.comentariosOt = true
            if (!form.FechaIniOrdenTrabajo) formErrors.value.FechaIniOrdenTrabajo = true
            if (!form.FechaFinOT) formErrors.value.FechaFinOT = true
            if (!form.orderComptraOt) formErrors.value.orderComptraOt = true


            return Object.keys(formErrors.value).length === 0
    };



     const submitForm = async () => {

            formErrors.value = {}
            if (!validateForm()){

                return
            }

            if(loadingStore.buttonLoading['crear']) return

            loadingStore.startButtonLoading('crear')

            const formData = new FormData();



            form.documentos.forEach((documento, index) => {
                formData.append(`documentos[${index}]`, documento);
            });


            Object.keys(form).forEach(key => {
                if (key !== 'documentos' || key !== 'tareas') {
                    formData.append(key, form[key]);
                }
            });

            formData.append('tareas', JSON.stringify(form.tareas));

            formData.append("responsable_interno", responsable_interno.value ?? null);

            try {
                const response = await axios.post(route('crearOrdenRenovacion'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                Object.assign(form, JSON.parse(JSON.stringify(formOriginal)));

                cerrarModal('creatOrden')

                getTablero(id.value)
                // if(!agruparTipo){
                //     getAccionesTipo(id.value)
                // }else{
                //     getAccionesDocumento(id.value)

                // }


            } catch (error) {
                loadingStore.stopButtonLoading('crear')
                console.error(error)
                // const { message, success  } = error.response.data



                // erroresBack.value = {}

                // if (error.response && error.response.data && error.response.data.errors) {
                //     Object.keys(error.response.data.errors).forEach(key => {
                //         erroresBack.value[key] = error.response.data.errors[key][0];
                //     });
                // }else{
                //     if(!success){
                //         swal.fire({
                //             text: message,
                //             icon: "error"
                //         });
                //     }
                // }
            }
        };

    const getPlan = async (id) => {
        try {

            const { data : { data } } = await axios.get(route('planPlanes.show', id));

            plan.value = data;
        } catch (error) {
            // console.error(error);
        }
    };

    const getTablero = async (id) => {
        try {

            const { data : { data } } = await axios.get(route('obtnerTableroPlanEquipos', id), {
                params:{
                    ver: filtroVer.value
                }
            });

            html.value = data;
        } 
        catch (error) {
            
        }
    };

     const obtenerPersonas = async() => {
        try {
            const { data: { data } } = await axios.get(route('obtenerPersonasPermisoEditarOT'))

            personas.value = data
        } catch (error) {

        }
    }

    const obtenerProvedores = async() => {
        try {
            const { data: { data } } = await axios.get(route('obtenerPersonasPreveedorPermisoAdministrador'))

            proveedores.value = data
        } catch (error) {

        }
    }


    return {
        getPlan,
        plan,
        id,
        html,
        getTablero,
        personas,
        proveedores,
        form,
        obtenerPersonas,
        obtenerProvedores,
        archivos,
        formOriginal,
        formErrors,
        submitForm,
        responsable_interno,
        filtroVer
    }
});
