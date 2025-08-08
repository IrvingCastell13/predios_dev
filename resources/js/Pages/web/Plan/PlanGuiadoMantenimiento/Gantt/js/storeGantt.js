import { defineStore } from 'pinia';
import { ref, reactive, computed, watch } from 'vue';
import { useLoadingStore } from '../../../../../../stores/loading';
import axios from 'axios';

export const usestoreGanttStore = defineStore('storeGantt', () => {

    const plan = ref({});
    const html = ref(null);
    const ganttData = ref({ Equipos: [] });
    const ganttCurrentDate = ref(new Date());

    const filtros = reactive({
        predios: [],
        edificios: [],
        niveles: [],
        zonas: [],
        sistemas: [],
        subsistemas: [],
        responsables: [],
    });

    const opcionesMaster = reactive({
        predios: [],
        edificios: [],
        niveles: [],
        zonas: [],
        sistemas: [],
        subsistemas: [],
        responsables: [],
    });

    // --- LÓGICA DE CASCADA MEJORADA ---

    const edificiosOptions = computed(() => {
        const prediosSeleccionadosIds = filtros.predios.map(p => p.IDPredio);
        if (prediosSeleccionadosIds.length === 0) {
            return opcionesMaster.edificios;
        }
        return opcionesMaster.edificios.filter(e => prediosSeleccionadosIds.includes(e.IDPredio));
    });

    const nivelesOptions = computed(() => {
        const edificiosSeleccionadosIds = filtros.edificios.map(e => e.IDEdificio);
        if (edificiosSeleccionadosIds.length > 0) {
            return opcionesMaster.niveles.filter(n => edificiosSeleccionadosIds.includes(n.IDEdificio));
        }
        const edificiosDisponiblesIds = edificiosOptions.value.map(e => e.IDEdificio);
        if (edificiosDisponiblesIds.length > 0) {
            return opcionesMaster.niveles.filter(n => edificiosDisponiblesIds.includes(n.IDEdificio));
        }
        return opcionesMaster.niveles;
    });

    const zonasOptions = computed(() => {
        const nivelesSeleccionadosIds = filtros.niveles.map(n => n.IDNivel);
        if (nivelesSeleccionadosIds.length > 0) {
            return opcionesMaster.zonas.filter(z => nivelesSeleccionadosIds.includes(z.IDNivel));
        }
        const nivelesDisponiblesIds = nivelesOptions.value.map(n => n.IDNivel);
        if (nivelesDisponiblesIds.length > 0) {
            return opcionesMaster.zonas.filter(z => nivelesDisponiblesIds.includes(z.IDNivel));
        }
        return opcionesMaster.zonas;
    });

    // --- NUEVA LÓGICA PARA SISTEMAS Y SUBSISTEMAS ---
    const subsistemasOptions = computed(() => {
        const sistemasSeleccionadosIds = filtros.sistemas.map(s => s.IDSistema);
        if (sistemasSeleccionadosIds.length === 0) {
            // Si no hay sistema seleccionado, no mostrar subsistemas.
            return [];
        }
        // Filtrar subsistemas basados en los sistemas seleccionados
        return opcionesMaster.subsistemas.filter(sub => sistemasSeleccionadosIds.includes(sub.IDSistema));
    });

    const opciones = reactive({
        predios: computed(() => opcionesMaster.predios),
        sistemas: computed(() => opcionesMaster.sistemas), // <-- NUEVO
        subsistemas: subsistemasOptions, // <-- MODIFICADO
        edificios: edificiosOptions,
        niveles: nivelesOptions,
        zonas: zonasOptions,
        responsables: computed(() => opcionesMaster.responsables),
    });

    const cargarTodasLasOpciones = async () => {
        try {
            const [prediosRes, edificiosRes, nivelesRes, zonasRes, subsistemasRes, sistemasRes, responsablesRes] = await Promise.all([
                axios.get('/api/filtros/predios'),
                axios.get('/api/filtros/edificios'),
                axios.get('/api/filtros/niveles'),
                axios.get('/api/filtros/zonas'),
                axios.get('/api/filtros/subsistemas'),
                axios.get('/api/filtros/sistemas-con-subsistemas'),
                axios.get('/api/filtros/responsables')
            ]);
            opcionesMaster.predios = prediosRes.data.data;
            opcionesMaster.edificios = edificiosRes.data.data;
            opcionesMaster.niveles = nivelesRes.data.data;
            opcionesMaster.zonas = zonasRes.data.data;
            opcionesMaster.subsistemas = subsistemasRes.data.data;
            opcionesMaster.sistemas = sistemasRes.data.data;
            opcionesMaster.responsables = responsablesRes.data.data;
        } catch (error) {
            console.error("Error crítico cargando todas las opciones de filtros:", error);
        }
    };

    const id = ref(null);
    const loadingStore = useLoadingStore();
    const getPlan = async (id) => { try { const { data: { data } } = await axios.get(`/api/planes/${id}`); plan.value = data; } catch (e) { } };

    const buildFilterParams = () => ({
        id_predio: (filtros.predios || []).map(p => p.IDPredio),
        id_edificio: (filtros.edificios || []).map(e => e.IDEdificio),
        id_nivel: (filtros.niveles || []).map(n => n.IDNivel),
        id_zona: (filtros.zonas || []).map(z => z.IDZona),
        id_sistema: (filtros.sistemas || []).map(s => s.IDSistema), 
        id_subsistema: (filtros.subsistemas || []).map(s => s.IDSubsistema),
        id_responsable: (filtros.responsables || []).map(r => r.IDPersona),
    });

    const processGanttData = (data) => { if (!data || !data.Equipos) { return { Equipos: [] }; } data.Equipos.forEach(equipo => { equipo.accionesPorFecha = {}; if (equipo.acciones) { equipo.acciones.forEach(accion => { if (accion.FechaInicioAccion && accion.FechaFinAccion) { let currentDate = new Date(accion.FechaInicioAccion + 'T00:00:00'); let endDate = new Date(accion.FechaFinAccion + 'T00:00:00'); while (currentDate <= endDate) { const dateString = currentDate.toISOString().split('T')[0]; equipo.accionesPorFecha[dateString] = accion; currentDate.setDate(currentDate.getDate() + 1); } } }); } }); return data; };

    const getGanttData = async (planId) => {
        try {
            loadingStore.startLoading();
            const currentDate = ganttCurrentDate.value;
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const fechaInicio = `${year}-${String(month + 1).padStart(2, '0')}-01`;
            const fechaFin = new Date(year, month + 1, 0).toISOString().split('T')[0];
            const params = { fecha_inicio: fechaInicio, fecha_fin: fechaFin, ...buildFilterParams() };
            const { data } = await axios.get(`/api/obtnerGanttPlanEquipos/${planId}`, { params });
            ganttData.value = processGanttData(data.data);
        } catch (error) {
            console.error("Error en getGanttData:", error);
            ganttData.value = { Equipos: [] };
        } finally {
            loadingStore.stopLoading();
        }
    };

    const getGanttDataForYear = async (planId) => {
        try {
            loadingStore.startLoading();
            const year = ganttCurrentDate.value.getFullYear();
            const fechaInicio = `${year}-01-01`;
            const fechaFin = `${year}-12-31`;
            const params = { fecha_inicio: fechaInicio, fecha_fin: fechaFin, ...buildFilterParams() };
            const { data } = await axios.get(`/api/obtnerGanttPlanEquipos/${planId}`, { params });
            ganttData.value = processGanttData(data.data);
        } catch (error) {
            console.error("Error en getGanttDataForYear:", error);
            ganttData.value = { Equipos: [] };
        } finally {
            loadingStore.stopLoading();
        }
    };

    // --- NUEVO WATCH PARA LA CASCADA SISTEMA -> SUBSISTEMA ---
    watch(() => filtros.sistemas, () => {
        // Limpia los subsistemas seleccionados si los sistemas cambian
        filtros.subsistemas = [];
    }, { deep: true });

    return {
        getPlan, ganttData, getGanttData, getGanttDataForYear, plan, id, html,
        filtros,
        opciones,
        cargarTodasLasOpciones,
        ganttCurrentDate,
    }
});