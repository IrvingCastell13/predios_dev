<template>
    <div>
        <!-- SECCIÓN DE CARGA Y DATOS VACÍOS (Sin cambios) -->
        <div v-if="isLoading" class="text-center text-secondary py-4">
            Cargando datos de la tabla...
        </div>
        <div v-else-if="documentos.length === 0" class="text-center text-secondary py-4">
            No hay datos para la selección actual.
        </div>

        <!-- CONTENEDOR DE LA TABLA -->
        <!-- La tabla ahora tiene un 'ref' para poder ser seleccionada por DataTables -->
        <div v-else class="table-responsive">
            <table ref="tablaDetallada" class="table table-striped table-hover table-sm" style="width:100%">
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
                    <!-- Vue sigue renderizando las filas como antes -->
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
// Importamos nextTick de Vue para asegurarnos de que el DOM está listo
import { nextTick } from 'vue';

// Asegúrate de que jQuery está disponible globalmente (lo estará si usaste el CDN)
const $ = window.jQuery;

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
            cancelTokenSource: null,
            dataTableInstance: null, // Guardará la instancia de DataTables
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
        // El watcher se mantiene, es el que dispara la actualización de datos
        filtros: {
            handler() { this.fetchData(); },
            deep: true,
            immediate: true
        }
    },
    methods: {
        async fetchData() {
            this.isLoading = true;
            
            // Destruimos la instancia anterior de DataTables antes de una nueva búsqueda
            // para evitar errores y duplicados.
            if (this.dataTableInstance) {
                this.dataTableInstance.destroy();
                this.dataTableInstance = null;
            }

            // Limpiamos los documentos para que la tabla desaparezca mientras se cargan los nuevos datos
            this.documentos = [];

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

                // Usamos nextTick para esperar a que Vue actualice el DOM con los nuevos datos.
                // Solo después de eso, inicializamos DataTables.
                await nextTick();
                
                if (this.documentos.length > 0) {
                    this.initDataTable();
                }

            } catch (error) {
                if (axios.isCancel(error)) {
                    console.log('Solicitud cancelada:', error.message);
                } else {
                    console.error('Error al cargar datos para la tabla detallada:', error);
                    this.documentos = [];
                }
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Inicializa la librería DataTables en nuestra tabla.
         */
        initDataTable() {
            // Usamos el 'ref' de la tabla para inicializar DataTables
            this.dataTableInstance = $(this.$refs.tablaDetallada).DataTable({
                responsive: true,
                pageLength: 10, // Número de registros por página por defecto
                lengthMenu: [10, 25, 50, 100], // Opciones para el usuario
                language: { // Traducción al español
                    "sProcessing":     "Procesando...",
                    "sLengthMenu":     "Mostrar _MENU_ registros",
                    "sZeroRecords":    "No se encontraron resultados",
                    "sEmptyTable":     "Ningún dato disponible en esta tabla",
                    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix":    "",
                    "sSearch":         "Buscar:",
                    "sUrl":            "",
                    "sInfoThousands":  ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst":    "Primero",
                        "sLast":     "Último",
                        "sNext":     "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                }
            });
        },

        getRowClass(doc) {
            if (doc.estado_documento === 'FALTANTE') return 'table-danger';
            if (doc.estado_accion === 'Ejecutado') return 'table-success';
            if (doc.estado_accion === 'Pendiente') return 'table-warning';
            if (doc.estado_accion === 'En Proceso') return 'table-info';
            return '';
        }
    },
    /**
     * Es una buena práctica destruir la instancia de DataTables cuando
     * el componente de Vue se elimina para liberar memoria.
     */
    beforeUnmount() {
        if (this.dataTableInstance) {
            this.dataTableInstance.destroy();
        }
    }
};
</script>
