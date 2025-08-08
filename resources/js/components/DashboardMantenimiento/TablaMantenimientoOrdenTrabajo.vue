<template>
  <div>
    <div v-if="isLoading" class="text-center text-secondary py-4">
      Cargando datos de la tabla...
    </div>
    <div v-else-if="rows.length === 0" class="text-center text-secondary py-4">
      No hay datos para la selección actual.
    </div>

    <div v-else>
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th scope="col">Predio</th>
              <th scope="col">Nombre OT</th>
              <th scope="col">Tipo OT</th>
              <th scope="col">Nombre Acción</th>
              <th scope="col">Número OT</th>
              <th scope="col">Fecha Inicio</th>
              <th scope="col">Fecha Fin</th>
              <th scope="col">Estado</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, index) in paginatedRows" :key="index">
              <td>{{ row.predio }}</td>
              <td>{{ row.nombre }}</td>
              <td>{{ row.tipo_ot }}</td>
              <td>{{ row.nombre_accion }}</td>
              <td>{{ row.numero_ot}}</td>
              <td>{{ row.fecha_inicio || "N/D" }}</td>
              <td>{{ row.fecha_fin || "N/D" }}</td>
              <td>
                <span :class="getStatusClass(row.estado)">
                  <span
                    :style="getCircleStyle(row.estado)"
                    class="status-circle"
                  ></span>
                  {{ row.estado }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <span class="text-muted small">
          Mostrando {{ (currentPage - 1) * itemsPerPage + 1 }} a
          {{ Math.min(currentPage * itemsPerPage, sortedRows.length) }} de
          {{ sortedRows.length }} registros
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
  name: "TablaMantenimientoOrdenTrabajo", // CORREGIDO
  props: {
    apiUrl: { type: String, required: true },
    idPredios: { type: Array, required: true },
    idEdificios: { type: Array, required: true },
    idNiveles: { type: Array, required: true },
    idZonas: { type: Array, required: true },
    idTiposEquipo: { type: Array, required: true },
    idSistemas: { type: Array, required: true },
    idSubsistemas: { type: Array, required: true },
    idPlanes: { type: Array, required: true }, // NUEVO
    fechaInicio: { type: String, default: null },
    fechaFin: { type: String, default: null },
  },
  data() {
    return {
      rows: [], // Almacena TODAS las filas de la API
      isLoading: true,
      // --- DATOS PARA PAGINACIÓN ---
      currentPage: 1, // Página actual
      itemsPerPage: 10, // Registros a mostrar por página
    };
  },
  computed: {
    filtros() {
      return {
        predios: this.idPredios,
        edificios: this.idEdificios,
        niveles: this.idNiveles,
        zonas: this.idZonas,
        tipos_equipo: this.idTiposEquipo,
        sistemas: this.idSistemas,
        subsistemas: this.idSubsistemas,
        planes: this.idPlanes, // NUEVO
        inicio: this.fechaInicio,
        fin: this.fechaFin,
      };
    },
    // Ordena todas las filas una sola vez
    sortedRows() {
      // CORREGIDO: Ordenar por los campos correctos de la tabla
      return [...this.rows].sort((a, b) => {
        if (a.predio < b.predio) return -1;
        if (a.predio > b.predio) return 1;
        if (a.nombre < b.nombre) return -1;
        if (a.nombre > b.nombre) return 1;
        return 0;
      });
    },
    // Calcula el número total de páginas
    totalPages() {
      if (!this.sortedRows || this.sortedRows.length === 0) return 1;
      return Math.ceil(this.sortedRows.length / this.itemsPerPage);
    },
    // Devuelve solo la porción de filas para la página actual
    paginatedRows() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      // Usa la data ya ordenada para paginar
      return this.sortedRows.slice(start, end);
    },
  },
  watch: {
    filtros: {
      handler() {
        this.fetchData();
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    async fetchData() {
      this.isLoading = true;
      try {
        const response = await axios.get(this.apiUrl, {
          params: {
            predio_ids: this.idPredios,
            edificio_ids: this.idEdificios,
            nivel_ids: this.idNiveles,
            zona_ids: this.idZonas,
            tipo_equipo_ids: this.idTiposEquipo,
            sistema_ids: this.idSistemas,
            subsistema_ids: this.idSubsistemas,
            plan_ids: this.idPlanes, // NUEVO
            fecha_inicio: this.fechaInicio,
            fecha_fin: this.fechaFin,
          },
        });
        this.rows = response.data;
        this.currentPage = 1; // Resetear a la página 1 con cada nueva búsqueda
      } catch (error) {
        console.error("Error al cargar datos para la tabla:", error);
        this.rows = []; // Limpiar en caso de error
      } finally {
        this.isLoading = false;
      }
    },
    // Cambia la página actual
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },
    getStatusClass(estado) {
      switch (estado) {
        case "Vigente":
          return "text-success fw-bold";
        case "Ejecutado":
          return "text-primary fw-bold";
        case "En Proceso":
          return "text-info fw-bold";
        case "Pendiente":
          return "text-warning fw-bold";
        case "Vencido":
          return "text-danger fw-bold";
        case "Sin estado":
          return "text-black fw-bold";
        default:
          return "text-muted";
      }
    },

    getCircleStyle(estado) {
      let color = "#9ca3af"; // Color gris por defecto

      if (estado === "Vigente") {
        color = "#198754"; // Verde
      } else if (estado === "Ejecutado") {
        color = "#0d6efd"; // Azul
      } else if (estado === "En Proceso") {
        color = "#0dcaf0"; // Cyan
      } else if (estado === "Pendiente") {
        color = "#ffc107"; // Amarillo
      } else if (estado === "Vencido") {
        color = "#dc3545"; // rojo
      } else if (estado === "Sin estado") {
        color = "#000000"; // Rojo
      }

      // La función devuelve un objeto de estilo que Vue aplicará
      return {
        display: "inline-block",
        width: "12px",
        height: "12px",
        borderRadius: "50%",
        backgroundColor: color, // ¡Aquí se asigna el color dinámicamente!
        marginRight: "8px", // Espacio entre el círculo y el texto
      };
    },
  },
};
</script>