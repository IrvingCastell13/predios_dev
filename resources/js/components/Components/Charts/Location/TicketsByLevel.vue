<template>
  <BaseHorizontalStackedBar
    apiUrl="/api/v1/tickets-by-level"
    chartTitle="Tickets por Nivel"
    :startDate="startDate"
    :endDate="endDate"
    :id="id"
  />
</template>
<script>
import BaseHorizontalStackedBar from '../BaseHorizontalStackedBar.vue';
export default {
  components: { BaseHorizontalStackedBar },
  props: ['startDate', 'endDate', 'id']
};
</script>
