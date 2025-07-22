<template>
    <div>
        <div v-if="isLoading" class="text-center text-secondary py-4">
            Cargando datos de la tabla...
        </div>
        <div v-else-if="rows.length === 0" class="text-center text-secondary py-4">
            No hay datos para la selección actual.
        </div>
        <div v-else class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Predio</th>
                        <th scope="col">Equipo</th>
                        <th scope="col">ID Acción</th>
                        <th scope="col">Tiene OT</th>
                        <th scope="col">Fecha Inicio</th>
                        <th scope="col">Fecha Fin</th>
                        <th scope="col">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in sortedRows" :key="index">
                        <td>{{ row.y }}</td>
                        <td>{{ row.x }}</td>
                        <td>{{ row.id_accion }}</td>
                        <td>{{ row.tiene_ot === 1 ? 'Sí' : 'No' }}</td>
                        <td>{{ row.fecha_inicio || 'N/D' }}</td>
                        <td>{{ row.fecha_fin || 'N/D' }}</td>
                        <td>
                            <span :class="getStatusClass(row.estado)">
                                ⚫ {{ row.estado }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'TablaMantenimiento',
     props: {
        apiUrl: { type: String, required: true },
        idPredios: { type: Array, required: true },
        idSistemas: { type: Array, required: true },    // <-- Cambiado a Array
        idSubsistemas: { type: Array, required: true }, // <-- Cambiado a Array
        fechaInicio: { type: String, default: null },
        fechaFin: { type: String, default: null }
    },
    data() {
        return {
            rows: [],
            isLoading: true,
        };
    },
    computed: {
         filtros() {
            return {
                predios: this.idPredios,
                sistemas: this.idSistemas,         // <-- Nombre de prop cambiado
                subsistemas: this.idSubsistemas,   // <-- Nombre de prop cambiado
                inicio: this.fechaInicio,
                fin: this.fechaFin
            };
        },
        sortedRows() {
            return [...this.rows].sort((a, b) => {
                if (a.y < b.y) return -1;
                if (a.y > b.y) return 1;
                if (a.x < b.x) return -1;
                if (a.x > b.x) return 1;
                return 0;
            });
        }
    },
    watch: {
        filtros: {
            handler() { this.fetchData(); },
            deep: true,
            immediate: true
        }
    },
    methods: {
        async fetchData() {
            this.isLoading = true;
            try {
                const response = await axios.get(this.apiUrl, {
                    params: {
                        predio_ids: this.idPredios,
                        sistema_ids: this.idSistemas,       // <-- Nombre de param cambiado
                        subsistema_ids: this.idSubsistemas, // <-- Nombre de param cambiado
                        fecha_inicio: this.fechaInicio,
                        fecha_fin: this.fechaFin
                    }
                });
                this.rows = response.data;
            } catch (error) {
                console.error('Error al cargar datos para la tabla:', error);
            } finally {
                this.isLoading = false;
            }
        },
        getStatusClass(estado) {
            switch (estado) {
                case 'Ejecutado': return 'text-success fw-bold';
                case 'En Proceso': return 'text-primary fw-bold';
                case 'Pendiente': return 'text-warning fw-bold';
                case 'Sin estado': return 'text-danger fw-bold';
                default: return 'text-muted';
            }
        }
    }
};
</script>