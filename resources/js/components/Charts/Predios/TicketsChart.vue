<template>
  <BaseHorizontalStackedBar
    apiUrl="/api/v1/tickets-by-predio"
    chartTitle="Tickets por Predio"
    :startDate="startDate"
    :endDate="endDate"
    :id="id"
  />
</template>

<script>
import BaseHorizontalStackedBar from '../BaseHorizontalStackedBar.vue';

export default {
  components: { BaseHorizontalStackedBar },
  props: {
    startDate: String,
    endDate: String,
    id: String
  }
};
</script>
