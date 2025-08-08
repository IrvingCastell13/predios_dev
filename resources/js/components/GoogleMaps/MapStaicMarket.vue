<template>
    <GMapMap
        :center="center"
        :zoom="zoom"
        :options="options"
        style="width: 100%; height: 200px" >
        <GMapMarker
        :position="center"
        :draggable="false"
        :icon="icon"
        />
    </GMapMap>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps(['zoom', 'lat', 'long'])
const center = ref({ lat: parseFloat(props.lat), lng: parseFloat(props.long) })
const icon = ref("/images/marker.png")
const options = ref({

        zoomControl: true,
        mapTypeControl: false,
        scaleControl: true,
        streetViewControl: false,
        rotateControl: true,
        fullscreenControl: true,
    })
</script>

<style lang="scss" scoped>

</style>
