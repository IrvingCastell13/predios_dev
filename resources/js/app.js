import './bootstrap';
import { createApp } from 'vue';


// Importa tu componente contenedor que tiene las dos gráficas
import GraficasReportes from './components/GraficasChartJs/GraficasReportes.vue';
import DashboardRenovacion from './components/DashboardRenovacion/DashboardRenovacion.vue'; // <-- Ruta cambiada
import DashboardMantenimiento from './components/DashboardMantenimiento/DashboardMantenimiento.vue';
import { createPinia } from 'pinia'; // Importar Pinia
import EquiposIndex from './components/Equipos/Index.vue';

const reportesAppDiv = document.getElementById('app');
if (reportesAppDiv) {
    createApp(GraficasReportes).mount(reportesAppDiv);
}



const renovacionApp = document.getElementById('app-renovacion'); // <-- ID cambiado

if (renovacionApp) {
    createApp(DashboardRenovacion).mount(renovacionApp); // <-- Componente cambiado
}


const mantenimientoApp = document.getElementById('app-mantenimiento');

if (mantenimientoApp) {
    createApp(DashboardMantenimiento).mount(mantenimientoApp);
}

const equiposAppDiv = document.getElementById('app-equipos');

if (equiposAppDiv) {
    const pinia = createPinia(); // Crear una instancia de Pinia
    const app = createApp(EquiposIndex); // Usar EquiposIndex como componente raíz
    app.use(pinia); // Usar Pinia en la aplicación
    app.mount(equiposAppDiv);
}