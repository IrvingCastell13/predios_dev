<template>
  <div class="container-fluid mt-4">
    <h1 class="mb-4 display-5">Dashboard de Mantenimiento de Equipos</h1>
    <p class="lead mb-5">
      Análisis de acciones de mantenimiento, Órdenes de Trabajo y estado de
      equipos.
    </p>
    <button class="btn btn-primary mb-4" @click="exportarMantenimiento">
      <i class="fas fa-file-excel"></i> Exportar a Excel
    </button>
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
          :class="{ active: activeTab === 'Equipos' }"
          @click="activeTab = 'Equipos'"
        >
          Equipos
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
      <div v-if="activeTab === 'Acciones'">
        <div class="card mb-4">
          <div class="card-body">
            <div class="row align-items-end">
              <div class="col-md-3">
                <label class="form-label fw-bold">Predio:</label>
                <vue-multiselect
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
                <label class="form-label fw-bold">Edificio:</label>
                <vue-multiselect
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
                <label class="form-label fw-bold">Nivel:</label>
                <vue-multiselect
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
                <label class="form-label fw-bold">Zona:</label>
                <vue-multiselect
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
                <label class="form-label fw-bold">Tipo de Equipo:</label>
                <vue-multiselect
                  v-model="filtrosAcciones.tiposEquipoSeleccionados"
                  :options="tiposEquipoDisponiblesAcciones"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreTipoEquipo"
                  track-by="IDTipoEquipo"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Sistema:</label>
                <vue-multiselect
                  v-model="filtrosAcciones.sistemasSeleccionados"
                  :options="sistemasDisponibles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreSistema"
                  track-by="IDSistema"
                ></vue-multiselect>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Subsistema:</label>
                <vue-multiselect
                  v-model="filtrosAcciones.subsistemasSeleccionados"
                  :options="subsistemasDisponiblesAcciones"
                  :multiple="true"
                  :close-on-select="false"
                  :disabled="filtrosAcciones.sistemasSeleccionados.length === 0"
                  placeholder="Seleccione sistema"
                  label="NombreSubsistema"
                  track-by="IDSubsistema"
                ></vue-multiselect>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Planes:</label>
                <vue-multiselect
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
                <label class="form-label fw-bold">Desde:</label>
                <date-picker
                  v-model:value="filtrosAcciones.fechaInicio"
                  format="YYYY-MM-DD"
                  value-type="format"
                  placeholder="Fecha inicio"
                ></date-picker>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Hasta:</label>
                <date-picker
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
              <div class="card-header">Estados de Acciones por Predio</div>
              <div class="card-body">
                <grafica-acciones-por-estados
                  :id-predios="idPrediosSeleccionadosAcciones"
                  :id-edificios="idEdificiosSeleccionadosAcciones"
                  :id-niveles="idNivelesSeleccionadosAcciones"
                  :id-zonas="idZonasSeleccionadasAcciones"
                  :id-tipos-equipo="idTiposEquipoSeleccionadosAcciones"
                  :id-sistemas="idSistemasSeleccionadosAcciones"
                  :id-subsistemas="idSubsistemasSeleccionadosAcciones"
                  :id-planes="idPlanesSeleccionadosAcciones"
                  :fecha-inicio="filtrosAcciones.fechaInicio"
                  :fecha-fin="filtrosAcciones.fechaFin"
                  api-url="/api/bi/estado-acciones"
                  chart-title="Estado de Acciones por Predio"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">Tabla de Acciones de Mantenimiento</div>
              <div class="card-body">
                <tabla-mantenimiento
                  :id-predios="idPrediosSeleccionadosAcciones"
                  :id-edificios="idEdificiosSeleccionadosAcciones"
                  :id-niveles="idNivelesSeleccionadosAcciones"
                  :id-zonas="idZonasSeleccionadasAcciones"
                  :id-tipos-equipo="idTiposEquipoSeleccionadosAcciones"
                  :id-sistemas="idSistemasSeleccionadosAcciones"
                  :id-subsistemas="idSubsistemasSeleccionadosAcciones"
                  :id-planes="idPlanesSeleccionadosAcciones"
                  :fecha-inicio="filtrosAcciones.fechaInicio"
                  :fecha-fin="filtrosAcciones.fechaFin"
                  api-url="/api/bi/estado-mantenimiento-equipos"
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
                <label class="form-label fw-bold">Predio:</label>
                <vue-multiselect
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
                <label class="form-label fw-bold">Edificio:</label>
                <vue-multiselect
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
                <label class="form-label fw-bold">Nivel:</label>
                <vue-multiselect
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
                <label class="form-label fw-bold">Zona:</label>
                <vue-multiselect
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
                <label class="form-label fw-bold">Tipo de Equipo:</label>
                <vue-multiselect
                  v-model="filtrosOTs.tiposEquipoSeleccionados"
                  :options="tiposEquipoDisponiblesOTs"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreTipoEquipo"
                  track-by="IDTipoEquipo"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Sistema:</label>
                <vue-multiselect
                  v-model="filtrosOTs.sistemasSeleccionados"
                  :options="sistemasDisponibles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreSistema"
                  track-by="IDSistema"
                ></vue-multiselect>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Subsistema:</label>
                <vue-multiselect
                  v-model="filtrosOTs.subsistemasSeleccionados"
                  :options="subsistemasDisponiblesOTs"
                  :multiple="true"
                  :close-on-select="false"
                  :disabled="filtrosOTs.sistemasSeleccionados.length === 0"
                  placeholder="Seleccione sistema"
                  label="NombreSubsistema"
                  track-by="IDSubsistema"
                ></vue-multiselect>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Planes:</label>
                <vue-multiselect
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
                <label class="form-label fw-bold">Desde:</label>
                <date-picker
                  v-model:value="filtrosOTs.fechaInicio"
                  format="YYYY-MM-DD"
                  value-type="format"
                  placeholder="Fecha inicio"
                ></date-picker>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Hasta:</label>
                <date-picker
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
                <grafica-acciones-por-estados
                  :id-predios="idPrediosSeleccionadosOTs"
                  :id-edificios="idEdificiosSeleccionadosOTs"
                  :id-niveles="idNivelesSeleccionadosOTs"
                  :id-zonas="idZonasSeleccionadasOTs"
                  :id-tipos-equipo="idTiposEquipoSeleccionadosOTs"
                  :id-sistemas="idSistemasSeleccionadosOTs"
                  :id-subsistemas="idSubsistemasSeleccionadosOTs"
                  :id-planes="idPlanesSeleccionadosOTs"
                  :fecha-inicio="filtrosOTs.fechaInicio"
                  :fecha-fin="filtrosOTs.fechaFin"
                  api-url="/api/bi/resumen-ot-estado"
                  chart-title="Estado de OTs por Predio"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">Tabla de Órdenes de Trabajo</div>
              <div class="card-body">
                <tabla-mantenimiento-orden-trabajo
                  :id-predios="idPrediosSeleccionadosOTs"
                  :id-edificios="idEdificiosSeleccionadosOTs"
                  :id-niveles="idNivelesSeleccionadosOTs"
                  :id-zonas="idZonasSeleccionadasOTs"
                  :id-tipos-equipo="idTiposEquipoSeleccionadosOTs"
                  :id-sistemas="idSistemasSeleccionadosOTs"
                  :id-subsistemas="idSubsistemasSeleccionadosOTs"
                  :id-planes="idPlanesSeleccionadosOTs"
                  :fecha-inicio="filtrosOTs.fechaInicio"
                  :fecha-fin="filtrosOTs.fechaFin"
                  api-url="/api/bi/detalle-estados-ot"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'Equipos'">
        <div class="card mb-4">
          <div class="card-body">
            <div class="row align-items-end">
              <div class="col-md-3">
                <label class="form-label fw-bold">Predio:</label>
                <vue-multiselect
                  v-model="filtrosEquipos.prediosSeleccionados"
                  :options="prediosDisponibles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombrePredio"
                  track-by="IDPredio"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Edificio:</label>
                <vue-multiselect
                  v-model="filtrosEquipos.edificiosSeleccionados"
                  :options="edificiosDisponiblesEquipos"
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
                  v-model="filtrosEquipos.nivelesSeleccionados"
                  :options="nivelesDisponiblesEquipos"
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
                  v-model="filtrosEquipos.zonasSeleccionadas"
                  :options="zonasDisponiblesEquipos"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreZona"
                  track-by="IDZona"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Tipo de Equipo:</label>
                <vue-multiselect
                  v-model="filtrosEquipos.tiposEquipoSeleccionados"
                  :options="tiposEquipoDisponiblesEquipos"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreTipoEquipo"
                  track-by="IDTipoEquipo"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Sistema:</label>
                <vue-multiselect
                  v-model="filtrosEquipos.sistemasSeleccionados"
                  :options="sistemasDisponibles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreSistema"
                  track-by="IDSistema"
                ></vue-multiselect>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Subsistema:</label>
                <vue-multiselect
                  v-model="filtrosEquipos.subsistemasSeleccionados"
                  :options="subsistemasDisponiblesEquipos"
                  :multiple="true"
                  :close-on-select="false"
                  :disabled="filtrosEquipos.sistemasSeleccionados.length === 0"
                  placeholder="Seleccione sistema"
                  label="NombreSubsistema"
                  track-by="IDSubsistema"
                ></vue-multiselect>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Planes:</label>
                <vue-multiselect
                  v-model="filtrosEquipos.planesSeleccionados"
                  :options="planesDisponibles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombrePlan"
                  track-by="IDPlan"
                ></vue-multiselect>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Desde:</label>
                <date-picker
                  v-model:value="filtrosEquipos.fechaInicio"
                  format="YYYY-MM-DD"
                  value-type="format"
                  placeholder="Fecha inicio"
                ></date-picker>
              </div>
              <div class="col-md-2 mt-3">
                <label class="form-label fw-bold">Hasta:</label>
                <date-picker
                  v-model:value="filtrosEquipos.fechaFin"
                  format="YYYY-MM-DD"
                  value-type="format"
                  placeholder="Fecha fin"
                ></date-picker>
              </div>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-body text-center text-muted py-5">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">Grafica de Estado Equipos</div>
                  <div class="card-body">
                    <grafica-accciones-por-estados
                      :id-predios="idPrediosSeleccionadosEquipos"
                      :id-edificios="idEdificiosSeleccionadosEquipos"
                      :id-niveles="idNivelesSeleccionadosEquipos"
                      :id-zonas="idZonasSeleccionadasEquipos"
                      :id-tipos-equipo="idTiposEquipoSeleccionadosEquipos"
                      :id-sistemas="idSistemasSeleccionadosEquipos"
                      :id-subsistemas="idSubsistemasSeleccionadosEquipos"
                      :id-planes="idPlanesSeleccionadosEquipos"
                      :fecha-inicio="filtrosEquipos.fechaInicio"
                      :fecha-fin="filtrosEquipos.fechaFin"
                      api-url="/api/bi/resumen-equipos-estado"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-4">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    Tabla de Acciones de Mantenimiento
                  </div>
                  <div class="card-body">
                    <tabla-estado-equipos
                      :id-predios="idPrediosSeleccionadosAcciones"
                      :id-edificios="idEdificiosSeleccionadosAcciones"
                      :id-niveles="idNivelesSeleccionadosAcciones"
                      :id-zonas="idZonasSeleccionadasAcciones"
                      :id-tipos-equipo="idTiposEquipoSeleccionadosAcciones"
                      :id-sistemas="idSistemasSeleccionadosAcciones"
                      :id-subsistemas="idSubsistemasSeleccionadosAcciones"
                      :id-planes="idPlanesSeleccionadosEquipos"
                      :fecha-inicio="filtrosAcciones.fechaInicio"
                      :fecha-fin="filtrosAcciones.fechaFin"
                      api-url="/api/bi/detalle-estados-equipos"
                    />
                  </div>
                </div>
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
import GraficaAccionesPorEstados from "./GraficaAccionesPorEstados.vue";
import TablaMantenimiento from "./TablaMantenimiento.vue";
import TablaMantenimientoOrdenTrabajo from "./TablaMantenimientoOrdenTrabajo.vue";
import TablaEstadoEquipos from "./TablaEstadoEquipos.vue";

