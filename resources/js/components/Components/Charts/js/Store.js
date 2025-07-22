import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useStoreStore = defineStore('Store', () => {

    const loadingE = ref(false);
    const loadingN = ref(false);
    const loadingZ = ref(false);
    const filters = ref({

        predio: '',
        edificio: '',
        nivel: '',
        zona: '',
        persona: '',
        persona_responsable: '',
        estado: '',
    })

        const catalogos = ref({
          predios: [],

        })
      const estadosInstancia = ref([]);

        const obtenerEstadosInstanciaTickets = async() =>{

            const { data: { data } } = await axios.get(route('obtenerEstadosInstanciaTicketsBi'))

            estadosInstancia.value = data;

        }



        const getPredios = async () => {

            try {
                const { data: { data } } = await axios.get(route('obtenerPrediosPermisoVerBi'));

                catalogos.value.predios = data;

            } catch (error) {
                console.error('Error al obtener los predios:', error);

            }
        }

        const obtenerEdificios = async () => {
            try {
                loadingE.value = true

                const { data: { data } } = await axios.get(route('obtenerEdificiosPrediosMultiple'), {
                    params: {
                        IDPredio: filters.value.predio
                    }
                })
                catalogos.value.edificios = data;

                loadingE.value = false
            } catch (error) {
                loadingE.value = false
                console.error('Error al obtener los edificios:', error);
            }
        }

        const obtenerNiveles = async () => {
            try {
                loadingN.value = true

                const { data: { data } } = await axios.get(route('obtenerNivelesEdificiosMultiple'), {
                    params: {
                        IDEdificio: filters.value.edificio
                    }
                })
                catalogos.value.niveles = data;
                loadingN.value = false
            } catch (error) {
                loadingN.value = false
            }
        }

        const obtenerZonas = async () => {

            try {

                loadingZ.value = true

                const { data: { data } } = await axios.get(route('obtenerZonasNivelesMultiple'), {
                    params: {
                        IDNivel: filters.value.nivel
                    }
                })
                catalogos.value.zonas = data
                loadingZ.value = false
            } catch (error) {
                loadingZ.value = false
                console.error('Error al obtener las zonas:', error);
            }
        }


    return {
        filters,
        catalogos,
        estadosInstancia,
        obtenerEstadosInstanciaTickets,
        getPredios,
        obtenerEdificios,
        obtenerNiveles,
        obtenerZonas,
        loadingE,
        loadingN,
        loadingZ
    }
});
