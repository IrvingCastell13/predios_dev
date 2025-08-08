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

// --- PROPS ---
const props = defineProps({
  apiUrl: { type: String, required: true },
  chartTitle: { type: String, default: "Resumen de Acciones por Estado y Predio" },
  idPredios: { type: Array, required: true },
  idEdificios: { type: Array, required: true },
  idNiveles: { type: Array, required: true },
  idZonas: { type: Array, required: true },
  idTiposEquipo: { type: Array, required: true },
  idSistemas: { type: Array, required: true },
  idSubsistemas: { type: Array, required: true },
  idPlanes: { type: Array, required: true },
  fechaInicio: { type: String, default: null },
  fechaFin: { type: String, default: null },
});

// --- ESTADO REACTIVO ---
const isLoading = ref(true);
const chartData = ref(null);
const chartOptions = ref({});
let cancelTokenSource = null;

// --- MÉTODOS ---
const fetchData = async () => {
  isLoading.value = true;
  chartData.value = null;

  if (cancelTokenSource) {
    cancelTokenSource.cancel("Nueva solicitud iniciada, cancelando la anterior.");
  }
  cancelTokenSource = axios.CancelToken.source();

  try {
    const response = await axios.get(props.apiUrl, {
      params: {
        predio_ids: props.idPredios,
        edificio_ids: props.idEdificios,
        nivel_ids: props.idNiveles,
        zona_ids: props.idZonas,
        tipo_equipo_ids: props.idTiposEquipo,
        sistema_ids: props.idSistemas,
        subsistema_ids: props.idSubsistemas,
        plan_ids: props.idPlanes,
        fecha_inicio: props.fechaInicio,
        fecha_fin: props.fechaFin,
      },
      cancelToken: cancelTokenSource.token,
    });

    if (response.data && response.data.length > 0) {
      chartData.value = response.data;
      processChartData(response.data);
    } else {
      // Si no hay datos, limpia la gráfica para que no muestre datos antiguos
      chartData.value = null;
    }
  } catch (error) {
    if (!axios.isCancel(error)) {
      console.error("Error al cargar datos de la gráfica de acciones:", error);
    }
  } finally {
    isLoading.value = false;
  }
};

const processChartData = (data) => {
  // 1. Calcular el total de acciones para cada predio.
  const predioTotals = data.reduce((acc, curr) => {
    acc[curr.predio] = (acc[curr.predio] || 0) + curr.acciones;
    return acc;
  }, {});

  // 2. Obtener la lista única de predios y ordenarla por total.
  let predios = [...new Set(data.map((d) => d.predio))];
  predios.sort((a, b) => predioTotals[a] - predioTotals[b]);

  // 3. Definir colores para los estados.
  const estados = [...new Set(data.map((d) => d.estado))];
  const colorPalette = [
    '#91cc75', '#fac858', '#ee6666', '#73c0de',
    '#3ba272', '#fc8452', '#9a60b4', '#5470c6'
  ];
  const colorMap = estados.reduce((acc, estado, index) => {
    acc[estado] = colorPalette[index % colorPalette.length];
    return acc;
  }, {});

  // 4. Construir las series para la gráfica.
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
      return match ? match.acciones : 0;
    }),
  }));

  // 5. Configurar el zoom si hay muchos predios.
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
    });
  }

  // 6. Configuración final de la gráfica.
  chartOptions.value = {
    title: { text: props.chartTitle, left: "center" },
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

// --- WATCHERS ---
watch(
  () => [
    props.idPredios,
    props.idEdificios,
    props.idNiveles,
    props.idZonas,
    props.idTiposEquipo,
    props.idSistemas,
    props.idSubsistemas,
    props.idPlanes,
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