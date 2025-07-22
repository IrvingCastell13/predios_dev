<template>
  <BaseHorizontalStackedBar
    apiUrl="/api/v1/incidents-by-predio"
    chartTitle="Incidencias por Predio"
    :startDate="startDate"
    :endDate="endDate"
  />
</template>
<script>
import BaseHorizontalStackedBar from '../BaseHorizontalStackedBar.vue';
export default {
  components: { BaseHorizontalStackedBar },
  props: ['startDate', 'endDate']
};
</script>