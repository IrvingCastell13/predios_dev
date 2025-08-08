<template>
  <div class="chart-container" style="height: 450px; position: relative">
    <div v-if="isLoading" class="loading-overlay">
      <span>Cargando gráfica...</span>
    </div>

    <div v-if="chartDataIsEmpty && !isLoading" class="loading-overlay">
      <span>No hay datos disponibles para los filtros seleccionados.</span>
    </div>

    <v-chart
      v-if="!isLoading && !chartDataIsEmpty"
      :option="option"
      autoresize
      class="chart"
    />
  </div>
</template>

<script setup>
import { ref, watch, onUnmounted } from "vue";
import { use } from "echarts/core";
import VChart from "vue-echarts";
import axios from "axios";
import { CanvasRenderer } from "echarts/renderers";
import { HeatmapChart } from "echarts/charts";
import {
  TitleComponent,
  TooltipComponent,
  GridComponent,
  VisualMapComponent,
  DataZoomComponent,
} from "echarts/components";

use([
  CanvasRenderer,
  HeatmapChart,
  TitleComponent,
  TooltipComponent,
  GridComponent,
  VisualMapComponent,
  DataZoomComponent,
]);

const props = defineProps({
  apiUrl: { type: String, required: true },
  chartTitle: { type: String, required: true },
  idPaises: { type: Array, default: () => [] },
  idEstados: { type: Array, default: () => [] },
  idMunicipios: { type: Array, default: () => [] },
  idPredios: { type: Array, required: true },
  idGrupos: { type: Array, required: true },
  idCategorias: { type: Array, required: true },
  idTiposDocumento: { type: Array, required: true },
  idTiposInmueble: { type: Array, required: true },
  estadoArchivo: { type: String, required: true },
  idEdificios: { type: Array, default: () => [] },
  idNiveles: { type: Array, default: () => [] },
  idZonas: { type: Array, default: () => [] },
});

// --- ESTADO REACTIVO ---
const option = ref({});
const isLoading = ref(true);
const chartDataIsEmpty = ref(false);
let cancelTokenSource = null;

// --- LÓGICA DE LA GRÁFICA ---
const fetchChartData = async () => {
  isLoading.value = true;
  chartDataIsEmpty.value = false;

  if (cancelTokenSource) {
    cancelTokenSource.cancel(
      "Nueva solicitud iniciada, cancelando la anterior."
    );
  }
  cancelTokenSource = axios.CancelToken.source();

  try {
    const response = await axios.get(props.apiUrl, {
      params: {
        id_paises: props.idPaises,
        id_estados: props.idEstados,
        id_municipios: props.idMunicipios,
        predio_ids: props.idPredios,
        grupo_ids: props.idGrupos,
        categoria_ids: props.idCategorias,
        tipo_doc_ids: props.idTiposDocumento,
        tipo_inmueble_ids: props.idTiposInmueble,
        estado_archivo: props.estadoArchivo,
        id_edificios: props.idEdificios,
        id_niveles: props.idNiveles,
        id_zonas: props.idZonas,
      },
      cancelToken: cancelTokenSource.token,
    });

    const data = response.data;

    if (!data || data.length === 0) {
      chartDataIsEmpty.value = true;
      option.value = {};
      return;
    }

    const allX = [...new Set(data.map((d) => d.x))].sort();
    const allY = [...new Set(data.map((d) => d.y))].sort();

    const seriesData = data.map((d) => [
      allX.indexOf(d.x),
      allY.indexOf(d.y),
      d.v,
    ]);

    const dataZoomConfig = [];
    const xItemThreshold = 3;
    const yItemThreshold = 6;

    const sliderStyle = {
      type: "slider",
      showDetail: false,
      showDataShadow: false,
      backgroundColor: "rgba(0,0,0,0.05)",
      fillerColor: "rgba(0,0,0,0.2)",
      borderColor: "transparent",
    };

    if (allX.length > xItemThreshold) {
      dataZoomConfig.push({
        ...sliderStyle,
        xAxisIndex: [0],
        start: 0,
        end: (xItemThreshold / allX.length) * 100,
        height: 12,
        bottom: "2px",
      });
    }

    if (allY.length > yItemThreshold) {
      dataZoomConfig.push({
        ...sliderStyle,
        yAxisIndex: [0],
        start: 0,
        end: (yItemThreshold / allY.length) * 100,
        width: 12,
        right: "2px",
      });
    }

    option.value = {
      title: {
        text: props.chartTitle,
        left: "center",
        top: "2%",
      },
      tooltip: {
        position: "top",
        formatter: (params) => {
          const xLabel = allX[params.value[0]];
          const yLabel = allY[params.value[1]];
          const value = params.value[2];
          return `<b>${yLabel}</b><br/>${xLabel}<br/>Cumplimiento: ${value}%`;
        },
      },
      grid: {
        left: "2%",
        right: "8%",
        bottom: "22%",
        top: "10%",
        containLabel: true,
      },
      xAxis: {
        type: "category",
        data: allX,
        splitArea: { show: true },
        position: "top",
        axisLabel: {
          rotate: 30,
          interval: 0,
        },
      },
      yAxis: {
        type: "category",
        data: allY,
        splitArea: { show: true },
        axisLabel: {
          interval: 0,
          overflow: "break",
        },
      },
      visualMap: {
        min: 0,
        max: 100,
        calculable: true,
        orient: "horizontal",
        left: "center",
        bottom: "10%",
        inRange: {
          color: ["#f87171", "#facc15", "#4ade80"],
        },
        textStyle: { color: "#333" },
      },
      dataZoom: dataZoomConfig,
      series: [
        {
          name: "Cumplimiento",
          type: "heatmap",
          data: seriesData,
          label: {
            show: true,
            formatter: (params) => `${params.value[2]}%`,
          },
          emphasis: {
            itemStyle: {
              shadowBlur: 10,
              shadowColor: "rgba(0, 0, 0, 0.5)",
            },
          },
        },
      ],
    };
  } catch (error) {
    if (axios.isCancel(error)) {
      console.log("Solicitud cancelada:", error.message);
    } else {
      console.error("Error al cargar datos de heatmap:", error);
      chartDataIsEmpty.value = true;
      option.value = {};
    }
  } finally {
    isLoading.value = false;
  }
};

// --- WATCHERS ---
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
  fetchChartData,
  {
    deep: true,
    immediate: true,
  }
);

// --- CICLO DE VIDA ---
onUnmounted(() => {
  if (cancelTokenSource) {
    cancelTokenSource.cancel("Componente destruido.");
  }
});
</script>

<style scoped>
.chart-container {
  width: 100%;
  height: 100%;
}
.chart {
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
  color: #6c757d; /* text-secondary de Bootstrap */
  font-size: 1.2rem;
}
</style>
