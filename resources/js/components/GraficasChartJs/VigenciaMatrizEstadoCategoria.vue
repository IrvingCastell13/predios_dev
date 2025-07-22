<template>
    <div style="height: 500px; position: relative;">
        <div v-if="isLoading" class="d-flex justify-content-center align-items-center h-100">
            <span class="text-secondary">Cargando gráfica...</span>
        </div>
        <Bar v-else-if="chartData" :data="chartData" :options="chartOptions" />
        <div v-else class="d-flex justify-content-center align-items-center h-100">
            <span class="text-secondary">No hay datos para la selección actual.</span>
        </div>
    </div>
</template>

<script>
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js';
import axios from 'axios';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

export default {
    name: 'VigenciaMatrizEstadoCategoria',
    components: { Bar },
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
            chartData: null,
            isLoading: true,
            cancelTokenSource: null
        };
    },
    computed: {
        chartOptions() {
            return {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: context => context.raw.tooltip || context.dataset.label
                        }
                    },
                    title: {
                        display: true,
                        text: 'Matriz de Estado de Documentos por Categoría'
                    }
                },
                scales: {
                    x: { stacked: true, ticks: { display: false }, grid: { display: false } },
                    y: { stacked: true }
                }
            };
        },
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
                
                // --- INICIO: LÍNEAS DE DEPURACIÓN ---
                console.log("1. Datos recibidos del API:", response.data);
                this.processChartData(response.data);
                // --- FIN: LÍNEAS DE DEPURACIÓN ---

            } catch (error) {
                if (axios.isCancel(error)) {
                    console.log('Solicitud cancelada:', error.message);
                } else {
                    console.error('Error al cargar datos para la matriz de vigencia:', error);
                }
            } finally {
                this.isLoading = false;
            }
        },
        processChartData(rows) {
            if (!rows || rows.length === 0) {
                this.chartData = null;
                return;
            }

            const documentos = [...new Set(rows.map(r => r.documento))];
            const predios = [...new Set(rows.map(r => r.predio))];
            
            console.log("2. Eje Y (Documentos):", documentos);
            console.log("3. Datasets (Predios):", predios);

            const estadoColorMap = {
                'Ejecutado': { value: 1, color: '#4ade80' },
                'En Proceso': { value: 1, color: '#60a5fa' },
                'Pendiente': { value: 1, color: '#facc15' },
                'FALTANTE': { value: 1, color: '#f87171' },
            };

            const datasets = predios.map(predio => {
                const data = documentos.map(doc => {
                    const match = rows.find(r => r.predio === predio && r.documento === doc);
                    let estado = 'FALTANTE';
                    if (match && match.estado_documento === 'CREADO') {
                        estado = match.estado_accion || 'Pendiente';
                    }
                    
                    const mapped = estadoColorMap[estado] || { value: 0, color: '#e5e7eb' };

                    return {
                        x: mapped.value,
                        tooltip: `Predio: ${predio} - Documento: ${doc} - Estado: ${estado}`,
                        backgroundColor: mapped.color
                    };
                });

                return {
                    label: predio,
                    data: data,
                    backgroundColor: data.map(d => d.backgroundColor),
                    borderWidth: 1,
                    borderColor: '#ffffff'
                };
            });
            
            console.log("4. Datasets finales para la gráfica:", datasets);

            this.chartData = {
                labels: documentos,
                datasets
            };
        }
    }
};
</script>