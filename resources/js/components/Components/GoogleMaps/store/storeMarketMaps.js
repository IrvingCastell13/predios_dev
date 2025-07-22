import { defineStore } from 'pinia';

export const usestoreMarketMapsStore = defineStore('storeMarketMaps', {
    state: () => ({
        zoom: 8,
        center:{ lat: 19.435364636481754,  lng: -99.13607044374632 },
        markerOptions:{
            position: {  lat: 0, lng: 0 },
            label: "",
            title: "",
            icon: "/images/merkar-modal.png"
        },
    }),

    actions: {

    },
});
