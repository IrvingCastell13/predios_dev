<template>
     <div  >
        <GMapAutocomplete
        class="form-control location"
            @place_changed="getLocation"
            placeholder="Ej. Av. Libertador 6665, CDMX"
            autocomplete="off"
            id="autocompleteGoogle"
            ref="autocomplete"
        />
    </div>
</template>

<script setup>
import { usestoreMarketMapsStore } from './store/storeMarketMaps';


import { defineEmits } from "vue";
    const emit = defineEmits(["changemarket"]); // Definir los eventos que se pueden emitir

    const storeMarker = usestoreMarketMapsStore()
    const getLocation = e => {



        storeMarker.markerOptions.position = {  lat:  e.geometry.location.lat(), lng: e.geometry.location.lng() }
        storeMarker.center = {  lat: e.geometry.location.lat(), lng: e.geometry.location.lng() }
        storeMarker.zoom = 15

        emit('changemarket', e.geometry.location.lat(), e.geometry.location.lng());
    }

</script>




