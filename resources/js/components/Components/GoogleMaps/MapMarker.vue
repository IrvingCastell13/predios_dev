<template>

    <div style="width: 100%; height: auto;">

        <GMapMap ref="map" :center="storeMarker.center" :zoom="storeMarker.zoom"
            :options="{
                zoomControl: true,
                mapTypeControl: true,
                scaleControl: true,
                streetViewControl: false,
                rotateControl: true,
                fullscreenControl: false,
            }"

            style="width: 100%; height: 250px">

            <GMapMarker :position="storeMarker.markerOptions.position" :draggable="true"
                :icon="storeMarker.markerOptions.label" @dragend="changeLocation"/>
            </GMapMap>
    </div>
</template>

<script setup>
    import {ref, onMounted, watch} from 'vue';
    import { usestoreMarketMapsStore } from "./store/storeMarketMaps";

    import { defineEmits } from "vue";
    const emit = defineEmits(["changemarket"]); // Definir los eventos que se pueden emitir

    // import eventBus from '../../eventBus'

    const props = defineProps(['lat', 'lng'])
    const storeMarker = usestoreMarketMapsStore()

    const changeLocation = e => {
        emit('changemarket',  e.latLng.lat(), e.latLng.lng());

    }

    watch(() => [props.lat, props.lng], ([newLat, newLng], [oldLat, oldLng]) => {

        storeMarker.markerOptions.position.lat = newLat
        storeMarker.markerOptions.position.lng = newLng
        storeMarker.center.lat = newLat
        storeMarker.center.lng = newLng


    })

    onMounted(() => {


        if(props.lat == NaN && props.lng == NaN){

            storeMarker.markerOptions.position.lat = 19.387554264205153
            storeMarker.markerOptions.position.lng = -99.14202762210536
            storeMarker.center.lat = 19.387554264205153
            storeMarker.center.lng = -99.14202762210536
        }

        // TODO: VER BIEN QUE MUESTR EL PIONT

        // if (typeof props.lat === 'number' && typeof props.lng === 'number') {
        //     storeMarker.markerOptions.position.lat = props.lat
        //     storeMarker.markerOptions.position.lng = props.lng
        //     storeMarker.center.lat = props.lat
        //     storeMarker.center.lng = props.lng
        // } else {
        //     console.error('Latitud y longitud deben ser números válidos');
        // }
    })
</script>
