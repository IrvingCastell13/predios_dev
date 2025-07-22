<template>
    <div class="container-fluid mt-4">
        <h1 class="mb-4 display-5">Dashboard de Mantenimiento de Equipos</h1>
        <p class="lead mb-5">Estado actual de las acciones de mantenimiento por equipo y predio.</p>

        <div class="row mb-5 align-items-end">
            <div class="col-md-3">
                 <label class="form-label fw-bold">Predio:</label>
                 <vue-multiselect
                    v-model="prediosSeleccionados"
                    :options="prediosDisponibles"
                    :multiple="true"
                    :close-on-select="false"
                    placeholder="Todos los predios"
                    label="NombrePredio"
                    track-by="IDPredio">
                </vue-multiselect>
            </div>
            <div class="col-md-3">
                 <label class="form-label fw-bold">Sistema:</label>
                 <vue-multiselect
                    v-model="sistemasSeleccionados"
                    :options="sistemasDisponibles"
                    :multiple="true"
                    :close-on-select="false"
                    placeholder="Todos los sistemas"
                    label="NombreSistema"
                    track-by="IDSistema">
                </vue-multiselect>
            </div>
            <div class="col-md-2">
                 <label class="form-label fw-bold">Subsistema:</label>
                 <vue-multiselect
                    v-model="subsistemasSeleccionados"
                    :options="subsistemasDisponibles"
                    :multiple="true"
                    :close-on-select="false"
                    :disabled="sistemasSeleccionados.length === 0"
                    placeholder="Seleccione sistema"
                    label="NombreSubsistema"
                    track-by="IDSubsistema">
                </vue-multiselect>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Desde:</label>
                <date-picker
                    v-model:value="fechaInicioSeleccionada"
                    format="YYYY-MM-DD"
                    value-type="format"
                    placeholder="Fecha inicio"
                    :disabled-date="disableStartDate">
                </date-picker>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Hasta:</label>
                <date-picker
                    v-model:value="fechaFinSeleccionada"
                    format="YYYY-MM-DD"
                    value-type="format"
                    placeholder="Fecha fin"
                    :disabled-date="disableEndDate">
                </date-picker>
            </div>
        </div>

        <div v-if="isReady">
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">Vista Detallada: Matriz de Estados</div>
                        <div class="card-body">
                            <matriz-estado-mantenimiento
                                :id-predios="idPrediosSeleccionados"
                                :id-sistemas="idSistemasSeleccionados"
                                :id-subsistemas="idSubsistemasSeleccionados"
                                :fecha-inicio="fechaInicioSeleccionada"
                                :fecha-fin="fechaFinSeleccionada"
                                api-url="/api/bi/estado-mantenimiento-equipos"
                                chart-title="Estado por Equipo vs. Predio">
                            </matriz-estado-mantenimiento>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">Vista de Tabla: Datos en Crudo</div>
                        <div class="card-body">
                            <tabla-mantenimiento
                               :id-predios="idPrediosSeleccionados"
                               :id-sistemas="idSistemasSeleccionados"
                               :id-subsistemas="idSubsistemasSeleccionados"
                               :fecha-inicio="fechaInicioSeleccionada"
                               :fecha-fin="fechaFinSeleccionada"
                               api-url="/api/bi/estado-mantenimiento-equipos">
                            </tabla-mantenimiento>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">Vista de Tabla tipo Matriz</div>
                        <div class="card-body">
                            <tabla-matriz-mantenimiento
                               :id-predios="idPrediosSeleccionados"
                               :id-sistemas="idSistemasSeleccionados"
                               :id-subsistemas="idSubsistemasSeleccionados"
                               :fecha-inicio="fechaInicioSeleccionada"
                               :fecha-fin="fechaFinSeleccionada"
                               api-url="/api/bi/estado-mantenimiento-equipos">
                            </tabla-matriz-mantenimiento>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="text-center text-secondary py-5">
            Cargando dashboard...
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import VueMultiselect from 'vue-multiselect';
import DatePicker from 'vue-datepicker-next';
import 'vue-datepicker-next/index.css';
import MatrizEstadoMantenimiento from './MatrizEstadoMantenimiento.vue';
import TablaMantenimiento from './TablaMantenimiento.vue';
import TablaMatrizMantenimiento from './TablaMatrizMantenimiento.vue';

export default {
    name: 'DashboardMantenimiento',
    components: {
        VueMultiselect,
        DatePicker,
        MatrizEstadoMantenimiento,
        TablaMantenimiento,
        TablaMatrizMantenimiento
    },
    data() {
        return {
            isReady: false,
            prediosDisponibles: [],
            sistemasDisponibles: [],
            subsistemasDisponibles: [],
            prediosSeleccionados: [],
            sistemasSeleccionados: [],
            subsistemasSeleccionados: [],
            fechaInicioSeleccionada: null,
            fechaFinSeleccionada: null
        };
    },
    computed: {
        idPrediosSeleccionados() {
            return this.prediosSeleccionados.map(p => p.IDPredio);
        },
        idSistemasSeleccionados() {
            return this.sistemasSeleccionados.map(s => s.IDSistema);
        },
        idSubsistemasSeleccionados() {
            return this.subsistemasSeleccionados.map(s => s.IDSubsistema);
        }
    },
    watch: {
        async sistemasSeleccionados(newSistemas, oldSistemas) {
            const newIds = newSistemas.map(s => s.IDSistema);
            
            // Limpia los subsistemas seleccionados que ya no pertenecen a los sistemas actuales
            this.subsistemasSeleccionados = this.subsistemasSeleccionados.filter(sub => {
                const subsistemaParentId = this.subsistemasDisponibles.find(s => s.IDSubsistema === sub.IDSubsistema)?.IDSistema;
                return newIds.includes(subsistemaParentId);
            });

            if (newSistemas && newSistemas.length > 0) {
                try {
                    const response = await axios.get('/api/bi/listar-subsistemas', {
                        params: { sistema_ids: newIds }
                    });
                    this.subsistemasDisponibles = response.data;
                } catch (error) {
                    console.error("No se pudo cargar la lista de subsistemas:", error);
                }
            } else {
                this.subsistemasDisponibles = [];
                this.subsistemasSeleccionados = [];
            }
        }
    },
    methods: {
        disableStartDate(date) {
            if (!this.fechaFinSeleccionada) return false;
            return date > new Date(this.fechaFinSeleccionada);
        },
        disableEndDate(date) {
            if (!this.fechaInicioSeleccionada) return false;
            return date < new Date(this.fechaInicioSeleccionada);
        }
    },
    async mounted() {
        try {
            const [prediosRes, sistemasRes] = await Promise.all([
                axios.get('/api/bi/listar-predios'),
                axios.get('/api/bi/listar-sistemas')
            ]);
            this.prediosDisponibles = prediosRes.data;
            this.sistemasDisponibles = sistemasRes.data;
        } catch (error) {
            console.error("No se pudo cargar los datos iniciales para los filtros:", error);
        } finally {
            this.isReady = true;
        }
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>