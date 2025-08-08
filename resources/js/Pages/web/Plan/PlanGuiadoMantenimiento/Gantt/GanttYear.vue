<template>
  <div class="white-box mt-2 mb-5 border flex-fill d-flex flex-column">
    <div class="table-grid-wrap-grid flex-shrink-0">
      <div class="border-end">
        <div style="width: 240px">
          <div
            class="py-4 px-3 d-flex align-items-center border-bottom"
            style="min-height: 145px"
          >
            <h6 class="fw-bold primary">Carta Gantt</h6>
          </div>
        </div>
      </div>
      <div>
        <div class="px-3 py-3">
          <div class="row align-items-center g-2">
           
            <div class="col-auto">
              <div class="row g-2 align-items-center">
                <div class="col-auto">
                  <select class="form-control">
                    <option>Usuario</option>
                  </select>
                </div>
                <div class="col-auto">
                  <select class="form-control" v-model="selectedYear">
                    <option
                      v-for="yearOption in yearRange"
                      :key="yearOption"
                      :value="yearOption"
                    >
                      {{ yearOption }}
                    </option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-auto">
              <h5 class="fw-bold mb-0 d-inline-block me-2">
                {{ selectedYear }}
              </h5>
              <button
                @click="previousSemester"
                :disabled="!canGoPrevious"
                class="btn btn-sm btn-outline-secondary"
              >
                &lt;
              </button>
              <button
                @click="nextSemester"
                :disabled="!canGoNext"
                class="btn btn-sm btn-outline-secondary"
              >
                &gt;
              </button>
            </div>
          </div>
        </div>
        <div class="table-grid-row-date border-top">
          <div
            v-for="month in visibleMonths"
            :key="month.value"
            class="bg-light-grey-2 p-1"
          >
            <p class="text-uppercase fs-xs fw-medium text-center">
              {{ month.name }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="gantt-body-scroll">
      <div class="gantt-body-grid">
        <template v-for="equipo in packedEquipos" :key="equipo.IDEquipo">
          <div class="gantt-left-cell">
            <p class="fw-semibold fs-sm primary">
              {{ equipo.NombreEquipo || "Equipo sin nombre" }}
            </p>
            <p class="fs-sm text-color-4">
              {{ equipo.tipo?.NombreTipoEquipo || "Tipo de equipo sin nombre" }}
            </p>
            <p class="fs-sm text-color-4">
              {{ equipo.ubicacion || "Equipo sin ubicación" }}
            </p>
          </div>

          <div class="gantt-right-cell">
            <div
              v-for="(rowOfActions, rowIndex) in equipo.actionRows"
              :key="rowIndex"
              class="table-grid-row"
            >
              <div
                v-for="accion in rowOfActions"
                :key="accion.IDAccionesMantenimiento"
                class="table-grid-item-box"
                :style="getTaskStyleForSemester(accion)"
              >
                <p v-if="accion?.orden?.persona">
                  <img
                    :src="accion.orden.persona.logo || '/images/user_icon.jpg'"
                    class="user-image mx-2"
                  />
                </p>
                <p
                  class="fw-semibold fs-sm primary task-title"
                  :title="
                    accion.acciones_definiciones?.NombreDefinicionAccion ||
                    'Acción sin nombre'
                  "
                >
                  {{
                    accion.acciones_definiciones?.NombreDefinicionAccion ||
                    "Acción sin nombre"
                  }}
                </p>
                <p
                  class="fw-semibold fs-sm text-info task-title"
                  :title="
                    accion?.instancia?.estado_actual?.NombreEstado ||
                    'Acción sin estado'
                  "
                >
                  {{
                    accion?.instancia?.estado_actual?.NombreEstado ||
                    "Acción sin estado"
                  }}
                </p>
                <p class="fs-sm text-color-4">
                  {{ accion.orden?.persona?.NombrePersona }}
                  {{ accion.orden?.persona?.ApellidoPaternoPersona }}
                </p>
                <p
                  class="fs-sm text-color-4"
                  :title="accion.orden?.NumOT ? 'Nº ' + accion.orden.NumOT : ''"
                >
                  {{ accion.orden?.NumOT ? "Nº " + accion.orden.NumOT : "" }}
                  <span
                    v-if="accion?.orden?.instancia?.estado_actual?.NombreEstado"
                    class="fw-bold text-color-4"
                    :title="accion.orden.instancia.estado_actual.NombreEstado"
                  >
                    - {{ accion.orden.instancia.estado_actual.NombreEstado }}
                  </span>
                </p>
              </div>
              <div
                v-for="month in visibleMonths"
                :key="`cell-${rowIndex}-${month.value}`"
                class="table-grid-col"
              ></div>
            </div>
            <div
              v-if="!equipo.actionRows || equipo.actionRows.length === 0"
              class="table-grid-row-empty"
            >
              <div
                v-for="month in visibleMonths"
                :key="`empty-cell-${month.value}`"
                class="table-grid-col"
              ></div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { usestoreGanttStore } from "./js/storeGantt";

const props = defineProps(["id"]);
const store = usestoreGanttStore();

const currentDate = ref(new Date());
const semesterPage = ref(0);

const selectedYear = computed({
  get: () => store.ganttCurrentDate.getFullYear(),
  set: (newYear) => {
    const newDate = new Date(store.ganttCurrentDate);
    newDate.setFullYear(newYear);
    store.ganttCurrentDate = newDate;
    semesterPage.value = 0;
  },
});

const yearRange = computed(() => {
  const currentYear = new Date().getFullYear();
  const range = 5;
  const years = [];
  for (let i = currentYear - range; i <= currentYear + range; i++) {
    years.push(i);
  }
  return years;
});

const yearMonths = [
  { name: "ENE", value: 0 },
  { name: "FEB", value: 1 },
  { name: "MAR", value: 2 },
  { name: "ABR", value: 3 },
  { name: "MAY", value: 4 },
  { name: "JUN", value: 5 },
  { name: "JUL", value: 6 },
  { name: "AGO", value: 7 },
  { name: "SEP", value: 8 },
  { name: "OCT", value: 9 },
  { name: "NOV", value: 10 },
  { name: "DIC", value: 11 },
];

const visibleMonths = computed(() => {
  const start = semesterPage.value * 6;
  const end = start + 6;
  return yearMonths.slice(start, end);
});

const previousSemester = () => {
  if (semesterPage.value > 0) semesterPage.value--;
};
const nextSemester = () => {
  if (semesterPage.value < 1) semesterPage.value++;
};
const canGoPrevious = computed(() => semesterPage.value > 0);
const canGoNext = computed(() => semesterPage.value < 1);

const isTaskOverlappingSemester = (accion) => {
  if (
    !visibleMonths.value ||
    visibleMonths.value.length === 0 ||
    !accion.FechaInicioAccion ||
    !accion.FechaFinAccion
  )
    return false;
  const year = selectedYear.value;
  const semesterStartMonth = visibleMonths.value[0].value;
  const semesterEndMonth = visibleMonths.value[5].value;
  const semesterStartDate = new Date(year, semesterStartMonth, 1);
  const semesterEndDate = new Date(year, semesterEndMonth + 1, 0);
  const taskStart = new Date(accion.FechaInicioAccion + "T00:00:00");
  const taskEnd = new Date(accion.FechaFinAccion + "T00:00:00");
  return taskStart <= semesterEndDate && taskEnd >= semesterStartDate;
};

const doActionsOverlapByMonth = (actionA, actionB) => {
  if (!actionA?.FechaInicioAccion || !actionB?.FechaInicioAccion) return false;
  const startA = new Date(actionA.FechaInicioAccion + "T00:00:00");
  const endA = new Date(actionA.FechaFinAccion + "T00:00:00");
  const startB = new Date(actionB.FechaInicioAccion + "T00:00:00");
  const endB = new Date(actionB.FechaFinAccion + "T00:00:00");
  const startAMonthValue = startA.getFullYear() * 12 + startA.getMonth();
  const endAMonthValue = endA.getFullYear() * 12 + endA.getMonth();
  const startBMonthValue = startB.getFullYear() * 12 + startB.getMonth();
  const endBMonthValue = endB.getFullYear() * 12 + endB.getMonth();
  return (
    startAMonthValue <= endBMonthValue && endAMonthValue >= startBMonthValue
  );
};

// --- LÓGICA MODIFICADA ---
const packedEquipos = computed(() => {
  if (!store.ganttData.Equipos) return [];

  // Se procesan TODOS los equipos que llegan del store
  const equiposOriginales = store.ganttData.Equipos.map((equipo) => {
    // Se buscan las acciones que caen en el semestre visible
    const visibleAcciones = equipo.acciones.filter((accion) =>
      isTaskOverlappingSemester(accion)
    );
    return { ...equipo, visibleAcciones };
  });
  // EL FILTRO QUE BORRABA EQUIPOS SIN ACCIONES HA SIDO ELIMINADO

  // Se sigue empaquetando las acciones para los equipos que sí las tengan
  return equiposOriginales.map((equipo) => {
    // Si no hay acciones visibles para este equipo, se devuelve con 'actionRows' vacío
    if (!equipo.visibleAcciones || equipo.visibleAcciones.length === 0) {
      return { ...equipo, actionRows: [] };
    }

    const sortedAcciones = [...equipo.visibleAcciones].sort(
      (a, b) => new Date(a.FechaInicioAccion) - new Date(b.FechaInicioAccion)
    );
    const actionRows = [];
    sortedAcciones.forEach((accion) => {
      let placed = false;
      for (const row of actionRows) {
        const overlaps = row.some((existingAction) =>
          doActionsOverlapByMonth(existingAction, accion)
        );
        if (!overlaps) {
          row.push(accion);
          placed = true;
          break;
        }
      }
      if (!placed) {
        actionRows.push([accion]);
      }
    });
    return { ...equipo, actionRows };
  });
});

const getTaskStyleForSemester = (accion) => {
  if (!accion || !accion.FechaInicioAccion || !accion.FechaFinAccion) return {};
  const year = selectedYear.value;
  const semesterStartMonth = visibleMonths.value[0].value;
  const semesterStartDate = new Date(year, semesterStartMonth, 1);
  const taskStartDate = new Date(accion.FechaInicioAccion + "T00:00:00");
  const taskEndDate = new Date(accion.FechaFinAccion + "T00:00:00");
  const effectiveStartDate =
    taskStartDate < semesterStartDate ? semesterStartDate : taskStartDate;
  const startMonth = effectiveStartDate.getMonth();
  const endMonth = taskEndDate.getMonth();
  const startMonthIndexInSemester = visibleMonths.value.findIndex(
    (m) => m.value === startMonth
  );
  if (startMonthIndexInSemester === -1) return {};
  let durationInMonths = 0;
  for (let i = startMonth; i <= endMonth; i++) {
    if (visibleMonths.value.some((m) => m.value === i)) {
      durationInMonths++;
    }
  }
  const totalColumns = 6;
  const columnWidthPercent = 100 / totalColumns;
  const leftPercent = startMonthIndexInSemester * columnWidthPercent;
  const widthPercent = durationInMonths * columnWidthPercent;
  return {
    left: `calc(${leftPercent}% + 5px)`,
    width: `calc(${widthPercent}% - 10px)`,
  };
};

watch(selectedYear, (newYear) => {
  const newDate = new Date(store.ganttCurrentDate);
  newDate.setFullYear(newYear);
  store.ganttCurrentDate = newDate;
  store.getGanttDataForYear(props.id);
});
</script>

<style scoped>
.table-grid-wrap-grid {
  display: grid;
  grid-template-columns: 240px 1fr;
  width: 100%;
}
.gantt-body-scroll {
  overflow-y: auto;
  max-height: calc(8 * 51px);
}

/* CABECERA DE MESES */
.table-grid-row-date {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  height: 71px;
  border-top: 1px solid #dee2e6;
  border-bottom: 1px solid #dee2e6;
  box-sizing: border-box;
}
.table-grid-row-date > div {
  display: flex;
  align-items: center;
  justify-content: center;
  border-right: 1px solid #e9ecef;
}

/* ===== NUEVA ESTRUCTURA DEL CUERPO DEL GANTT ===== */
.gantt-body-grid {
  display: grid;
  grid-template-columns: 240px 1fr;
}

.gantt-left-cell {
  border-right: 1px solid #dee2e6;
  border-bottom: 1px solid #dee2e6;
  padding: 0.5rem 1rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-height: 51px;
}

.gantt-right-cell {
  border-bottom: 1px solid #dee2e6;
  position: relative;
}

/* FILAS DE ACCIÓN INDIVIDUALES */
.table-grid-row {
  height: auto;
  box-sizing: border-box;
  position: relative;
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  border-top: 1px solid #f0f0f0;
  padding-bottom: 5%;
}
.table-grid-row:first-child {
  border-top: none;
}
.table-grid-row .table-grid-col {
  border-right: 1px solid #e9ecef;
}

/* --- NUEVA CLASE PARA FILAS VACÍAS --- */
.table-grid-row-empty {
  height: 51px;
  display: grid;
  grid-template-columns: repeat(6, 1fr);
}
.table-grid-row-empty .table-grid-col {
  border-right: 1px solid #e9ecef;
}

/* Barra de la tarea */
.table-grid-item-box {
  position: absolute;
  height: auto;
  top: 10px;
  bottom: 10px;
  z-index: 1;
  background-color: white;
  border: none;
  box-shadow: 0 0 0 1px #0f6491;
  border-radius: 4px;
  padding: 8px 12px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

/* Título de la tarea */
.task-title {
  white-space: normal;
  word-break: break-word;
  width: 100%;
}
</style>