<template>
  <div>
    <h1 class="mb-4 display-5">Dashboard de Órdenes de Trabajo</h1>
    <p class="lead mb-5">
      Análisis interactivo del rendimiento, estados y asignaciones de las
      órdenes de trabajo.
    </p>

    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Filtros</h5>
        <div class="row align-items-end">
          <div class="col-md-4">
            <label class="form-label fw-bold">Predio:</label>
            <vue-multiselect
              v-model="filtros.prediosSeleccionados"
              :options="opcionesFiltros.predios"
              :multiple="true"
              :close-on-select="false"
              placeholder="Todos los predios"
              label="NombrePredio"
              track-by="IDPredio"
            ></vue-multiselect>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-bold">Tipo de Orden de Trabajo:</label>
            <vue-multiselect
              v-model="filtros.tiposOtSeleccionados"
              :options="opcionesFiltros.tiposOT"
              :multiple="true"
              :close-on-select="false"
              placeholder="Todos los tipos"
              label="NombreTipoOT"
              track-by="IDTipoOT"
            ></vue-multiselect>
          </div>

          <div class="col-md-4">
            <div class="row">
              <div class="col-6">
                <label class="form-label fw-bold">Fecha de Inicio:</label>
                <input
                  type="date"
                  class="form-control"
                  v-model="filtros.fechaInicio"
                />
              </div>
              <div class="col-6">
                <label class="form-label fw-bold">Fecha de Fin:</label>
                <input
                  type="date"
                  class="form-control"
                  v-model="filtros.fechaFin"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <nav>
      <div class="nav nav-tabs mb-4">
        <button
          class="nav-link"
          :class="{ active: activeTab === 'Generales' }"
          @click="activeTab = 'Generales'"
        >
          Generales
        </button>
        <button
          class="nav-link"
          :class="{ active: activeTab === 'Responsables' }"
          @click="activeTab = 'Responsables'"
        >
          Responsables
        </button>
        <button
          class="nav-link"
          :class="{ active: activeTab === 'Categoria' }"
          @click="activeTab = 'Categoria'"
        >
          Categoría
        </button>
      </div>
    </nav>

    <div class="tab-content">
      <div v-if="activeTab === 'Generales'">
        <div class="row">
          <div class="col-12 mb-4">
            <div class="card">
              <div class="card-body">
                <GraficaOtDetalle
                  v-if="isReady"
                  :predio-ids="idPrediosSeleccionados"
                  :tipo-ot-ids="idTiposOtSeleccionados"
                  :fecha-inicio="filtros.fechaInicio"
                  :fecha-fin="filtros.fechaFin"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 mb-4">
            <div class="card">
              <div class="card-body">
                <GraficaOtPorEstado
                  v-if="isReady"
                  :predio-ids="idPrediosSeleccionados"
                  :tipo-ot-ids="idTiposOtSeleccionados"
                  :fecha-inicio="filtros.fechaInicio"
                  :fecha-fin="filtros.fechaFin"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <GraficaOtGlobal
                  v-if="isReady"
                  :predio-ids="idPrediosSeleccionados"
                  :tipo-ot-ids="idTiposOtSeleccionados"
                  :fecha-inicio="filtros.fechaInicio"
                  :fecha-fin="filtros.fechaFin"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <GraficaOtATiempoPorPredio
                  v-if="isReady"
                  :predio-ids="idPrediosSeleccionados"
                  :tipo-ot-ids="idTiposOtSeleccionados"
                  :fecha-inicio="filtros.fechaInicio"
                  :fecha-fin="filtros.fechaFin"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'Responsables'">
        <div class="row">
          <div class="col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <GraficaOtPorResponsable
                  v-if="isReady"
                  :predio-ids="idPrediosSeleccionados"
                  :tipo-ot-ids="idTiposOtSeleccionados"
                  :fecha-inicio="filtros.fechaInicio"
                  :fecha-fin="filtros.fechaFin"
                />
              </div>
            </div>
          </div>
          <div class="col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <GraficaOtPorResponsableEstado
                  v-if="isReady"
                  :predio-ids="idPrediosSeleccionados"
                  :tipo-ot-ids="idTiposOtSeleccionados"
                  :fecha-inicio="filtros.fechaInicio"
                  :fecha-fin="filtros.fechaFin"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <GraficaOtATiempoPorResponsable
                  v-if="isReady"
                  :predio-ids="idPrediosSeleccionados"
                  :tipo-ot-ids="idTiposOtSeleccionados"
                  :fecha-inicio="filtros.fechaInicio"
                  :fecha-fin="filtros.fechaFin"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'Categoria'">
        <div class="row">
          <div class="col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <GraficaOtPorTipo
                  v-if="isReady"
                  :predio-ids="idPrediosSeleccionados"
                  :tipo-ot-ids="idTiposOtSeleccionados"
                  :fecha-inicio="filtros.fechaInicio"
                  :fecha-fin="filtros.fechaFin"
                />
              </div>
            </div>
          </div>
          <div class="col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <GraficaOtATiempoPorTipo
                  v-if="isReady"
                  :predio-ids="idPrediosSeleccionados"
                  :tipo-ot-ids="idTiposOtSeleccionados"
                  :fecha-inicio="filtros.fechaInicio"
                  :fecha-fin="filtros.fechaFin"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import VueMultiselect from "vue-multiselect";