export default {
  name: "DashboardMantenimiento",
  components: {
    VueMultiselect,
    DatePicker,
    GraficaAccionesPorEstados,
    TablaMantenimiento,
    TablaMantenimientoOrdenTrabajo,
    TablaEstadoEquipos,
  },
  data() {
    return {
      isReady: false,
      activeTab: "Acciones",

      // Listas maestras
      allPredios: [],
      allEdificios: [],
      allNiveles: [],
      allZonas: [],
      allSistemas: [],
      allSubsistemas: [],
      allTiposEquipo: [],
      allPlanes: [], // NUEVO

      // Listas de opciones globales
      prediosDisponibles: [],
      sistemasDisponibles: [],
      planesDisponibles: [], // NUEVO

      // Opciones para filtros de Acciones
      edificiosDisponiblesAcciones: [],
      nivelesDisponiblesAcciones: [],
      zonasDisponiblesAcciones: [],
      tiposEquipoDisponiblesAcciones: [],
      subsistemasDisponiblesAcciones: [],

      // Opciones para filtros de OTs
      edificiosDisponiblesOTs: [],
      nivelesDisponiblesOTs: [],
      zonasDisponiblesOTs: [],
      tiposEquipoDisponiblesOTs: [],
      subsistemasDisponiblesOTs: [],

      // Opciones para filtros de Equipos
      edificiosDisponiblesEquipos: [],
      nivelesDisponiblesEquipos: [],
      zonasDisponiblesEquipos: [],
      tiposEquipoDisponiblesEquipos: [],
      subsistemasDisponiblesEquipos: [],

      // Modelo de filtros para ACCIONES
      filtrosAcciones: {
        prediosSeleccionados: [],
        edificiosSeleccionados: [],
        nivelesSeleccionados: [],
        zonasSeleccionadas: [],
        tiposEquipoSeleccionados: [],
        sistemasSeleccionados: [],
        subsistemasSeleccionados: [],
        planesSeleccionados: [], // NUEVO
        fechaInicio: null,
        fechaFin: null,
      },

      // Modelo de filtros para OTs
      filtrosOTs: {
        prediosSeleccionados: [],
        edificiosSeleccionados: [],
        nivelesSeleccionados: [],
        zonasSeleccionadas: [],
        tiposEquipoSeleccionados: [],
        sistemasSeleccionados: [],
        subsistemasSeleccionados: [],
        planesSeleccionados: [], // NUEVO
        fechaInicio: null,
        fechaFin: null,
      },

      // Modelo de filtros para EQUIPOS
      filtrosEquipos: {
        prediosSeleccionados: [],
        edificiosSeleccionados: [],
        nivelesSeleccionados: [],
        zonasSeleccionadas: [],
        tiposEquipoSeleccionados: [],
        sistemasSeleccionados: [],
        subsistemasSeleccionados: [],
        planesSeleccionados: [], // NUEVO
        fechaInicio: null,
        fechaFin: null,
      },
    };
  },
  computed: {
    // === COMPUTEDS PARA ACCIONES ===
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
    idTiposEquipoSeleccionadosAcciones() {
      return this.filtrosAcciones.tiposEquipoSeleccionados.map(
        (t) => t.IDTipoEquipo
      );
    },
    idSistemasSeleccionadosAcciones() {
      return this.filtrosAcciones.sistemasSeleccionados.map((s) => s.IDSistema);
    },
    idSubsistemasSeleccionadosAcciones() {
      return this.filtrosAcciones.subsistemasSeleccionados.map(
        (s) => s.IDSubsistema
      );
    },
    // NUEVO
    idPlanesSeleccionadosAcciones() {
      return this.filtrosAcciones.planesSeleccionados.map((p) => p.IDPlan);
    },

    // === COMPUTEDS PARA OTs ===
    idPrediosSeleccionadosOTs() {
      return this.filtrosOTs.prediosSeleccionados.map((p) => p.IDPredio);
    },
    idEdificiosSeleccionadosOTs() {
      return this.filtrosOTs.edificiosSeleccionados.map((e) => e.IDEdificio);
    },
    idNivelesSeleccionadosOTs() {
      return this.filtrosOTs.nivelesSeleccionados.map((n) => n.IDNivel);
    },
    idZonasSeleccionadasOTs() {
      return this.filtrosOTs.zonasSeleccionadas.map((z) => z.IDZona);
    },
    idTiposEquipoSeleccionadosOTs() {
      return this.filtrosOTs.tiposEquipoSeleccionados.map(
        (t) => t.IDTipoEquipo
      );
    },
    idSistemasSeleccionadosOTs() {
      return this.filtrosOTs.sistemasSeleccionados.map((s) => s.IDSistema);
    },
    idSubsistemasSeleccionadosOTs() {
      return this.filtrosOTs.subsistemasSeleccionados.map(
        (s) => s.IDSubsistema
      );
    },
    // NUEVO
    idPlanesSeleccionadosOTs() {
      return this.filtrosOTs.planesSeleccionados.map((p) => p.IDPlan);
    },

    // === COMPUTEDS PARA EQUIPOS ===
    idPrediosSeleccionadosEquipos() {
      return this.filtrosEquipos.prediosSeleccionados.map((p) => p.IDPredio);
    },
    idEdificiosSeleccionadosEquipos() {
      return this.filtrosEquipos.edificiosSeleccionados.map(
        (e) => e.IDEdificio
      );
    },
    idNivelesSeleccionadosEquipos() {
      return this.filtrosEquipos.nivelesSeleccionados.map((n) => n.IDNivel);
    },
    idZonasSeleccionadasEquipos() {
      return this.filtrosEquipos.zonasSeleccionadas.map((z) => z.IDZona);
    },
    idTiposEquipoSeleccionadosEquipos() {
      return this.filtrosEquipos.tiposEquipoSeleccionados.map(
        (t) => t.IDTipoEquipo
      );
    },
    idSistemasSeleccionadosEquipos() {
      return this.filtrosEquipos.sistemasSeleccionados.map((s) => s.IDSistema);
    },
    idSubsistemasSeleccionadosEquipos() {
      return this.filtrosEquipos.subsistemasSeleccionados.map(
        (s) => s.IDSubsistema
      );
    },
    // NUEVO
    idPlanesSeleccionadosEquipos() {
      return this.filtrosEquipos.planesSeleccionados.map((p) => p.IDPlan);
    },
  },
  watch: {
    // === WATCHERS PARA ACCIONES ===
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
    "filtrosAcciones.zonasSeleccionadas": {
      handler() {
        this.actualizarTiposEquipoDisponibles("Acciones");
      },
      deep: true,
    },
    "filtrosAcciones.sistemasSeleccionados": {
      handler(nuevos) {
        this.actualizarSubsistemas("Acciones", nuevos);
      },
      deep: true,
    },

    // === WATCHERS PARA OTs ===
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
    "filtrosOTs.zonasSeleccionadas": {
      handler() {
        this.actualizarTiposEquipoDisponibles("OTs");
      },
      deep: true,
    },
    "filtrosOTs.sistemasSeleccionados": {
      handler(nuevos) {
        this.actualizarSubsistemas("OTs", nuevos);
      },
      deep: true,
    },

    // === WATCHERS PARA EQUIPOS ===
    "filtrosEquipos.prediosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Equipos");
      },
      deep: true,
    },
    "filtrosEquipos.edificiosSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Equipos", "edificio");
      },
      deep: true,
    },
    "filtrosEquipos.nivelesSeleccionados": {
      handler() {
        this.actualizarCascadaUbicacion("Equipos", "nivel");
      },
      deep: true,
    },
    "filtrosEquipos.zonasSeleccionadas": {
      handler() {
        this.actualizarTiposEquipoDisponibles("Equipos");
      },
      deep: true,
    },
    "filtrosEquipos.sistemasSeleccionados": {
      handler(nuevos) {
        this.actualizarSubsistemas("Equipos", nuevos);
      },
      deep: true,
    },
  },
  methods: {
    actualizarCascadaUbicacion(tab, nivelCambiado = "predio") {
      const filtros = this[`filtros${tab}`];
      const predioIds = filtros.prediosSeleccionados.map((p) => p.IDPredio);

      if (nivelCambiado === "predio") {
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

      const edificioIds = filtros.edificiosSeleccionados.map(
        (e) => e.IDEdificio
      );
      const edificiosDispIds = this[`edificiosDisponibles${tab}`].map(
        (e) => e.IDEdificio
      );

      if (["predio", "edificio"].includes(nivelCambiado)) {
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

      const nivelIds = filtros.nivelesSeleccionados.map((n) => n.IDNivel);
      const nivelesDispIds = this[`nivelesDisponibles${tab}`].map(
        (n) => n.IDNivel
      );

      if (["predio", "edificio", "nivel"].includes(nivelCambiado)) {
        this[`zonasDisponibles${tab}`] =
          nivelIds.length > 0
            ? this.allZonas.filter((z) => nivelIds.includes(z.IDNivel))
            : this.allZonas.filter((z) => nivelesDispIds.includes(z.IDNivel));
        filtros.zonasSeleccionadas = filtros.zonasSeleccionadas.filter((z) =>
          this[`zonasDisponibles${tab}`].some((zd) => zd.IDZona === z.IDZona)
        );
      }

      this.actualizarTiposEquipoDisponibles(tab);
    },

    actualizarTiposEquipoDisponibles(tab) {
      const filtros = this[`filtros${tab}`];
      const predioIds = filtros.prediosSeleccionados.map((p) => p.IDPredio);
      const edificioIds = filtros.edificiosSeleccionados.map(
        (e) => e.IDEdificio
      );
      const nivelIds = filtros.nivelesSeleccionados.map((n) => n.IDNivel);
      const zonaIds = filtros.zonasSeleccionadas.map((z) => z.IDZona);

      let tiposFiltrados = this.allTiposEquipo;

      if (zonaIds.length > 0)
        tiposFiltrados = tiposFiltrados.filter((t) =>
          zonaIds.includes(t.IDZona)
        );
      else if (nivelIds.length > 0)
        tiposFiltrados = tiposFiltrados.filter((t) =>
          nivelIds.includes(t.IDNivel)
        );
      else if (edificioIds.length > 0)
        tiposFiltrados = tiposFiltrados.filter((t) =>
          edificioIds.includes(t.IDEdificio)
        );
      else if (predioIds.length > 0)
        tiposFiltrados = tiposFiltrados.filter((t) =>
          predioIds.includes(t.IDPredio)
        );

      this[`tiposEquipoDisponibles${tab}`] = [
        ...new Map(
          tiposFiltrados.map((item) => [item["IDTipoEquipo"], item])
        ).values(),
      ];
      filtros.tiposEquipoSeleccionados =
        filtros.tiposEquipoSeleccionados.filter((t) =>
          this[`tiposEquipoDisponibles${tab}`].some(
            (td) => td.IDTipoEquipo === t.IDTipoEquipo
          )
        );
    },

    actualizarSubsistemas(tab, nuevosSistemas) {
      const filtros = this[`filtros${tab}`];
      const sistemaIds = nuevosSistemas.map((s) => s.IDSistema);
      this[`subsistemasDisponibles${tab}`] =
        sistemaIds.length > 0
          ? this.allSubsistemas.filter((sub) =>
              sistemaIds.includes(sub.IDSistema)
            )
          : [];
      filtros.subsistemasSeleccionados =
        filtros.subsistemasSeleccionados.filter((sub) =>
          this[`subsistemasDisponibles${tab}`].some(
            (s) => s.IDSubsistema === sub.IDSubsistema
          )
        );
    },

    async inicializarDatos() {
      try {
        const [
          prediosRes,
          sistemasRes,
          edificiosRes,
          nivelesRes,
          zonasRes,
          subsistemasRes,
          tiposEquipoRes,
          planesRes, // NUEVO
        ] = await Promise.all([
          axios.get("/api/bi/listar-predios"),
          axios.get("/api/bi/listar-sistemas"),
          axios.get("/api/bi/listar-edificios"),
          axios.get("/api/bi/listar-niveles"),
          axios.get("/api/bi/listar-zonas"),
          axios.get("/api/bi/listar-subsistemas"),
          axios.get("/api/bi/listar-tipos-equipo-con-ubicacion"),
          axios.get("/api/bi/listar-planes-mantenimiento"), // NUEVO
        ]);

        this.allPredios = prediosRes.data;
        this.allSistemas = sistemasRes.data;
        this.allEdificios = edificiosRes.data;
        this.allNiveles = nivelesRes.data;
        this.allZonas = zonasRes.data;
        this.allSubsistemas = subsistemasRes.data;
        this.allTiposEquipo = tiposEquipoRes.data;
        this.allPlanes = planesRes.data; // NUEVO

        this.prediosDisponibles = this.allPredios;
        this.sistemasDisponibles = this.allSistemas;
        this.planesDisponibles = this.allPlanes; // NUEVO

        const tabs = ["Acciones", "OTs", "Equipos"];
        tabs.forEach((tab) => {
          this[`edificiosDisponibles${tab}`] = this.allEdificios;
          this[`nivelesDisponibles${tab}`] = this.allNiveles;
          this[`zonasDisponibles${tab}`] = this.allZonas;
          this[`subsistemasDisponibles${tab}`] = [];
          this[`tiposEquipoDisponibles${tab}`] = [
            ...new Map(
              this.allTiposEquipo.map((item) => [item["IDTipoEquipo"], item])
            ).values(),
          ];
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
      const computedProps = this[`idPrediosSeleccionados${tab}`]; // Solo para verificar existencia

      if (!filtrosTab || !computedProps) {
        console.error("No se encontraron filtros para la pestaña:", tab);
        return {};
      }

      return {
        predio_ids: this[`idPrediosSeleccionados${tab}`],
        edificio_ids: this[`idEdificiosSeleccionados${tab}`],
        nivel_ids: this[`idNivelesSeleccionados${tab}`],
        zona_ids: this[`idZonasSeleccionadas${tab}`],
        tipo_equipo_ids: this[`idTiposEquipoSeleccionados${tab}`],
        sistema_ids: this[`idSistemasSeleccionados${tab}`],
        subsistema_ids: this[`idSubsistemasSeleccionados${tab}`],
        plan_ids: this[`idPlanesSeleccionados${tab}`],
        fecha_inicio: filtrosTab.fechaInicio,
        fecha_fin: filtrosTab.fechaFin,
      };
    },

    /**
     * Inicia la exportación a Excel con los filtros de la pestaña activa.
     */
    exportarMantenimiento() {
      console.log(
        `Exportando datos de la pestaña de Mantenimiento: ${this.activeTab}`
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

      // 3. Creamos la URL final y le decimos al navegador que la abra.
      const queryString = params.toString();
      const url = `/api/reportes/mantenimiento/exportar?${queryString}`;

      console.log("URL de exportación:", url);

      // Esto iniciará la descarga del archivo.
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
/* Estilos para una apariencia más moderna */
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