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
    const response = await axios.get("/api/bi/ordenes-trabajo/por-tipo", {
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
    });

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
  chartOptions.value = {
    title: {
      text: "Distribución de OTs por Tipo",
      left: "center",
    },
    tooltip: {
      trigger: "item",
      formatter: "{b}: {c} OTs ({d}%)",
    },
    legend: {
      orient: "vertical",
      left: "left",
      top: "middle",
      type: 'scroll', // Añadido para que la leyenda sea desplazable
    },
    series: [
      {
        name: "Tipo de OT",
        type: "pie",
        radius: "70%",
        center: ["65%", "50%"],
        data: data.map((item) => ({
          name: item.tipo,
          value: item.total,
        })),
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