//Generales
import GraficaOtDetalle from "./Generales/GraficaOtDetalle.vue";
import GraficaOtPorEstado from "./Generales/GraficaOtPorEstado.vue";
import GraficaOtGlobal from "./Generales/GraficaOtGlobal.vue";
import GraficaOtATiempoPorPredio from "./Generales/GraficaOtATiempoPorPredio.vue";
//Responsables
import GraficaOtPorResponsable from "./Responsables/GraficaOtPorResponsable.vue";
import GraficaOtPorResponsableEstado from "./Responsables/GraficaOtPorResponsableEstado.vue";
import GraficaOtATiempoPorResponsable from "./Responsables/GraficaOtATiempoPorResponsable.vue";
//Categoria
import GraficaOtPorTipo from "./Categoria/GraficaOtPorTipo.vue";
import GraficaOtATiempoPorTipo from './Categoria/GraficaOtATiempoPorTipo.vue';

export default {
  components: {
    VueMultiselect,
    //Generales
    GraficaOtDetalle,
    GraficaOtPorEstado,
    GraficaOtGlobal,
    GraficaOtATiempoPorPredio,
    //Responsables
    GraficaOtPorResponsable,
    GraficaOtPorResponsableEstado,
    GraficaOtATiempoPorResponsable,
    GraficaOtPorTipo,
    GraficaOtATiempoPorTipo
  },
  data() {
    return {
      activeTab: "Generales",

      // Almacena las opciones para los dropdowns
      opcionesFiltros: {
        predios: [],
        tiposOT: [],
      },

      // Almacena los valores seleccionados en los filtros
      filtros: {
        prediosSeleccionados: [],
        tiposOtSeleccionados: [],
        fechaInicio: null,
        fechaFin: null,
      },

      isReady: false, // Para saber cuándo se han cargado los filtros
    };
  },
  computed: {
    // Propiedades computadas para pasar solo los IDs a las gráficas hijas
    idPrediosSeleccionados() {
      return this.filtros.prediosSeleccionados.map((p) => p.IDPredio);
    },
    idTiposOtSeleccionados() {
      return this.filtros.tiposOtSeleccionados.map((t) => t.IDTipoOT);
    },
  },
  async mounted() {
    // Carga los datos para los filtros cuando el componente se monta
    await this.cargarOpcionesFiltros();
    this.isReady = true;
  },
  methods: {
    async cargarOpcionesFiltros() {
      try {
        // Hacemos las llamadas a la API en paralelo para más eficiencia
        const [prediosRes, tiposOtRes] = await Promise.all([
          axios.get("/api/bi/listar-predios"), // Reutilizamos este endpoint
          axios.get("/api/bi/ordenes-trabajo/listar-tipos"), // Necesitaremos crear este endpoint
        ]);

        this.opcionesFiltros.predios = prediosRes.data;
        this.opcionesFiltros.tiposOT = tiposOtRes.data;
      } catch (error) {
        console.error("Error al cargar las opciones para los filtros:", error);
        // Aquí podrías mostrar una notificación al usuario
      }
    },
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style scoped>
/* Estilos opcionales para mejorar la apariencia */
.card-title {
  margin-bottom: 1.2rem;
}
.form-label {
  margin-bottom: 0.5rem;
}
</style>