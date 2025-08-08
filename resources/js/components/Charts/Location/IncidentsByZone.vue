<template>
  <BaseHorizontalStackedBar
    apiUrl="/api/v1/incidents-by-zone"
    chartTitle="Incidencias por Zona"
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
