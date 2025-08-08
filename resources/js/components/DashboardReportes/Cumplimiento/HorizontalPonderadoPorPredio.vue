<template>
  <div class="chart-container" style="position: relative; height: 450px">
    <!-- Overlay para estado de carga -->
    <div v-if="isLoading" class="loading-overlay">
      <span>Cargando gráfica...</span>
    </div>
    <!-- Overlay para cuando no hay datos -->
    <div v-if="!isLoading && !hasData" class="loading-overlay">
      <span>No hay datos disponibles para los filtros seleccionados.</span>
    </div>
    <!-- Contenedor de la gráfica -->
    <v-chart v-if="!isLoading && hasData" :option="chartOptions" autoresize />
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

// Registro de componentes de ECharts
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
  chartTitle: { type: String, required: true },
  idPredios: { type: Array, required: true },
  idGrupos: { type: Array, required: true },
  idCategorias: { type: Array, required: true },
  idTiposDocumento: { type: Array, required: true },
  idTiposInmueble: { type: Array, required: true },
  estadoArchivo: { type: String, required: true },
  idPaises: { type: Array, default: () => [] },
  idEstados: { type: Array, default: () => [] },
  idMunicipios: { type: Array, default: () => [] },
  idEdificios: { type: Array, default: () => [] },
  idNiveles: { type: Array, default: () => [] },
  idZonas: { type: Array, default: () => [] },
});

// --- ESTADO REACTIVO ---
const isLoading = ref(true);
const hasData = ref(false);
const chartOptions = ref({});
let cancelTokenSource = null;

// --- LÓGICA DE DATOS ---
const fetchData = async () => {
  isLoading.value = true;
  hasData.value = false;
  if (cancelTokenSource) cancelTokenSource.cancel("Nueva solicitud iniciada.");
  cancelTokenSource = axios.CancelToken.source();

  try {
    const response = await axios.get(props.apiUrl, {
      params: {
        predio_ids: props.idPredios,
        grupo_ids: props.idGrupos,
        categoria_ids: props.idCategorias,
        tipo_doc_ids: props.idTiposDocumento,
        tipo_inmueble_ids: props.idTiposInmueble,
        estado_archivo: props.estadoArchivo,
        id_paises: props.idPaises,
        id_estados: props.idEstados,
        id_municipios: props.idMunicipios,
        id_edificio: props.idEdificios,
        id_nivel: props.idNiveles,
        id_zona: props.idZonas,
      },
      cancelToken: cancelTokenSource.token,
    });

    if (
      response.data &&
      response.data.labels &&
      response.data.labels.length > 0
    ) {
      processChartData(response.data);
      hasData.value = true;
    }
  } catch (error) {
    if (!axios.isCancel(error))
      console.error("Error al cargar datos de cumplimiento:", error);
  } finally {
    isLoading.value = false;
  }
};

// --- PROCESAMIENTO DE LA GRÁFICA ---
const processChartData = (data) => {
  const creadosDataset = data.datasets.find((d) =>
    d.label.includes("Documentos creados")
  );
  const conArchivoDataset = data.datasets.find((d) =>
    d.label.includes("Con archivo")
  );

  if (!creadosDataset || !conArchivoDataset) {
    hasData.value = false;
    return;
  }

  // --- 1. ORDENAMIENTO DE DATOS ---
  let combinedData = data.labels.map((label, index) => ({
    label: label,
    creados: parseFloat(creadosDataset.data[index]) || 0,
    conArchivo: parseFloat(conArchivoDataset.data[index]) || 0,
  }));

  // ✨ ¡CAMBIO AQUÍ! Ordenar de menor a mayor para que ECharts muestre los más altos arriba.
  combinedData.sort((a, b) => a.creados - b.creados);

  const predios = combinedData.map((d) => d.label);
  const creadosData = combinedData.map((d) => d.creados);
  const conArchivoData = combinedData.map((d) => d.conArchivo);

  const faltanteCreadosData = creadosData.map((value) => 100 - value);
  const faltanteArchivoData = conArchivoData.map((value) => 100 - value);

  // --- 2. AJUSTE DEL SCROLL (DataZoom) ---
  const dataZoomConfig = [];
  const yAxisThreshold = 5;

  if (predios.length > yAxisThreshold) {
    const percentageToShow = (yAxisThreshold / predios.length) * 100;
    dataZoomConfig.push({
      type: "slider",
      yAxisIndex: 0,
      start: 100 - percentageToShow,
      end: 100,
      right: "15px",
      width: 8,
      handleSize: "100%",
      showDetail: false,
      zoomLock: false,
      brushSelect: false,
    });
  }

  // --- OPCIONES DE ECHARTS ---
  chartOptions.value = {
    title: {
      text: props.chartTitle,
      left: "center",
    },
    tooltip: {
      trigger: "axis",
      axisPointer: { type: "shadow" },
      formatter: (params) => {
        const predioName = params[0].name;
        let tooltipHtml = `<strong>${predioName}</strong><br/>`;
        const creados = params.find(
          (p) => p.seriesName === "% Documentos creados"
        );
        const conArchivo = params.find((p) => p.seriesName === "% Con archivo");

        if (creados) {
          tooltipHtml += `${creados.marker} ${creados.seriesName}: ${creados.value}%<br/>`;
        }
        if (conArchivo) {
          tooltipHtml += `${conArchivo.marker} ${conArchivo.seriesName}: ${conArchivo.value}%<br/>`;
        }
        return tooltipHtml;
      },
    },
    legend: {
      data: ["% Documentos creados", "% Con archivo", "Faltante"],
      bottom: 0,
    },
    grid: {
      left: "3%",
      right: "4%",
      bottom: "10%",
      containLabel: true,
    },
    xAxis: {
      type: "value",
      min: 0,
      max: 100,
      axisLabel: { formatter: "{value}%" },
    },
    yAxis: {
      type: "category",
      data: predios,
    },
    dataZoom: dataZoomConfig,
    series: [
      {
        name: "% Documentos creados",
        type: "bar",
        stack: "Documentos creados",
        label: {
          show: true,
          formatter: "{c}%",
          fontSize: 10,
          color: "#fff",
          position: "inside",
        },
        itemStyle: { color: "#4ade80" },
        data: creadosData,
      },
      {
        name: "Faltante",
        type: "bar",
        stack: "Documentos creados",
        itemStyle: { color: "#f87171" },
        data: faltanteCreadosData,
      },
      {
        name: "% Con archivo",
        type: "bar",
        stack: "Con archivo",
        label: {
          show: true,
          formatter: "{c}%",
          fontSize: 10,
          color: "#fff",
          position: "inside",
        },
        itemStyle: { color: "#60a5fa" },
        data: conArchivoData,
      },
      {
        name: "Faltante",
        type: "bar",
        stack: "Con archivo",
        itemStyle: { color: "#f87171" },
        data: faltanteArchivoData,
      },
    ],
  };
};

// --- WATCHER ---
watch(
  () => [
    props.idPredios,
    props.idGrupos,
    props.idCategorias,
    props.idTiposDocumento,
    props.idTiposInmueble,
    props.estadoArchivo,
    props.idPaises,
    props.idEstados,
    props.idMunicipios,
    props.idEdificios,
    props.idNiveles,
    props.idZonas,
  ],
  fetchData,
  {
    deep: true,
    immediate: true,
  }
);
</script>
<style scoped>
/* Estilos para los overlays de carga y datos vacíos */
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
