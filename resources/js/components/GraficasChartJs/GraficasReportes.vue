<template>
    <div>
        <h1 class="mb-4 display-5">Dashboard de Cumplimiento</h1>
        <p class="lead mb-5">Resumen visual del estado de la documentación por predio y categoría.</p>


        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Filtro por Subcategoría:</label>
                        <vue-multiselect
                            v-model="prediosSeleccionadosFila1"
                            :options="prediosDisponibles"
                            :multiple="true"
                            :close-on-select="false"
                            placeholder="Seleccione predios..."
                            label="NombrePredio"
                            track-by="IDPredio">
                        </vue-multiselect>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">Matriz de Cumplimiento por SubCategoría</div>
                    <div class="card-body">
                        <base-heatmap-matrix
                            :id-predios="idPrediosSeleccionadosFila1"
                            api-url="/api/bi/matriz-subcategoria" 
                            chart-title="Cumplimiento por Predio vs. SubCategoría">
                        </base-heatmap-matrix>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">Matriz de Archivos Adjuntos por SubCategoría</div>
                    <div class="card-body">
                        <base-heatmap-matrix-archivos
                            :id-predios="idPrediosSeleccionadosFila1"
                            api-url="/api/bi/matriz-subcategoria-archivos"
                            chart-title="SubCategorías con o sin archivo adjunto por Predio">
                        </base-heatmap-matrix-archivos>
                    </div>
                </div>
            </div>
        </div>

         <div class="card my-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Filtro por Categoría:</label>
                        <vue-multiselect
                            v-model="prediosSeleccionadosFila2"
                            :options="prediosDisponibles"
                            :multiple="true"
                            :close-on-select="false"
                            placeholder="Seleccione predios..."
                            label="NombrePredio"
                            track-by="IDPredio">
                        </vue-multiselect>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">Matriz de Cumplimiento por Categoria</div>
                    <div class="card-body">
                        <matriz-por-grupo 
                            :id-predios="idPrediosSeleccionadosFila2"
                            api-url="/api/bi/matriz-grupo" 
                            chart-title="Cumplimiento por Predio vs. Categoria de Documento">
                        </matriz-por-grupo>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">Matriz de Archivos por Categoria</div>
                    <div class="card-body">
                        <matriz-archivos-por-grupo
                            :id-predios="idPrediosSeleccionadosFila2"
                            api-url="/api/bi/matriz-grupo-archivos"
                            chart-title="Archivos adjuntos por Predio vs. Categoria de Documento">
                        </matriz-archivos-por-grupo>
                    </div>
                </div>
            </div>
        </div>


        <div class="card my-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Filtro para Resumen General:</label>
                        <vue-multiselect
                            v-model="prediosSeleccionadosFila3"
                            :options="prediosDisponibles"
                            :multiple="true"
                            :close-on-select="false"
                            placeholder="Seleccione predios..."
                            label="NombrePredio"
                            track-by="IDPredio">
                        </vue-multiselect>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Resumen de Cumplimiento General por Predio</div>
                    <div class="card-body">
                        <horizontal-ponderado-por-predio
                            :id-predios="idPrediosSeleccionadosFila3"
                            api-url="/api/bi/calificaciones-ponderadas"
                            chart-title="Cumplimiento ponderado por predio (%)">
                        </horizontal-ponderado-por-predio>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import VueMultiselect from 'vue-multiselect';
import BaseHeatmapMatrix from './BaseHeatmapMatrix.vue';
import BaseHeatmapMatrixArchivos from './BaseHeatmapMatrixArchivos.vue';
import HorizontalPonderadoPorPredio from './HorizontalPonderadoPorPredio.vue';
import MatrizPorGrupo from './MatrizPorGrupo.vue';
import MatrizArchivosPorGrupo from './MatrizArchivosPorGrupo.vue';

export default {
    components: {
        VueMultiselect,
        BaseHeatmapMatrix,
        BaseHeatmapMatrixArchivos,
        HorizontalPonderadoPorPredio,
        MatrizPorGrupo,
        MatrizArchivosPorGrupo
    },
     data() {
        return {
            prediosDisponibles: [],
            prediosSeleccionadosFila1: [],
            prediosSeleccionadosFila2: [],
            prediosSeleccionadosFila3: []
        };
    },
    computed: {
        // --- INICIA CORRECCIÓN ---
        // Aquí estaba el error. Estas funciones estaban vacías.
        // Ahora, cada una procesa su respectiva lista de predios seleccionados.
        idPrediosSeleccionadosFila1() {
            return this.prediosSeleccionadosFila1.map(p => p.IDPredio);
        },
        idPrediosSeleccionadosFila2() {
            return this.prediosSeleccionadosFila2.map(p => p.IDPredio);
        },
        // --- TERMINA CORRECCIÓN ---
        idPrediosSeleccionadosFila3() {
            return this.prediosSeleccionadosFila3.map(p => p.IDPredio);
        }
    },
    async mounted() {
        try {
            const response = await axios.get('/api/bi/listar-predios');
            this.prediosDisponibles = response.data;
        } catch (error) {
            console.error("No se pudo cargar la lista de predios:", error);
        }
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>