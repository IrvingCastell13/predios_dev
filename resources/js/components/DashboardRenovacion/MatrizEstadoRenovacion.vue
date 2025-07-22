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
    name: 'MatrizEstadoRenovacion',
    props: {
        apiUrl: { type: String, required: true },
        idPredios: { type: Array, required: true },
        idGrupos: { type: Array, required: true },       // <-- Cambiado a Array
        idCategorias: { type: Array, required: true },   // <-- Cambiado a Array
        fechaInicio: { type: String, default: null },
        fechaFin: { type: String, default: null }
    },
    data() {
        return {
            chart: null,
            isLoading: true,
            chartDataIsEmpty: false
        };
    },
    computed: {
        filtros() {
            return {
                predios: this.idPredios,
                grupos: this.idGrupos,           // <-- Nombre de prop cambiado
                categorias: this.idCategorias,   // <-- Nombre de prop cambiado
                inicio: this.fechaInicio,
                fin: this.fechaFin
            };
        },
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
            if (this.chart) { this.chart.destroy(); }

            try {
                const response = await axios.get(this.apiUrl, {
                    params: {
                        predio_ids: this.idPredios,
                        grupo_ids: this.idGrupos,         // <-- Nombre de param cambiado
                        categoria_ids: this.idCategorias, // <-- Nombre de param cambiado
                        fecha_inicio: this.fechaInicio,
                        fecha_fin: this.fechaFin
                    }
                });
                const data = response.data;
                if (data.length === 0) {
                    this.chartDataIsEmpty = true;
                    return;
                }
                
                const allX = [...new Set(data.map(d => d.x))].sort();
                const allY = [...new Set(data.map(d => d.y))].sort();
                const colorMap = {
                    'estado-01': '#facc15',
                    'estado-02': '#60a5fa',
                    'estado-03': '#4ade80',
                };
                const cellWidth = 150;
                const cellHeight = 40;
                const ctx = this.$refs.canvasRef.getContext('2d');
                this.chart = new ChartJS(ctx, {
                     type: 'matrix',
                     data: { datasets: [{
                            label: 'Estado', data: data,
                            backgroundColor: (ctx) => {
                                const value = ctx.raw.v;
                                if (value === null) return '#f87171';
                                return colorMap[value] || '#64748b';
                            },
                            borderWidth: 1, borderColor: '#ffffff',
                            width: () => cellWidth, height: () => cellHeight
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            title: { display: true, text: this.chartTitle },
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => [
                                        `Estado: ${ctx.raw.estado}`,
                                        `Tiene OT: ${ctx.raw.tiene_ot === 1 ? 'Sí' : 'No'}`,
                                        `Fecha Inicio: ${ctx.raw.fecha_inicio || 'N/D'}`,
                                        `Fecha Fin: ${ctx.raw.fecha_fin || 'N/D'}`
                                    ]
                                }
                            }
                        },
                        scales: {
                            x: { type: 'category', labels: allX, ticks: { autoSkip: false } },
                            y: { type: 'category', labels: allY, offset: true, reverse: true }
                        }
                    }
                });
            } catch (error) {
                console.error('Error al cargar la matriz:', error);
            } finally {
                this.isLoading = false;
            }
        }
    }
};
</script>