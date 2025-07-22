<template>
  <BaseHorizontalStackedBar
    apiUrl="/api/v1/tickets-by-zone"
    chartTitle="Tickets por Zona"
    :startDate="startDate"
    :endDate="endDate"
    :id="id"
  />
</template>
<script>
import BaseHorizontalStackedBar from '../BaseHorizontalStackedBar.vue';
export default {
  components: { BaseHorizontalStackedBar },
  props: ['startDate', 'endDate','id']
};
</script>
