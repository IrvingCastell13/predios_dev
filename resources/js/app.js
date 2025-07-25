import './bootstrap';
import { createApp } from 'vue';


// Importa tu componente contenedor que tiene las dos gráficas
import DashboardReportes from './components/DashboardReportes/Dashboard.vue';
import DashboardRenovacion from './components/DashboardRenovacion/Dashboard.vue'; // <-- Ruta cambiada
import DashboardMantenimiento from './components/DashboardMantenimiento/Dashboard.vue';
import DashboardOrdenTrabajo from './components/DashboardOrdenTrabajo/Dashboard.vue';

const reportesApp = document.getElementById('app-reportes'); // <-- ID cambiado

if (reportesApp) {
    createApp(DashboardReportes).mount(reportesApp); // <-- Componente cambiado
}


const renovacionApp = document.getElementById('app-renovacion'); // <-- ID cambiado

if (renovacionApp) {
    createApp(DashboardRenovacion).mount(renovacionApp); // <-- Componente cambiado
}


const mantenimientoApp = document.getElementById('app-mantenimiento');

if (mantenimientoApp) {
    createApp(DashboardMantenimiento).mount(mantenimientoApp);
}


const ordenTrabajo = document.getElementById('app-orden-trabajo');

if (ordenTrabajo) {
    createApp(DashboardOrdenTrabajo).mount(ordenTrabajo);
}
