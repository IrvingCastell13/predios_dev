<template>
    <div class="container-fluid mt-4">
        <h1 class="mb-4 display-5">Dashboard de Renovación</h1>
        <p class="lead mb-5">Estado actual de las acciones de renovación por documento y predio.</p>

        <div class="row mb-5 align-items-end">
            <div class="col-md-3">
                 <label class="form-label fw-bold">Predio:</label>
                 <vue-multiselect
                    v-model="prediosSeleccionados"
                    :options="prediosDisponibles"
                    :multiple="true"
                    :close-on-select="false"
                    placeholder="Todos"
                    label="NombrePredio"
                    track-by="IDPredio">
                </vue-multiselect>
            </div>
            <div class="col-md-3">
                 <label class="form-label fw-bold">Categoría (Grupo):</label>
                 <vue-multiselect
                    v-model="gruposSeleccionados"
                    :options="gruposDisponibles"
                    :multiple="true"
                    :close-on-select="false"
                    placeholder="Todas"
                    label="NombreGrupoDoc"
                    track-by="IDGrupoDoc">
                </vue-multiselect>
            </div>
            <div class="col-md-2">
                 <label class="form-label fw-bold">Subcategoría:</label>
                 <vue-multiselect
                    v-model="categoriasSeleccionadas"
                    :options="categoriasDisponibles"
                    :multiple="true"
                    :close-on-select="false"
                    :disabled="gruposSeleccionados.length === 0"
                    placeholder="Seleccione categoría"
                    label="NombreCategoriaDoc"
                    track-by="IDCategoriaDoc">
                </vue-multiselect>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Desde:</label>
                <date-picker v-model:value="fechaInicioSeleccionada" format="YYYY-MM-DD" value-type="format" placeholder="Fecha inicio"></date-picker>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Hasta:</label>
                <date-picker v-model:value="fechaFinSeleccionada" format="YYYY-MM-DD" value-type="format" placeholder="Fecha fin"></date-picker>
            </div>
        </div>

        <div v-if="isReady">
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">Vista Detallada: Matriz de Estados</div>
                        <div class="card-body">
                            <matriz-estado-renovacion
                                :id-predios="idPrediosSeleccionados"
                                :id-grupos="idGruposSeleccionados"
                                :id-categorias="idCategoriasSeleccionadas"
                                :fecha-inicio="fechaInicioSeleccionada"
                                :fecha-fin="fechaFinSeleccionada"
                                api-url="/api/bi/estado-renovacion-documentos"
                                chart-title="Estado por Documento vs. Predio">
                            </matriz-estado-renovacion>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">Vista de Tabla: Datos en Crudo</div>
                        <div class="card-body">
                            <tabla-renovacion
                               :id-predios="idPrediosSeleccionados"
                               :id-grupos="idGruposSeleccionados"
                               :id-categorias="idCategoriasSeleccionadas"
                               :fecha-inicio="fechaInicioSeleccionada"
                               :fecha-fin="fechaFinSeleccionada"
                               api-url="/api/bi/estado-renovacion-documentos">
                            </tabla-renovacion>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">Vista de Tabla tipo Matriz</div>
                        <div class="card-body">
                            <tabla-matriz-renovacion
                               :id-predios="idPrediosSeleccionados"
                               :id-grupos="idGruposSeleccionados"
                               :id-categorias="idCategoriasSeleccionadas"
                               :fecha-inicio="fechaInicioSeleccionada"
                               :fecha-fin="fechaFinSeleccionada"
                               api-url="/api/bi/estado-renovacion-documentos">
                            </tabla-matriz-renovacion>
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
import MatrizEstadoRenovacion from './MatrizEstadoRenovacion.vue';
import TablaRenovacion from './TablaRenovacion.vue';
import TablaMatrizRenovacion from './TablaMatrizRenovacion.vue';

export default {
    name: 'DashboardRenovacion',
    components: {
        VueMultiselect,
        DatePicker,
        MatrizEstadoRenovacion,
        TablaRenovacion,
        TablaMatrizRenovacion
    },
    data() {
        return {
            isReady: false,
            prediosDisponibles: [],
            gruposDisponibles: [],
            categoriasDisponibles: [],
            prediosSeleccionados: [],
            gruposSeleccionados: [],
            categoriasSeleccionadas: [],
            fechaInicioSeleccionada: null,
            fechaFinSeleccionada: null
        };
    },
    computed: {
        idPrediosSeleccionados() {
            return this.prediosSeleccionados.map(p => p.IDPredio);
        },
        idGruposSeleccionados() {
            return this.gruposSeleccionados.map(g => g.IDGrupoDoc);
        },
        idCategoriasSeleccionadas() {
            return this.categoriasSeleccionadas.map(c => c.IDCategoriaDoc);
        }
    },
    watch: {
        async gruposSeleccionados(newGrupos) {
            const newGrupoIds = newGrupos.map(g => g.IDGrupoDoc);

            if (newGrupos && newGrupos.length > 0) {
                try {
                    const response = await axios.get('/api/bi/listar-categorias-doc', {
                        params: { grupo_ids: newGrupoIds }
                    });
                    this.categoriasDisponibles = response.data;

                    // Limpia las subcategorías seleccionadas que ya no pertenecen a los grupos actuales
                    this.categoriasSeleccionadas = this.categoriasSeleccionadas.filter(cat => 
                        this.categoriasDisponibles.some(dispo => dispo.IDCategoriaDoc === cat.IDCategoriaDoc)
                    );
                } catch (error) {
                    console.error("No se pudo cargar la lista de subcategorías:", error);
                }
            } else {
                this.categoriasDisponibles = [];
                this.categoriasSeleccionadas = [];
            }
        }
    },
    async mounted() {
        try {
            const [prediosRes, gruposRes] = await Promise.all([
                axios.get('/api/bi/listar-predios'),
                axios.get('/api/bi/listar-grupos-doc')
            ]);
            this.prediosDisponibles = prediosRes.data;
            this.gruposDisponibles = gruposRes.data;
        } catch (error) {
            console.error("Error al cargar filtros:", error);
        } finally {
            this.isReady = true;
        }
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>