// resources/js/eventBus.js
import { reactive } from 'vue';

const state = reactive({
    IDPredioGlobal: localStorage.getItem('IDPredioGlobal') || null,
    IDClienteGlobal: localStorage.getItem('IDClienteGlobal') || null,
    IDPersonaGlobal: localStorage.getItem('IDPersonaGlobal') || null,
});

const methods = {
    setIDPredioGlobal(id) {
        state.IDPredioGlobal = id;
        localStorage.setItem('IDPredioGlobal', id);
    },
    setIDClienteGlobal(id) {
        state.IDClienteGlobal = id;
        localStorage.setItem('IDClienteGlobal', id);
    },
    setIDPersonaGlobal(id) {
        state.IDPersonaGlobal = id;
        localStorage.setItem('IDPersonaGlobal', id);
    }
};

export default {
    state,
    methods
};