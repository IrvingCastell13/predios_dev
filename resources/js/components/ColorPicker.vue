<template>
    <div class="color-picker-container" ref="containerRef">
      <!-- Panel del color picker -->
      <div v-if="show" class="picker-popup">
        <Chrome v-model="colorValue" @update:modelValue="updateColor" />
      </div>
    </div>
  </template>

  <script>
  import { Chrome } from '@ckpack/vue-color'

  export default {
    name: 'ReusableColorPicker',
    components: {
      Chrome
    },
    props: {
      // Propiedad para controlar la visibilidad desde el componente padre
      show: {
        type: Boolean,
        default: false
      },
      // Propiedad para v-model (valor del color)
      modelValue: {
        type: String,
        default: '#42b883' // Color verde de Vue por defecto
      }
    },
    emits: ['update:modelValue', 'outside-click'],
    data() {
      return {
        // Copia local del valor del color para el v-model interno
        colorValue: {
          hex: this.modelValue,
          rgba: { r: 66, g: 184, b: 131, a: 1 }
        }
      }
    },
    watch: {
      // Observar cambios en la propiedad modelValue para actualizar la copia local
      modelValue: {
        handler(newVal) {
          if (newVal !== this.colorValue.hex) {
            this.initializeFromHex(newVal);
          }
        },
        immediate: true
      },
      // Observar cuando se muestra el picker para inicializar colores
      show: {
        handler(newVal) {
          if (newVal) {
            this.initializeFromHex(this.modelValue);
          }
        }
      }
    },
    mounted() {
      // Inicializar colores desde el prop
      this.initializeFromHex(this.modelValue);

      // Agregar event listener para cerrar al hacer clic fuera
      document.addEventListener('mousedown', this.handleClickOutside);
    },
    beforeUnmount() {
      // Limpiar event listener al desmontar
      document.removeEventListener('mousedown', this.handleClickOutside);
    },
    methods: {
      updateColor(newColor) {
        // Emitir evento para actualizar v-model en el componente padre
        this.$emit('update:modelValue', newColor.hex);
      },

      handleClickOutside(event) {
        // Emitir evento cuando se hace clic fuera del componente
        if (this.show && this.$refs.containerRef && !this.$refs.containerRef.contains(event.target)) {
          this.$emit('outside-click');
        }
      },

      initializeFromHex(hexColor) {
        // Inicializar los colores desde un valor hexadecimal
        const rgb = this.hexToRgb(hexColor);
        if (rgb) {
          this.colorValue = {
            hex: hexColor,
            rgba: { r: rgb.r, g: rgb.g, b: rgb.b, a: 1 }
          };
        }
      },

      hexToRgb(hex) {
        // Convertir color hexadecimal a RGB
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
          r: parseInt(result[1], 16),
          g: parseInt(result[2], 16),
          b: parseInt(result[3], 16)
        } : null;
      }
    }
  }
  </script>

  <style scoped>
  .color-picker-container {
    position: relative;
    display: inline-block;
  }

  .picker-popup {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 100;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    border-radius: 4px;
    background-color: white;
  }
  </style>