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
                  <select class="form-control" v-model="selectedMonth">
                    <option
                      v-for="mes in meses"
                      :key="mes.value"
                      :value="mes.value"
                    >
                      {{ mes.name }}
                    </option>
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
                <div class="col-auto">
                  <h5 class="fw-bold mb-0 text-capitalize d-inline-block me-2">
                    {{ monthName }} {{ year }}
                  </h5>
                  <button
                    @click="previousWeek"
                    class="btn btn-sm btn-outline-secondary"
                  >
                    &lt;
                  </button>
                  <button
                    @click="nextWeek"
                    class="btn btn-sm btn-outline-secondary"
                  >
                    &gt;
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="table-grid-row-date border-top">
          <div
            v-for="day in visibleWeek"
            :key="day.fullDate"
            class="bg-light-grey-2 p-1"
          >
            <p class="text-uppercase fs-xs fw-medium text-center">
              {{ day.dayOfWeek }} {{ day.date }}
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
            <p class="fs-sm text-color-4">
              {{
                equipo.subsistema.sistema.NombreSistema || "Sistema sin nombre"
              }}
              <br />
              {{
                equipo.subsistema.NombreSubsistema || "Subsistema sin nombre"
              }}
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
                :style="getTaskStyleForWeek(accion)"
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
                <p class="fs-sm text-color-4">
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
                v-for="day in visibleWeek"
                :key="`cell-${rowIndex}-${day.fullDate}`"
                class="table-grid-col"
                :class="{ 'bg-light-grey-4': rowIndex % 2 !== 0 }"
              ></div>
            </div>
            <div
              v-if="!equipo.actionRows || equipo.actionRows.length === 0"
              class="table-grid-row-empty"
            >
              <div
                v-for="day in visibleWeek"
                :key="`empty-cell-${day.fullDate}`"
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

const weekOffset = ref(0);

const monthName = computed(() =>
  store.ganttCurrentDate.toLocaleString("es-MX", { month: "long" })
);
const year = computed(() => store.ganttCurrentDate.getFullYear());

const daysInMonth = computed(() => {
  const date = store.ganttCurrentDate;
  const year = date.getFullYear();
  const month = date.getMonth();
  const days = new Date(year, month + 1, 0).getDate();
  const daysArray = [];
  for (let i = 1; i <= days; i++) {
    const dayDate = new Date(year, month, i);
    daysArray.push({
      date: i,
      dayOfWeek: dayDate
        .toLocaleString("es-MX", { weekday: "short" })
        .substring(0, 3)
        .toUpperCase(),
      fullDate: dayDate.toISOString().split("T")[0],
    });
  }
  return daysArray;
});

const visibleWeek = computed(() => {
  const start = weekOffset.value * 7;
  const end = start + 7;
  return daysInMonth.value.slice(start, end);
});

const previousWeek = () => {
  if (weekOffset.value > 0) {
    weekOffset.value--;
  } else {
    const newDate = new Date(store.ganttCurrentDate);
    newDate.setMonth(newDate.getMonth() - 1);
    store.ganttCurrentDate = newDate;
  }
};

const nextWeek = () => {
  if ((weekOffset.value + 1) * 7 < daysInMonth.value.length) {
    weekOffset.value++;
  } else {
    const newDate = new Date(store.ganttCurrentDate);
    newDate.setMonth(newDate.getMonth() + 1);
    store.ganttCurrentDate = newDate;
  }
};

const meses = [
  { name: "Enero", value: 0 },
  { name: "Febrero", value: 1 },
  { name: "Marzo", value: 2 },
  { name: "Abril", value: 3 },
  { name: "Mayo", value: 4 },
  { name: "Junio", value: 5 },
  { name: "Julio", value: 6 },
  { name: "Agosto", value: 7 },
  { name: "Septiembre", value: 8 },
  { name: "Octubre", value: 9 },
  { name: "Noviembre", value: 10 },
  { name: "Diciembre", value: 11 },
];

const yearRange = computed(() => {
  const currentYear = new Date().getFullYear();
  const range = 5;
  const years = [];
  for (let i = currentYear - range; i <= currentYear + range; i++) {
    years.push(i);
  }
  return years;
});

const selectedMonth = computed({
  get: () => store.ganttCurrentDate.getMonth(),
  set: (newMonth) => {
    const newDate = new Date(store.ganttCurrentDate);
    newDate.setMonth(newMonth);
    store.ganttCurrentDate = newDate;
  },
});

