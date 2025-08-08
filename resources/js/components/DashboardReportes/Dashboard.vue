<template>
  <div>
    <h1 class="mb-4 display-5">Dashboard</h1>
    <p class="lead mb-5">
      Resumen visual del estado de la documentación por predio y categoría.
    </p>

    <div class="mb-3">
      <button class="btn btn-primary" @click="exportarDocumentos">
        <i class="fas fa-file-excel"></i> Exportar a Excel
      </button>
    </div>

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
          :class="{ active: activeTab === 'Detalles' }"
          @click="activeTab = 'Detalles'"
        >
          Detalles
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
                      <label class="form-label fw-bold">País:</label>
                      <vue-multiselect
                        v-model="filtrosCumplimiento.paisesSeleccionados"
                        :options="paisesDisponiblesCumplimiento"
                        :multiple="true"
                        :close-on-select="false"
                        placeholder="Todos"
                        label="NombrePais"
                        track-by="IDPais"
                      ></vue-multiselect>
                    </div>

                    <div class="col-md-3">
                      <label class="form-label fw-bold">Estado:</label>
                      <vue-multiselect
                        v-model="filtrosCumplimiento.estadosSeleccionados"
                        :options="estadosDisponiblesCumplimiento"
                        :multiple="true"
                        :close-on-select="false"
                        placeholder="Todos"
                        label="NombreEstado"
                        track-by="IDEstado"
                      ></vue-multiselect>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label fw-bold">Municipio:</label>
                      <vue-multiselect
                        v-model="filtrosCumplimiento.municipiosSeleccionados"
                        :options="municipiosDisponiblesCumplimiento"
                        :multiple="true"
                        :close-on-select="false"
                        placeholder="Todos"
                        label="NombreMunicipio"
                        track-by="IDMunicipio"
                      ></vue-multiselect>
                    </div>

                    <div class="col-md-3">
                      <label class="form-label fw-bold">Predio:</label>
                      <vue-multiselect
                        v-model="filtrosCumplimiento.prediosSeleccionados"
                        :options="prediosDisponiblesCumplimiento"
                        :multiple="true"
                        :close-on-select="false"
                        placeholder="Todos"
                        label="NombrePredio"
                        track-by="IDPredio"
                      ></vue-multiselect>
                    </div>

                    <div class="col-md-3 mt-3">
                      <label class="form-label fw-bold">Edificio:</label>
                      <vue-multiselect
                        v-model="filtrosCumplimiento.edificiosSeleccionados"
                        :options="edificiosDisponiblesCumplimiento"
                        :multiple="true"
                        :close-on-select="false"
                        placeholder="Todos"
                        label="NombreEdificio"
                        track-by="IDEdificio"
                      ></vue-multiselect>
                    </div>

                    <div class="col-md-3 mt-3">
                      <label class="form-label fw-bold">Nivel:</label>
                      <vue-multiselect
                        v-model="filtrosCumplimiento.nivelesSeleccionados"
                        :options="nivelesDisponiblesCumplimiento"
                        :multiple="true"
                        :close-on-select="false"
                        placeholder="Todos"
                        label="NombreNivel"
                        track-by="IDNivel"
                      ></vue-multiselect>
                    </div>

                    <div class="col-md-3 mt-3">
                      <label class="form-label fw-bold">Zona:</label>
                      <vue-multiselect
                        v-model="filtrosCumplimiento.zonasSeleccionadas"
                        :options="zonasDisponiblesCumplimiento"
                        :multiple="true"
                        :close-on-select="false"
                        placeholder="Todos"
                        label="NombreZona"
                        track-by="IDZona"
                      ></vue-multiselect>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label fw-bold"
                        >Categoría (Grupo):</label
                      >
                      <vue-multiselect
                        v-model="filtrosCumplimiento.gruposSeleccionados"
                        :options="gruposDisponiblesCumplimiento"
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
                        v-model="filtrosCumplimiento.categoriasSeleccionadas"
                        :options="categoriasDisponiblesCumplimiento"
                        :multiple="true"
                        :close-on-select="false"
                        :disabled="
                          filtrosCumplimiento.gruposSeleccionados.length === 0
                        "
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
                        v-model="
                          filtrosCumplimiento.tiposDocumentoSeleccionados
                        "
                        :options="tiposDocumentoDisponiblesCumplimiento"
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
                        v-model="filtrosCumplimiento.tiposInmuebleSeleccionados"
                        :options="tiposInmuebleDisponiblesCumplimiento"
                        :multiple="true"
                        :close-on-select="false"
                        placeholder="Todos"
                        label="NombreTipoInmueble"
                        track-by="IDTipoInmueble"
                      ></vue-multiselect>
                    </div>
                    <div class="col-md-3 mt-3">
                      <label class="form-label fw-bold">Archivos:</label>
                      <select
                        v-model="filtrosCumplimiento.estadoArchivo"
                        class="form-select"
                      >
                        <option
                          v-for="opcion in opcionesArchivo"
                          :key="opcion.value"
                          :value="opcion.value"
                        >
                          {{ opcion.text }}
                        </option>
                      </select>
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
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
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
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
                          api-url="/api/bi/matriz-subcategoria-archivos"
                          chart-title="SubCategorías con o sin archivo adjunto por Predio"
                        >
                        </base-heatmap-matrix-archivos>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                      <div class="card-header">
                        Matriz de Cumplimiento por SubCategoría por Zona
                      </div>
                      <div class="card-body">
                        <base-heatmap-matrix
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
                          api-url="/api/bi/matriz-subcategoria-zona"
                          chart-title="Cumplimiento por Predio vs. SubCategoría por Zona"
                        >
                        </base-heatmap-matrix>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                      <div class="card-header">
                        Matriz de Archivos Adjuntos por SubCategoría por Zona
                      </div>
                      <div class="card-body">
                        <base-heatmap-matrix-archivos
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
                          api-url="/api/bi/matriz-subcategoria-archivos-zona"
                          chart-title="SubCategorías con o sin archivo adjunto por Zona"
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
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
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
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
                          api-url="/api/bi/matriz-grupo-archivos"
                          chart-title="Archivos adjuntos por Predio vs. Categoria de Documento"
                        >
                        </matriz-archivos-por-grupo>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row mt-4">
                  <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                      <div class="card-header">
                        Matriz de Cumplimiento por Categoria por Zona
                      </div>
                      <div class="card-body">
                        <matriz-por-grupo
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
                          api-url="/api/bi/matriz-grupo-zona"
                          chart-title="Cumplimiento por Predio vs. Categoria de Documento por Zona"
                        >
                        </matriz-por-grupo>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                      <div class="card-header">
                        Matriz de Archivos por Categoria por Zona
                      </div>
                      <div class="card-body">
                        <matriz-archivos-por-grupo
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
                          api-url="/api/bi/matriz-grupo-archivos-zona"
                          chart-title="Archivos adjuntos por Predio vs. Categoria de Documento por Zona"
                        >
                        </matriz-archivos-por-grupo>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row mt-4">
                  <div class="col-md-6">
                    <div class="card">
                      <div class="card-header">
                        Resumen de Cumplimiento Global Creados
                      </div>
                      <div class="card-body">
                        <grafica-global-creados
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
                          api-url="/api/bi/calificaciones-ponderadas"
                          chart-title="Cumplimiento ponderado global creados (%)"
                        >
                        </grafica-global-creados>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card">
                      <div class="card-header">
                        Resumen de Cumplimiento Global Con Archivos
                      </div>
                      <div class="card-body">
                        <grafica-global-con-archivo
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
                          api-url="/api/bi/calificaciones-ponderadas"
                          chart-title="Cumplimiento ponderado global con archivos (%)"
                        >
                        </grafica-global-con-archivo>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row mt-4">
                  <div class="col-md-6">
                    <div class="card">
                      <div class="card-header">
                        Resumen de Cumplimiento Global Creados por Zona
                      </div>
                      <div class="card-body">
                        <grafica-global-creados
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
                          api-url="/api/bi/calificaciones-ponderadas-zona"
                          chart-title="Cumplimiento ponderado global creados por zona (%)"
                        >
                        </grafica-global-creados>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card">
                      <div class="card-header">
                        Resumen de Cumplimiento Global Con Archivos por Zona
                      </div>
                      <div class="card-body">
                        <grafica-global-con-archivo
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
                          api-url="/api/bi/calificaciones-ponderadas-zona"
                          chart-title="Cumplimiento ponderado global con archivos por zona (%)"
                        >
                        </grafica-global-con-archivo>
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
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
                          api-url="/api/bi/calificaciones-ponderadas"
                          chart-title="Cumplimiento ponderado por predio (%)"
                        >
                        </horizontal-ponderado-por-predio>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row mt-4">
                  <div class="col-12">
                    <div class="card">
                      <div class="card-header">
                        Resumen de Cumplimiento General por Zona
                      </div>
                      <div class="card-body">
                        <horizontal-ponderado-por-predio
                          :id-predios="idPrediosSeleccionadosCumplimiento"
                          :id-grupos="idGruposSeleccionadosCumplimiento"
                          :id-categorias="idCategoriasSeleccionadasCumplimiento"
                          :id-tipos-documento="
                            idTiposDocumentoSeleccionadosCumplimiento
                          "
                          :id-tipos-inmueble="
                            idTiposInmuebleSeleccionadosCumplimiento
                          "
                          :estado-archivo="estadoArchivoCumplimiento"
                          :id-paises="idPaisSeleccionadoCumplimiento"
                          :id-estados="idEstadoSeleccionadoCumplimiento"
                          :id-municipios="idMunicipioSeleccionadoCumplimiento"
                          :id-edificios="idEdificiosSeleccionadosCumplimiento"
                          :id-niveles="idNivelesSeleccionadosCumplimiento"
                          :id-zonas="idZonasSeleccionadasCumplimiento"
                          api-url="/api/bi/calificaciones-ponderadas-zona"
                          chart-title="Cumplimiento ponderado por predio por zona (%)"
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
      </div>

      <div v-if="activeTab === 'Vigencia'">
        <div class="card mb-4">
          <div class="card-body">
            <div class="row align-items-end">
              <div class="col-md-3">
                <label class="form-label fw-bold">País:</label>
                <vue-multiselect
                  v-model="filtrosVigencia.paisesSeleccionados"
                  :options="paisesDisponiblesVigencia"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombrePais"
                  track-by="IDPais"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Estado:</label>
                <vue-multiselect
                  v-model="filtrosVigencia.estadosSeleccionados"
                  :options="estadosDisponiblesVigencia"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreEstado"
                  track-by="IDEstado"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Municipio:</label>
                <vue-multiselect
                  v-model="filtrosVigencia.municipiosSeleccionados"
                  :options="municipiosDisponiblesVigencia"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreMunicipio"
                  track-by="IDMunicipio"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Predio:</label>
                <vue-multiselect
                  v-model="filtrosVigencia.prediosSeleccionados"
                  :options="prediosDisponiblesVigencia"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombrePredio"
                  track-by="IDPredio"
                ></vue-multiselect>
              </div>

              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Edificio:</label>
                <vue-multiselect
                  v-model="filtrosVigencia.edificiosSeleccionados"
                  :options="edificiosDisponiblesVigencia"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreEdificio"
                  track-by="IDEdificio"
                ></vue-multiselect>
              </div>

              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Nivel:</label>
                <vue-multiselect
                  v-model="filtrosVigencia.nivelesSeleccionados"
                  :options="nivelesDisponiblesVigencia"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreNivel"
                  track-by="IDNivel"
                ></vue-multiselect>
              </div>

              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Zona:</label>
                <vue-multiselect
                  v-model="filtrosVigencia.zonasSeleccionadas"
                  :options="zonasDisponiblesVigencia"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreZona"
                  track-by="IDZona"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Categoría (Grupo):</label>
                <vue-multiselect
                  v-model="filtrosVigencia.gruposSeleccionados"
                  :options="gruposDisponiblesVigencia"
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
                  v-model="filtrosVigencia.categoriasSeleccionadas"
                  :options="categoriasDisponiblesVigencia"
                  :multiple="true"
                  :close-on-select="false"
                  :disabled="filtrosVigencia.gruposSeleccionados.length === 0"
                  placeholder="Seleccione categoría"
                  label="NombreCategoriaDoc"
                  track-by="IDCategoriaDoc"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Tipo de Documento:</label>
                <vue-multiselect
                  v-model="filtrosVigencia.tiposDocumentoSeleccionados"
                  :options="tiposDocumentoDisponiblesVigencia"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreTipoDocumento"
                  track-by="IDTipoDocumento"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Tipo de Inmueble:</label>
                <vue-multiselect
                  v-model="filtrosVigencia.tiposInmuebleSeleccionados"
                  :options="tiposInmuebleDisponiblesVigencia"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreTipoInmueble"
                  track-by="IDTipoInmueble"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Archivos:</label>
                <select
                  v-model="filtrosVigencia.estadoArchivo"
                  class="form-select"
                >
                  <option
                    v-for="opcion in opcionesArchivo"
                    :key="opcion.value"
                    :value="opcion.value"
                  >
                    {{ opcion.text }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 mb-4">
            <div class="card mb-5 h-100">
              <div class="card-header">
                Matriz de Estado de Documentos por Categoría
              </div>
              <div class="card-body">
                <VigenciaMatrizEstadoCategoria
                  :id-edificios="idEdificiosSeleccionadosVigencia"
                  :id-niveles="idNivelesSeleccionadosVigencia"
                  :id-zonas="idZonasSeleccionadasVigencia"
                  :id-predios="idPrediosSeleccionadosVigencia"
                  :id-grupos="idGruposSeleccionadosVigencia"
                  :id-categorias="idCategoriasSeleccionadasVigencia"
                  :id-tipos-documento="idTiposDocumentoSeleccionadosVigencia"
                  :id-tipos-inmueble="idTiposInmuebleSeleccionadosVigencia"
                  :estado-archivo="estadoArchivoVigencia"
                  :id-paises="idPaisSeleccionadoVigencia"
                  :id-estados="idEstadoSeleccionadoVigencia"
                  :id-municipios="idMunicipioSeleccionadoVigencia"
                  api-url="/api/bi/documentos-con-estado"
                />
              </div>
            </div>
          </div>
          <div class="col-lg-6 mb-4">
            <div class="card mb-4 h-100">
              <div class="card-header">
                Matriz de Estado de Documentos por Subcategoría
              </div>
              <div class="card-body">
                <VigenciaMatrizEstadoSubcategoria
                  :id-edificios="idEdificiosSeleccionadosVigencia"
                  :id-niveles="idNivelesSeleccionadosVigencia"
                  :id-zonas="idZonasSeleccionadasVigencia"
                  :id-predios="idPrediosSeleccionadosVigencia"
                  :id-grupos="idGruposSeleccionadosVigencia"
                  :id-categorias="idCategoriasSeleccionadasVigencia"
                  :id-tipos-documento="idTiposDocumentoSeleccionadosVigencia"
                  :id-tipos-inmueble="idTiposInmuebleSeleccionadosVigencia"
                  :estado-archivo="estadoArchivoVigencia"
                  :id-paises="idPaisSeleccionadoVigencia"
                  :id-estados="idEstadoSeleccionadoVigencia"
                  :id-municipios="idMunicipioSeleccionadoVigencia"
                  api-url="/api/bi/documentos-por-subcategoria"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 mb-4">
            <div class="card mb-5 h-100">
              <div class="card-header">
                Matriz de Estado de Documentos por Categoría por Zona
              </div>
              <div class="card-body">
                <VigenciaMatrizEstadoCategoria
                  :id-edificios="idEdificiosSeleccionadosVigencia"
                  :id-niveles="idNivelesSeleccionadosVigencia"
                  :id-zonas="idZonasSeleccionadasVigencia"
                  :id-predios="idPrediosSeleccionadosVigencia"
                  :id-grupos="idGruposSeleccionadosVigencia"
                  :id-categorias="idCategoriasSeleccionadasVigencia"
                  :id-tipos-documento="idTiposDocumentoSeleccionadosVigencia"
                  :id-tipos-inmueble="idTiposInmuebleSeleccionadosVigencia"
                  :estado-archivo="estadoArchivoVigencia"
                  :id-paises="idPaisSeleccionadoVigencia"
                  :id-estados="idEstadoSeleccionadoVigencia"
                  :id-municipios="idMunicipioSeleccionadoVigencia"
                  api-url="/api/bi/documentos-con-estado-zona"
                />
              </div>
            </div>
          </div>
          <div class="col-lg-6 mb-4">
            <div class="card mb-4 h-100">
              <div class="card-header">
                Matriz de Estado de Documentos por Subcategoría por Zona
              </div>
              <div class="card-body">
                <VigenciaMatrizEstadoSubcategoria
                  :id-edificios="idEdificiosSeleccionadosVigencia"
                  :id-niveles="idNivelesSeleccionadosVigencia"
                  :id-zonas="idZonasSeleccionadasVigencia"
                  :id-predios="idPrediosSeleccionadosVigencia"
                  :id-grupos="idGruposSeleccionadosVigencia"
                  :id-categorias="idCategoriasSeleccionadasVigencia"
                  :id-tipos-documento="idTiposDocumentoSeleccionadosVigencia"
                  :id-tipos-inmueble="idTiposInmuebleSeleccionadosVigencia"
                  :estado-archivo="estadoArchivoVigencia"
                  :id-paises="idPaisSeleccionadoVigencia"
                  :id-estados="idEstadoSeleccionadoVigencia"
                  :id-municipios="idMunicipioSeleccionadoVigencia"
                  api-url="/api/bi/documentos-por-subcategoria-zona"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                Resumen de Vigencia General por Predio
              </div>
              <div class="card-body">
                <horizontal-vigencia-por-predio
                  :id-edificios="idEdificiosSeleccionadosVigencia"
                  :id-niveles="idNivelesSeleccionadosVigencia"
                  :id-zonas="idZonasSeleccionadasVigencia"
                  :id-predios="idPrediosSeleccionadosVigencia"
                  :id-grupos="idGruposSeleccionadosVigencia"
                  :id-categorias="idCategoriasSeleccionadasVigencia"
                  :id-tipos-documento="idTiposDocumentoSeleccionadosVigencia"
                  :id-tipos-inmueble="idTiposInmuebleSeleccionadosVigencia"
                  :estado-archivo="estadoArchivoVigencia"
                  :id-paises="idPaisSeleccionadoVigencia"
                  :id-estados="idEstadoSeleccionadoVigencia"
                  :id-municipios="idMunicipioSeleccionadoVigencia"
                  api-url="/api/bi/porcentaje-vigencia-por-predio"
                  chart-title="Vencimiento de documentos ponderado por predio (%)"
                >
                </horizontal-vigencia-por-predio>
              </div>
            </div>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                Resumen de Vigencia General por Zona
              </div>
              <div class="card-body">
                <horizontal-vigencia-por-zona
                  :id-edificios="idEdificiosSeleccionadosVigencia"
                  :id-niveles="idNivelesSeleccionadosVigencia"
                  :id-zonas="idZonasSeleccionadasVigencia"
                  :id-predios="idPrediosSeleccionadosVigencia"
                  :id-grupos="idGruposSeleccionadosVigencia"
                  :id-categorias="idCategoriasSeleccionadasVigencia"
                  :id-tipos-documento="idTiposDocumentoSeleccionadosVigencia"
                  :id-tipos-inmueble="idTiposInmuebleSeleccionadosVigencia"
                  :estado-archivo="estadoArchivoVigencia"
                  :id-paises="idPaisSeleccionadoVigencia"
                  :id-estados="idEstadoSeleccionadoVigencia"
                  :id-municipios="idMunicipioSeleccionadoVigencia"
                >
                </horizontal-vigencia-por-zona>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'Detalles'">
        <div class="card mb-4">
          <div class="card-body">
            <div class="row align-items-end">
              <div class="col-md-3">
                <label class="form-label fw-bold">País:</label>
                <vue-multiselect
                  v-model="filtrosDetalles.paisesSeleccionados"
                  :options="paisesDisponiblesDetalles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombrePais"
                  track-by="IDPais"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Estado:</label>
                <vue-multiselect
                  v-model="filtrosDetalles.estadosSeleccionados"
                  :options="estadosDisponiblesDetalles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreEstado"
                  track-by="IDEstado"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Municipio:</label>
                <vue-multiselect
                  v-model="filtrosDetalles.municipiosSeleccionados"
                  :options="municipiosDisponiblesDetalles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreMunicipio"
                  track-by="IDMunicipio"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Predio:</label>
                <vue-multiselect
                  v-model="filtrosDetalles.prediosSeleccionados"
                  :options="prediosDisponiblesDetalles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombrePredio"
                  track-by="IDPredio"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Edificio:</label>
                <vue-multiselect
                  v-model="filtrosDetalles.edificiosSeleccionados"
                  :options="edificiosDisponiblesDetalles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreEdificio"
                  track-by="IDEdificio"
                ></vue-multiselect>
              </div>

              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Nivel:</label>
                <vue-multiselect
                  v-model="filtrosDetalles.nivelesSeleccionados"
                  :options="nivelesDisponiblesDetalles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreNivel"
                  track-by="IDNivel"
                ></vue-multiselect>
              </div>

              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Zona:</label>
                <vue-multiselect
                  v-model="filtrosDetalles.zonasSeleccionadas"
                  :options="zonasDisponiblesDetalles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreZona"
                  track-by="IDZona"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Categoría (Grupo):</label>
                <vue-multiselect
                  v-model="filtrosDetalles.gruposSeleccionados"
                  :options="gruposDisponiblesDetalles"
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
                  v-model="filtrosDetalles.categoriasSeleccionadas"
                  :options="categoriasDisponiblesDetalles"
                  :multiple="true"
                  :close-on-select="false"
                  :disabled="filtrosDetalles.gruposSeleccionados.length === 0"
                  placeholder="Seleccione categoría"
                  label="NombreCategoriaDoc"
                  track-by="IDCategoriaDoc"
                ></vue-multiselect>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Tipo de Documento:</label>
                <vue-multiselect
                  v-model="filtrosDetalles.tiposDocumentoSeleccionados"
                  :options="tiposDocumentoDisponiblesDetalles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreTipoDocumento"
                  track-by="IDTipoDocumento"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Tipo de Inmueble:</label>
                <vue-multiselect
                  v-model="filtrosDetalles.tiposInmuebleSeleccionados"
                  :options="tiposInmuebleDisponiblesDetalles"
                  :multiple="true"
                  :close-on-select="false"
                  placeholder="Todos"
                  label="NombreTipoInmueble"
                  track-by="IDTipoInmueble"
                ></vue-multiselect>
              </div>
              <div class="col-md-3 mt-3">
                <label class="form-label fw-bold">Archivos:</label>
                <select
                  v-model="filtrosDetalles.estadoArchivo"
                  class="form-select"
                >
                  <option
                    v-for="opcion in opcionesArchivo"
                    :key="opcion.value"
                    :value="opcion.value"
                  >
                    {{ opcion.text }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">Tabla Detallada de Documentos</div>
          <div class="card-body">
            <VigenciaTablaDetallada
              :id-edificios="idEdificiosSeleccionadosDetalles"
              :id-niveles="idNivelesSeleccionadosDetalles"
              :id-zonas="idZonasSeleccionadosDetalles"
              :id-predios="idPrediosSeleccionadosDetalles"
              :id-grupos="idGruposSeleccionadosDetalles"
              :id-categorias="idCategoriasSeleccionadasDetalles"
              :id-tipos-documento="idTiposDocumentoSeleccionadosDetalles"
              :id-tipos-inmueble="idTiposInmuebleSeleccionadosDetalles"
              :estado-archivo="estadoArchivoDetalles"
              :id-paises="idPaisSeleccionadoDetalles"
              :id-estados="idEstadoSeleccionadoDetalles"
              :id-municipios="idMunicipioSeleccionadoDetalles"
              api-url="/api/bi/tabla-detallada-vigencia"
            />
          </div>
        </div>

        <div class="card">
          <div class="card-header">Tabla Detallada de Documentos por Zona</div>
          <div class="card-body">
            <VigenciaTablaDetallada
              :id-edificios="idEdificiosSeleccionadosDetalles"
              :id-niveles="idNivelesSeleccionadosDetalles"
              :id-zonas="idZonasSeleccionadosDetalles"
              :id-predios="idPrediosSeleccionadosDetalles"
              :id-grupos="idGruposSeleccionadosDetalles"
              :id-categorias="idCategoriasSeleccionadasDetalles"
              :id-tipos-documento="idTiposDocumentoSeleccionadosDetalles"
              :id-tipos-inmueble="idTiposInmuebleSeleccionadosDetalles"
              :estado-archivo="estadoArchivoDetalles"
              :id-paises="idPaisSeleccionadoDetalles"
              :id-estados="idEstadoSeleccionadoDetalles"
              :id-municipios="idMunicipioSeleccionadoDetalles"
              api-url="/api/bi/tabla-detallada-vigencia-zona"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import VueMultiselect from "vue-multiselect";
// CUMPLIMIENTO
import BaseHeatmapMatrix from "./Cumplimiento/BaseHeatmapMatrix.vue";
import BaseHeatmapMatrixArchivos from "./Cumplimiento/BaseHeatmapMatrixArchivos.vue";
import HorizontalPonderadoPorPredio from "./Cumplimiento/HorizontalPonderadoPorPredio.vue";
import MatrizPorGrupo from "./Cumplimiento/MatrizPorGrupo.vue";
import MatrizArchivosPorGrupo from "./Cumplimiento/MatrizArchivosPorGrupo.vue";
import GraficaGlobalConArchivo from "./Cumplimiento/GraficaGlobalConArchivo.vue";
import GraficaGlobalCreados from "./Cumplimiento/GraficaGlobalCreados.vue";

// VIGENCIA Y DETALLES
import VigenciaMatrizEstadoCategoria from "./Vigencia/VigenciaMatrizEstadoCategoria.vue";
import VigenciaMatrizEstadoSubcategoria from "./Vigencia/VigenciaMatrizEstadoSubcategoria.vue";
import VigenciaTablaDetallada from "./Vigencia/VigenciaTablaDetallada.vue";
import HorizontalVigenciaPorPredio from "./Vigencia/HorizontalVigenciaPorPredio.vue";
import HorizontalVigenciaPorZona from "./Vigencia/HorizontalVigenciaPorZona.vue";

export default {
  components: {
    VueMultiselect,
    // CUMPLIMIENTO
    BaseHeatmapMatrix,
    BaseHeatmapMatrixArchivos,
    HorizontalPonderadoPorPredio,
    MatrizPorGrupo,
    MatrizArchivosPorGrupo,
    GraficaGlobalConArchivo,
    GraficaGlobalCreados,

    // VIGENCIA Y DETALLES
    VigenciaMatrizEstadoCategoria,
    VigenciaMatrizEstadoSubcategoria,
    VigenciaTablaDetallada,
    HorizontalVigenciaPorPredio,
    HorizontalVigenciaPorZona,
  },
  data() {
    return {
      isReady: false,
      activeTab: "Cumplimiento",
      opcionesArchivo: [
        { value: "todos", text: "Todos" },
        { value: "con", text: "Con Archivo" },
        { value: "sin", text: "Sin Archivo" },
      ],

      // --- Listas Maestras (se cargan una vez y no cambian) ---
      allPaises: [],
      allEstados: [],
      allMunicipios: [],
      allPredios: [],
      allEdificios: [],
      allNiveles: [],
      allZonas: [],
      allGrupos: [],
      allTiposDocumento: [],
      allTiposInmueble: [],

      // --- Opciones Disponibles por Pestaña ---
      // Cumplimiento
      paisesDisponiblesCumplimiento: [],
      estadosDisponiblesCumplimiento: [],
      municipiosDisponiblesCumplimiento: [],
      prediosDisponiblesCumplimiento: [],
      edificiosDisponiblesCumplimiento: [],
      nivelesDisponiblesCumplimiento: [],
      zonasDisponiblesCumplimiento: [],
      gruposDisponiblesCumplimiento: [],
      categoriasDisponiblesCumplimiento: [],
      tiposDocumentoDisponiblesCumplimiento: [],
      tiposInmuebleDisponiblesCumplimiento: [],

      // Vigencia
      paisesDisponiblesVigencia: [],
      estadosDisponiblesVigencia: [],
      municipiosDisponiblesVigencia: [],
      prediosDisponiblesVigencia: [],
      edificiosDisponiblesVigencia: [],
      nivelesDisponiblesVigencia: [],
      zonasDisponiblesVigencia: [],
      gruposDisponiblesVigencia: [],
      categoriasDisponiblesVigencia: [],
      tiposDocumentoDisponiblesVigencia: [],
      tiposInmuebleDisponiblesVigencia: [],

      // Detalles
      paisesDisponiblesDetalles: [],
      estadosDisponiblesDetalles: [],
      municipiosDisponiblesDetalles: [],
      prediosDisponiblesDetalles: [],
      edificiosDisponiblesDetalles: [],
      nivelesDisponiblesDetalles: [],
      zonasDisponiblesDetalles: [],
      gruposDisponiblesDetalles: [],
      categoriasDisponiblesDetalles: [],
      tiposDocumentoDisponiblesDetalles: [],
      tiposInmuebleDisponiblesDetalles: [],

      // --- Estado de Filtros para Cumplimiento ---
      filtrosCumplimiento: {
        paisesSeleccionados: [],
        estadosSeleccionados: [],
        municipiosSeleccionados: [],
        prediosSeleccionados: [],
        edificiosSeleccionados: [],
        nivelesSeleccionados: [],
        zonasSeleccionadas: [],
        gruposSeleccionados: [],
        categoriasSeleccionadas: [],
        tiposDocumentoSeleccionados: [],
        tiposInmuebleSeleccionados: [],
        estadoArchivo: "todos",
      },

      // Estado de los filtros para la pestaña VIGENCIA
      filtrosVigencia: {
        paisesSeleccionados: [],
        estadosSeleccionados: [],
        municipiosSeleccionados: [],
        prediosSeleccionados: [],
        gruposSeleccionados: [],
        categoriasSeleccionadas: [],
        tiposDocumentoSeleccionados: [],
        tiposInmuebleSeleccionados: [],
        edificiosSeleccionados: [],
        nivelesSeleccionados: [],
        zonasSeleccionadas: [],
        estadoArchivo: "todos",
      },

      // Estado de los filtros para la pestaña DETALLES
      filtrosDetalles: {
        paisesSeleccionados: [],
        estadosSeleccionados: [],
        municipiosSeleccionados: [],
        prediosSeleccionados: [],
        edificiosSeleccionados: [],
        nivelesSeleccionados: [],
        zonasSeleccionadas: [],
        gruposSeleccionados: [],
        categoriasSeleccionadas: [],
        tiposDocumentoSeleccionados: [],
        tiposInmuebleSeleccionados: [],
        estadoArchivo: "todos",
      },
    };
  },

  computed: {
    // --- Computeds para Cumplimiento ---
    idPaisSeleccionadoCumplimiento() {
      return this.filtrosCumplimiento.paisesSeleccionados.map((p) => p.IDPais);
    },
    idEstadoSeleccionadoCumplimiento() {
      return this.filtrosCumplimiento.estadosSeleccionados.map(
        (e) => e.IDEstado
      );
    },
    idMunicipioSeleccionadoCumplimiento() {
      return this.filtrosCumplimiento.municipiosSeleccionados.map(
        (m) => m.IDMunicipio
      );
    },
    idPrediosSeleccionadosCumplimiento() {
      return this.filtrosCumplimiento.prediosSeleccionados.map(
        (p) => p.IDPredio
      );
    },
    idEdificiosSeleccionadosCumplimiento() {
      return this.filtrosCumplimiento.edificiosSeleccionados.map(
        (e) => e.IDEdificio
      );
    },
    idNivelesSeleccionadosCumplimiento() {
      return this.filtrosCumplimiento.nivelesSeleccionados.map(
        (n) => n.IDNivel
      );
    },
    idZonasSeleccionadasCumplimiento() {
      return this.filtrosCumplimiento.zonasSeleccionadas.map((z) => z.IDZona);
    },
    idGruposSeleccionadosCumplimiento() {
      return this.filtrosCumplimiento.gruposSeleccionados.map(
        (g) => g.IDGrupoDoc
      );
    },
    idCategoriasSeleccionadasCumplimiento() {
      return this.filtrosCumplimiento.categoriasSeleccionadas.map(
        (c) => c.IDCategoriaDoc
      );
    },
    idTiposDocumentoSeleccionadosCumplimiento() {
      return this.filtrosCumplimiento.tiposDocumentoSeleccionados.map(
        (t) => t.IDTipoDocumento
      );
    },
    idTiposInmuebleSeleccionadosCumplimiento() {
      return this.filtrosCumplimiento.tiposInmuebleSeleccionados.map(
        (t) => t.IDTipoInmueble
      );
    },
    estadoArchivoCumplimiento() {
      return this.filtrosCumplimiento.estadoArchivo;
    },

    // --- Computeds para Vigencia ---
    idPaisSeleccionadoVigencia() {
      return this.filtrosVigencia.paisesSeleccionados.map((p) => p.IDPais);
    },
    idEstadoSeleccionadoVigencia() {
      return this.filtrosVigencia.estadosSeleccionados.map((e) => e.IDEstado);
    },
    idMunicipioSeleccionadoVigencia() {
      return this.filtrosVigencia.municipiosSeleccionados.map(
        (m) => m.IDMunicipio
      );
    },
    idPrediosSeleccionadosVigencia() {
      return this.filtrosVigencia.prediosSeleccionados.map((p) => p.IDPredio);
    },
    idEdificiosSeleccionadosVigencia() {
      return this.filtrosVigencia.edificiosSeleccionados.map(
        (e) => e.IDEdificio
      );
    },
    idNivelesSeleccionadosVigencia() {
      return this.filtrosVigencia.nivelesSeleccionados.map((n) => n.IDNivel);
    },
    idZonasSeleccionadasVigencia() {
      return this.filtrosVigencia.zonasSeleccionadas.map((z) => z.IDZona);
    },
    idGruposSeleccionadosVigencia() {
      return this.filtrosVigencia.gruposSeleccionados.map((g) => g.IDGrupoDoc);
    },
    idCategoriasSeleccionadasVigencia() {
      return this.filtrosVigencia.categoriasSeleccionadas.map(
        (c) => c.IDCategoriaDoc
      );
    },
    idTiposDocumentoSeleccionadosVigencia() {
      return this.filtrosVigencia.tiposDocumentoSeleccionados.map(
        (t) => t.IDTipoDocumento
      );
    },
    idTiposInmuebleSeleccionadosVigencia() {
      return this.filtrosVigencia.tiposInmuebleSeleccionados.map(
        (t) => t.IDTipoInmueble
      );
    },
    estadoArchivoVigencia() {
      return this.filtrosVigencia.estadoArchivo;
    },

    // --- Computeds para Detalles ---
    idPaisSeleccionadoDetalles() {
      return this.filtrosDetalles.paisesSeleccionados.map((p) => p.IDPais);
    },
    idEstadoSeleccionadoDetalles() {
      return this.filtrosDetalles.estadosSeleccionados.map((e) => e.IDEstado);
    },
    idMunicipioSeleccionadoDetalles() {
      return this.filtrosDetalles.municipiosSeleccionados.map(
        (m) => m.IDMunicipio
      );
    },
    idPrediosSeleccionadosDetalles() {
      return this.filtrosDetalles.prediosSeleccionados.map((p) => p.IDPredio);
    },
    idEdificiosSeleccionadosDetalles() {
      return this.filtrosDetalles.edificiosSeleccionados.map(
        (e) => e.IDEdificio
      );
    },
    idNivelesSeleccionadosDetalles() {
      return this.filtrosDetalles.nivelesSeleccionados.map((n) => n.IDNivel);
    },
    idZonasSeleccionadosDetalles() {
      return this.filtrosDetalles.zonasSeleccionadas.map((z) => z.IDZona);
    },
    idGruposSeleccionadosDetalles() {
      return this.filtrosDetalles.gruposSeleccionados.map((g) => g.IDGrupoDoc);
    },
    idCategoriasSeleccionadasDetalles() {
      return this.filtrosDetalles.categoriasSeleccionadas.map(
        (c) => c.IDCategoriaDoc
      );
    },
    idTiposDocumentoSeleccionadosDetalles() {
      return this.filtrosDetalles.tiposDocumentoSeleccionados.map(
        (t) => t.IDTipoDocumento
      );
    },
    idTiposInmuebleSeleccionadosDetalles() {
      return this.filtrosDetalles.tiposInmuebleSeleccionados.map(
        (t) => t.IDTipoInmueble
      );
    },
    estadoArchivoDetalles() {
      return this.filtrosDetalles.estadoArchivo;
    },
  },

  watch: {
    // --- LÓGICA EN CASCADA PARA "CUMPLIMIENTO" ---
    "filtrosCumplimiento.paisesSeleccionados": {
      handler(nuevosPaises) {
        const ids = nuevosPaises.map((p) => p.IDPais);
        console.log(this.allEstados);
        // Si se seleccionan países, filtra los estados. Si no, muestra todos los estados.
        this.estadosDisponiblesCumplimiento =
          ids.length > 0
            ? this.allEstados.filter((e) => ids.includes(e.IDPais))
            : this.allEstados;
        // Limpia selecciones de estado que ya no son válidas.
        this.filtrosCumplimiento.estadosSeleccionados =
          this.filtrosCumplimiento.estadosSeleccionados.filter((e) =>
            this.estadosDisponiblesCumplimiento.some(
              (ed) => ed.IDEstado === e.IDEstado
            )
          );
      },
      deep: true,
    },
    "filtrosCumplimiento.estadosSeleccionados": {
      handler(nuevosEstados) {
        const ids = nuevosEstados.map((e) => e.IDEstado);
        // Si se seleccionan estados, filtra los municipios.
        console.log(this.allMunicipios);

        if (ids.length > 0) {
          this.municipiosDisponiblesCumplimiento = this.allMunicipios.filter(
            (m) => ids.includes(m.IDEstado)
          );
        } else {
          // Si no, los municipios disponibles dependen de los estados disponibles (filtrados por país).
          const idEstadosDisponibles = this.estadosDisponiblesCumplimiento.map(
            (e) => e.IDEstado
          );
          this.municipiosDisponiblesCumplimiento = this.allMunicipios.filter(
            (m) => idEstadosDisponibles.includes(m.IDEstado)
          );
        }
        // Limpia selecciones de municipio que ya no son válidas.
        this.filtrosCumplimiento.municipiosSeleccionados =
          this.filtrosCumplimiento.municipiosSeleccionados.filter((m) =>
            this.municipiosDisponiblesCumplimiento.some(
              (md) => md.IDMunicipio === m.IDMunicipio
            )
          );
      },
      deep: true,
    },
    "filtrosCumplimiento.municipiosSeleccionados": {
      handler(nuevosMunicipios) {
        const ids = nuevosMunicipios.map((m) => m.IDMunicipio);
        if (ids.length > 0) {
          this.prediosDisponiblesCumplimiento = this.allPredios.filter((p) =>
            ids.includes(p.IDMunicipio)
          );
        } else {
          const idMunicipiosDisponibles =
            this.municipiosDisponiblesCumplimiento.map((m) => m.IDMunicipio);
          this.prediosDisponiblesCumplimiento = this.allPredios.filter((p) =>
            idMunicipiosDisponibles.includes(p.IDMunicipio)
          );
        }
        this.filtrosCumplimiento.prediosSeleccionados =
          this.filtrosCumplimiento.prediosSeleccionados.filter((p) =>
            this.prediosDisponiblesCumplimiento.some(
              (pd) => pd.IDPredio === p.IDPredio
            )
          );
      },
      deep: true,
    },
    "filtrosCumplimiento.prediosSeleccionados": {
      handler(nuevosPredios) {
        const ids = nuevosPredios.map((p) => p.IDPredio);
        if (ids.length > 0) {
          this.edificiosDisponiblesCumplimiento = this.allEdificios.filter(
            (e) => ids.includes(e.IDPredio)
          );
        } else {
          const idPrediosDisponibles = this.prediosDisponiblesCumplimiento.map(
            (p) => p.IDPredio
          );
          this.edificiosDisponiblesCumplimiento = this.allEdificios.filter(
            (e) => idPrediosDisponibles.includes(e.IDPredio)
          );
        }
        this.filtrosCumplimiento.edificiosSeleccionados =
          this.filtrosCumplimiento.edificiosSeleccionados.filter((e) =>
            this.edificiosDisponiblesCumplimiento.some(
              (ed) => ed.IDEdificio === e.IDEdificio
            )
          );
      },
      deep: true,
    },
    "filtrosCumplimiento.edificiosSeleccionados": {
      handler(nuevosEdificios) {
        const ids = nuevosEdificios.map((e) => e.IDEdificio);
        if (ids.length > 0) {
          this.nivelesDisponiblesCumplimiento = this.allNiveles.filter((n) =>
            ids.includes(n.IDEdificio)
          );
        } else {
          const idEdificiosDisponibles =
            this.edificiosDisponiblesCumplimiento.map((e) => e.IDEdificio);
          this.nivelesDisponiblesCumplimiento = this.allNiveles.filter((n) =>
            idEdificiosDisponibles.includes(n.IDEdificio)
          );
        }
        this.filtrosCumplimiento.nivelesSeleccionados =
          this.filtrosCumplimiento.nivelesSeleccionados.filter((n) =>
            this.nivelesDisponiblesCumplimiento.some(
              (nd) => nd.IDNivel === n.IDNivel
            )
          );
      },
      deep: true,
    },
    "filtrosCumplimiento.nivelesSeleccionados": {
      handler(nuevosNiveles) {
        const ids = nuevosNiveles.map((n) => n.IDNivel);
        if (ids.length > 0) {
          this.zonasDisponiblesCumplimiento = this.allZonas.filter((z) =>
            ids.includes(z.IDNivel)
          );
        } else {
          const idNivelesDisponibles = this.nivelesDisponiblesCumplimiento.map(
            (n) => n.IDNivel
          );
          this.zonasDisponiblesCumplimiento = this.allZonas.filter((z) =>
            idNivelesDisponibles.includes(z.IDNivel)
          );
        }
        this.filtrosCumplimiento.zonasSeleccionadas =
          this.filtrosCumplimiento.zonasSeleccionadas.filter((z) =>
            this.zonasDisponiblesCumplimiento.some(
              (zd) => zd.IDZona === z.IDZona
            )
          );
      },
      deep: true,
    },
    "filtrosCumplimiento.gruposSeleccionados": {
      handler(newGrupos) {
        this.actualizarSubcategorias(newGrupos, "Cumplimiento");
      },
      deep: true,
    },

    // --- LÓGICA EN CASCADA PARA "VIGENCIA" ---
    "filtrosVigencia.paisesSeleccionados": {
      handler(nuevosPaises) {
        const ids = nuevosPaises.map((p) => p.IDPais);
        this.estadosDisponiblesVigencia =
          ids.length > 0
            ? this.allEstados.filter((e) => ids.includes(e.IDPais))
            : this.allEstados;
        this.filtrosVigencia.estadosSeleccionados =
          this.filtrosVigencia.estadosSeleccionados.filter((e) =>
            this.estadosDisponiblesVigencia.some(
              (ed) => ed.IDEstado === e.IDEstado
            )
          );
      },
      deep: true,
    },
    "filtrosVigencia.estadosSeleccionados": {
      handler(nuevosEstados) {
        const ids = nuevosEstados.map((e) => e.IDEstado);
        if (ids.length > 0) {
          this.municipiosDisponiblesVigencia = this.allMunicipios.filter((m) =>
            ids.includes(m.IDEstado)
          );
        } else {
          const idEstadosDisponibles = this.estadosDisponiblesVigencia.map(
            (e) => e.IDEstado
          );
          this.municipiosDisponiblesVigencia = this.allMunicipios.filter((m) =>
            idEstadosDisponibles.includes(m.IDEstado)
          );
        }
        this.filtrosVigencia.municipiosSeleccionados =
          this.filtrosVigencia.municipiosSeleccionados.filter((m) =>
            this.municipiosDisponiblesVigencia.some(
              (md) => md.IDMunicipio === m.IDMunicipio
            )
          );
      },
      deep: true,
    },
    "filtrosVigencia.municipiosSeleccionados": {
      handler(nuevosMunicipios) {
        const ids = nuevosMunicipios.map((m) => m.IDMunicipio);
        if (ids.length > 0) {
          this.prediosDisponiblesVigencia = this.allPredios.filter((p) =>
            ids.includes(p.IDMunicipio)
          );
        } else {
          const idMunicipiosDisponibles =
            this.municipiosDisponiblesVigencia.map((m) => m.IDMunicipio);
          this.prediosDisponiblesVigencia = this.allPredios.filter((p) =>
            idMunicipiosDisponibles.includes(p.IDMunicipio)
          );
        }
        this.filtrosVigencia.prediosSeleccionados =
          this.filtrosVigencia.prediosSeleccionados.filter((p) =>
            this.prediosDisponiblesVigencia.some(
              (pd) => pd.IDPredio === p.IDPredio
            )
          );
      },
      deep: true,
    },
    "filtrosVigencia.prediosSeleccionados": {
      handler(nuevosPredios) {
        const ids = nuevosPredios.map((p) => p.IDPredio);
        if (ids.length > 0) {
          this.edificiosDisponiblesVigencia = this.allEdificios.filter((e) =>
            ids.includes(e.IDPredio)
          );
        } else {
          const idPrediosDisponibles = this.prediosDisponiblesVigencia.map(
            (p) => p.IDPredio
          );
          this.edificiosDisponiblesVigencia = this.allEdificios.filter((e) =>
            idPrediosDisponibles.includes(e.IDPredio)
          );
        }
        this.filtrosVigencia.edificiosSeleccionados =
          this.filtrosVigencia.edificiosSeleccionados.filter((e) =>
            this.edificiosDisponiblesVigencia.some(
              (ed) => ed.IDEdificio === e.IDEdificio
            )
          );
      },
      deep: true,
    },
    "filtrosVigencia.edificiosSeleccionados": {
      handler(nuevosEdificios) {
        const ids = nuevosEdificios.map((e) => e.IDEdificio);
        if (ids.length > 0) {
          this.nivelesDisponiblesVigencia = this.allNiveles.filter((n) =>
            ids.includes(n.IDEdificio)
          );
        } else {
          const idEdificiosDisponibles = this.edificiosDisponiblesVigencia.map(
            (e) => e.IDEdificio
          );
          this.nivelesDisponiblesVigencia = this.allNiveles.filter((n) =>
            idEdificiosDisponibles.includes(n.IDEdificio)
          );
        }
        this.filtrosVigencia.nivelesSeleccionados =
          this.filtrosVigencia.nivelesSeleccionados.filter((n) =>
            this.nivelesDisponiblesVigencia.some(
              (nd) => nd.IDNivel === n.IDNivel
            )
          );
      },
      deep: true,
    },
    "filtrosVigencia.nivelesSeleccionados": {
      handler(nuevosNiveles) {
        const ids = nuevosNiveles.map((n) => n.IDNivel);
        if (ids.length > 0) {
          this.zonasDisponiblesVigencia = this.allZonas.filter((z) =>
            ids.includes(z.IDNivel)
          );
        } else {
          const idNivelesDisponibles = this.nivelesDisponiblesVigencia.map(
            (n) => n.IDNivel
          );
          this.zonasDisponiblesVigencia = this.allZonas.filter((z) =>
            idNivelesDisponibles.includes(z.IDNivel)
          );
        }
        this.filtrosVigencia.zonasSeleccionadas =
          this.filtrosVigencia.zonasSeleccionadas.filter((z) =>
            this.zonasDisponiblesVigencia.some((zd) => zd.IDZona === z.IDZona)
          );
      },
      deep: true,
    },
    "filtrosVigencia.gruposSeleccionados": {
      handler(newGrupos) {
        this.actualizarSubcategorias(newGrupos, "Vigencia");
      },
      deep: true,
    },

    // --- LÓGICA EN CASCADA PARA "DETALLES" ---
    "filtrosDetalles.paisesSeleccionados": {
      handler(nuevosPaises) {
        const ids = nuevosPaises.map((p) => p.IDPais);
        this.estadosDisponiblesDetalles =
          ids.length > 0
            ? this.allEstados.filter((e) => ids.includes(e.IDPais))
            : this.allEstados;
        this.filtrosDetalles.estadosSeleccionados =
          this.filtrosDetalles.estadosSeleccionados.filter((e) =>
            this.estadosDisponiblesDetalles.some(
              (ed) => ed.IDEstado === e.IDEstado
            )
          );
      },
      deep: true,
    },
    "filtrosDetalles.estadosSeleccionados": {
      handler(nuevosEstados) {
        const ids = nuevosEstados.map((e) => e.IDEstado);
        if (ids.length > 0) {
          this.municipiosDisponiblesDetalles = this.allMunicipios.filter((m) =>
            ids.includes(m.IDEstado)
          );
        } else {
          const idEstadosDisponibles = this.estadosDisponiblesDetalles.map(
            (e) => e.IDEstado
          );
          this.municipiosDisponiblesDetalles = this.allMunicipios.filter((m) =>
            idEstadosDisponibles.includes(m.IDEstado)
          );
        }
        this.filtrosDetalles.municipiosSeleccionados =
          this.filtrosDetalles.municipiosSeleccionados.filter((m) =>
            this.municipiosDisponiblesDetalles.some(
              (md) => md.IDMunicipio === m.IDMunicipio
            )
          );
      },
      deep: true,
    },
    "filtrosDetalles.municipiosSeleccionados": {
      handler(nuevosMunicipios) {
        const ids = nuevosMunicipios.map((m) => m.IDMunicipio);
        if (ids.length > 0) {
          this.prediosDisponiblesDetalles = this.allPredios.filter((p) =>
            ids.includes(p.IDMunicipio)
          );
        } else {
          const idMunicipiosDisponibles =
            this.municipiosDisponiblesDetalles.map((m) => m.IDMunicipio);
          this.prediosDisponiblesDetalles = this.allPredios.filter((p) =>
            idMunicipiosDisponibles.includes(p.IDMunicipio)
          );
        }
        this.filtrosDetalles.prediosSeleccionados =
          this.filtrosDetalles.prediosSeleccionados.filter((p) =>
            this.prediosDisponiblesDetalles.some(
              (pd) => pd.IDPredio === p.IDPredio
            )
          );
      },
      deep: true,
    },
    "filtrosDetalles.prediosSeleccionados": {
      handler(nuevosPredios) {
        const ids = nuevosPredios.map((p) => p.IDPredio);
        if (ids.length > 0) {
          this.edificiosDisponiblesDetalles = this.allEdificios.filter((e) =>
            ids.includes(e.IDPredio)
          );
        } else {
          const idPrediosDisponibles = this.prediosDisponiblesDetalles.map(
            (p) => p.IDPredio
          );
          this.edificiosDisponiblesDetalles = this.allEdificios.filter((e) =>
            idPrediosDisponibles.includes(e.IDPredio)
          );
        }
        this.filtrosDetalles.edificiosSeleccionados =
          this.filtrosDetalles.edificiosSeleccionados.filter((e) =>
            this.edificiosDisponiblesDetalles.some(
              (ed) => ed.IDEdificio === e.IDEdificio
            )
          );
      },
      deep: true,
    },
    "filtrosDetalles.edificiosSeleccionados": {
      handler(nuevosEdificios) {
        const ids = nuevosEdificios.map((e) => e.IDEdificio);
        if (ids.length > 0) {
          this.nivelesDisponiblesDetalles = this.allNiveles.filter((n) =>
            ids.includes(n.IDEdificio)
          );
        } else {
          const idEdificiosDisponibles = this.edificiosDisponiblesDetalles.map(
            (e) => e.IDEdificio
          );
          this.nivelesDisponiblesDetalles = this.allNiveles.filter((n) =>
            idEdificiosDisponibles.includes(n.IDEdificio)
          );
        }
        this.filtrosDetalles.nivelesSeleccionados =
          this.filtrosDetalles.nivelesSeleccionados.filter((n) =>
            this.nivelesDisponiblesDetalles.some(
              (nd) => nd.IDNivel === n.IDNivel
            )
          );
      },
      deep: true,
    },
    "filtrosDetalles.nivelesSeleccionados": {
      handler(nuevosNiveles) {
        const ids = nuevosNiveles.map((n) => n.IDNivel);
        if (ids.length > 0) {
          this.zonasDisponiblesDetalles = this.allZonas.filter((z) =>
            ids.includes(z.IDNivel)
          );
        } else {
          const idNivelesDisponibles = this.nivelesDisponiblesDetalles.map(
            (n) => n.IDNivel
          );
          this.zonasDisponiblesDetalles = this.allZonas.filter((z) =>
            idNivelesDisponibles.includes(z.IDNivel)
          );
        }
        this.filtrosDetalles.zonasSeleccionadas =
          this.filtrosDetalles.zonasSeleccionadas.filter((z) =>
            this.zonasDisponiblesDetalles.some((zd) => zd.IDZona === z.IDZona)
          );
      },
      deep: true,
    },
    "filtrosDetalles.gruposSeleccionados": {
      handler(newGrupos) {
        this.actualizarSubcategorias(newGrupos, "Detalles");
      },
      deep: true,
    },
  },

  methods: {
    async actualizarSubcategorias(newGrupos, tipoFiltro) {
      const newGrupoIds = newGrupos.map((g) => g.IDGrupoDoc);

      let categoriasDisponiblesKey;
      let filtrosKey;

      if (tipoFiltro === "Cumplimiento") {
        categoriasDisponiblesKey = "categoriasDisponiblesCumplimiento";
        filtrosKey = "filtrosCumplimiento";
      } else if (tipoFiltro === "Vigencia") {
        categoriasDisponiblesKey = "categoriasDisponiblesVigencia";
        filtrosKey = "filtrosVigencia";
      } else {
        // Detalles
        categoriasDisponiblesKey = "categoriasDisponiblesDetalles";
        filtrosKey = "filtrosDetalles";
      }

      if (newGrupos && newGrupos.length > 0) {
        try {
          const response = await axios.get("/api/bi/listar-categorias-doc", {
            params: { grupo_ids: newGrupoIds },
          });
          this[categoriasDisponiblesKey] = response.data;
          this[filtrosKey].categoriasSeleccionadas = this[
            filtrosKey
          ].categoriasSeleccionadas.filter((cat) =>
            this[categoriasDisponiblesKey].some(
              (dispo) => dispo.IDCategoriaDoc === cat.IDCategoriaDoc
            )
          );
        } catch (error) {
          console.error(
            `Error al cargar subcategorías para ${tipoFiltro}:`,
            error
          );
        }
      } else {
        this[categoriasDisponiblesKey] = [];
        this[filtrosKey].categoriasSeleccionadas = [];
      }
    },

    exportarDocumentos() {
      console.log(`Iniciando exportación para la pestaña: ${this.activeTab}`);

      let filtrosParaApi = {};

      if (this.activeTab === "Cumplimiento") {
        filtrosParaApi = {
          predio_ids: this.idPrediosSeleccionadosCumplimiento,
          grupo_ids: this.idGruposSeleccionadosCumplimiento,
          categoria_ids: this.idCategoriasSeleccionadasCumplimiento,
          tipo_doc_ids: this.idTiposDocumentoSeleccionadosCumplimiento,
          tipo_inmueble_ids: this.idTiposInmuebleSeleccionadosCumplimiento,
          estado_archivo: this.estadoArchivoCumplimiento,
          id_paises: this.idPaisSeleccionadoCumplimiento,
          id_estados: this.idEstadoSeleccionadoCumplimiento,
          id_municipios: this.idMunicipioSeleccionadoCumplimiento,
          id_edificios: this.idEdificiosSeleccionadosCumplimiento,
          id_niveles: this.idNivelesSeleccionadosCumplimiento,
          id_zonas: this.idZonasSeleccionadasCumplimiento,
        };
      } else if (this.activeTab === "Vigencia") {
        filtrosParaApi = {
          predio_ids: this.idPrediosSeleccionadosVigencia,
          grupo_ids: this.idGruposSeleccionadosVigencia,
          categoria_ids: this.idCategoriasSeleccionadasVigencia,
          tipo_doc_ids: this.idTiposDocumentoSeleccionadosVigencia,
          tipo_inmueble_ids: this.idTiposInmuebleSeleccionadosVigencia,
          estado_archivo: this.estadoArchivoVigencia,
          id_paises: this.idPaisSeleccionadoVigencia,
          id_estados: this.idEstadoSeleccionadoVigencia,
          id_municipios: this.idMunicipioSeleccionadoVigencia,
          id_edificios: this.idEdificiosSeleccionadosVigencia,
          id_niveles: this.idNivelesSeleccionadosVigencia,
          id_zonas: this.idZonasSeleccionadasVigencia,
        };
      } else if (this.activeTab === "Detalles") {
        filtrosParaApi = {
          predio_ids: this.idPrediosSeleccionadosDetalles,
          grupo_ids: this.idGruposSeleccionadosDetalles,
          categoria_ids: this.idCategoriasSeleccionadasDetalles,
          tipo_doc_ids: this.idTiposDocumentoSeleccionadosDetalles,
          tipo_inmueble_ids: this.idTiposInmuebleSeleccionadosDetalles,
          estado_archivo: this.estadoArchivoDetalles,
          id_paises: this.idPaisSeleccionadoDetalles,
          id_estados: this.idEstadoSeleccionadoDetalles,
          id_municipios: this.idMunicipioSeleccionadoDetalles,
          id_edificios: this.idEdificiosSeleccionadosDetalles,
          id_niveles: this.idNivelesSeleccionadosDetalles,
          id_zonas: this.idZonasSeleccionadosDetalles,
        };
      }

      const params = new URLSearchParams();
      for (const key in filtrosParaApi) {
        const value = filtrosParaApi[key];
        if (value && Array.isArray(value)) {
          if (value.length > 0) {
            value.forEach((item) => params.append(`${key}[]`, item));
          }
        } else if (value) {
          params.append(key, value);
        }
      }

      const queryString = params.toString();
      const url = `/api/reportes/documentos/exportar?${queryString}`;

      console.log("URL de exportación generada:", url);
      window.location.href = url;
    },
  },

  async mounted() {
    try {
      const [
        paisesRes,
        estadosRes,
        municipiosRes,
        prediosRes,
        gruposRes,
        tiposDocRes,
        tiposInmuebleRes,
        edificiosRes,
        nivelesRes,
        zonasRes,
      ] = await Promise.all([
        axios.get("/api/bi/listar-paises"),
        axios.get("/api/bi/listar-estados"),
        axios.get("/api/bi/listar-municipios"),
        axios.get("/api/bi/listar-predios"),
        axios.get("/api/bi/listar-grupos-doc"),
        axios.get("/api/bi/listar-tipos-documento"),
        axios.get("/api/bi/listar-tipos-inmueble"),
        axios.get("/api/bi/listar-edificios"),
        axios.get("/api/bi/listar-niveles"),
        axios.get("/api/bi/listar-zonas"),
      ]);

      // Guardamos las copias maestras
      this.allPaises = paisesRes.data;
      this.allEstados = estadosRes.data;
      this.allMunicipios = municipiosRes.data;
      this.allPredios = prediosRes.data;
      this.allEdificios = edificiosRes.data;
      this.allNiveles = nivelesRes.data;
      this.allZonas = zonasRes.data;
      this.allGrupos = gruposRes.data;
      this.allTiposDocumento = tiposDocRes.data;
      this.allTiposInmueble = tiposInmuebleRes.data;

      // Asignamos las listas de opciones iniciales a TODAS las pestañas
      const tabs = ["Cumplimiento", "Vigencia", "Detalles"];
      tabs.forEach((tab) => {
        this[`paisesDisponibles${tab}`] = this.allPaises;
        this[`estadosDisponibles${tab}`] = this.allEstados;
        this[`municipiosDisponibles${tab}`] = this.allMunicipios;
        this[`prediosDisponibles${tab}`] = this.allPredios;
        this[`edificiosDisponibles${tab}`] = this.allEdificios;
        this[`nivelesDisponibles${tab}`] = this.allNiveles;
        this[`zonasDisponibles${tab}`] = this.allZonas;
        this[`gruposDisponibles${tab}`] = this.allGrupos;
        this[`tiposDocumentoDisponibles${tab}`] = this.allTiposDocumento;
        this[`tiposInmuebleDisponibles${tab}`] = this.allTiposInmueble;
      });
    } catch (error) {
      console.error("No se pudo cargar los filtros:", error);
    } finally {
      this.isReady = true;
    }
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style scoped>
.nav-tabs .nav-link {
  background-color: transparent;
  border-width: 0 0 2px 0;
  border-color: transparent;
  color: #6c757d;
}
.nav-tabs .nav-link.active {
  border-color: #0d6efd;
  color: #0d6efd;
  font-weight: bold;
}
</style>