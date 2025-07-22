
import { defineStore } from 'pinia';
import { computed, watch } from 'vue';
import { reactive } from 'vue';
import { ref } from 'vue';

export const usecodigoBarrasStore = defineStore('codigoBarras', () => {


     const predios = ref([])
           const edificios = ref([])
           const niveles = ref([])
           const zonas = ref([])
           const equipos = ref([])
           const searchSeleccionados = ref('');
           const equiposAgregados = ref([]);

           const sistemas = ref([]);
           const subsistemas = ref([]);
           const tiposEquipos = ref([]);

           const filtros = reactive({
               IDPredio: '',
               IDEdificio: '',
               IDNivel: '',
               IDZona: '',
               IDSistema: '',
               IDSubsistema: '',
               IDTipoEquipo: '',
               search: ''
           });



           const obtenerPredios = async() => {

               try {
                   const { data: { data } } = await axios.get(route('obtenerPrediosPermisoCrearEquipo'))
                   predios.value = data

                   if(data.length > 0){
                       filtros.IDPredio = data[0].IDPredio
                   }
               } catch (error) {
                   console.log(error)
               }
           }



           const obtenerEdificios = async() => {
               try {

                   filtros.IDEdificio =  '';
                   const { data: { data } } = await axios.get(route('edificios.index'), {
                       params: {
                           IDPredio: filtros.IDPredio
                       }
                   })
                   edificios.value = data


                   // if(data.length == 1){
                   //     filtros.IDEdificio = data[0].IDEdificio
                   //     obtenerNiveles()
                   // }

               } catch (error) {
                   console.log(error)
               }
           }

           const obtenerNiveles = async() => {
               try {

                   filtros.IDNivel =  '';
                   const { data: { data } } = await axios.get(route('niveles.index'), {
                       params: {
                           IDEdificio: filtros.IDEdificio
                       }
                   })
                   niveles.value = data

                   // if(data.length == 1){
                   //     filtros.IDNivel = data[0].IDNivel
                   //     obtenerZonas()
                   // }
               } catch (error) {
                   console.log(error)
               }
           }

           const obtenerZonas = async() => {
               try {

                   filtros.IDZona = '';
                   const { data: { data } } = await axios.get(route('zonas.index'), {
                       params: {
                           IDNivel: filtros.IDNivel
                       }
                   })
                   zonas.value = data

                   // if(data.length == 1){
                   //     filtros.IDZona = data[0].IDZona
                   // }
               } catch (error) {
                   console.log(error)
               }
           }


            const obtenerEquipos = async() => {
                try {
                    const { data: { data } } = await axios.get(route('obtenerEquiposCliente'), {
                        params: {
                            ...filtros
                        }
                    })
                    equipos.value = data

                } catch (error) {

                }
            }



           const equiposAgregadosFiltrados = computed(() => {

               if (!searchSeleccionados.value) {
                   return equiposAgregados.value;
               }
               return equiposAgregados.value.filter(doc =>
                   doc.DescripcionEquipo.toLowerCase().includes(searchSeleccionados.value.toLowerCase())
               );
           });

           const getSistemas = async () => {
               try {

                //    const { data : { data } } = await axios.get(route('obtenerCategoriasDocumentosCliente'));
                   const { data : { data } } = await axios.get(route('obtenerSistemasEquiposCliente'));

                   sistemas.value = data.map(sis => ({
                       ...sis,
                       label: `${sis.NombreSistema}`
                   }));
               } catch (error) {
                   console.error(error);
               }
           };


           watch(
               () => [filtros.IDPredio, filtros.IDCategoria, filtros.IDSubcategoria, filtros.IDTipoDocumento, filtros.IDEdificio, filtros.IDNivel, filtros.IDZona, filtros.IDContrato],
               () => {
                 obtenerEquipos();
               }
             );


           const getSubsistemas = async () => {
               try {

                   const { data : { data } } = await axios.get(route('obtenerSubsistemasEquiposCliente'), {
                //    const { data : { data } } = await axios.get(route('obtenerSubCategoriasDocumentosCliente'), {
                       params: {
                          IDSistema: filtros.IDSistema,
                        },

                   });

                   subsistemas.value = data.map(sub => ({
                       ...sub,
                       label: `${sub.NombreSubsistema}`
                   }));


               } catch (error) {
                   console.error(error);
               }
           };


           const getTiposEquipos = async () => {
               try {

                   //    const { data : { data } } = await axios.get(route('obtenerTipoDocumentosCliente'), {
                   const { data : { data } } = await axios.get(route('obtenerTipoEquiposCliente'));

                   tiposEquipos.value = data.map(sub => ({
                       ...sub,
                       label: `${sub.NombreTipoEquipo}`
                   }));
               } catch (error) {
                   console.error(error);
               }
           };



           return {
               obtenerPredios,
               predios,
               filtros,
               obtenerEquipos,
               equipos,
               equiposAgregadosFiltrados,
               equiposAgregados,
               obtenerEdificios,
               edificios,
               obtenerNiveles,
               niveles,
               obtenerZonas,
               zonas,
               getSistemas,
               getSubsistemas,
               sistemas,
               subsistemas,
               getTiposEquipos,
               tiposEquipos,
               searchSeleccionados

           }
});
