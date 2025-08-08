import './bootstrap';
import '../css/app.css'; 
import { createApp, h} from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from 'pinia';

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


const pinia = createPinia();

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
    return pages[`./Pages/${name}.vue`];
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(pinia)
      .mount(el);
  },
});