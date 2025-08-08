<template>
  <div class="chart-container" style="position: relative; height: 400px">
    <div v-if="isLoading" class="loading-overlay">
      <span>Cargando datos de vigencia...</span>
    </div>
    <div v-if="!isLoading && !hasData" class="loading-overlay">
      <span>No hay datos para mostrar con los filtros seleccionados.</span>
    </div>
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
// No se necesita cambiar las props, el componente sigue recibiendo los mismos filtros
const props = defineProps({
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
    // <-- Cambio: Apuntar a la nueva API de zonas
    const response = await axios.get("/api/bi/porcentaje-vigencia-por-zona", {
      params: {
        id_predios: props.idPredios,
        id_grupos_doc: props.idGrupos,
        id_categorias_doc: props.idCategorias,
        id_tipos_documento: props.idTiposDocumento,
        id_tipos_inmueble: props.idTiposInmueble,
        estado_archivo: props.estadoArchivo,
        id_paises: props.idPaises,
        id_estados: props.idEstados,
        id_municipios: props.idMunicipios,
        id_edificios: props.idEdificios,
        id_niveles: props.idNiveles,
        id_zonas: props.idZonas,
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
    } else {
      hasData.value = false;
    }
  } catch (error) {
    if (!axios.isCancel(error)) {
      console.error("Error al cargar datos de vigencia:", error);
    }
  } finally {
    isLoading.value = false;
  }
};

// --- PROCESAMIENTO DE LA GRÁFICA ---
// La lógica interna no cambia, ya que la API devuelve la misma estructura de datos
const processChartData = (data) => {
  const vigentesDataset = data.datasets.find((d) => d.label === "Vigentes");
  if (!vigentesDataset) {
    hasData.value = false;
    return;
  }

  const combinedData = data.labels.map((label, index) => ({
    label: label,
    Vigentes: parseFloat(vigentesDataset.data?.[index]) || 0,
  }));

  combinedData.sort((a, b) => a.Vigentes - b.Vigentes);

  // <-- Cambio: Renombrar variable para mayor claridad
  const zonas = combinedData.map((item) => item.label);
  const vigentesData = combinedData.map((item) => item.Vigentes);
  const noCumpleData = vigentesData.map((value) => (100 - value).toFixed(2));

  const dataZoomConfig = [];
  const yAxisThreshold = 8;
  // <-- Cambio: Usar la nueva variable 'zonas'
  if (zonas.length > yAxisThreshold) {
    const percentageToShow = (yAxisThreshold / zonas.length) * 100;
    dataZoomConfig.push({
      type: "slider",
      yAxisIndex: 0,
      start: 100 - percentageToShow,
      end: 100,
      right: "15px",
      width: 8,
      handleSize: "100%",
      showDetail: false,
    });
  }

  chartOptions.value = {
    title: {
      // <-- Cambio: Actualizar título
      text: "Nivel de Vigencia de Documentos por Zona",
      left: "center",
    },
    tooltip: {
      trigger: "axis",
      axisPointer: { type: "shadow" },
      formatter: (params) => {
        // <-- Cambio: Actualizar tooltip
        const zonaName = params[0].name;
        let tooltipHtml = `<strong>${zonaName}</strong><br/>`;
        params.forEach((p) => {
          if (p.value > 0) {
            tooltipHtml += `${p.marker} ${p.seriesName}: ${p.value}%<br/>`;
          }
        });
        return tooltipHtml;
      },
    },
    legend: {
      data: ["Vigentes", "No Cumple"],
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
      // <-- Cambio: Usar la data de zonas
      data: zonas,
    },
    dataZoom: dataZoomConfig,
    series: [
      {
        name: "Vigentes",
        type: "bar",
        stack: "total",
        label: {
          show: true,
          position: "inside",
          formatter: (params) => (params.value > 10 ? `${params.value}%` : ""),
          color: "#fff",
          fontSize: 10,
        },
        itemStyle: { color: "#4ade80" },
        data: vigentesData,
      },
      {
        name: "No Cumple",
        type: "bar",
        stack: "total",
        label: {
          show: true,
          position: "inside",
          formatter: (params) => (params.value > 10 ? `${params.value}%` : ""),
          color: "#fff",
          fontSize: 10,
        },
        itemStyle: { color: "#f87171" },
        data: noCumpleData,
      },
    ],
  };
};

// --- WATCHER ---
// No necesita cambios, sigue reaccionando a los mismos filtros
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
.chart-container {
  width: 100%;
  height: 100%;
  padding: 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  background-color: #ffffff;
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