<template>
  <div>
    <h1 class="mb-4 display-5">Dashboard de Órdenes de Trabajo</h1>
    <p class="lead mb-4">
      Análisis interactivo del rendimiento, estados y asignaciones de las
      órdenes de trabajo.
    </p>
    <button class="btn btn-primary mb-4" @click="exportarActivo">
      <i class="fas fa-file-excel"></i> Exportar a Excel
    </button>

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

    <div v-if="!isReady" class="text-center text-secondary py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
      <p class="mt-2">Cargando dashboard...</p>
    </div>

    <div v-else class="tab-content">
      <div v-if="activeTab === 'Generales'">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Filtros de Generales</h5>
            <div class="row align-items-end">
              <div class="col-md-3">
                <label class="form-label fw-bold">Predio:</label
                ><vue-multiselect
                  v-model="filtros.Generales.prediosSeleccionados"
                  :options="opciones.predios"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombrePredio"
                  track-by="IDPredio"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Edificio:</label
                ><vue-multiselect
                  v-model="filtros.Generales.edificiosSeleccionados"
                  :options="opcionesDisponibles.Generales.edificios"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreEdificio"
                  track-by="IDEdificio"
                ></vue-multiselect>
              </div>
              <div class="col-md-2">
                <label class="form-label fw-bold">Nivel:</label
                ><vue-multiselect
                  v-model="filtros.Generales.nivelesSeleccionados"
                  :options="opcionesDisponibles.Generales.niveles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreNivel"
                  track-by="IDNivel"
                ></vue-multiselect>
              </div>
              <div class="col-md-2">
                <label class="form-label fw-bold">Zona:</label
                ><vue-multiselect
                  v-model="filtros.Generales.zonasSeleccionadas"
                  :options="opcionesDisponibles.Generales.zonas"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreZona"
                  track-by="IDZona"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Tipo de OT:</label
                ><vue-multiselect
                  v-model="filtros.Generales.tiposOtSeleccionados"
                  :options="opciones.tiposOT"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreTipoOT"
                  track-by="IDTipoOT"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Desde:</label
                ><input
                  type="date"
                  class="form-control"
                  v-model="filtros.Generales.fechaInicio"
                />
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Hasta:</label
                ><input
                  type="date"
                  class="form-control"
                  v-model="filtros.Generales.fechaFin"
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
                  v-bind="getPropsForTab('Generales')"
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
                  v-bind="getPropsForTab('Generales')"
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
                  v-bind="getPropsForTab('Generales')"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'Responsables'">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Filtros de Responsables</h5>
            <div class="row align-items-end">
              <div class="col-md-3">
                <label class="form-label fw-bold">Predio:</label
                ><vue-multiselect
                  v-model="filtros.Responsables.prediosSeleccionados"
                  :options="opciones.predios"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombrePredio"
                  track-by="IDPredio"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Edificio:</label
                ><vue-multiselect
                  v-model="filtros.Responsables.edificiosSeleccionados"
                  :options="opcionesDisponibles.Responsables.edificios"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreEdificio"
                  track-by="IDEdificio"
                ></vue-multiselect>
              </div>
              <div class="col-md-2">
                <label class="form-label fw-bold">Nivel:</label
                ><vue-multiselect
                  v-model="filtros.Responsables.nivelesSeleccionados"
                  :options="opcionesDisponibles.Responsables.niveles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreNivel"
                  track-by="IDNivel"
                ></vue-multiselect>
              </div>
              <div class="col-md-2">
                <label class="form-label fw-bold">Zona:</label
                ><vue-multiselect
                  v-model="filtros.Responsables.zonasSeleccionadas"
                  :options="opcionesDisponibles.Responsables.zonas"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreZona"
                  track-by="IDZona"
                ></vue-multiselect>
              </div>

              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Responsable:</label
                ><vue-multiselect
                  v-model="filtros.Responsables.responsablesSeleccionados"
                  :options="opciones.responsables"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreCompleto"
                  track-by="IDPersona"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Tipo de OT:</label
                ><vue-multiselect
                  v-model="filtros.Responsables.tiposOtSeleccionados"
                  :options="opciones.tiposOT"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreTipoOT"
                  track-by="IDTipoOT"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Desde:</label
                ><input
                  type="date"
                  class="form-control"
                  v-model="filtros.Responsables.fechaInicio"
                />
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Hasta:</label
                ><input
                  type="date"
                  class="form-control"
                  v-model="filtros.Responsables.fechaFin"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <GraficaOtPorResponsable
                  v-if="isReady"
                  v-bind="getPropsForTab('Responsables')"
                />
              </div>
            </div>
          </div>
          <div class="col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <GraficaOtPorResponsableEstado
                  v-if="isReady"
                  v-bind="getPropsForTab('Responsables')"
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
                  v-bind="getPropsForTab('Responsables')"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'Categoria'">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Filtros de Categoría</h5>
            <div class="row align-items-end">
              <div class="col-md-3">
                <label class="form-label fw-bold">Predio:</label
                ><vue-multiselect
                  v-model="filtros.Categoria.prediosSeleccionados"
                  :options="opciones.predios"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombrePredio"
                  track-by="IDPredio"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Edificio:</label
                ><vue-multiselect
                  v-model="filtros.Categoria.edificiosSeleccionados"
                  :options="opcionesDisponibles.Categoria.edificios"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreEdificio"
                  track-by="IDEdificio"
                ></vue-multiselect>
              </div>
              <div class="col-md-2">
                <label class="form-label fw-bold">Nivel:</label
                ><vue-multiselect
                  v-model="filtros.Categoria.nivelesSeleccionados"
                  :options="opcionesDisponibles.Categoria.niveles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreNivel"
                  track-by="IDNivel"
                ></vue-multiselect>
              </div>
              <div class="col-md-2">
                <label class="form-label fw-bold">Zona:</label
                ><vue-multiselect
                  v-model="filtros.Categoria.zonasSeleccionadas"
                  :options="opcionesDisponibles.Categoria.zonas"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreZona"
                  track-by="IDZona"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Tipo de OT:</label
                ><vue-multiselect
                  v-model="filtros.Categoria.tiposOtSeleccionados"
                  :options="opciones.tiposOT"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreTipoOT"
                  track-by="IDTipoOT"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Desde:</label
                ><input
                  type="date"
                  class="form-control"
                  v-model="filtros.Categoria.fechaInicio"
                />
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Hasta:</label
                ><input
                  type="date"
                  class="form-control"
                  v-model="filtros.Categoria.fechaFin"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <GraficaOtPorTipo
                  v-if="isReady"
                  v-bind="getPropsForTab('Categoria')"
                />
              </div>
            </div>
          </div>
          <div class="col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-body">
                <GraficaOtATiempoPorTipo
                  v-if="isReady"
                  v-bind="getPropsForTab('Categoria')"
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
import GraficaOtPorEstado from "./Generales/GraficaOtPorEstado.vue";
import GraficaOtGlobal from "./Generales/GraficaOtGlobal.vue";
import GraficaOtATiempoPorPredio from "./Generales/GraficaOtATiempoPorPredio.vue";
import GraficaOtPorResponsable from "./Responsables/GraficaOtPorResponsable.vue";
import GraficaOtPorResponsableEstado from "./Responsables/GraficaOtPorResponsableEstado.vue";
import GraficaOtATiempoPorResponsable from "./Responsables/GraficaOtATiempoPorResponsable.vue";
import GraficaOtPorTipo from "./Categoria/GraficaOtPorTipo.vue";
import GraficaOtATiempoPorTipo from "./Categoria/GraficaOtATiempoPorTipo.vue";

