<template>
  <div>
    <div v-if="isLoading" class="text-center text-secondary py-4">
      Cargando datos de la tabla...
    </div>
    <div
      v-else-if="documentos.length === 0"
      class="text-center text-secondary py-4"
    >
      No hay datos para la selección actual.
    </div>

    <!-- CONTENEDOR DE LA TABLA Y PAGINACIÓN -->
    <div v-else>
      <!-- TABLA (Ahora itera sobre 'paginatedDocumentos') -->
      <div class="table-responsive">
        <table class="table table-striped table-hover table-sm">
          <thead class="table-light">
            <tr>
              <th scope="col">Predio</th>
              
              <th scope="col">Categoría</th>
              <th scope="col">Subcategoría</th>
              <th scope="col">Tipo de Documento</th>
              <th scope="col">Estado Documento</th>
              <th scope="col">Archivo Adjunto</th>
            </tr>
          </thead>
          <tbody>
            <!-- Se itera sobre la data ya paginada -->
            <tr
              v-for="(doc, index) in paginatedDocumentos"
              :key="index"
              :class="getRowClass(doc)"
            >
              <td>{{ doc.predio ? doc.predio : doc.zona }}</td>
              <td>{{ doc.categoria }}</td>
              <td>{{ doc.subcategoria }}</td>
              <td>{{ doc.tipo_documento }}</td>
              <td>{{ doc.estado_documento }}</td>
              <td>{{ doc.archivo_adjunto ? "Sí" : "-" }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- NAVEGACIÓN DE PAGINACIÓN -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <span class="text-muted small">
          Mostrando {{ (currentPage - 1) * itemsPerPage + 1 }} a
          {{ Math.min(currentPage * itemsPerPage, documentos.length) }} de
          {{ documentos.length }} registros
        </span>
        <nav>
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <a
                class="page-link"
                href="#"
                @click.prevent="changePage(currentPage - 1)"
                >&laquo; Anterior</a
              >
            </li>
            <li
              class="page-item"
              :class="{ disabled: currentPage === totalPages }"
            >
              <a
                class="page-link"
                href="#"
                @click.prevent="changePage(currentPage + 1)"
                >Siguiente &raquo;</a
              >
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "VigenciaTablaDetallada",
  props: {
    apiUrl: { type: String, required: true },
    idPredios: { type: Array, required: true },
    idGrupos: { type: Array, required: true },
    idCategorias: { type: Array, required: true },
    idTiposDocumento: { type: Array, required: true },
    idTiposInmueble: { type: Array, required: true },
    estadoArchivo: { type: String, required: true },
    idPaises: { type: Array, default: () => [] },
    idEstados: { type: Array, default: () => [] },
    idMunicipios: { type: Array, default: () => [] },
    idEdificios: { type: Array, default: () => [] },
    idNiveles: { type: Array, default: () => [] },
    idZonas: { type: Array, default: () => [] },
  },
  data() {
    return {
      documentos: [], // Almacena TODOS los documentos de la API
      isLoading: true,
      cancelTokenSource: null,
      // --- DATOS PARA PAGINACIÓN ---
      currentPage: 1, // Página actual
      itemsPerPage: 10, // Registros a mostrar por página
    };
  },
  computed: {
    filtros() {
      return {
        predios: this.idPredios,
        grupos: this.idGrupos,
        categorias: this.idCategorias,
        tiposDocumento: this.idTiposDocumento,
        tiposInmueble: this.idTiposInmueble,
      };
    },

    /**
     * Calcula el número total de páginas necesarias.
     */
    totalPages() {
      if (!this.documentos || this.documentos.length === 0) return 1;
      return Math.ceil(this.documentos.length / this.itemsPerPage);
    },

    /**
     * Devuelve solo la porción de documentos que corresponde a la página actual.
     */
    paginatedDocumentos() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.documentos.slice(start, end);
    },
  },
  watch: {
    // Observamos cada prop de filtro de forma individual.
    // Cuando cualquiera de ellas cambie, se volverá a llamar a fetchData.
    idPaises() {
      this.fetchData();
    },
    idEstados() {
      this.fetchData();
    },
    idMunicipios() {
      this.fetchData();
    },
    idEdificios() {
      this.fetchData();
    },
    idNiveles() {
      this.fetchData();
    },
    idZonas() {
      this.fetchData();
    },
    idPredios() {
      this.fetchData();
    },
    idGrupos() {
      this.fetchData();
    },
    idCategorias() {
      this.fetchData();
    },
    idTiposDocumento() {
      this.fetchData();
    },
    idTiposInmueble() {
      this.fetchData();
    },
    estadoArchivo() {
      this.fetchData();
    }, // <-- ESTA ES LA LÍNEA CLAVE A AÑADIR
  },
  methods: {
    async fetchData() {
      this.isLoading = true;
      if (this.cancelTokenSource) {
        this.cancelTokenSource.cancel("Nueva solicitud iniciada.");
      }
      this.cancelTokenSource = axios.CancelToken.source();

      try {
        const response = await axios.get(this.apiUrl, {
          params: {
            predio_ids: this.idPredios,
            grupo_ids: this.idGrupos,
            categoria_ids: this.idCategorias,
            tipo_doc_ids: this.idTiposDocumento,
            tipo_inmueble_ids: this.idTiposInmueble,
            estado_archivo: this.estadoArchivo,
            id_paises: this.idPaises,
            id_estados: this.idEstados,
            id_municipios: this.idMunicipios,
            id_edificios: this.idEdificios,
            id_niveles: this.idNiveles,
            id_zonas: this.idZonas,
          },
          cancelToken: this.cancelTokenSource.token,
        });
        this.documentos = response.data;
        this.currentPage = 1; // Resetear a la página 1 con cada nueva búsqueda
      } catch (error) {
        if (axios.isCancel(error)) {
          console.log("Solicitud cancelada:", error.message);
        } else {
          console.error(
            "Error al cargar datos para la tabla detallada:",
            error
          );
          this.documentos = []; // Limpiar en caso de error
        }
      } finally {
        this.isLoading = false;
      }
    },

    /**
     * Cambia la página actual, asegurándose de no salirse de los límites.
     */
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },

    getRowClass(doc) {
      if (doc.estado_documento === "FALTANTE") {
        return "table-danger";
      }
      if (doc.estado_accion === "Ejecutado") {
        return "table-success";
      }
      if (doc.estado_accion === "Pendiente") {
        return "table-warning";
      }
      if (doc.estado_accion === "En Proceso") {
        return "table-info";
      }
      return "";
    },
  },
  mounted() {
    this.fetchData();
  },
};
</script>
