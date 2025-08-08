<template>
  <div class="chart-container" style="position: relative; height: 450px">
    <div v-if="isLoading" class="loading-overlay"><span>Cargando...</span></div>
    <div v-if="!isLoading && !chartData" class="loading-overlay">
      <span>No hay datos.</span>
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
  DataZoomComponent,
} from "echarts/components";
import { CanvasRenderer } from "echarts/renderers";

use([
  BarChart,
  TitleComponent,
  TooltipComponent,
  GridComponent,
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
      "/api/bi/ordenes-trabajo/global-a-tiempo",
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

    if (response.data) {
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
  const categorias = data.map((item) => item.estado);

  const dataZoomConfig = [];
  const xAxisThreshold = 3;

  if (categorias.length > xAxisThreshold) {
    dataZoomConfig.push({
      type: "slider",
      xAxisIndex: 0,
      start: 0,
      end: (xAxisThreshold / categorias.length) * 100,
      bottom: "10px",
      height: 8,
      handleSize: "100%",
      showDetail: false,
      zoomLock: false,
      brushSelect: false,
    });
  }

  chartOptions.value = {
    title: { text: "Resumen Global de Cumplimiento", left: "center" },
    tooltip: { trigger: "axis", axisPointer: { type: "shadow" } },
    grid: {
      top: 60,
      left: "3%",
      right: "4%",
      bottom: "3%",
      containLabel: true,
    },
    xAxis: { type: "category", data: data.map((item) => item.estado) },
    yAxis: { type: "value" },
    dataZoom: dataZoomConfig, // Corregido el nombre de la propiedad
    series: [
      {
        name: "Órdenes",
        type: "bar",
        barWidth: "40%",
        itemStyle: {
          color: (params) =>
            params.name === "A Tiempo" ? "#27ae60" : "#c0392b",
        },
        label: { show: true, position: "top" },
        data: data.map((item) => item.total),
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