const selectedYear = computed({
  get: () => store.ganttCurrentDate.getFullYear(),
  set: (newYear) => {
    const newDate = new Date(store.ganttCurrentDate);
    newDate.setFullYear(newYear);
    store.ganttCurrentDate = newDate;
  },
});

const getTaskStyleForWeek = (accion) => {
  if (
    !visibleWeek.value ||
    visibleWeek.value.length === 0 ||
    !accion.FechaInicioAccion ||
    !accion.FechaFinAccion
  )
    return {};
  const weekStartDate = new Date(visibleWeek.value[0].fullDate + "T00:00:00");
  const weekEndDate = new Date(
    visibleWeek.value[visibleWeek.value.length - 1].fullDate + "T00:00:00"
  );
  const taskStartDate = new Date(accion.FechaInicioAccion + "T00:00:00");
  const taskEndDate = new Date(accion.FechaFinAccion + "T00:00:00");
  const effectiveStartDate =
    taskStartDate < weekStartDate ? weekStartDate : taskStartDate;
  const effectiveEndDate =
    taskEndDate > weekEndDate ? weekEndDate : taskEndDate;
  const startDayIndex = visibleWeek.value.findIndex(
    (d) =>
      new Date(d.fullDate + "T00:00:00").getTime() ===
      effectiveStartDate.getTime()
  );
  const endDayIndex = visibleWeek.value.findIndex(
    (d) =>
      new Date(d.fullDate + "T00:00:00").getTime() ===
      effectiveEndDate.getTime()
  );
  if (startDayIndex === -1) return {};
  const duration = endDayIndex - startDayIndex + 1;
  const totalColumns =
    visibleWeek.value.length > 0 ? visibleWeek.value.length : 7;
  const columnWidthPercent = 100 / totalColumns;
  const leftPercent = startDayIndex * columnWidthPercent;
  const widthPercent = duration * columnWidthPercent;
  return { left: `${leftPercent}%`, width: `calc(${widthPercent}% - 10px)` };
};

const isTaskOverlappingWeek = (accion) => {
  if (!accion) return false;
  if (
    !visibleWeek.value ||
    visibleWeek.value.length === 0 ||
    !accion.FechaInicioAccion ||
    !accion.FechaFinAccion
  )
    return false;
  const weekStart = new Date(visibleWeek.value[0].fullDate + "T00:00:00");
  const weekEnd = new Date(
    visibleWeek.value[visibleWeek.value.length - 1].fullDate + "T00:00:00"
  );
  const taskStart = new Date(accion.FechaInicioAccion + "T00:00:00");
  const taskEnd = new Date(accion.FechaFinAccion + "T00:00:00");
  return taskStart <= weekEnd && taskEnd >= weekStart;
};

const doActionsOverlap = (actionA, actionB) => {
  const startA = new Date(actionA.FechaInicioAccion);
  const endA = new Date(actionA.FechaFinAccion);
  const startB = new Date(actionB.FechaInicioAccion);
  const endB = new Date(actionB.FechaFinAccion);
  return startA <= endB && endA >= startB;
};

// --- LÓGICA MODIFICADA ---
const packedEquipos = computed(() => {
  if (!store.ganttData.Equipos) return [];

  // Se procesan TODOS los equipos que llegan del store
  const equiposOriginales = store.ganttData.Equipos.map((equipo) => {
    // Se buscan las acciones que caen en la semana visible
    const visibleAcciones = equipo.acciones.filter((accion) =>
      isTaskOverlappingWeek(accion)
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
          doActionsOverlap(existingAction, accion)
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

watch(
  () => store.ganttCurrentDate,
  (newDate, oldDate) => {
    if (
      newDate.getMonth() !== oldDate.getMonth() ||
      newDate.getFullYear() !== oldDate.getFullYear()
    ) {
      weekOffset.value = 0;
      store.getGanttData(props.id);
    }
  }
);
</script>


<style scoped>
/* ESTRUCTURA PRINCIPAL DEL LAYOUT */
.table-grid-wrap-grid {
  display: grid;
  grid-template-columns: 240px 1fr;
  width: 100%;
}
.gantt-body-scroll {
  overflow-y: auto;
  max-height: calc(8 * 51px);
}

/* CABECERA DE DÍAS */
.table-grid-row-date {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
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
  min-height: 51px; /* Altura mínima para equipos sin acciones */
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
  grid-template-columns: repeat(7, 1fr);
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
  height: 51px; /* Altura mínima igual a la celda izquierda */
  display: grid;
  grid-template-columns: repeat(7, 1fr);
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
  padding: 8px 12px 8px;
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