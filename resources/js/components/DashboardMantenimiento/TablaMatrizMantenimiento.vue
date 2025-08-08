<template>
  <div>
    <div v-if="isLoading" class="text-center text-secondary py-4">
      Cargando datos de la tabla...
    </div>
    <div
      v-else-if="tableRows.length === 0"
      class="text-center text-secondary py-4"
    >
      No hay datos para la selección actual.
    </div>
    <div v-else class="table-responsive">
      <table class="table table-bordered table-hover" style="min-width: 800px">
        <thead>
          <tr class="table-light">
            <th scope="col">Predio</th>
            <th
              v-for="header in tableHeaders"
              :key="header"
              class="text-center"
            >
              {{ header }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in tableRows" :key="row.predio">
            <th scope="row" class="align-middle">{{ row.predio }}</th>
            <td
              v-for="header in tableHeaders"
              :key="header"
              class="text-center"
            >
              <div v-if="row.statuses[header]">
                <span :class="getStatusClass(row.statuses[header].estado)">
                  {{ row.statuses[header].estado }} - Tiene ot:
                  {{ row.statuses[header].num_ot }}
                </span>
                <br />
                <small class="text-muted">
                  Inicio: {{ row.statuses[header].fecha_inicio || "N/D" }}
                  <br />
                  Fin: {{ row.statuses[header].fecha_fin || "N/D" }}
                </small>
              </div>
              <span v-else class="text-muted"> N/A </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "TablaMatrizMantenimiento",
  props: {
    apiUrl: { type: String, required: true },
    idPredios: { type: Array, required: true },
    idEdificios: { type: Array, required: true },
    idNiveles: { type: Array, required: true },
    idZonas: { type: Array, required: true },
    idTiposEquipo: { type: Array, required: true },
    idSistemas: { type: Array, required: true },
    idSubsistemas: { type: Array, required: true },
    fechaInicio: { type: String, default: null },
    fechaFin: { type: String, default: null },
  },
  data() {
    return {
      rawData: [],
      isLoading: true,
    };
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
  computed: {
    filtros() {
      return {
        predios: this.idPredios,
        edificios: this.idEdificios,
        niveles: this.idNiveles,
        zonas: this.idZonas,
        tipo_equipo_ids: this.idTiposEquipo,
        sistemas: this.idSistemas,
        subsistemas: this.idSubsistemas,
        inicio: this.fechaInicio,
        fin: this.fechaFin,
      };
    },
    tableHeaders() {
      const documentNames = new Set(this.rawData.map((item) => item.x));
      return [...documentNames].sort();
    },
    tableRows() {
      const predios = [...new Set(this.rawData.map((item) => item.y))].sort();

      return predios.map((predio) => {
        const predioData = this.rawData.filter((item) => item.y === predio);
        const statuses = {};

        for (const item of predioData) {
          statuses[item.x] = {
            estado: item.estado,
            tiene_ot: item.tiene_ot,
            num_ot: item.num_ot,
            fecha_inicio: item.fecha_inicio,
            fecha_fin: item.fecha_fin,
          };
        }

        return {
          predio: predio,
          statuses: statuses,
        };
      });
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
            fecha_inicio: this.fechaInicio,
            fecha_fin: this.fechaFin,
          },
        });
        this.rawData = response.data;
      } catch (error) {
        console.error("Error al cargar datos para la tabla-matriz:", error);
      } finally {
        this.isLoading = false;
      }
    },
    getStatusClass(estado) {
      if (!estado) return "text-muted";
      switch (estado) {
        case "Ejecutado":
          return "text-success fw-bold";
        case "En Proceso":
          return "text-primary fw-bold";
        case "Pendiente":
          return "text-warning fw-bold";
        case "Sin estado":
          return "text-danger fw-bold";
        default:
          return "text-muted";
      }
    },
  },
};
</script>