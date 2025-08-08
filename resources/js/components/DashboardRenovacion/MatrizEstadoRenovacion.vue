<template>
  <div class="chart-container" style="height: 450px; position: relative">
    <div
      v-if="isLoading"
      class="loading-overlay"
    >
      <span>Cargando gráfica...</span>
    </div>

    <div
      v-if="chartDataIsEmpty && !isLoading"
      class="loading-overlay"
    >
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

// --- PROPS ---
const props = defineProps({
  apiUrl: { type: String, required: true },
  chartTitle: { type: String, default: "Matriz de Estado de Renovación" },
  idPredios: { type: Array, required: true },
  idGrupos: { type: Array, required: true },
  idCategorias: { type: Array, required: true },
  fechaInicio: { type: String, default: null },
  fechaFin: { type: String, default: null },
});

// --- ESTADO REACTIVO ---
const option = ref({});
const isLoading = ref(true);
const chartDataIsEmpty = ref(false);
let cancelTokenSource = null;

// --- LÓGICA DE LA GRÁFICA ---
const mapEstadoToValue = (estadoId) => {
  if (estadoId === 'estado-03') return 3; // Ejecutado
  if (estadoId === 'estado-02') return 2; // En Proceso
  if (estadoId === 'estado-01') return 1; // Pendiente
  return 0; // Sin Datos o Faltante
};

const fetchChartData = async () => {
  isLoading.value = true;
  chartDataIsEmpty.value = false;

  // Si ya existe una petición en curso, la cancela para evitar condiciones de carrera.
  if (cancelTokenSource) {
    cancelTokenSource.cancel("Nueva solicitud iniciada, cancelando la anterior.");
  }
  cancelTokenSource = axios.CancelToken.source();

  try {
    const response = await axios.get(props.apiUrl, {
      params: {
        predio_ids: props.idPredios,
        grupo_ids: props.idGrupos,
        categoria_ids: props.idCategorias,
        fecha_inicio: props.fechaInicio,
        fecha_fin: props.fechaFin,
      },
      cancelToken: cancelTokenSource.token,
    });

    const data = response.data;

    // Si la API no devuelve datos, se muestra el mensaje correspondiente.
    if (!data || data.length === 0) {
      chartDataIsEmpty.value = true;
      option.value = {};
      return;
    }

    // Se extraen y ordenan las etiquetas únicas para los ejes X e Y.
    const allX = [...new Set(data.map((d) => d.x))].sort();
    const allY = [...new Set(data.map((d) => d.y))].sort();

    // Se transforman los datos al formato que ECharts requiere para un heatmap: [indiceX, indiceY, valor]
    // También se adjunta el objeto original (`raw`) para usarlo en el tooltip.
    const seriesData = data.map(d => ({
        value: [
            allX.indexOf(d.x),
            allY.indexOf(d.y),
            mapEstadoToValue(d.v) // 'v' es el ID del estado
        ],
        raw: d // Guardamos el objeto original para el tooltip
    }));
    
    const dataZoomConfig = [];
    if (allX.length > 10) {
        dataZoomConfig.push({
            type: 'slider',
            xAxisIndex: [0],
            start: 0,
            end: (10 / allX.length) * 100,
            bottom: '2px',
        });
    }
    if (allY.length > 15) {
        dataZoomConfig.push({
            type: 'slider',
            yAxisIndex: [0],
            start: 0,
            end: (15 / allY.length) * 100,
            right: '2px',
        });
    }

    // Se construye el objeto de configuración de ECharts.
    option.value = {
      title: {
        text: props.chartTitle,
        left: 'center',
        top: '2%',
      },
      tooltip: {
        position: 'top',
        formatter: (params) => {
          // El tooltip se construye usando los datos originales guardados en 'raw'.
          const rawData = params.data.raw;
          const xLabel = rawData.x;
          const yLabel = rawData.y;
          return `<b>${yLabel}</b><br/>` +
                 `${xLabel}<br/>` +
                 `Estado: <b>${rawData.estado || 'N/D'}</b><br/>` +
                 `Tiene OT: ${rawData.num_ot}<br/>` +
                 `Fecha Inicio: ${rawData.fecha_inicio || 'N/D'}<br/>` +
                 `Fecha Fin: ${rawData.fecha_fin || 'N/D'}`;
        }
      },
      grid: {
        left: '2%',
        right: '8%',
        bottom: '18%',
        top: '10%',
        containLabel: true,
      },
      xAxis: {
        type: 'category',
        data: allX,
        splitArea: { show: true },
        axisLabel: { rotate: 30, interval: 0 }
      },
      yAxis: {
        type: 'category',
        data: allY,
        splitArea: { show: true },
        axisLabel: { interval: 0 }
      },
      visualMap: {
        type: 'piecewise',
        orient: 'horizontal',
        left: 'center',
        bottom: '5%',
        pieces: [
            { value: 3, label: 'Ejecutado', color: '#4ade80' },
            { value: 2, label: 'En Proceso', color: '#60a5fa' },
            { value: 1, label: 'Pendiente', color: '#facc15' },
            { value: 0, label: 'Sin Datos', color: '#f87171' }
        ],
        calculable: false,
      },
      dataZoom: dataZoomConfig,
      series: [{
        name: 'Estado de Renovación',
        type: 'heatmap',
        data: seriesData,
        itemStyle: {
            borderColor: '#fff',
            borderWidth: 2
        },
        label: { show: false },
        emphasis: {
          itemStyle: {
            shadowBlur: 10,
            shadowColor: 'rgba(0, 0, 0, 0.5)'
          }
        }
      }]
    };

  } catch (error) {
    if (axios.isCancel(error)) {
      console.log("Solicitud cancelada:", error.message);
    } else {
      console.error("Error al cargar datos de la matriz:", error);
      chartDataIsEmpty.value = true;
      option.value = {};
    }
  } finally {
    isLoading.value = false;
  }
};

// --- WATCHERS ---
// Observa cambios en cualquiera de los filtros y vuelve a llamar a fetchData.
watch(
  () => [
    props.idPredios,
    props.idGrupos,
    props.idCategorias,
    props.fechaInicio,
    props.fechaFin,
  ],
  fetchChartData,
  {
    deep: true, // Necesario para observar cambios dentro de los arrays.
    immediate: true, // Ejecuta la función inmediatamente al montar el componente.
  }
);

// --- CICLO DE VIDA ---
// Se asegura de cancelar cualquier petición pendiente cuando el componente se destruye.
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
  color: #6c757d;
  font-size: 1.2rem;
}
</style>
