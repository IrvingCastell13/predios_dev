<template>
    <div style="height: 600px; position: relative; overflow: auto;">
        <canvas ref="canvasRef"></canvas>
        <div v-if="isLoading" class="d-flex justify-content-center align-items-center h-100">
            <span class="text-secondary">Cargando matriz...</span>
        </div>
        <div v-if="!isLoading && chartDataIsEmpty" class="d-flex justify-content-center align-items-center h-100">
             <span class="text-secondary">No hay datos para la selección actual.</span>
        </div>
    </div>
</template>

<script>
import { Chart as ChartJS, CategoryScale, LinearScale, Title, Tooltip, Legend } from 'chart.js';
import { MatrixController, MatrixElement } from 'chartjs-chart-matrix';
import axios from 'axios';

ChartJS.register(CategoryScale, LinearScale, Title, Tooltip, Legend, MatrixController, MatrixElement);

export default {
    name: 'VigenciaMatrizEstadoSubcategoria',
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
            chart: null,
            isLoading: true,
            chartDataIsEmpty: false,
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
            this.chartDataIsEmpty = false;
            if (this.chart) {
                this.chart.destroy();
            }
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
                
                this.renderMatrix(response.data);

            } catch (error) {
                if (axios.isCancel(error)) {
                    console.log('Solicitud cancelada:', error.message);
                } else {
                    this.chartDataIsEmpty = true;
                    console.error('Error al cargar datos para la matriz de vigencia por subcategoría:', error);
                }
            } finally {
                this.isLoading = false;
            }
        },
        renderMatrix(data) {
            if (!data || data.length === 0) {
                this.chartDataIsEmpty = true;
                return;
            }

            const allX = [...new Set(data.map(d => d.x))].sort();
            const allY = [...new Set(data.map(d => d.y))].sort();

            const colorMap = {
                'estado-03': '#4ade80',  // Ejecutado
                'estado-02': '#60a5fa',  // En Proceso
                'estado-01': '#facc15',  // Pendiente
                 null: '#f87171'         // Faltante (cuando el estado_id es null)
            };

            const cellWidth = 120;
            const cellHeight = 40;
            const ctx = this.$refs.canvasRef.getContext('2d');
            
            this.chart = new ChartJS(ctx, {
                type: 'matrix',
                data: {
                    datasets: [{
                        label: 'Estado',
                        data: data,
                        backgroundColor: (ctx) => {
                            const value = ctx.raw.v;
                            return colorMap[value] || '#e5e7eb';
                        },
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        width: () => cellWidth,
                        height: () => cellHeight
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `Estado: ${ctx.raw.estado}`
                            }
                        },
                        title: {
                            display: true,
                            text: 'Matriz de Estado de Documentos por Subcategoría'
                        }
                    },
                    scales: {
                        x: { type: 'category', labels: allX, ticks: { autoSkip: false, font: { size: 10 } } },
                        y: { type: 'category', labels: allY, offset: true, reverse: true }
                    }
                }
            });
        }
    }
};
</script>