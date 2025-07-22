<template>
  <BaseLineChart
    apiUrl="/api/v1/tickets-resueltos-por-mes"
    chartTitle="Resumen de Tickets del Predio Resueltos"
    :startDate="startDate"
    :endDate="endDate"
  />
</template>

<script>
import BaseLineChart from '../BaseLineChart.vue';

export default {
  name: 'SummaryTickets',
  components: { BaseLineChart },
  props: ['startDate', 'endDate']
};
</script>
