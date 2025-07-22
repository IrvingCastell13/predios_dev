<template>
    <div style="height: 400px; position: relative; overflow-x: auto;">
        <canvas ref="canvasRef"></canvas>
        <div v-if="isLoading" class="d-flex justify-content-center align-items-center h-100">
            <span class="text-secondary">Cargando gráfica...</span>
        </div>
        <div v-if="!isLoading && chartDataIsEmpty" class="d-flex justify-content-center align-items-center h-100">
             <span class="text-secondary">Seleccione uno o más predios para ver los datos.</span>
        </div>
    </div>
</template>

<script>
import { Chart as ChartJS, CategoryScale, LinearScale, Title, Tooltip, Legend } from 'chart.js';
import { MatrixController, MatrixElement } from 'chartjs-chart-matrix';
import axios from 'axios';

ChartJS.register(CategoryScale, LinearScale, Title, Tooltip, Legend, MatrixController, MatrixElement);

export default {
    name: 'BaseHeatmapMatrix',
    props: {
        apiUrl: { type: String, required: true },
        chartTitle: { type: String, required: true },
        idPredios: { type: Array, required: true }
    },
    data() {
        return {
            chart: null,
            isLoading: true,
            chartDataIsEmpty: false,
        };
    },
    watch: {
        idPredios: {
            handler() {
                this.fetchChartData();
            },
            immediate: true
        }
    },
    methods: {
        async fetchChartData() {
            this.isLoading = true;
            this.chartDataIsEmpty = false;
            
            if (this.chart) {
                this.chart.destroy();
            }

            try {
                const response = await axios.get(this.apiUrl, {
                    params: { predio_ids: this.idPredios }
                });
                const data = response.data;

                if (data.length === 0) {
                    this.chartDataIsEmpty = true;
                    return;
                }

                const allX = [...new Set(data.map(d => d.x))].sort();
                const allY = [...new Set(data.map(d => d.y))].sort();
                const cellWidth = 120;
                const cellHeight = 40;
                const ctx = this.$refs.canvasRef.getContext('2d');

                this.chart = new ChartJS(ctx, {
                    type: 'matrix',
                    data: {
                        datasets: [{
                            label: 'Cumplimiento',
                            data: data,
                            backgroundColor(ctx) {
                                const value = ctx.raw.v;
                                if (value >= 80) return '#4ade80'; // verde
                                if (value >= 40) return '#facc15'; // amarillo
                                return '#f87171'; // rojo
                            },
                            borderWidth: 1,
                            borderColor: '#ffffff',
                            width: () => cellWidth,
                            height: () => cellHeight
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: this.chartTitle
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => `Cumplimiento: ${ctx.raw.v}%`
                                }
                            },
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                type: 'category',
                                labels: allX,
                                ticks: { autoSkip: false }
                            },
                            y: {
                                type: 'category',
                                labels: allY,
                                offset: true,
                                reverse: true
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error al cargar datos de heatmap:', error);
            } finally {
                this.isLoading = false;
            }
        }
    }
};
</script>