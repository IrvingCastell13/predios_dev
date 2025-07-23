<template>
    <div>
        <div v-if="isLoading" class="text-center text-secondary py-4">
            Cargando datos de la tabla...
        </div>
        <div v-else-if="documentos.length === 0" class="text-center text-secondary py-4">
            No hay datos para la selección actual.
        </div>
        <div v-else class="table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Predio</th>
                        <th scope="col">Categoría</th>
                        <th scope="col">Subcategoría</th>
                        <th scope="col">Tipo de Documento</th>
                        <th scope="col">Estado Documento</th>
                        <th scope="col">Estado Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(doc, index) in documentos" :key="index" :class="getRowClass(doc)">
                        <td>{{ doc.predio }}</td>
                        <td>{{ doc.categoria }}</td>
                        <td>{{ doc.subcategoria }}</td>
                        <td>{{ doc.tipo_documento }}</td>
                        <td>{{ doc.estado_documento }}</td>
                        <td>{{ doc.estado_accion || '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'VigenciaTablaDetallada',
    props: {
        apiUrl: { type: String, required: true },
        idPredios: { type: Array, required: true },
        idGrupos: { type: Array, required: true },
        idCategorias: { type: Array, required: true },
        idTiposDocumento: { type: Array, required: true },
        idTiposInmueble: { type: Array, required: true }
    },
    data() {
        return {
            documentos: [],
            isLoading: true,
            cancelTokenSource: null
        };
    },
    computed: {
        filtros() {
            return {
                predios: this.idPredios,
                grupos: this.idGrupos,
                categorias: this.idCategorias,
                tiposDocumento: this.idTiposDocumento,
                tiposInmueble: this.idTiposInmueble,
            };
        }
    },
    watch: {
        filtros: {
            handler() { this.fetchData(); },
            deep: true,
            immediate: true
        }
    },
    methods: {
        async fetchData() {
            this.isLoading = true;
            if (this.cancelTokenSource) {
                this.cancelTokenSource.cancel('Nueva solicitud iniciada.');
            }
            this.cancelTokenSource = axios.CancelToken.source();

            try {
                const response = await axios.get(this.apiUrl, {
                    params: {
                        predio_ids: this.idPredios,
                        grupo_ids: this.idGrupos,
                        categoria_ids: this.idCategorias,
                        tipo_doc_ids: this.idTiposDocumento,
                        tipo_inmueble_ids: this.idTiposInmueble,
                    },
                    cancelToken: this.cancelTokenSource.token
                });
                this.documentos = response.data;
            } catch (error) {
                if (axios.isCancel(error)) {
                    console.log('Solicitud cancelada:', error.message);
                } else {
                    console.error('Error al cargar datos para la tabla detallada:', error);
                }
            } finally {
                this.isLoading = false;
            }
        },
        getRowClass(doc) {
            if (doc.estado_documento === 'FALTANTE') {
                return 'table-danger';
            }
            if (doc.estado_accion === 'Ejecutado') {
                return 'table-success';
            }
            if (doc.estado_accion === 'Pendiente') {
                return 'table-warning';
            }
            if (doc.estado_accion === 'En Proceso') {
                return 'table-info';
            }
            return '';
        }
    }
};
</script>