<template>
  <div class="chart-container" style="position: relative; height: 450px">
    <div v-if="isLoading" class="loading-overlay">
      <span>Cargando gráfica...</span>
    </div>
    <div v-if="!isLoading && !chartData" class="loading-overlay">
      <span>No hay datos para mostrar con los filtros seleccionados.</span>
    </div>
    <v-chart v-if="!isLoading && chartData" :option="chartOptions" autoresize />
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import axios from "axios";
import { use } from "echarts/core";
import VChart from "vue-echarts";
import { BarChart } from "echarts/charts";
import {
  TitleComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent,
  DataZoomComponent,
} from "echarts/components";
import { CanvasRenderer } from "echarts/renderers";

use([
  BarChart,
  TitleComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent,
  CanvasRenderer,
  DataZoomComponent,
]);

// --- INICIO DE LA MODIFICACIÓN ---
const props = defineProps({
  // Props existentes
  predioIds: Array,
  tipoOtIds: Array,
  fechaInicio: String,
  fechaFin: String,
  // Nuevas props para ubicación
  edificioIds: Array,
  nivelIds: Array,
  zonaIds: Array,
});
// --- FIN DE LA MODIFICACIÓN ---

const isLoading = ref(true);
const chartData = ref(null);
const chartOptions = ref({});
let cancelTokenSource = null;

const fetchData = async () => {
  isLoading.value = true;
  chartData.value = null;

  if (cancelTokenSource) {
    cancelTokenSource.cancel("Nueva solicitud iniciada.");
  }
  cancelTokenSource = axios.CancelToken.source();

  try {
    const response = await axios.get(
      "/api/bi/ordenes-trabajo/por-predio-estado",
      {
        // --- INICIO DE LA MODIFICACIÓN ---
        params: {
          predio_ids: props.predioIds,
          edificio_ids: props.edificioIds, // Nuevo
          nivel_ids: props.nivelIds,       // Nuevo
          zona_ids: props.zonaIds,         // Nuevo
          tipo_ot_ids: props.tipoOtIds,
          fecha_inicio: props.fechaInicio,
          fecha_fin: props.fechaFin,
        },
        // --- FIN DE LA MODIFICACIÓN ---
        cancelToken: cancelTokenSource.token,
      }
    );

    if (response.data && response.data.length > 0) {
      chartData.value = response.data;
      processChartData(response.data);
    }
  } catch (error) {
    if (!axios.isCancel(error)) {
      console.error("Error al cargar datos de la gráfica:", error);
    }
  } finally {
    isLoading.value = false;
  }
};

const processChartData = (data) => {
  const predioTotals = data.reduce((acc, curr) => {
    acc[curr.predio] = (acc[curr.predio] || 0) + curr.total;
    return acc;
  }, {});

  let predios = [...new Set(data.map((d) => d.predio))];
  predios.sort((a, b) => predioTotals[a] - predioTotals[b]);

  const estados = [...new Set(data.map((d) => d.estado))];
  const colorPalette = [
    '#5470c6', '#91cc75', '#fac858', '#ee6666', 
    '#73c0de', '#3ba272', '#fc8452', '#9a60b4'
  ];
  const colorMap = estados.reduce((acc, estado, index) => {
    acc[estado] = colorPalette[index % colorPalette.length];
    return acc;
  }, {});

  const series = estados.map((estado) => ({
    name: estado,
    type: "bar",
    stack: "total",
    itemStyle: { color: colorMap[estado] },
    label: {
      show: true,
      position: "inside",
      formatter: (params) => (params.value > 0 ? params.value : ""),
    },
    data: predios.map((predio) => {
      const match = data.find(
        (d) => d.predio === predio && d.estado === estado
      );
      return match ? match.total : 0;
    }),
  }));
  
  const dataZoomConfig = [];
  const yAxisThreshold = 8;

  if (predios.length > yAxisThreshold) {
    const endPercentage = (yAxisThreshold / predios.length) * 100;
    dataZoomConfig.push({
      type: "slider",
      yAxisIndex: 0,
      start: 100 - endPercentage,
      end: 100,
      right: "15px",
      top: 70, 
      bottom: 40,
      width: 8,
      handleSize: "100%",
      showDetail: false,
      zoomLock: false,
      brushSelect: false,
    });
  }

  chartOptions.value = {
    title: { text: "Resumen de OTs por Estado y Predio", left: "center" },
    tooltip: { trigger: "axis", axisPointer: { type: "shadow" } },
    legend: { top: 30 },
    grid: {
      left: "3%",
      right: "4%",
      bottom: "3%",
      top: 70,
      containLabel: true,
    },
    xAxis: { type: "value" },
    yAxis: { type: "category", data: predios },
    dataZoom: dataZoomConfig,
    series: series,
  };
};

// --- INICIO DE LA MODIFICACIÓN ---
watch(
  () => [
    props.predioIds,
    props.edificioIds, // Nuevo
    props.nivelIds,    // Nuevo
    props.zonaIds,     // Nuevo
    props.tipoOtIds,
    props.fechaInicio,
    props.fechaFin
  ],
  fetchData,
  {
    deep: true,
    immediate: true,
  }
);
// --- FIN DE LA MODIFICACIÓN ---
</script>

<style scoped>
.chart-container {
  width: 100%;
  height: 100%;
}
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: rgba(255, 255, 255, 0.8);
  z-index: 10;
  color: #6c757d;
  font-size: 1.2rem;
}
</style>