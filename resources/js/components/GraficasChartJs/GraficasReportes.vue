<template>
  <div>
    <h1 class="mb-4 display-5">Dashboard</h1>
    <p class="lead mb-5">
      Resumen visual del estado de la documentación por predio y categoría.
    </p>

    <nav>
      <div class="nav nav-tabs mb-4">
        <button
          class="nav-link"
          :class="{ active: activeTab === 'Cumplimiento' }"
          @click="activeTab = 'Cumplimiento'"
        >
          Cumplimiento
        </button>
        <button
          class="nav-link"
          :class="{ active: activeTab === 'Vigencia' }"
          @click="activeTab = 'Vigencia'"
        >
          Vigencia
        </button>
        <button
          class="nav-link"
          :class="{ active: activeTab === 'Proximamente' }"
          @click="activeTab = 'Proximamente'"
        >
          Próximamente...
        </button>
      </div>
    </nav>
    <div class="tab-content">
      <div v-if="activeTab === 'Cumplimiento'">
        <div class="card mb-4">
          <div class="card-body">
            <div>
              <h1 class="mb-4 display-5">Cumplimiento</h1>
              <p class="lead mb-5">
                Resumen visual del estado de la documentación por predio y
                categoría.
              </p>

              <div class="card mb-4">
                <div class="card-body">
                  <div class="row align-items-end">
                    <div class="col-md-3">
                      <label class="form-label fw-bold"
                        >Filtro de Predios:</label
                      >
                      <vue-multiselect
                        v-model="prediosSeleccionados"
                        :options="prediosDisponibles"
                        :multiple="true"
                        :close-on-select="false"
                        placeholder="Todos"
                        label="NombrePredio"
                        track-by="IDPredio"
                      ></vue-multiselect>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label fw-bold"
                        >Categoría (Grupo):</label
                      >
                      <vue-multiselect
                        v-model="gruposSeleccionados"
                        :options="gruposDisponibles"
                        :multiple="true"
                        :close-on-select="false"
                        placeholder="Todas"
                        label="NombreGrupoDoc"
                        track-by="IDGrupoDoc"
                      ></vue-multiselect>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label fw-bold">Subcategoría:</label>
                      <vue-multiselect
                        v-model="categoriasSeleccionadas"
                        :options="categoriasDisponibles"
                        :multiple="true"
                        :close-on-select="false"
                        :disabled="gruposSeleccionados.length === 0"
                        placeholder="Seleccione categoría"
                        label="NombreCategoriaDoc"
                        track-by="IDCategoriaDoc"
                      ></vue-multiselect>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label fw-bold"
                        >Tipo de Documento:</label
                      >
                      <vue-multiselect
                        v-model="tiposDocumentoSeleccionados"
                        :options="tiposDocumentoDisponibles"
                        :multiple="true"
                        :close-on-select="false"
                        placeholder="Todos"
                        label="NombreTipoDocumento"
                        track-by="IDTipoDocumento"
                      ></vue-multiselect>
                    </div>
                    <div class="col-md-3 mt-3">
                      <label class="form-label fw-bold"
                        >Tipo de Inmueble:</label
                      >
                      <vue-multiselect
                        v-model="tiposInmuebleSeleccionados"
                        :options="tiposInmuebleDisponibles"
                        :multiple="true"
                        :close-on-select="false"
                        placeholder="Todos"
                        label="NombreTipoInmueble"
                        track-by="IDTipoInmueble"
                      ></vue-multiselect>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="isReady">
                <div class="row">
                  <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                      <div class="card-header">
                        Matriz de Cumplimiento por SubCategoría
                      </div>
                      <div class="card-body">
                        <base-heatmap-matrix
                          :id-predios="idPrediosSeleccionados"
                          :id-grupos="idGruposSeleccionados"
                          :id-categorias="idCategoriasSeleccionadas"
                          :id-tipos-documento="idTiposDocumentoSeleccionados"
                          :id-tipos-inmueble="idTiposInmuebleSeleccionados"
                          api-url="/api/bi/matriz-subcategoria"
                          chart-title="Cumplimiento por Predio vs. SubCategoría"
                        >
                        </base-heatmap-matrix>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                      <div class="card-header">
                        Matriz de Archivos Adjuntos por SubCategoría
                      </div>
                      <div class="card-body">
                        <base-heatmap-matrix-archivos
                          :id-predios="idPrediosSeleccionados"
                          :id-grupos="idGruposSeleccionados"
                          :id-categorias="idCategoriasSeleccionadas"
                          :id-tipos-documento="idTiposDocumentoSeleccionados"
                          :id-tipos-inmueble="idTiposInmuebleSeleccionados"
                          api-url="/api/bi/matriz-subcategoria-archivos"
                          chart-title="SubCategorías con o sin archivo adjunto por Predio"
                        >
                        </base-heatmap-matrix-archivos>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mt-4">
                  <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                      <div class="card-header">
                        Matriz de Cumplimiento por Categoria
                      </div>
                      <div class="card-body">
                        <matriz-por-grupo
                          :id-predios="idPrediosSeleccionados"
                          :id-grupos="idGruposSeleccionados"
                          :id-categorias="idCategoriasSeleccionadas"
                          :id-tipos-documento="idTiposDocumentoSeleccionados"
                          :id-tipos-inmueble="idTiposInmuebleSeleccionados"
                          api-url="/api/bi/matriz-grupo"
                          chart-title="Cumplimiento por Predio vs. Categoria de Documento"
                        >
                        </matriz-por-grupo>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                      <div class="card-header">
                        Matriz de Archivos por Categoria
                      </div>
                      <div class="card-body">
                        <matriz-archivos-por-grupo
                          :id-predios="idPrediosSeleccionados"
                          :id-grupos="idGruposSeleccionados"
                          :id-categorias="idCategoriasSeleccionadas"
                          :id-tipos-documento="idTiposDocumentoSeleccionados"
                          :id-tipos-inmueble="idTiposInmuebleSeleccionados"
                          api-url="/api/bi/matriz-grupo-archivos"
                          chart-title="Archivos adjuntos por Predio vs. Categoria de Documento"
                        >
                        </matriz-archivos-por-grupo>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mt-4">
                  <div class="col-12">
                    <div class="card">
                      <div class="card-header">
                        Resumen de Cumplimiento General por Predio
                      </div>
                      <div class="card-body">
                        <horizontal-ponderado-por-predio
                          :id-predios="idPrediosSeleccionados"
                          :id-grupos="idGruposSeleccionados"
                          :id-categorias="idCategoriasSeleccionadas"
                          :id-tipos-documento="idTiposDocumentoSeleccionados"
                          :id-tipos-inmueble="idTiposInmuebleSeleccionados"
                          api-url="/api/bi/calificaciones-ponderadas"
                          chart-title="Cumplimiento ponderado por predio (%)"
                        >
                        </horizontal-ponderado-por-predio>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center text-secondary py-5">
                Cargando dashboard...
              </div>
            </div>
          </div>
        </div>

        <div v-if="isReady">
          <div class="row"></div>
          <div class="row mt-4"></div>
          <div class="row mt-4"></div>
        </div>
        <div v-else class="text-center text-secondary py-5">
          Cargando dashboard...
        </div>
      </div>

      <div v-if="activeTab === 'Vigencia'">
        <div class="card mb-5">
          <div class="card-header">
            Matriz de Estado de Documentos por Categoría
          </div>
          <div class="card-body">
            <VigenciaMatrizEstadoCategoria
              :id-predios="idPrediosSeleccionados"
              :id-grupos="idGruposSeleccionados"
              :id-categorias="idCategoriasSeleccionadas"
              :id-tipos-documento="idTiposDocumentoSeleccionados"
              :id-tipos-inmueble="idTiposInmuebleSeleccionados"
              api-url="/api/bi/documentos-con-estado"
            />
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">
            Matriz de Estado de Documentos por Subcategoría
          </div>
          <div class="card-body">
            <VigenciaMatrizEstadoSubcategoria
              :id-predios="idPrediosSeleccionados"
              :id-grupos="idGruposSeleccionados"
              :id-categorias="idCategoriasSeleccionadas"
              :id-tipos-documento="idTiposDocumentoSeleccionados"
              :id-tipos-inmueble="idTiposInmuebleSeleccionados"
              api-url="/api/bi/documentos-por-subcategoria"
            />
          </div>
        </div>

        <div class="card">
          <div class="card-header">Tabla Detallada de Documentos</div>
          <div class="card-body">
            <VigenciaTablaDetallada
              :id-predios="idPrediosSeleccionados"
              :id-grupos="idGruposSeleccionados"
              :id-categorias="idCategoriasSeleccionadas"
              :id-tipos-documento="idTiposDocumentoSeleccionados"
              :id-tipos-inmueble="idTiposInmuebleSeleccionados"
              api-url="/api/bi/tabla-detallada-vigencia"
            />
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'Proximamente'">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Pestaña Próximamente</h5>
            <p class="card-text">
              Este espacio está reservado para futuras visualizaciones.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import AppLayout from "../../Layout/App.vue";
