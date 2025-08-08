<template>
  <div class="chart-container" style="position: relative; height: 450px">
    <div v-if="isLoading" class="loading-overlay">
      <span>Cargando gráfica...</span>
    </div>
    <div v-if="!isLoading && !hasData" class="loading-overlay">
      <span>No hay datos disponibles para los filtros seleccionados.</span>
    </div>
    <v-chart v-if="!isLoading && hasData" :option="chartOptions" autoresize />
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import axios from "axios";
import { use } from "echarts/core";
import VChart from "vue-echarts";
import { PieChart } from "echarts/charts";
import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
} from "echarts/components";
import { CanvasRenderer } from "echarts/renderers";

use([
  PieChart,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  CanvasRenderer,
]);

// --- PROPS ---
const props = defineProps({
  apiUrl: { type: String, required: true },
  chartTitle: { type: String, default: "Cumplimiento Global (Con Archivo)" },
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
  const conArchivoDataset = data.datasets.find((d) =>
    d.label.includes("Con archivo")
  );

  if (!conArchivoDataset) {
    hasData.value = false;
    return;
  }

  //  Sumamos los valores de todos los predios.
  const totalPredios = conArchivoDataset.data.length;
  const sumaConArchivo = conArchivoDataset.data.reduce(
    (acc, value) => acc + (parseFloat(value) || 0),
    0
  );

  // Calculamos el promedio global de cumplimiento y el faltante.
  const promedioGlobalConArchivo =
    totalPredios > 0 ? sumaConArchivo / totalPredios : 0;
  const promedioGlobalFaltante = 100 - promedioGlobalConArchivo;

  const chartData = [
    { value: promedioGlobalConArchivo.toFixed(2), name: "% Con Archivo" },
    { value: promedioGlobalFaltante.toFixed(2), name: "% Faltante" },
  ];

  // --- OPCIONES DE ECHARTS (estilo Pie/Dona) ---
  chartOptions.value = {
    title: {
      text: props.chartTitle,
      left: "center",
    },
    tooltip: {
      trigger: "item",
      formatter: "{b}: {c}% ({d}%)",
    },
    legend: {
      orient: "vertical",
      left: "left",
      top: "middle",
      data: ["% Con Archivo", "% Faltante"],
    },
    series: [
      {
        name: "Cumplimiento Con Archivo",
        type: "pie",
        radius: ["40%", "70%"], // Para efecto de dona
        center: ["65%", "50%"],
        data: chartData,
        itemStyle: {
          color: (params) =>
            params.name === "% Con Archivo" ? "#60a5fa" : "#f87171",
        },
        label: {
          show: true,
          position: "inside",
          formatter: "{c}%",
          color: "#fff",
          fontSize: 12,
        },
        emphasis: {
          itemStyle: {
            shadowBlur: 10,
            shadowOffsetX: 0,
            shadowColor: "rgba(0, 0, 0, 0.5)",
          },
        },
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