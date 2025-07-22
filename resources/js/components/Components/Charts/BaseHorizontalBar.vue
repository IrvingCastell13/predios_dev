<template>
  <div
    id="chart-scroll-wrapper"
    style="background: #fff; padding: 16px; overflow-x: hidden;"
  >
    <div v-if="isLoading || !option.series?.length" class="text-center text-gray-400 py-4">
      Cargando gráfica...
    </div>
    <div
      v-else
      :style="{
        maxHeight: maxContainerHeight + 'px',
        overflowY: chartHeight > maxContainerHeight ? 'auto' : 'visible'
      }"
    >
      <v-chart
        :option="option"
        :style="{height: chartHeight + 'px', width: '100%'}"
        autoresize
      />
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from 'vue';
import { use } from 'echarts/core';
import VChart from 'vue-echarts';
import { BarChart } from 'echarts/charts';
import { TitleComponent, TooltipComponent, GridComponent, LegendComponent } from 'echarts/components';
import { CanvasRenderer } from 'echarts/renderers';
import axios from 'axios';

use([BarChart, TitleComponent, TooltipComponent, GridComponent, LegendComponent, CanvasRenderer]);

const props = defineProps({
  apiUrl: { type: String, required: true },
  chartTitle: { type: String, required: true },
  startDate: { type: String, required: true },
  endDate: { type: String, required: true },
  predio: { type: String, default: '' },
  edificio: { type: String, default: '' },
  zona: { type: String, default: '' },
  nivel: { type: String, default: '' },
  persona: { type: String, default: '' },
  estado: { type: String, default: '' },
  id: { type: String, default: '' }
});

const chartData = ref(null);
const option = ref({});
const rowHeight = 55;
const minChartHeight = 200;
const maxContainerHeight = 500;
const isLoading = ref(false);
const cancelTokenSource = ref(null);
const TOTAL_LABEL = "Total Tickets";
const RECHAZADOS_LABEL = "Rechazados";

// Ordenar por total tickets de mayor a menor y luego invertir para ECharts
const sortedInfo = computed(() => {
  if (!chartData.value?.labels) return [];
  const labels = chartData.value.labels;
  const totalData = chartData.value.datasets[0]?.data || [];
  const rechazadosData = chartData.value.datasets[1]?.data || [];
  return labels
    .map((label, i) => ({
      label,
      total: totalData[i] || 0,
      rechazados: rechazadosData[i] || 0
    }))
    .sort((a, b) => b.total - a.total)
    .reverse();
});

const sortedLabels = computed(() => sortedInfo.value.map(obj => obj.label));
const totalTicketsData = computed(() => sortedInfo.value.map(obj => obj.total));
const rechazadosData = computed(() => sortedInfo.value.map(obj => obj.rechazados));
const chartHeight = computed(() => {
  const items = sortedLabels.value.length || 0;
  return Math.max(items * rowHeight, minChartHeight);
});

const fetchChartData = async () => {

     if (cancelTokenSource.value) cancelTokenSource.value.cancel('Cambio de tab');
  cancelTokenSource.value = axios.CancelToken.source();
  try {
    isLoading.value = true;
    const params = {
      start: props.startDate,
      end: props.endDate
    };
    if (props.predio) params.predio = props.predio;
    if (props.edificio) params.edificio = props.edificio;
    if (props.zona) params.zona = props.zona;
    if (props.nivel) params.nivel = props.nivel;
    if (props.persona) params.persona = props.persona;
    if (props.estado) params.estado = props.estado;

    const response = await axios.get(props.apiUrl, { params, cancelToken: cancelTokenSource.value.token });
    chartData.value = response.data;

    option.value = {
      backgroundColor: '#fff',
      title: {
        text: props.chartTitle,
        left: 'center',
        textStyle: {
          color: '#232323', // Negro oscuro
          fontSize: 22,
          fontWeight: 'bold'
        }
      },
      tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
      legend: {
        data: [TOTAL_LABEL, RECHAZADOS_LABEL],
        bottom: 0,
        textStyle: { color: '#232323' } // Negro oscuro
      },
      grid: {
        left: '3%',
        right: '4%',
        bottom: 40,
        containLabel: true
      },
      xAxis: {
        type: 'value',
        axisLabel: { color: '#232323' }, // Negro oscuro
        splitLine: { lineStyle: { color: '#bdbdbd' } }
      },
      yAxis: {
        type: 'category',
        data: sortedLabels.value,
        axisLabel: { color: '#232323' } // Negro oscuro
      },
      series: [
        {
          name: TOTAL_LABEL,
          type: 'bar',
          barGap: '20%',
          barWidth: 20,
          label: {
            show: false
          },
          itemStyle: {
            color: '#b4e0ff'
          },
          data: totalTicketsData.value,
          z: 2
        },
        {
          name: RECHAZADOS_LABEL,
          type: 'bar',
          barGap: '80%',
          barWidth: 8,
          label: {
            show: false
          },
          itemStyle: {
            color: '#e4b100'
          },
          data: rechazadosData.value,
          z: 3
        }
      ]
    };
  } catch (error) {
    chartData.value = null;
    option.value = {};
    console.error('Error al obtener datos del API:', error);
  } finally {
    isLoading.value = false;
  }
};

watch(
  () => [
    props.startDate,
    props.endDate,
    props.predio,
    props.edificio,
    props.zona,
    props.nivel,
    props.persona,
    props.estado
  ],
  fetchChartData
);

onMounted(fetchChartData);

onUnmounted(() => {
    if (cancelTokenSource.value) cancelTokenSource.value.cancel('Componente destruido');
});
</script>
