<template>
  <div>
    <div v-if="isLoading" class="text-center text-secondary py-4">
      Cargando datos...
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
              <th scope="col">Nombre Documento</th>
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
              <td>{{ row.nombre_documento }}</td>
              <td>{{ row.nombre }}</td>
              <td>{{ row.tipo_ot }}</td>
              <td>{{ row.nombre_accion }}</td>
              <td>{{ row.numero_ot }}</td>
              <td>{{ row.fecha_inicio || "N/D" }}</td>
              <td>{{ row.fecha_fin || "N/D" }}</td>
              <td>
                <span :class="getStatusClass(row.estado)"
                  ><span
                    :style="getCircleStyle(row.estado)"
                    class="status-circle"
                  ></span
                  >{{ row.estado }}</span
                >
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-between align-items-center mt-3">
        <span class="text-muted small"
          >Mostrando {{ (currentPage - 1) * itemsPerPage + 1 }} a
          {{ Math.min(currentPage * itemsPerPage, sortedRows.length) }} de
          {{ sortedRows.length }} registros</span
        >
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
  name: "TablaRenovacionOrdenTrabajo",
  props: {
    apiUrl: { type: String, required: true },
    idPredios: { type: Array, required: true },
    idEdificios: { type: Array, required: true },
    idNiveles: { type: Array, required: true },
    idZonas: { type: Array, required: true },
    idGrupos: { type: Array, required: true },
    idCategorias: { type: Array, required: true },
    idPlanes: { type: Array, required: true },
    fechaInicio: { type: String, default: null },
    fechaFin: { type: String, default: null },
  },
  data() {
    return { rows: [], isLoading: true, currentPage: 1, itemsPerPage: 10 };
  },
  computed: {
    filtros() {
      return {
        predios: this.idPredios,
        edificios: this.idEdificios,
        niveles: this.idNiveles,
        zonas: this.idZonas,
        grupos: this.idGrupos,
        categorias: this.idCategorias,
        planes: this.idPlanes,
        inicio: this.fechaInicio,
        fin: this.fechaFin,
      };
    },

    // --- INICIO DE LA CORRECCIÓN ---
    sortedRows() {
      return [...this.rows].sort((a, b) => {
        // Asignar un string vacío si el valor es nulo o indefinido
        const predioA = a.predio || "";
        const predioB = b.predio || "";
        const nombreA = a.nombre || "";
        const nombreB = b.nombre || "";

        // Comparar primero por predio
        const predioCompare = predioA.localeCompare(predioB);
        if (predioCompare !== 0) {
          return predioCompare;
        }

        // Si los predios son iguales, comparar por nombre
        return nombreA.localeCompare(nombreB);
      });
    },
    // --- FIN DE LA CORRECCIÓN ---

    totalPages() {
      return Math.ceil(this.sortedRows.length / this.itemsPerPage);
    },
    paginatedRows() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      return this.sortedRows.slice(start, start + this.itemsPerPage);
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
            grupo_ids: this.idGrupos,
            categoria_ids: this.idCategorias,
            plan_ids: this.idPlanes,
            fecha_inicio: this.fechaInicio,
            fecha_fin: this.fechaFin,
          },
        });
        this.rows = response.data;
        this.currentPage = 1;
      } catch (error) {
        console.error("Error al cargar datos de OTs:", error);
        this.rows = [];
      } finally {
        this.isLoading = false;
      }
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },
    getStatusClass(estado) {
      const classes = {
        Vigente: "text-success fw-bold",
        Ejecutado: "text-primary fw-bold",
        "En Proceso": "text-info fw-bold",
        Pendiente: "text-warning fw-bold",
        Vencido: "text-danger fw-bold",
        "Sin estado": "text-black fw-bold",
        "Sin estado OT": "text-black fw-bold",
      };
      return classes[estado] || "text-muted";
    },
    getCircleStyle(estado) {
      const colors = {
        Vigente: "#198754",
        Ejecutado: "#0d6efd",
        "En Proceso": "#0dcaf0",
        Pendiente: "#ffc107",
        Vencido: "#dc3545",
        "Sin estado": "#000000",
        "Sin estado OT": "#000000",
      };
      return { backgroundColor: colors[estado] || "#9ca3af" };
    },
  },
};
</script>
<style scoped>
.status-circle {
  display: inline-block;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin-right: 8px;
}
</style>