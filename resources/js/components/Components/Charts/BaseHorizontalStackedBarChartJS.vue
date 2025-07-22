<template>
  <div
    id="chart-scroll-wrapper"
    style="background: #fff; padding: 16px; overflow-x: auto;"
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
import { ref, watch, computed, onMounted } from 'vue';
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
const minChartHeight = 300;
const maxContainerHeight = 500;
const isLoading = ref(false);

const colorMap = [
  "#666666", "#00a488", "#D4AC0D", "#A8A8A8", "red", "#0f6491"
];

// Ordenar labels y datasets de mayor a menor total, y luego invertir para que los más grandes queden arriba
const sortedLabels = computed(() => {
  if (!chartData.value?.labels) return [];
  const labels = chartData.value.labels;
  const datasets = chartData.value.datasets;
  const totals = labels.map((_, i) =>
    datasets.reduce((sum, ds) => sum + (ds.data[i] || 0), 0)
  );
  // Ordenar de mayor a menor total y luego invertir para que el mayor quede arriba
  return labels
    .map((label, i) => ({ label, total: totals[i], index: i }))
    .sort((a, b) => b.total - a.total)
    .map(obj => obj.label)
    .reverse();
});

const sortedDatasets = computed(() => {
  if (!chartData.value?.datasets) return [];
  const labels = chartData.value.labels;
  const datasets = chartData.value.datasets;
  const totals = labels.map((_, i) =>
    datasets.reduce((sum, ds) => sum + (ds.data[i] || 0), 0)
  );
  const sortedIndices = labels
    .map((_, i) => i)
    .sort((a, b) => totals[b] - totals[a])
    .reverse();
  return datasets.map(ds => ({
    ...ds,
    data: sortedIndices.map(i => ds.data[i])
  }));
});

const chartHeight = computed(() => {
  const items = sortedLabels.value.length || 0;
  return Math.max(items * rowHeight, minChartHeight);
});

const fetchChartData = async () => {
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

    const response = await axios.get(props.apiUrl, { params });
    chartData.value = response.data;

    // Series para ECharts
    const series = sortedDatasets.value.map((ds, idx) => ({
      name: ds.label,
      type: 'bar',
      stack: 'total',
      label: {
        show: true,
        position: 'insideRight',
        color: '#fff'
      },
      emphasis: { focus: 'series' },
      itemStyle: {
        color: ds.backgroundColor || colorMap[idx % colorMap.length]
      },
      data: ds.data
    }));

    option.value = {
      backgroundColor: '#fff',
      title: {
        text: props.chartTitle,
        left: 'center',
        textStyle: {
          color: '#232323',
          fontSize: 18,
          fontWeight: 'bold'
        }
      },
      tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
      legend: {
        data: sortedDatasets.value.map(ds => ds.label),
        bottom: 0,
        textStyle: { color: '#232323' }
      },
      grid: {
        left: '3%',
        right: '4%',
        bottom: 40,
        containLabel: true
      },
      xAxis: {
        type: 'value',
        axisLabel: { color: '#232323' },
        splitLine: { lineStyle: { color: '#eee' } }
      },
      yAxis: {
        type: 'category',
        data: sortedLabels.value,
        axisLabel: { color: '#232323' }
      },
      series
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
</script>
