import { defineStore } from 'pinia';
import { computed } from 'vue';
import { ref } from 'vue';
import eventBus from '../../eventBus';
import { useAuthStore } from '../auth/Auth';

export const useFuncionesStore = defineStore('Funciones', () => {

    const IDPredioGlobal = ref(localStorage.getItem('IDPredioGlobal'))
    const IDClienteGlobal = computed(() => eventBus.state.IDClienteGlobal);

    // const IDPersona = ref(localStorage.getItem('IDPersonaGlobal'))

    const funciones = async () => {
        try {

            const user = JSON.parse(localStorage.getItem('user')) || null;

            if(user.IDRol == 1){

                    const {data: {data}} = await axios.get(route('obtenerFunciones'), {
                        params: {
                            IDPersona: localStorage.getItem('IDPersonaGlobal'),
                            IDCliente: localStorage.getItem('IDClienteGlobal'),
                            IDPredio: localStorage.getItem('IDPredioGlobal')
                        }
                    });


                    localStorage.setItem('funciones', JSON.stringify(data));
            }else{


                    const {data: {data}} = await axios.get(route('obtenerFunciones'), {
                        params: {
                            IDPredio: localStorage.getItem('IDPredioGlobal')
                        }
                    });

                    localStorage.setItem('funciones', JSON.stringify(data));
                }



        } catch (error) {
            console.log(error.response)
        //   throw new Error('Invalid login');
        }
      };


    return {
        funciones
    }
});
