<template>
  <AppLayout clases="flex-fill">
    <div class="h-full d-flex flex-column">
      <div class="white-box px-3 py-2">
        <div class="py-2">
          <div class="row g-3 align-items-center">
            <div class="col-auto">
              <router-link
                :to="{ name: 'planes.index' }"
                class="text-decoration-none"
              >
                <i class="fas fa-arrow-left fa-2x"></i>
              </router-link>
            </div>
            <div class="col-auto">
              <i class="fas fa-tasks fa-2x"></i>
            </div>
            <div class="col">
              <div class="row g-2 align-items-center">
                <div class="col">
                  <div>
                    <h6 class="fw-bold primary">
                      {{ store.plan?.NombrePlan }} [ID-{{
                        store.plan?.ClavePlan
                      }}]
                    </h6>
                  </div>
                </div>
                <div class="col-auto">
                  <select class="form-control" v-model="selectedView">
                    <option value="month">Mensual</option>
                    <option value="year">Anual</option>
                  </select>
                </div>
                <div class="col-auto"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3 px-2 pb-3 border-top pt-3 mt-2">
          <div class="col-md-2">
            <label class="form-label fw-bold">Predio:</label>
            <vue-multiselect
              v-model="store.filtros.predios"
              :options="store.opciones.predios"
              :multiple="true"
              :close-on-select="false"
              placeholder="Todos"
              label="NombrePredio"
              track-by="IDPredio"
            ></vue-multiselect>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Edificio:</label>
            <vue-multiselect
              v-model="store.filtros.edificios"
              :options="store.opciones.edificios"
              :multiple="true"
              :close-on-select="false"
              placeholder="Todos"
              label="NombreEdificio"
              track-by="IDEdificio"
            ></vue-multiselect>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Nivel:</label>
            <vue-multiselect
              v-model="store.filtros.niveles"
              :options="store.opciones.niveles"
              :multiple="true"
              :close-on-select="false"
              placeholder="Todos"
              label="NombreNivel"
              track-by="IDNivel"
            ></vue-multiselect>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Zona:</label>
            <vue-multiselect
              v-model="store.filtros.zonas"
              :options="store.opciones.zonas"
              :multiple="true"
              :close-on-select="false"
              placeholder="Todos"
              label="NombreZona"
              track-by="IDZona"
            ></vue-multiselect>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Sistema:</label>
            <vue-multiselect
              v-model="store.filtros.sistemas"
              :options="store.opciones.sistemas"
              :multiple="true"
              :close-on-select="false"
              placeholder="Todos"
              label="NombreSistema"
              track-by="IDSistema"
            ></vue-multiselect>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Subsistema:</label>
            <vue-multiselect
              v-model="store.filtros.subsistemas"
              :options="store.opciones.subsistemas"
              :multiple="true"
              :close-on-select="false"
              :disabled="store.filtros.sistemas.length === 0"
              placeholder="Seleccione sistema"
              label="NombreSubsistema"
              track-by="IDSubsistema"
            ></vue-multiselect>
          </div>

          <div class="col-md-2">
            <label class="form-label fw-bold">Responsable:</label>
            <vue-multiselect
              v-model="store.filtros.responsables"
              :options="store.opciones.responsables"
              :multiple="true"
              :close-on-select="false"
              placeholder="Todos"
              label="full_name"
              track-by="IDPersona"
            ></vue-multiselect>
          </div>
        </div>
      </div>

      <component :is="currentGanttComponent" :id="id" />
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import AppLayout from "../../../../../Layout/App.vue";
import { usestoreGanttStore } from "./js/storeGantt";
import VueMultiselect from "vue-multiselect";
import GanttMonth from "./GanttMonth.vue";
import GanttYear from "./GanttYear.vue";

const props = defineProps(["id"]);
const store = usestoreGanttStore();

const selectedView = ref("month");

const currentGanttComponent = computed(() => {
  return selectedView.value === "year" ? GanttYear : GanttMonth;
});

function debounce(func, delay) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), delay);
  };
}

const reloadGanttData = () => {
  if (!store.plan?.IDPlan) return;
  if (selectedView.value === "year") {
    store.getGanttDataForYear(props.id);
  } else {
    store.getGanttData(props.id);
  }
};

const debouncedReload = debounce(reloadGanttData, 400);

// Watcher principal para recargar los datos del Gantt cuando CUALQUIER filtro cambia.
watch(() => store.filtros, debouncedReload, { deep: true });

// Watcher para cambiar entre vista mensual y anual.
watch(selectedView, reloadGanttData);

// --- LÓGICA DE VALIDACIÓN DE CASCADA (NUEVA Y MEJORADA) ---
// En lugar de borrar selecciones, las validamos para mantener solo las que son válidas.

// Si cambian las OPCIONES de edificio (porque se cambió un predio), validamos la SELECCIÓN de edificios.
watch(
  () => store.opciones.edificios,
  (nuevasOpcionesDeEdificio) => {
    const opcionesIds = nuevasOpcionesDeEdificio.map((o) => o.IDEdificio);
    store.filtros.edificios = store.filtros.edificios.filter((seleccion) =>
      opcionesIds.includes(seleccion.IDEdificio)
    );
  },
  { deep: true }
);

// Si cambian las OPCIONES de nivel, validamos la SELECCIÓN de niveles.
watch(
  () => store.opciones.niveles,
  (nuevasOpcionesDeNivel) => {
    const opcionesIds = nuevasOpcionesDeNivel.map((o) => o.IDNivel);
    store.filtros.niveles = store.filtros.niveles.filter((seleccion) =>
      opcionesIds.includes(seleccion.IDNivel)
    );
  },
  { deep: true }
);

// Si cambian las OPCIONES de zona, validamos la SELECCIÓN de zonas.
watch(
  () => store.opciones.zonas,
  (nuevasOpcionesDeZona) => {
    const opcionesIds = nuevasOpcionesDeZona.map((o) => o.IDZona);
    store.filtros.zonas = store.filtros.zonas.filter((seleccion) =>
      opcionesIds.includes(seleccion.IDZona)
    );
  },
  { deep: true }
);

onMounted(async () => {
  store.id = props.id;
  await store.cargarTodasLasOpciones();
  await store.getPlan(props.id);
  reloadGanttData();
});
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>