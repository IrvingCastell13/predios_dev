// Importa la función para crear la aplicación de Vue
import { createApp } from 'vue';

// Importa tu componente contenedor que tiene las dos gráficas
import GraficasReportes from './components/GraficasChartJs/GraficasReportes.vue';
import DashboardRenovacion from './components/DashboardRenovacion/DashboardRenovacion.vue'; // <-- Ruta cambiada
import DashboardMantenimiento from './components/DashboardMantenimiento/DashboardMantenimiento.vue';
// import EquiposIndex from './components/Equipos/Index.vue';


// Crea la instancia de la aplicación Vue
const app = createApp({});

// Registra tu componente de forma global con la etiqueta <graficas-reportes>
app.component('graficas-reportes', GraficasReportes);

// Monta la aplicación en el elemento con id="app" de tu archivo Blade
app.mount('#app');


const renovacionApp = document.getElementById('app-renovacion'); // <-- ID cambiado

if (renovacionApp) {
    createApp(DashboardRenovacion).mount(renovacionApp); // <-- Componente cambiado
}


const mantenimientoApp = document.getElementById('app-mantenimiento');

if (mantenimientoApp) {
    createApp(DashboardMantenimiento).mount(mantenimientoApp);
}

// const equiposApp = document.getElementById('app-equipos');
// if (equiposApp) {
//     app.component('EquiposIndex', EquiposIndex);
//     app.mount(equiposApp);
// }