<template>
  <BaseHorizontalBar
    apiUrl="/api/v1/tickets-rejected"
    chartTitle="Rechazo por Predio"
    :startDate="startDate"
    :endDate="endDate"
    :id="id"
  />
</template>
<script>
import BaseHorizontalBar from '../BaseHorizontalBar.vue';
export default {
  components: { BaseHorizontalBar },
  props: ['startDate', 'endDate', 'id']
};
</script>