export default {
  components: {
    VueMultiselect,
    GraficaOtPorEstado,
    GraficaOtGlobal,
    GraficaOtATiempoPorPredio,
    GraficaOtPorResponsable,
    GraficaOtPorResponsableEstado,
    GraficaOtATiempoPorResponsable,
    GraficaOtPorTipo,
    GraficaOtATiempoPorTipo,
  },
  data() {
    return {
      activeTab: "Generales",
      isReady: false,
      opciones: {
        predios: [],
        tiposOT: [],
        edificios: [],
        niveles: [],
        zonas: [],
        responsables: [], // NUEVO: Para guardar la lista de responsables
      },
      opcionesDisponibles: {
        Generales: { edificios: [], niveles: [], zonas: [] },
        Responsables: { edificios: [], niveles: [], zonas: [] },
        Categoria: { edificios: [], niveles: [], zonas: [] },
      },
      filtros: {
        Generales: {
          prediosSeleccionados: [],
          edificiosSeleccionados: [],
          nivelesSeleccionados: [],
          zonasSeleccionadas: [],
          tiposOtSeleccionados: [],
          fechaInicio: null,
          fechaFin: null,
        },
        // INICIO DE LA MODIFICACIÓN
        Responsables: {
          prediosSeleccionados: [],
          edificiosSeleccionados: [],
          nivelesSeleccionados: [],
          zonasSeleccionadas: [],
          tiposOtSeleccionados: [],
          responsablesSeleccionados: [],
          fechaInicio: null,
          fechaFin: null,
        },
        // FIN DE LA MODIFICACIÓN
        Categoria: {
          prediosSeleccionados: [],
          edificiosSeleccionados: [],
          nivelesSeleccionados: [],
          zonasSeleccionadas: [],
          tiposOtSeleccionados: [],
          fechaInicio: null,
          fechaFin: null,
        },
      },
    };
  },
  watch: {
    "filtros.Generales.prediosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Generales");
      },
      deep: true,
    },
    "filtros.Generales.edificiosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Generales", "edificio");
      },
      deep: true,
    },
    "filtros.Generales.nivelesSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Generales", "nivel");
      },
      deep: true,
    },
    "filtros.Responsables.prediosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Responsables");
      },
      deep: true,
    },
    "filtros.Responsables.edificiosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Responsables", "edificio");
      },
      deep: true,
    },
    "filtros.Responsables.nivelesSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Responsables", "nivel");
      },
      deep: true,
    },
    "filtros.Categoria.prediosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Categoria");
      },
      deep: true,
    },
    "filtros.Categoria.edificiosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Categoria", "edificio");
      },
      deep: true,
    },
    "filtros.Categoria.nivelesSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Categoria", "nivel");
      },
      deep: true,
    },
  },
  async mounted() {
    await this.cargarOpcionesFiltros();
    this.isReady = true;
  },
  methods: {
    async cargarOpcionesFiltros() {
      try {
        // INICIO DE LA MODIFICACIÓN: Se añade la llamada a la nueva API
        const [
          prediosRes,
          tiposOtRes,
          edificiosRes,
          nivelesRes,
          zonasRes,
          responsablesRes,
        ] = await Promise.all([
          axios.get("/api/bi/listar-predios"),
          axios.get("/api/bi/ordenes-trabajo/listar-tipos"),
          axios.get("/api/bi/listar-edificio-ot"),
          axios.get("/api/bi/listar-nivel-ot"),
          axios.get("/api/bi/listar-zona-ot"),
          axios.get("/api/bi/listar-responsables-ot"), // Nueva llamada a la API
        ]);
        // FIN DE LA MODIFICACIÓN

        this.opciones.predios = prediosRes.data;
        this.opciones.tiposOT = tiposOtRes.data;
        this.opciones.edificios = edificiosRes.data;
        this.opciones.niveles = nivelesRes.data;
        this.opciones.zonas = zonasRes.data;
        this.opciones.responsables = responsablesRes.data; // NUEVO: Se guardan los responsables

        ["Generales", "Responsables", "Categoria"].forEach((tab) => {
          this.opcionesDisponibles[tab].edificios = this.opciones.edificios;
          this.opcionesDisponibles[tab].niveles = this.opciones.niveles;
          this.opcionesDisponibles[tab].zonas = this.opciones.zonas;
        });
      } catch (error) {
        console.error("Error al cargar las opciones para los filtros:", error);
      }
    },
    actualizarCascadaUbicacion(tab, nivelCambiado = "predio") {
      const filtrosTab = this.filtros[tab];
      const opcionesTab = this.opcionesDisponibles[tab];

      if (nivelCambiado === "predio") {
        const predioIds = filtrosTab.prediosSeleccionados.map(
          (p) => p.IDPredio
        );
        opcionesTab.edificios =
          predioIds.length > 0
            ? this.opciones.edificios.filter((e) =>
                predioIds.includes(e.IDPredio)
              )
            : this.opciones.edificios;
        filtrosTab.edificiosSeleccionados =
          filtrosTab.edificiosSeleccionados.filter((e) =>
            opcionesTab.edificios.some((ed) => ed.IDEdificio === e.IDEdificio)
          );
      }
      if (["predio", "edificio"].includes(nivelCambiado)) {
        const edificioIds = filtrosTab.edificiosSeleccionados.map(
          (e) => e.IDEdificio
        );
        const edificiosDispIds = opcionesTab.edificios.map((e) => e.IDEdificio);
        opcionesTab.niveles =
          edificioIds.length > 0
            ? this.opciones.niveles.filter((n) =>
                edificioIds.includes(n.IDEdificio)
              )
            : this.opciones.niveles.filter((n) =>
                edificiosDispIds.includes(n.IDEdificio)
              );
        filtrosTab.nivelesSeleccionados =
          filtrosTab.nivelesSeleccionados.filter((n) =>
            opcionesTab.niveles.some((nd) => nd.IDNivel === n.IDNivel)
          );
      }
      if (["predio", "edificio", "nivel"].includes(nivelCambiado)) {
        const nivelIds = filtrosTab.nivelesSeleccionados.map((n) => n.IDNivel);
        const nivelesDispIds = opcionesTab.niveles.map((n) => n.IDNivel);
        opcionesTab.zonas =
          nivelIds.length > 0
            ? this.opciones.zonas.filter((z) => nivelIds.includes(z.IDNivel))
            : this.opciones.zonas.filter((z) =>
                nivelesDispIds.includes(z.IDNivel)
              );
        filtrosTab.zonasSeleccionadas = filtrosTab.zonasSeleccionadas.filter(
          (z) => opcionesTab.zonas.some((zd) => zd.IDZona === z.IDZona)
        );
      }
    },
    
    getPropsForTab(tab) {
      const filtrosTab = this.filtros[tab];
      const props = {
        predioIds: filtrosTab.prediosSeleccionados.map((p) => p.IDPredio),
        edificioIds: filtrosTab.edificiosSeleccionados.map((e) => e.IDEdificio),
        nivelIds: filtrosTab.nivelesSeleccionados.map((n) => n.IDNivel),
        zonaIds: filtrosTab.zonasSeleccionadas.map((z) => z.IDZona),
        tipoOtIds: filtrosTab.tiposOtSeleccionados.map((t) => t.IDTipoOT),
        fechaInicio: filtrosTab.fechaInicio,
        fechaFin: filtrosTab.fechaFin,
      };

      // INICIO DE LA MODIFICACIÓN: Se añade el filtro de responsable si estamos en la pestaña correcta
      if (tab === "Responsables") {
        props.responsableIds = filtrosTab.responsablesSeleccionados.map(
          (r) => r.IDPersona
        );
      }
      // FIN DE LA MODIFICACIÓN

      return props;
    },

    exportarActivo() {
      console.log(`Exportando datos de la pestaña: ${this.activeTab}`);

      // 1. Obtenemos los filtros de la pestaña activa usando un método que ya tienes
      const filtros = this.getPropsForTab(this.activeTab);

      // 2. Construimos los parámetros de la URL de forma segura
      const params = new URLSearchParams();

      for (const key in filtros) {
        const value = filtros[key];
        if (value !== null && value !== undefined) {
          if (Array.isArray(value)) {
            // Para arrays (multiselect), los enviamos con corchetes
            if (value.length > 0) {
              value.forEach((item) => params.append(`${key}[]`, item));
            }
          } else if (value !== "") {
            // Para valores simples (fechas)
            params.append(key, value);
          }
        }
      }

      // 3. Creamos la URL final y le decimos al navegador que la abra
      const queryString = params.toString();
      const url = `/api/bi/ordenes-trabajo/exportar-excel?${queryString}`;

      console.log("URL de exportación:", url);

      // Esto iniciará la descarga del archivo en el navegador
      window.location.href = url;
    },
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style scoped>
.card-title {
  margin-bottom: 1.2rem;
}
.form-label {
  margin-bottom: 0.5rem;
}
.nav-link {
  cursor: pointer;
}
</style>