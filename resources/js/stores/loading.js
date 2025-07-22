// stores/loading.js
import { defineStore } from 'pinia';

export const useLoadingStore = defineStore('loading', {
  state: () => ({
    isLoadingHeader: true, // Bandera de carga global
    isLoadingNav: true, // Bandera de carga global
    isLoading: true, // Bandera de carga global
    buttonLoading: {},
  }),
  actions: {
    startLoading() {
      this.isLoading = true;
    },
    stopLoading() {
      this.isLoading = false;
    },

    startLoadingHeader() {
        this.isLoadingHeader = true;
    },
    stopLoadingHeader() {
        this.isLoadingHeader = false;
    },
    startLoadingNav() {
        this.isLoadingNav = true;
    },
    stopLoadingNav() {
        this.isLoadingNav = false;
    },

    startButtonLoading(buttonId) {
        this.buttonLoading[buttonId] = true;
    },
    stopButtonLoading(buttonId) {
        this.buttonLoading[buttonId] = false;
    },
  },
});