import VueMultiselect from "vue-multiselect";
import BaseHeatmapMatrix from "./BaseHeatmapMatrix.vue";
import BaseHeatmapMatrixArchivos from "./BaseHeatmapMatrixArchivos.vue";
import HorizontalPonderadoPorPredio from "./HorizontalPonderadoPorPredio.vue";
import MatrizPorGrupo from "./MatrizPorGrupo.vue";
import MatrizArchivosPorGrupo from "./MatrizArchivosPorGrupo.vue";
import VigenciaMatrizEstadoCategoria from "./VigenciaMatrizEstadoCategoria.vue";
import VigenciaMatrizEstadoSubcategoria from "./VigenciaMatrizEstadoSubcategoria.vue";
import VigenciaTablaDetallada from "./VigenciaTablaDetallada.vue";

export default {
  components: {
    VueMultiselect,
    AppLayout,
    BaseHeatmapMatrix,
    BaseHeatmapMatrixArchivos,
    HorizontalPonderadoPorPredio,
    MatrizPorGrupo,
    MatrizArchivosPorGrupo,
    VigenciaMatrizEstadoCategoria,
    VigenciaMatrizEstadoSubcategoria,
    VigenciaTablaDetallada,
  },
  data() {
    return {
      isReady: false,
      activeTab: "Cumplimiento",
      prediosDisponibles: [],
      gruposDisponibles: [],
      categoriasDisponibles: [],
      tiposDocumentoDisponibles: [],
      tiposInmuebleDisponibles: [],
      prediosSeleccionados: [],
      gruposSeleccionados: [],
      categoriasSeleccionadas: [],
      tiposDocumentoSeleccionados: [],
      tiposInmuebleSeleccionados: [],
    };
  },
  computed: {
    idPrediosSeleccionados() {
      return this.prediosSeleccionados.map((p) => p.IDPredio);
    },
    idGruposSeleccionados() {
      return this.gruposSeleccionados.map((g) => g.IDGrupoDoc);
    },
    idCategoriasSeleccionadas() {
      return this.categoriasSeleccionadas.map((c) => c.IDCategoriaDoc);
    },
    idTiposDocumentoSeleccionados() {
      return this.tiposDocumentoSeleccionados.map((t) => t.IDTipoDocumento);
    },
    idTiposInmuebleSeleccionados() {
      return this.tiposInmuebleSeleccionados.map((t) => t.IDTipoInmueble);
    },
  },
  watch: {
    async gruposSeleccionados(newGrupos) {
      const newGrupoIds = newGrupos.map((g) => g.IDGrupoDoc);
      if (newGrupos && newGrupos.length > 0) {
        try {
          const response = await axios.get("/api/bi/listar-categorias-doc", {
            params: { grupo_ids: newGrupoIds },
          });
          this.categoriasDisponibles = response.data;
          this.categoriasSeleccionadas = this.categoriasSeleccionadas.filter(
            (cat) =>
              this.categoriasDisponibles.some(
                (dispo) => dispo.IDCategoriaDoc === cat.IDCategoriaDoc
              )
          );
        } catch (error) {
          console.error("No se pudo cargar la lista de subcategorías:", error);
        }
      } else {
        this.categoriasDisponibles = [];
        this.categoriasSeleccionadas = [];
      }
    },
  },
  async mounted() {
    try {
      const [prediosRes, gruposRes, tiposDocRes, tiposInmuebleRes] =
        await Promise.all([
          axios.get("/api/bi/listar-predios"),
          axios.get("/api/bi/listar-grupos-doc"),
          axios.get("/api/bi/listar-tipos-documento"),
          axios.get("/api/bi/listar-tipos-inmueble"),
        ]);
      this.prediosDisponibles = prediosRes.data;
      this.gruposDisponibles = gruposRes.data;
      this.tiposDocumentoDisponibles = tiposDocRes.data;
      this.tiposInmuebleDisponibles = tiposInmuebleRes.data;
    } catch (error) {
      console.error("No se pudo cargar los filtros:", error);
    } finally {
      this.isReady = true;
    }
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>