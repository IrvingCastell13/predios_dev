<template>
     <div class="tooltipcontent">
            <slot></slot>
            <span v-if="text" class="tooltiptext">{{ text }}</span>
        </div>
  </template>

  <script setup>
  defineProps({
    text: String, // Define la prop correctamente
  });
  </script>
