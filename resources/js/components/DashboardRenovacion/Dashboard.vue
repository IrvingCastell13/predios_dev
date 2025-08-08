<template>
  <div class="container-fluid mt-4">
    <h1 class="mb-4 display-5">Dashboard de Renovación de Documentos</h1>
    <p class="lead mb-5">
      Análisis de acciones de renovación, Órdenes de Trabajo y estado de
      vigencia de documentos.
    </p>

    <nav>
      <div class="nav nav-tabs mb-4">
        <button
          class="nav-link"
          :class="{ active: activeTab === 'Acciones' }"
          @click="activeTab = 'Acciones'"
        >
          Acciones
        </button>
        <button
          class="nav-link"
          :class="{ active: activeTab === 'OTs' }"
          @click="activeTab = 'OTs'"
        >
          OTs
        </button>
        <button
          class="nav-link"
          :class="{ active: activeTab === 'Documentos' }"
          @click="activeTab = 'Documentos'"
        >
          Documentos
        </button>
      </div>
    </nav>

    <button class="btn btn-primary mb-4" @click="exportarRenovacion">
      <i class="fas fa-file-excel"></i> Exportar a Excel
    </button>

    <div v-if="!isReady" class="text-center text-secondary py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
      <p class="mt-2">Cargando dashboard...</p>
    </div>

    <div v-else class="tab-content">
      <div v-if="activeTab === 'Acciones'">
        <div class="card mb-4">
          <div class="card-body">
            <div class="row align-items-end">
              <div class="col-md-3">
                <label class="form-label fw-bold">Predio:</label
                ><vue-multiselect
                  v-model="filtrosAcciones.prediosSeleccionados"
                  :options="prediosDisponibles"
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
                  v-model="filtrosAcciones.edificiosSeleccionados"
                  :options="edificiosDisponiblesAcciones"
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
                  v-model="filtrosAcciones.nivelesSeleccionados"
                  :options="nivelesDisponiblesAcciones"
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
                  v-model="filtrosAcciones.zonasSeleccionadas"
                  :options="zonasDisponiblesAcciones"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreZona"
                  track-by="IDZona"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Categoria Documento:</label
                ><vue-multiselect
                  v-model="filtrosAcciones.gruposSeleccionados"
                  :options="gruposDisponibles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreGrupoDoc"
                  track-by="IDGrupoDoc"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Subcategoria Documento:</label
                ><vue-multiselect
                  v-model="filtrosAcciones.categoriasSeleccionadas"
                  :options="categoriasDisponiblesAcciones"
                  :multiple="true"
                  :close-on-select="false"
                  :disabled="filtrosAcciones.gruposSeleccionados.length === 0"
                  placeholder="Seleccione grupo"
                  label="NombreCategoriaDoc"
                  track-by="IDCategoriaDoc"
                ></vue-multiselect>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Planes:</label
                ><vue-multiselect
                  v-model="filtrosAcciones.planesSeleccionados"
                  :options="planesDisponibles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombrePlan"
                  track-by="IDPlan"
                ></vue-multiselect>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Desde:</label
                ><date-picker
                  v-model:value="filtrosAcciones.fechaInicio"
                  format="YYYY-MM-DD"
                  value-type="format"
                  placeholder="Fecha inicio"
                ></date-picker>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Hasta:</label
                ><date-picker
                  v-model:value="filtrosAcciones.fechaFin"
                  format="YYYY-MM-DD"
                  value-type="format"
                  placeholder="Fecha fin"
                ></date-picker>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                Estados de Acciones de Renovación por Predio
              </div>
              <div class="card-body">
                <grafica-renovacion-por-estados
                  :id-predios="idPrediosSeleccionadosAcciones"
                  :id-edificios="idEdificiosSeleccionadosAcciones"
                  :id-niveles="idNivelesSeleccionadosAcciones"
                  :id-zonas="idZonasSeleccionadasAcciones"
                  :id-grupos="idGruposSeleccionadosAcciones"
                  :id-categorias="idCategoriasSeleccionadasAcciones"
                  :id-planes="idPlanesSeleccionadosAcciones"
                  :fecha-inicio="filtrosAcciones.fechaInicio"
                  :fecha-fin="filtrosAcciones.fechaFin"
                  api-url="/api/bi/resumen-estado-acciones-renovacion"
                  chart-title="Estado de Acciones por Predio"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">Tabla de Acciones de Renovación</div>
              <div class="card-body">
                <tabla-acciones-renovacion
                  :id-predios="idPrediosSeleccionadosAcciones"
                  :id-edificios="idEdificiosSeleccionadosAcciones"
                  :id-niveles="idNivelesSeleccionadosAcciones"
                  :id-zonas="idZonasSeleccionadasAcciones"
                  :id-grupos="idGruposSeleccionadosAcciones"
                  :id-categorias="idCategoriasSeleccionadasAcciones"
                  :id-planes="idPlanesSeleccionadosAcciones"
                  :fecha-inicio="filtrosAcciones.fechaInicio"
                  :fecha-fin="filtrosAcciones.fechaFin"
                  api-url="/api/bi/detalle-estado-acciones-renovacion"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'OTs'">
        <div class="card mb-4">
          <div class="card-body">
            <div class="row align-items-end">
              <div class="col-md-3">
                <label class="form-label fw-bold">Predio:</label
                ><vue-multiselect
                  v-model="filtrosOTs.prediosSeleccionados"
                  :options="prediosDisponibles"
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
                  v-model="filtrosOTs.edificiosSeleccionados"
                  :options="edificiosDisponiblesOTs"
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
                  v-model="filtrosOTs.nivelesSeleccionados"
                  :options="nivelesDisponiblesOTs"
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
                  v-model="filtrosOTs.zonasSeleccionadas"
                  :options="zonasDisponiblesOTs"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreZona"
                  track-by="IDZona"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Grupo Documento:</label
                ><vue-multiselect
                  v-model="filtrosOTs.gruposSeleccionados"
                  :options="gruposDisponibles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreGrupoDoc"
                  track-by="IDGrupoDoc"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Subcategoria Documento:</label
                ><vue-multiselect
                  v-model="filtrosOTs.categoriasSeleccionadas"
                  :options="categoriasDisponiblesOTs"
                  :multiple="true"
                  :close-on-select="false"
                  :disabled="filtrosOTs.gruposSeleccionados.length === 0"
                  placeholder="Seleccione grupo"
                  label="NombreCategoriaDoc"
                  track-by="IDCategoriaDoc"
                ></vue-multiselect>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Planes:</label
                ><vue-multiselect
                  v-model="filtrosOTs.planesSeleccionados"
                  :options="planesDisponibles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombrePlan"
                  track-by="IDPlan"
                ></vue-multiselect>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Desde:</label
                ><date-picker
                  v-model:value="filtrosOTs.fechaInicio"
                  format="YYYY-MM-DD"
                  value-type="format"
                  placeholder="Fecha inicio"
                ></date-picker>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Hasta:</label
                ><date-picker
                  v-model:value="filtrosOTs.fechaFin"
                  format="YYYY-MM-DD"
                  value-type="format"
                  placeholder="Fecha fin"
                ></date-picker>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                Estados de Órdenes de Trabajo por Predio
              </div>
              <div class="card-body">
                <grafica-renovacion-por-estados
                  :id-predios="idPrediosSeleccionadosOTs"
                  :id-edificios="idEdificiosSeleccionadosOTs"
                  :id-niveles="idNivelesSeleccionadosOTs"
                  :id-zonas="idZonasSeleccionadasOTs"
                  :id-grupos="idGruposSeleccionadosOTs"
                  :id-categorias="idCategoriasSeleccionadasOTs"
                  :id-planes="idPlanesSeleccionadosOTs"
                  :fecha-inicio="filtrosOTs.fechaInicio"
                  :fecha-fin="filtrosOTs.fechaFin"
                  api-url="/api/bi/resumen-estado-ots-renovacion"
                  chart-title="Estado de OTs por Predio"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                Tabla de Órdenes de Trabajo de Renovación
              </div>
              <div class="card-body">
                <tabla-renovacion-orden-trabajo
                  :id-predios="idPrediosSeleccionadosOTs"
                  :id-edificios="idEdificiosSeleccionadosOTs"
                  :id-niveles="idNivelesSeleccionadosOTs"
                  :id-zonas="idZonasSeleccionadasOTs"
                  :id-grupos="idGruposSeleccionadosOTs"
                  :id-categorias="idCategoriasSeleccionadasOTs"
                  :id-planes="idPlanesSeleccionadosOTs"
                  :fecha-inicio="filtrosOTs.fechaInicio"
                  :fecha-fin="filtrosOTs.fechaFin"
                  api-url="/api/bi/detalle-estado-ots-renovacion"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'Documentos'">
        <div class="card mb-4">
          <div class="card-body">
            <div class="row align-items-end">
              <div class="col-md-3">
                <label class="form-label fw-bold">Predio:</label
                ><vue-multiselect
                  v-model="filtrosDocumentos.prediosSeleccionados"
                  :options="prediosDisponibles"
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
                  v-model="filtrosDocumentos.edificiosSeleccionados"
                  :options="edificiosDisponiblesDocumentos"
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
                  v-model="filtrosDocumentos.nivelesSeleccionados"
                  :options="nivelesDisponiblesDocumentos"
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
                  v-model="filtrosDocumentos.zonasSeleccionadas"
                  :options="zonasDisponiblesDocumentos"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreZona"
                  track-by="IDZona"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Grupo Documento:</label
                ><vue-multiselect
                  v-model="filtrosDocumentos.gruposSeleccionados"
                  :options="gruposDisponibles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreGrupoDoc"
                  track-by="IDGrupoDoc"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Subcategoria Documento:</label
                ><vue-multiselect
                  v-model="filtrosDocumentos.categoriasSeleccionadas"
                  :options="categoriasDisponiblesDocumentos"
                  :multiple="true"
                  :close-on-select="false"
                  :disabled="filtrosDocumentos.gruposSeleccionados.length === 0"
                  placeholder="Seleccione grupo"
                  label="NombreCategoriaDoc"
                  track-by="IDCategoriaDoc"
                ></vue-multiselect>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Planes:</label
                ><vue-multiselect
                  v-model="filtrosDocumentos.planesSeleccionados"
                  :options="planesDisponibles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombrePlan"
                  track-by="IDPlan"
                ></vue-multiselect>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Desde:</label
                ><date-picker
                  v-model:value="filtrosDocumentos.fechaInicio"
                  format="YYYY-MM-DD"
                  value-type="format"
                  placeholder="Fecha inicio"
                ></date-picker>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Hasta:</label
                ><date-picker
                  v-model:value="filtrosDocumentos.fechaFin"
                  format="YYYY-MM-DD"
                  value-type="format"
                  placeholder="Fecha fin"
                ></date-picker>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                Gráfica de Estado de Vigencia de Documentos
              </div>
              <div class="card-body">
                <grafica-renovacion-por-estados
                  :id-predios="idPrediosSeleccionadosDocumentos"
                  :id-edificios="idEdificiosSeleccionadosDocumentos"
                  :id-niveles="idNivelesSeleccionadosDocumentos"
                  :id-zonas="idZonasSeleccionadasDocumentos"
                  :id-grupos="idGruposSeleccionadosDocumentos"
                  :id-categorias="idCategoriasSeleccionadasDocumentos"
                  :id-planes="idPlanesSeleccionadosDocumentos"
                  :fecha-inicio="filtrosDocumentos.fechaInicio"
                  :fecha-fin="filtrosDocumentos.fechaFin"
                  api-url="/api/bi/resumen-estado-documentos"
                  chart-title="Estado de Vigencia de Documentos por Predio"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                Tabla de Estado de Vigencia de Documentos
              </div>
              <div class="card-body">
                <tabla-estado-documentos
                  :id-predios="idPrediosSeleccionadosDocumentos"
                  :id-edificios="idEdificiosSeleccionadosDocumentos"
                  :id-niveles="idNivelesSeleccionadosDocumentos"
                  :id-zonas="idZonasSeleccionadasDocumentos"
                  :id-grupos="idGruposSeleccionadosDocumentos"
                  :id-categorias="idCategoriasSeleccionadasDocumentos"
                  :id-planes="idPlanesSeleccionadosDocumentos"
                  :fecha-inicio="filtrosDocumentos.fechaInicio"
                  :fecha-fin="filtrosDocumentos.fechaFin"
                  api-url="/api/bi/detalle-estado-documentos"
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
import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import GraficaRenovacionPorEstados from "./GraficaRenovacionPorEstados.vue";
import TablaAccionesRenovacion from "./TablaAccionesRenovacion.vue";
import TablaRenovacionOrdenTrabajo from "./TablaRenovacionOrdenTrabajo.vue";
import TablaEstadoDocumentos from "./TablaEstadoDocumentos.vue";

