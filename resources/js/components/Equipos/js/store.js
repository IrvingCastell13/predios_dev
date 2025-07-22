import { defineStore } from 'pinia';
import { ref, watch } from 'vue';
import { useAuthStore } from '../../../stores/auth/Auth';
import { reactive } from 'vue';
import { useLoadingStore } from '../../../stores/loading';
import { abrirModal, cerrarModal } from '../../../Utilities';

export const usestoreStore = defineStore('store', () => {
    const authStore = useAuthStore()
    const equipos = ref([])
    const totalEquipos = ref(0)
    const datatableRef = ref(null)

     const loadingStore = useLoadingStore();

    const sistemas = ref([])
    const equipo = ref([])
    const subsistemas = ref([])
    const subsistemasIndex = ref([])
    const tipos = ref([])

    const prediosCrear = ref([])
    const edificios = ref([])
    const niveles = ref([])
    const zonas = ref([])

    const edificiosIndex = ref([])
    const nivelesIndex = ref([])
    const zonasIndex = ref([])

    const loadingEdificios = ref(false)
    const loadingNivel = ref(false)
    const loadingZona = ref(false)
    const IsEditing = ref(false)

    const filters = ref({
        search: '',
        IDTipoEquipo: '',
        IDSubsistema: '',
        IDSistema: '',
        IDPredio: '',
        IDEdificio: '',
        IDNivel: '',
        IDZona: '',

    })

    const tipo = reactive({
        marca: '',
        modelo: '',
        clave: '',
        frecuenciaMantenimiento: '',
    })


    const form = reactive({
        IDSistema: '',
        IDSubsistema: '',
        IDTipoEquipo: '',
        NoSerieEquipo: '',
        IdentificacionEquipo: '',
        ValorCompraEquipo: '',
        ValorResidualEquipo: '',
        DescripcionEquipo: '',
        IDPredio: '',
        IDEdificio: '',
        IDNivel: '',
        IDZona: '',
        frecuenciaMantenimiento: '',
        FechaUltMantEquipo: '',
        FechaCompraEquipo: '',
        FechaProxMantEquipo: '',
        ComentariosEquipo: '',
        documentos: [],

    })

    const archivos = ref([])
    const formOriginal = JSON.parse(JSON.stringify(form));

    const formErrors = ref({})

    // Watch para filtros
    watch(filters, (newFilters) => {
        obtenerDatatables(newFilters);
    }, { deep: true });

    const obtenerDatatables = async () => {

        if ($.fn.DataTable.isDataTable('#equipos')) {
            $('#equipos').DataTable().destroy();
        }
        datatableRef.value = $('#equipos').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: route('equipos.index'),
                type: 'GET',
                beforeSend: function(xhr) {
                        xhr.setRequestHeader('Authorization', `Bearer ${authStore.token}`);
                },
                dataSrc: function(json) {
                    equipos.value = json.data; // Almacena la data en el estado de Vue
                    totalEquipos.value = json.data.length; // Almacena la data en el estado de Vue
                    // loadingStore.stopLoading();
                    return json.data;
                },
                data: function (d) {

                   Object.assign(d, filters.value);


                }
            },

            columns: [
                // { data: 'IDDefinicionRutina', name: 'IDDefinicionRutina'},
                { data: 'checkbox', name: 'checkbox'},
                { data: 'gerarquia', name: 'gerarquia'},
                { data: 'identificadorEquipo', name: 'identificadorEquipo'},
                { data: 'marca', name: 'marca'},
                { data: 'estado', name: 'estado'},
                { data: 'FechaProxMantEquipo', name: 'FechaProxMantEquipo'},
                { data: 'ubicacion', name: 'ubicacion'},
                { data: 'planAsociado', name: 'planAsociado'},
                {
                    data: null,
                    render: function (data, type, row) {
                        // Supongamos que la bandera se llama 'puedeEditar'
                        // if (row.editar_predio) {
                            return `
                                <div class='p-0 d-flex justify-content-center' onclick="event.stopPropagation()">
                                    <div class='justify-content-center cursor-pointer mx-2' onclick="window.vueMethods.editarEquipo('${row.IDEquipo}')">
                                        <img src='/images/table-edit-icon.svg' alt='' />
                                    </div>
                                </div>
                            `;
                        // } else {
                        //     return ''; // No se muestra nada si no tiene permisos
                        // }
                    }
                }
           ],

            info: false,
            paging: false,
            searching: false,
        });

         $('#equipos tbody').on('click', 'tr', function() {

            const data = datatableRef.value.row(this).data();

            if (data) {
                abrirDetalleEquipó(data.IDEquipo);
            }
        });
    };


    const abrirDetalleEquipó = async(IDEquipo) => {
        // detalleTicket.value = ticket
        await obtenerEquipoOnliy(IDEquipo)
        abrirModal('detalleEquipó')
    }



    const obtenerSistemas = async() => {
            try {
                const { data: { data } } = await axios.get(route('obtenerSistemasEquiposCliente'))
                sistemas.value = data
            } catch (error) {

            }
        }


    const obtenerSubsistemas = async() => {
        try {
            const { data: { data } } = await axios.get(route('obtenerSubsistemasEquiposCliente'), {
                params: {
                    IDSistema: form.IDSistema
                }
            })
            subsistemas.value = data
        } catch (error) {

        }
    }
    const obtenerEquipoOnliy = async(IDEquipo) => {
        try {
            const { data: { data } } = await axios.get(route('equipos.show', IDEquipo))
            equipo.value = data
        } catch (error) {

        }
    }

    const obtenerSubsistemasIndex = async() => {
        try {
            const { data: { data } } = await axios.get(route('obtenerSubsistemasEquiposCliente'), {
                params: {
                    IDSistema: filters.value.IDSistema
                }
            })
            subsistemasIndex.value = data
        } catch (error) {

        }
    }



    const obtenerTipos = async() => {
        try {
            const { data: { data } } = await axios.get(route('tipoEquipos.index'))
            tipos.value = data
        } catch (error) {

        }
    }


    const getPrediosCrear = async () => {

        try {

            const { data: { data } } = await axios.get(route('obtenerPrediosPermisoCrearEquipo'));

            prediosCrear.value = data;

            if(data.length == 1){
                form.IDPredio = data[0].IDPredio
                obtenerEdificios()
            }

        } catch (error) {

            console.error('Error al obtener los predios:', error);

        }
    }


    const cambiarPredio = () => {

        obtenerEdificios()
    }



    const cleanUrl = url => {
        return url?.replaceAll('&amp;', '&') ?? '';
    }

    const obtenerEdificios = async() => {
        try {
            loadingEdificios.value = true;
            form.IDEdificio =  '';
            const { data: { data } } = await axios.get(route('edificios.index'), {
                params: {
                    IDPredio: form.IDPredio
                }
            })
            edificios.value = data
            loadingEdificios.value = false;

            if(data.length == 1){
                form.IDEdificio = data[0].IDEdificio
                obtenerNiveles()
            }

        } catch (error) {
            loadingEdificios.value = false;
        }
    }

    const obtenerNiveles = async() => {
        try {
            loadingNivel.value = true;
            form.IDNivel =  '';
            const { data: { data } } = await axios.get(route('niveles.index'), {
                params: {
                    IDEdificio: form.IDEdificio
                }
            })
            niveles.value = data
            loadingNivel.value = false;

            if(data.length == 1){
                form.IDNivel = data[0].IDNivel
                obtenerZonas()
            }
        } catch (error) {
            loadingNivel.value = false;
        }
    }

    const obtenerZonas = async() => {
        try {
            loadingZona.value = true;
            form.IDZona = '';
            const { data: { data } } = await axios.get(route('zonas.index'), {
                params: {
                    IDNivel: form.IDNivel
                }
            })
            zonas.value = data
            loadingZona.value = false;


            if(data.length == 1){
                form.IDZona = data[0].IDZona
            }
        } catch (error) {
            loadingZona.value = false;
        }
    }


     const obtenerEdificiosIndex = async() => {
        try {
            filters.value.IDEdificio =  '';
            const { data: { data } } = await axios.get(route('edificios.index'), {
                params: {
                    IDPredio: filters.value.IDPredio
                }
            })
            edificiosIndex.value = data


        } catch (error) {
            console.log(error)
        }
    }

    const obtenerNivelesIndex = async() => {
        try {
            filters.value.IDNivel =  '';
            const { data: { data } } = await axios.get(route('niveles.index'), {
                params: {
                    IDEdificio: filters.value.IDEdificio
                }
            })
            nivelesIndex.value = data

        } catch (error) {
            console.log(error)
        }
    }

    const obtenerZonasIndex = async() => {
        try {

            filters.value.IDZona = '';
            const { data: { data } } = await axios.get(route('zonas.index'), {
                params: {
                    IDNivel: filters.value.IDNivel
                }
            })
            zonasIndex.value = data

        } catch (error) {
             console.log(error)
        }
    }


      const validateForm = () => {
        formErrors.value = {}

            // if (!form.NombreTicket) errors.value.NombreTicket = 'Este campo es requerido'
            if (!form.IDSistema) formErrors.value.IDSistema = true
            if (!form.IDSubsistema) formErrors.value.IDSubsistema = true
            if (!form.IDTipoEquipo) formErrors.value.IDTipoEquipo = true
            if (!form.NoSerieEquipo) formErrors.value.NoSerieEquipo = true
            if (!form.IdentificacionEquipo) formErrors.value.IdentificacionEquipo = true
            if (!form.ValorCompraEquipo) formErrors.value.ValorCompraEquipo = true

            if (!form.ValorResidualEquipo) formErrors.value.ValorResidualEquipo = true
            if (!form.DescripcionEquipo) formErrors.value.DescripcionEquipo = true
            if (!form.IDPredio) formErrors.value.IDPredio = true
            if (!form.IDEdificio) formErrors.value.IDEdificio = true
            if (!form.IDNivel) formErrors.value.IDNivel = true
            if (!form.IDZona) formErrors.value.IDZona = true
            if (!form.frecuenciaMantenimiento) formErrors.value.frecuenciaMantenimiento = true
            if (!form.FechaUltMantEquipo) formErrors.value.FechaUltMantEquipo = true
            if (!form.FechaCompraEquipo) formErrors.value.FechaCompraEquipo = true
            if (!form.FechaProxMantEquipo) formErrors.value.FechaProxMantEquipo = true


            return Object.keys(formErrors.value).length === 0
    };

    const limpiarFormulario = () => {
        Object.assign(form, JSON.parse(JSON.stringify(formOriginal)));
        formErrors.value = {}
        archivos.value = []
        sistemas.value = []
        subsistemas.value = []
        tipos.value = []
        prediosCrear.value = []
        edificios.value = []
        niveles.value = []
        zonas.value = []
    }

     const enviarFormulario = async () => {

            try {

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
                    if (!['documentos'].includes(key)) {
                        formData.append(key, form[key]);
                    }
                });

                let response;
                if (IsEditing.value) {
                    // PUT para editar
                    response = await axios.post(
                        route('equipos.update', form.IDEquipo),
                        formData,
                        { headers: { 'X-HTTP-Method-Override': 'PUT' } }
                    );
                } else {
                    // POST para crear
                    response = await axios.post(route('equipos.store'), formData);
                }
                const { data: { data } } = response;

                formErrors.value = {}

                limpiarFormulario()

                equipos.value.push(data)

                totalEquipos.value = equipos.value.length
                loadingStore.stopButtonLoading('crear')

                cerrarModal('modalCreatEquipo')

                datatableRef.value.ajax.reload();


            } catch (error) {
                loadingStore.stopButtonLoading('crear')
                console.log(error)
            }
        }

    const editarEquipo = async (ID) => {

        const equipo = equipos.value.find(equipo => equipo.IDEquipo == ID);

        console.log(equipo)
        abrirModal('modalCreatEquipo')
        await obtenerSistemas()

        form.IDSistema = equipo.subsistema.IDSistema;
        form.IDEquipo = equipo.IDEquipo;
        await obtenerSubsistemas();
        form.IDSubsistema = equipo.IDSubsistema;
        await obtenerTipos();
        form.IDTipoEquipo = equipo.IDTipoEquipo;
        cambiarTipo();
        form.NoSerieEquipo = equipo.NoSerieEquipo;
        form.IdentificacionEquipo = equipo.ClaveEquipo;
        form.ValorCompraEquipo = equipo.ValorCompraEquipo;
        form.ValorResidualEquipo = equipo.ValorResidualEquipo;
        form.DescripcionEquipo = equipo.DescripcionEquipo;
        await getPrediosCrear();
        form.IDPredio = equipo.IDPredio;
        await obtenerEdificios();
        form.IDEdificio = equipo.IDEdificio;
        await obtenerNiveles();
        form.IDNivel = equipo.IDNivel;
        await obtenerZonas();
        form.IDZona = equipo.IDZona;
        form.frecuenciaMantenimiento = equipo.frecuenciaMantenimiento;
        form.FechaUltMantEquipo = equipo.FechaUltMantEquipo;
        form.FechaCompraEquipo = equipo.FechaCompraEquipo;
        form.FechaProxMantEquipo = equipo.FechaProxMantEquipo;
        form.ComentariosEquipo = equipo.ComentariosEquipo;

        // form.documentos = equipo.documentos || []; // Asegurarse de que documentos sea un array
        archivos.value = equipo.documentos
        IsEditing.value = true


    }

    window.vueMethods = {
        editarEquipo,
    };




    const cambiarTipo = () => {

        const tipoSeleccionado = tipos.value.find(tipo => tipo.IDTipoEquipo === form.IDTipoEquipo);

        if (tipoSeleccionado) {
            tipo.marca = tipoSeleccionado.MarcaTipoEquipo;
            tipo.modelo = tipoSeleccionado.ModeloTipoEquipo;
            tipo.clave = tipoSeleccionado.ClaveTipoEquipo;
            tipo.frecuenciaMantenimiento = '';
        }
    }

   const limpiarTipo = () => {
       form.IDTipoEquipo = null;
       tipo.marca = '';
       tipo.modelo = '';
       tipo.clave = '';
       tipo.frecuenciaMantenimiento = '';
   }


    return {
        obtenerDatatables,
        equipos,
        totalEquipos,
        datatableRef,
        sistemas,
        subsistemas,
        form,
        formOriginal,
        formErrors,
        obtenerSistemas,
        obtenerSubsistemas,
        obtenerTipos,
        tipos,
        archivos,
        prediosCrear,
        edificios,
        niveles,
        zonas,
        loadingEdificios,
        loadingNivel,
        loadingZona,
        getPrediosCrear,
        obtenerEdificios,
        obtenerNiveles,
        obtenerZonas,
        cambiarPredio,
        enviarFormulario,
        limpiarFormulario,
        tipo,
        cambiarTipo,
        limpiarTipo,
        IsEditing,
        cleanUrl,
        filters,
        obtenerSubsistemasIndex,
        subsistemasIndex,
        obtenerEdificiosIndex,
        edificiosIndex,
        obtenerNivelesIndex,
        nivelesIndex,
        obtenerZonasIndex,
        zonasIndex,
        equipo

    }
});
