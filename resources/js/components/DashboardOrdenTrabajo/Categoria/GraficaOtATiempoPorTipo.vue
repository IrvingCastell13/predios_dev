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

const props = defineProps({
  predioIds: Array,
  tipoOtIds: Array,
  fechaInicio: String,
  fechaFin: String,
  edificioIds: Array,
  nivelIds: Array,
  zonaIds: Array,
});

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
      "/api/bi/ordenes-trabajo/a-tiempo-por-tipo",
      {
        params: {
          predio_ids: props.predioIds,
          edificio_ids: props.edificioIds,
          nivel_ids: props.nivelIds,
          zona_ids: props.zonaIds,
          tipo_ot_ids: props.tipoOtIds,
          fecha_inicio: props.fechaInicio,
          fecha_fin: props.fechaFin,
        },
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
  // --- LÓGICA DE ORDENAMIENTO (NUEVO) ---

  // 1. Ordenamos el array de datos de menor a mayor según la suma de sus valores.
  data.sort((a, b) => {
    const totalA = (a.a_tiempo || 0) + (a.fuera_tiempo || 0);
    const totalB = (b.a_tiempo || 0) + (b.fuera_tiempo || 0);
    return totalA - totalB;
  });

  // 2. Extraemos los datos para la gráfica del array ya ordenado.
  const tipos = data.map((item) => item.tipo);
  const aTiempoData = data.map((item) => item.a_tiempo);
  const fueraTiempoData = data.map((item) => item.fuera_tiempo);

  // --- AJUSTE DEL SCROLL (MODIFICADO) ---
  const dataZoomConfig = [];
  const yAxisThreshold = 6;

  if (tipos.length > yAxisThreshold) {
    const endPercentage = (yAxisThreshold / tipos.length) * 100;
    dataZoomConfig.push({
      type: "slider",
      yAxisIndex: 0,
      start: 100 - endPercentage, // Inicia mostrando los de arriba
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
    title: { text: "Cumplimiento de Inicio por Tipo de OT", left: "center" },
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
    // Usa los tipos ya ordenados
    yAxis: { type: "category", data: tipos },
    dataZoom: dataZoomConfig,

    series: [
      {
        name: "A Tiempo",
        type: "bar",
        stack: "total",
        itemStyle: { color: "#27ae60" }, // Verde
        label: {
          show: true,
          position: "inside",
          formatter: (p) => (p.value > 0 ? p.value : ""),
        },
        // Usa los datos ordenados
        data: aTiempoData,
      },
      {
        name: "Fuera de Tiempo",
        type: "bar",
        stack: "total",
        itemStyle: { color: "#c0392b" }, // Rojo
        label: {
          show: true,
          position: "inside",
          formatter: (p) => (p.value > 0 ? p.value : ""),
        },
        // Usa los datos ordenados
        data: fueraTiempoData,
      },
    ],
  };
};

watch(
  () => [
    props.predioIds,
    props.tipoOtIds,
    props.fechaInicio,
    props.fechaFin,
    props.tipoOtIds,
    props.fechaInicio,
    props.fechaFin,
  ],
  fetchData,
  {
    deep: true,
    immediate: true,
  }
);
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