export default {
  name: "DashboardRenovacion",
  components: {
    VueMultiselect,
    DatePicker,
    GraficaRenovacionPorEstados,
    TablaAccionesRenovacion,
    TablaRenovacionOrdenTrabajo,
    TablaEstadoDocumentos,
  },
  data() {
    return {
      isReady: false,
      activeTab: "Acciones",
      allPredios: [],
      allEdificios: [],
      allNiveles: [],
      allZonas: [],
      allGrupos: [],
      allCategorias: [],
      allPlanes: [],
      prediosDisponibles: [],
      gruposDisponibles: [],
      planesDisponibles: [],
      edificiosDisponiblesAcciones: [],
      nivelesDisponiblesAcciones: [],
      zonasDisponiblesAcciones: [],
      categoriasDisponiblesAcciones: [],
      edificiosDisponiblesOTs: [],
      nivelesDisponiblesOTs: [],
      zonasDisponiblesOTs: [],
      categoriasDisponiblesOTs: [],
      edificiosDisponiblesDocumentos: [],
      nivelesDisponiblesDocumentos: [],
      zonasDisponiblesDocumentos: [],
      categoriasDisponiblesDocumentos: [],
      filtrosAcciones: {
        prediosSeleccionados: [],
        edificiosSeleccionados: [],
        nivelesSeleccionados: [],
        zonasSeleccionadas: [],
        gruposSeleccionados: [],
        categoriasSeleccionadas: [],
        planesSeleccionados: [],
        fechaInicio: null,
        fechaFin: null,
      },
      filtrosOTs: {
        prediosSeleccionados: [],
        edificiosSeleccionados: [],
        nivelesSeleccionados: [],
        zonasSeleccionadas: [],
        gruposSeleccionados: [],
        categoriasSeleccionadas: [],
        planesSeleccionados: [],
        fechaInicio: null,
        fechaFin: null,
      },
      filtrosDocumentos: {
        prediosSeleccionados: [],
        edificiosSeleccionados: [],
        nivelesSeleccionados: [],
        zonasSeleccionadas: [],
        gruposSeleccionados: [],
        categoriasSeleccionadas: [],
        planesSeleccionados: [],
        fechaInicio: null,
        fechaFin: null,
      },
    };
  },
  computed: {
    idPrediosSeleccionadosAcciones() {
      return this.filtrosAcciones.prediosSeleccionados.map((p) => p.IDPredio);
    },
    idEdificiosSeleccionadosAcciones() {
      return this.filtrosAcciones.edificiosSeleccionados.map(
        (e) => e.IDEdificio
      );
    },
    idNivelesSeleccionadosAcciones() {
      return this.filtrosAcciones.nivelesSeleccionados.map((n) => n.IDNivel);
    },
    idZonasSeleccionadasAcciones() {
      return this.filtrosAcciones.zonasSeleccionadas.map((z) => z.IDZona);
    },
    idGruposSeleccionadosAcciones() {
      return this.filtrosAcciones.gruposSeleccionados.map((g) => g.IDGrupoDoc);
    },
    idCategoriasSeleccionadasAcciones() {
      return this.filtrosAcciones.categoriasSeleccionadas.map(
        (c) => c.IDCategoriaDoc
      );
    },
    idPlanesSeleccionadosAcciones() {
      return this.filtrosAcciones.planesSeleccionados.map((p) => p.IDPlan);
    },
    idPrediosSeleccionadosOTs() {
      return this.filtrosOTs.prediosSeleccionados.map((p) => p.IDPredio);
    },
    idEdificiosSeleccionadosOTs() {
      return this.filtrosOTs.edificiosSeleccionados.map((e) => e.IDEdificio);
    },
    idNivelesSeleccionadosOTs() {
      return this.filtrosOTs.nivelesSeleccionados.map((n) => n.IDNivel);
    },
    idZonasSeleccionadosOTs() {
      return this.filtrosOTs.zonasSeleccionadas.map((z) => z.IDZona);
    },
    idGruposSeleccionadosOTs() {
      return this.filtrosOTs.gruposSeleccionados.map((g) => g.IDGrupoDoc);
    },
    idCategoriasSeleccionadasOTs() {
      return this.filtrosOTs.categoriasSeleccionadas.map(
        (c) => c.IDCategoriaDoc
      );
    },
    idPlanesSeleccionadosOTs() {
      return this.filtrosOTs.planesSeleccionados.map((p) => p.IDPlan);
    },
    idPrediosSeleccionadosDocumentos() {
      return this.filtrosDocumentos.prediosSeleccionados.map((p) => p.IDPredio);
    },
    idEdificiosSeleccionadosDocumentos() {
      return this.filtrosDocumentos.edificiosSeleccionados.map(
        (e) => e.IDEdificio
      );
    },
    idNivelesSeleccionadosDocumentos() {
      return this.filtrosDocumentos.nivelesSeleccionados.map((n) => n.IDNivel);
    },
    idZonasSeleccionadosDocumentos() {
      return this.filtrosDocumentos.zonasSeleccionadas.map((z) => z.IDZona);
    },
    idGruposSeleccionadosDocumentos() {
      return this.filtrosDocumentos.gruposSeleccionados.map(
        (g) => g.IDGrupoDoc
      );
    },
    idCategoriasSeleccionadasDocumentos() {
      return this.filtrosDocumentos.categoriasSeleccionadas.map(
        (c) => c.IDCategoriaDoc
      );
    },
    idPlanesSeleccionadosDocumentos() {
      return this.filtrosDocumentos.planesSeleccionados.map((p) => p.IDPlan);
    },
  },
  watch: {
    "filtrosAcciones.prediosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Acciones");
      },
      deep: true,
    },
    "filtrosAcciones.edificiosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Acciones", "edificio");
      },
      deep: true,
    },
    "filtrosAcciones.nivelesSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Acciones", "nivel");
      },
      deep: true,
    },
    "filtrosAcciones.gruposSeleccionados": {
      handler(nuevos) {
        this.actualizarCategorias("Acciones", nuevos);
      },
      deep: true,
    },
    "filtrosOTs.prediosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("OTs");
      },
      deep: true,
    },
    "filtrosOTs.edificiosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("OTs", "edificio");
      },
      deep: true,
    },
    "filtrosOTs.nivelesSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("OTs", "nivel");
      },
      deep: true,
    },
    "filtrosOTs.gruposSeleccionados": {
      handler(nuevos) {
        this.actualizarCategorias("OTs", nuevos);
      },
      deep: true,
    },
    "filtrosDocumentos.prediosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Documentos");
      },
      deep: true,
    },
    "filtrosDocumentos.edificiosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Documentos", "edificio");
      },
      deep: true,
    },
    "filtrosDocumentos.nivelesSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Documentos", "nivel");
      },
      deep: true,
    },
    "filtrosDocumentos.gruposSeleccionados": {
      handler(nuevos) {
        this.actualizarCategorias("Documentos", nuevos);
      },
      deep: true,
    },
  },
  methods: {
    actualizarCascadaUbicacion(tab, nivelCambiado = "predio") {
      const filtros = this[`filtros${tab}`];
      if (nivelCambiado === "predio") {
        const predioIds = filtros.prediosSeleccionados.map((p) => p.IDPredio);
        this[`edificiosDisponibles${tab}`] =
          predioIds.length > 0
            ? this.allEdificios.filter((e) => predioIds.includes(e.IDPredio))
            : this.allEdificios;
        filtros.edificiosSeleccionados = filtros.edificiosSeleccionados.filter(
          (e) =>
            this[`edificiosDisponibles${tab}`].some(
              (ed) => ed.IDEdificio === e.IDEdificio
            )
        );
      }
      if (["predio", "edificio"].includes(nivelCambiado)) {
        const edificioIds = filtros.edificiosSeleccionados.map(
          (e) => e.IDEdificio
        );
        const edificiosDispIds = this[`edificiosDisponibles${tab}`].map(
          (e) => e.IDEdificio
        );
        this[`nivelesDisponibles${tab}`] =
          edificioIds.length > 0
            ? this.allNiveles.filter((n) => edificioIds.includes(n.IDEdificio))
            : this.allNiveles.filter((n) =>
                edificiosDispIds.includes(n.IDEdificio)
              );
        filtros.nivelesSeleccionados = filtros.nivelesSeleccionados.filter(
          (n) =>
            this[`nivelesDisponibles${tab}`].some(
              (nd) => nd.IDNivel === n.IDNivel
            )
        );
      }
      if (["predio", "edificio", "nivel"].includes(nivelCambiado)) {
        const nivelIds = filtros.nivelesSeleccionados.map((n) => n.IDNivel);
        const nivelesDispIds = this[`nivelesDisponibles${tab}`].map(
          (n) => n.IDNivel
        );
        this[`zonasDisponibles${tab}`] =
          nivelIds.length > 0
            ? this.allZonas.filter((z) => nivelIds.includes(z.IDNivel))
            : this.allZonas.filter((z) => nivelesDispIds.includes(z.IDNivel));
        filtros.zonasSeleccionadas = filtros.zonasSeleccionadas.filter((z) =>
          this[`zonasDisponibles${tab}`].some((zd) => zd.IDZona === z.IDZona)
        );
      }
    },
    actualizarCategorias(tab, nuevosGrupos) {
      const filtros = this[`filtros${tab}`];
      const grupoIds = nuevosGrupos.map((g) => g.IDGrupoDoc);
      this[`categoriasDisponibles${tab}`] =
        grupoIds.length > 0
          ? this.allCategorias.filter((cat) =>
              grupoIds.includes(cat.IDGrupoDoc)
            )
          : [];
      filtros.categoriasSeleccionadas = filtros.categoriasSeleccionadas.filter(
        (cat) =>
          this[`categoriasDisponibles${tab}`].some(
            (c) => c.IDCategoriaDoc === cat.IDCategoriaDoc
          )
      );
    },
    async inicializarDatos() {
      try {
        const [
          prediosRes,
          edificiosRes,
          nivelesRes,
          zonasRes,
          gruposRes,
          categoriasRes,
          planesRes,
        ] = await Promise.all([
          axios.get("/api/bi/listar-predios"),
          axios.get("/api/bi/listar-edificios"),
          axios.get("/api/bi/listar-niveles"),
          axios.get("/api/bi/listar-zonas"),
          axios.get("/api/bi/listar-grupos-doc"),
          axios.get("/api/bi/listar-categorias-doc"),
          axios.get("/api/bi/listar-planes-renovacion"),
        ]);
        this.allPredios = prediosRes.data;
        this.allEdificios = edificiosRes.data;
        this.allNiveles = nivelesRes.data;
        this.allZonas = zonasRes.data;
        this.allGrupos = gruposRes.data;
        this.allCategorias = categoriasRes.data;
        this.allPlanes = planesRes.data;
        this.prediosDisponibles = this.allPredios;
        this.gruposDisponibles = this.allGrupos;
        this.planesDisponibles = this.allPlanes;
        const tabs = ["Acciones", "OTs", "Documentos"];
        tabs.forEach((tab) => {
          this[`edificiosDisponibles${tab}`] = this.allEdificios;
          this[`nivelesDisponibles${tab}`] = this.allNiveles;
          this[`zonasDisponibles${tab}`] = this.allZonas;
          this[`categoriasDisponibles${tab}`] = [];
        });
      } catch (error) {
        console.error(
          "No se pudo cargar los datos iniciales para los filtros:",
          error
        );
      } finally {
        this.isReady = true;
      }
    },

    getPropsForTab(tab) {
      const filtrosTab = this[`filtros${tab}`];
      // Comprobamos que existan los filtros para la pestaña solicitada.
      if (!filtrosTab) {
        console.error("No se encontraron filtros para la pestaña:", tab);
        return {};
      }

      return {
        predio_ids: this[`idPrediosSeleccionados${tab}`],
        edificio_ids: this[`idEdificiosSeleccionados${tab}`],
        nivel_ids: this[`idNivelesSeleccionados${tab}`],
        zona_ids: this[`idZonasSeleccionadas${tab}`],
        grupo_ids: this[`idGruposSeleccionados${tab}`],
        categoria_ids: this[`idCategoriasSeleccionadas${tab}`],
        plan_ids: this[`idPlanesSeleccionados${tab}`],
        fecha_inicio: filtrosTab.fechaInicio,
        fecha_fin: filtrosTab.fechaFin,
      };
    },

    /**
     * Inicia la exportación a Excel con los filtros de la pestaña activa.
     */
    exportarRenovacion() {
      console.log(
        `Exportando datos de la pestaña de Renovación: ${this.activeTab}`
      );

      // 1. Obtenemos los filtros de la pestaña activa.
      const filtros = this.getPropsForTab(this.activeTab);

      // 2. Construimos los parámetros de la URL.
      const params = new URLSearchParams();
      for (const key in filtros) {
        const value = filtros[key];
        if (value !== null && value !== undefined) {
          if (Array.isArray(value)) {
            if (value.length > 0) {
              value.forEach((item) => params.append(`${key}[]`, item));
            }
          } else if (value !== "") {
            params.append(key, value);
          }
        }
      }

      // 3. Creamos la URL final y redirigimos para iniciar la descarga.
      const queryString = params.toString();
      // Apuntamos a una nueva ruta que crearemos en el backend
      const url = `/api/reportes/renovacion/exportar?${queryString}`;

      console.log("URL de exportación:", url);
      window.location.href = url;
    },
  },
  mounted() {
    this.inicializarDatos();
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style scoped>
.card {
  border: 1px solid #e9ecef;
  border-radius: 0.5rem;
}
.card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  font-weight: 600;
  padding: 1rem 1.25rem;
}
.nav-tabs .nav-link {
  border-width: 0 0 2px 0;
  border-color: transparent;
  color: #6c757d;
}
.nav-tabs .nav-link.active {
  border-color: #0d6efd;
  color: #0d6efd;
  font-weight: bold;
}
.form-label {
  margin-bottom: 0.5rem;
  font-size: 0.875em;
}
</style>