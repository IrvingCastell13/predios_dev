<template>
  <div class="chart-container" style="position: relative; height: 450px">
    <div v-if="isLoading" class="loading-overlay">
      <span>Cargando gráfica...</span>
    </div>
    <div v-if="!isLoading && !rawData.length" class="loading-overlay">
      <span>No hay datos para mostrar.</span>
    </div>
    <v-chart
      v-if="!isLoading && rawData.length"
      :option="chartOptions"
      autoresize
    />
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
});

const isLoading = ref(true);
const rawData = ref([]);
const chartOptions = ref({});
let cancelTokenSource = null;

const fetchData = async () => {
  isLoading.value = true;
  rawData.value = [];
  if (cancelTokenSource) cancelTokenSource.cancel();
  cancelTokenSource = axios.CancelToken.source();

  try {
    const response = await axios.get(
      "/api/bi/ordenes-trabajo/detalle-por-predio",
      {
        params: {
          predio_ids: props.predioIds,
          tipo_ot_ids: props.tipoOtIds,
          fecha_inicio: props.fechaInicio,
          fecha_fin: props.fechaFin,
        },
        cancelToken: cancelTokenSource.token,
      }
    );

    if (response.data && response.data.length > 0) {
      rawData.value = response.data;
      processChartData(response.data);
    }
  } catch (error) {
    if (!axios.isCancel(error)) console.error("Error al cargar datos:", error);
  } finally {
    isLoading.value = false;
  }
};

const processChartData = (data) => {
  const predios = [...new Set(data.map((d) => d.NombrePredio))].sort();
  const estados = [...new Set(data.map((d) => d.estado || "Sin estado"))];
  const colorMap = {
   
  };

  const series = estados.map((estado) => ({
    name: estado,
    type: "bar",
    stack: "ordenes",
    itemStyle: { color: colorMap[estado] },
    data: predios.map((predio) => {
      const ot = data.find(
        (d) =>
          d.NombrePredio === predio && (d.estado || "Sin estado") === estado
      );
      return ot ? { value: 1, raw_data: ot } : { value: 0 };
    }),
  }));

  const dataZoomConfig = [];
  const yAxisThreshold = 8; // Define cuántas barras se ven antes de que aparezca el scroll

  if (predios.length > yAxisThreshold) {
    dataZoomConfig.push({
      type: "slider",
      yAxisIndex: 0, // Controla el eje Y
      start: 0,
      end: (yAxisThreshold / predios.length) * 100,
      // Estilos para la barra de scroll
      right: "15px",
      top: 70, // Alineado con el 'top' del grid
      bottom: 40, // Alineado con el 'bottom' del grid
      width: 8,
      handleSize: "100%",
      showDetail: false,
      zoomLock: false,
      brushSelect: false,
    });
  }

  chartOptions.value = {
    title: { text: "Visor Detallado de OTs por Predio" },
    tooltip: {
      trigger: "item",
      formatter: (params) => {
        const item = params.data?.raw_data;
        if (!item) return "Sin datos";
        return `
          <strong>${item.DescripcionOt || "Sin descripción"}</strong><br/>
          <b>Predio:</b> ${item.NombrePredio}<br/>
          <b>Inicio Prog.:</b> ${item.FechaIniOrdenTrabajo || "-"}<br/>
          <b>Inicio Real:</b> ${item.FechaInicioReal || "-"}<br/>
          <b>Fin Prog.:</b> ${item.FechaFinOT || "-"}<br/>
          <b>Estado:</b> ${item.estado || "-"}
        `;
      },
    },
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

watch(
  () => [props.predioIds, props.tipoOtIds, props.fechaInicio, props.fechaFin],
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