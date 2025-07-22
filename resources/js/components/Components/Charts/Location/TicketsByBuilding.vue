<template>
  <BaseHorizontalStackedBar
    apiUrl="/api/v1/tickets-by-building"
    chartTitle="Tickets por Edificio"
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
