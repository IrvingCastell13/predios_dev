<template>
  <div class="chart-container" style="position: relative; height: 450px">
    <div v-if="isLoading" class="loading-overlay">
      <span>Cargando gráfica...</span>
    </div>
    <div v-if="!isLoading && !chartData" class="loading-overlay">
      <span>No hay datos para mostrar.</span>
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
  if (cancelTokenSource) cancelTokenSource.cancel();
  cancelTokenSource = axios.CancelToken.source();

  try {
    const response = await axios.get(
      "/api/bi/ordenes-trabajo/a-tiempo-por-predio",
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
    if (!axios.isCancel(error)) console.error("Error al cargar datos:", error);
  } finally {
    isLoading.value = false;
  }
};

const processChartData = (data) => {
  data.sort((a, b) => {
    const totalA = (a.a_tiempo || 0) + (a.fuera_tiempo || 0);
    const totalB = (b.a_tiempo || 0) + (b.fuera_tiempo || 0);
    return totalA - totalB;
  });

  const predios = data.map((item) => item.predio);
  const aTiempoData = data.map((item) => item.a_tiempo);
  const fueraTiempoData = data.map((item) => item.fuera_tiempo);

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
    title: { text: "Cumplimiento de Inicio por Predio", left: "center" },
    tooltip: { trigger: "axis", axisPointer: { type: "shadow" } },
    legend: { top: 30, data: ["A Tiempo", "Fuera de Tiempo"] },
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
    series: [
      {
        name: "A Tiempo",
        type: "bar",
        stack: "total",
        itemStyle: { color: "#27ae60" },
        label: {
          show: true,
          position: "inside",
          formatter: (p) => (p.value > 0 ? p.value : ""),
        },
        data: aTiempoData,
      },
      {
        name: "Fuera de Tiempo",
        type: "bar",
        stack: "total",
        itemStyle: { color: "#c0392b" },
        label: {
          show: true,
          position: "inside",
          formatter: (p) => (p.value > 0 ? p.value : ""),
        },
        data: fueraTiempoData,
      },
    ],
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
/* Estilos sin cambios */
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