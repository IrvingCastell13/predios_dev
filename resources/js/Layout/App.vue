<template>
    <div class="dashboard">

        <header class="dashboard-header">



            <div  class="nav-icon"></div>

            <a   href="#" class="logo"><img loading="lazy" src="/images/logos_metik/metik-logo.png" alt=""></a>

            <HederSkeleton v-if="loadingStore.isLoadingHeader" />

            <div v-else class="dashboard-header-links">

                <!-- <div v-if="user.IDRol == 1">

                    <select v-model="IDClienteGlobal" @change="obtenerData" class="form-select">
                        <option value="" hidden>Selecciona un cliente</option>
                        <option v-for="cliente in clientes" :value="cliente.IDCliente" :key="cliente.IDCliente">{{ cliente.NombreCliente }}</option>
                    </select>

                </div>


                <div v-if="user.IDRol == 1">

                    <select v-model="IDPersonaGlobal" @change="cambiarPersona" class="form-select">
                        <option value="" hidden>Selecciona un usuario</option>
                        <option v-for="user in usuarios" :value="user.IDPersona" :key="user.IDPersona">{{ user.full_name }}</option>
                    </select>
                </div> -->

                <!-- <a href="#" class="notification-link">
                    <span>2</span>
                    <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M6 15V16C6 16.7956 6.31607 17.5587 6.87868 18.1213C7.44129 18.6839 8.20435 19 9 19C9.79565 19 10.5587 18.6839 11.1213 18.1213C11.6839 17.5587 12 16.7956 12 16V15M7 3C7 2.46957 7.21071 1.96086 7.58579 1.58579C7.96086 1.21071 8.46957 1 9 1C9.53043 1 10.0391 1.21071 10.4142 1.58579C10.7893 1.96086 11 2.46957 11 3C12.1484 3.54303 13.1274 4.38833 13.8321 5.4453C14.5367 6.50227 14.9404 7.73107 15 9V12C15.0753 12.6217 15.2954 13.2171 15.6428 13.7381C15.9902 14.2592 16.4551 14.6914 17 15H1C1.54494 14.6914 2.00981 14.2592 2.35719 13.7381C2.70457 13.2171 2.92474 12.6217 3 12V9C3.05956 7.73107 3.4633 6.50227 4.16795 5.4453C4.8726 4.38833 5.85159 3.54303 7 3Z"
                            stroke="#0F6491" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a> -->

                <!-- <a href="#" class="user">
                    <div class="dropdown">
                        <div class="dropdown-toggle p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex">
                                <img :src="user.image ?? '/images/avatar_default.jpg'" alt="" class="user-image mx-2">
                                <p>{{ user ? user.Usuario : 'invitado' }}</p>
                            </div>
                        </div>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" @click="miPerfil">Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="#" @click="cerrarSesion">Cerrar sesión</a></li>
                        </ul>
                    </div>
                </a> -->
            </div>
        </header>
        <!-- //End Dashboard Header -->

        <main class="dashboard-content">
            <aside class="dashboard-sidebar icon-only">
                <NavSkeleton v-if="loadingStore.isLoadingNav"/>
                <ul v-else  class="menu">
                    <li v-for="(menu,k) in menuComposer">
                        <a :class="{ 'active': isActiveRoute(menu.href) }" v-if="menu.opciones.length == 0" :href="menu.href">
                            <span v-if="menu.svg" class="menu-icon">
                                <span v-html="menu.svg"></span>
                            </span>
                            <p>{{ menu.NombreModulo }}</p>
                            <span v-if="menu.opciones.length > 0" class="ml-auto">
                                <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L7 7L13 1" stroke="#4C6272" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </a>
                        <a v-else href="#" @click.prevent="toggleMenuOption(menu.IDModulo)">
                            <span v-if="menu.svg" class="menu-icon">
                                <span v-html="menu.svg"></span>
                            </span>
                            <p>{{ menu.NombreModulo }}</p>
                            <span v-if="menu.opciones.length > 0" class="ml-auto" :class="optionsDeploy.includes(menu.IDModulo) ? 'rotate-180' : 'animate'">
                                <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L7 7L13 1" stroke="#4C6272" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </a>
                        <ul v-if="optionsDeploy.includes(menu.IDModulo)" style="margin-left: 30px;">
                            <li v-for="opcion in menu.opciones">
                                <a  :href="opcion.href">
                                    <p>{{ opcion.NombreModulo }}</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <p><span>Versión</span> 2.0</p>
            </aside>
            <!-- //End Sidebar -->

            <div class="dashboard-pages" :class="clases">
                <slot></slot>
            </div>
            <!-- // End Dashboar Pages -->

        </main>
        <!-- //End Main -->

        </div>


        <Profile/>
</template>

<script setup>


import eventBus from '../eventBus';
import { onMounted, ref } from 'vue';
import { useFuncionesStore } from '../stores/funciones/Funciones';
import { useAuthStore } from '../stores/auth/Auth';
import { ucwords } from '../Utilities';
import Profile from './Profile.vue';
import { usestoreProfileStore } from './js/storeProfile';
import NavSkeleton from '../Components/Skeleton/NavSkeleton.vue';
import HederSkeleton from '../Components/Skeleton/HederSkeleton.vue';
import { useLoadingStore } from '../stores/loading';
const props = defineProps(['clases'])
const { user } = useAuthStore();
const menuComposer = ref([])
const loadingStore = useLoadingStore();
const storeProfile = usestoreProfileStore()

const optionsDeploy = ref([])
const predios = ref([])
const clientes = ref([])
const usuarios = ref([])

const IDPredioGlobal = ref(localStorage.getItem('IDPredioGlobal') ?? '')
const IDClienteGlobal = ref(localStorage.getItem('IDClienteGlobal') ?? '')
const IDPersonaGlobal = ref(localStorage.getItem('IDPersonaGlobal') ?? '')

const funcionesStore = useFuncionesStore();

const cerrarSesion = async () => {
    try {
            await axios.post(route('logout'));  // Llama a la API Laravel para cerrar sesión
            localStorage.removeItem('token'); // Elimina el token almacenado
            localStorage.removeItem('user'); // Elimina el token almacenado
            localStorage.removeItem('IDCliente'); // Elimina el token almacenado
            localStorage.removeItem('IDClienteGlobal'); // Elimina el token almacenado
            localStorage.removeItem('IDPersonaGlobal'); // Elimina el token almacenado
            localStorage.removeItem('IDPlan'); // Elimina el token almacenado
            localStorage.removeItem('IDPredio'); // Elimina el token almacenado
            localStorage.removeItem('IDPredioGlobal'); // Elimina el token almacenado
            axios.defaults.headers.common['Authorization'] = null;  // Limpia el encabezado

            window.location.href = '/'
        } catch (error) {
            // errorMessage.value = 'Error al cerrar sesión';
        } finally {

        }
}
const miPerfil = async () => {
    storeProfile.getPersona()
}
const toggleMenuOption = (menuID) => {
    const index = optionsDeploy.value.indexOf(menuID);
    if (index === -1) {
        // Si no existe, lo agrega
        optionsDeploy.value.push(menuID);
    } else {
        // Si ya existe, lo quita
        optionsDeploy.value.splice(index, 1);
    }
}

const isActiveRoute = (href) => {
  const currentUrl = window.location.pathname;
  return currentUrl === href;
}

const menu = async () => {

    // const user = JSON.parse(localStorage.getItem('user')) || null;

    // const { data : { data } } = await axios.get(route('menu'), {
    //     params:{
    //         // IDPredio: IDPredioGlobal.value,
    //     }
    // })

    // menuComposer.value = data

    loadingStore.stopLoadingNav()
}

const cambiarPersona = async () => {

    eventBus.methods.setIDPersonaGlobal(IDPersonaGlobal.value);

    menu()

    funcionesStore.funciones(IDPersonaGlobal.value);
    window.location.reload()

}

const obtenerData = () => {

    eventBus.methods.setIDClienteGlobal(IDClienteGlobal.value);
    window.location.reload()
}

const getPredios = async () => {
    try {

        const user = JSON.parse(localStorage.getItem('user')) || null;

        if(user?.IDRol == 1){
            var params = {
                IDCliente: IDClienteGlobal.value ?? null,
            }
        }
        const { data: { data } } = await axios.get(route('obtenerPrediosRolesLayout'), params);
        predios.value = data;
    } catch (error) {
        console.error('Error al obtener los predios:', error);
    }
}

const getClientes = async () => {
        try {
            const { data: { data } } = await axios.get(route('clientes.index'));
            clientes.value = data;
        } catch (error) {
            console.error('Error al obtener los predios:', error);
        }
    }

const getPersonas = async () => {
    try {
        const { data: { data } } = await axios.get(route('obtenerPersonasApp'), {
            params:{
                IDCliente: IDClienteGlobal.value ?? null,
                IDPredio: IDPredioGlobal.value ?? null,
            }
        });
        usuarios.value = data;
    } catch (error) {
        console.error('Error al obtener los predios:', error);
    }
}

onMounted(() => {

    setTimeout(() => {
        loadingStore.stopLoadingHeader()
    }, 1000);
    menu();

    // getClientes();
    // getPersonas();

    const togglePassword = document.querySelectorAll(".password-toggle-btn");
        togglePassword.forEach((button) => {
        button.addEventListener("click", function () {
            const passwordInput = document.getElementById(button.dataset.target);
            if (passwordInput.type === "password") {
            this.classList.add("active");
            passwordInput.type = "text";
            } else {
            this.classList.remove("active");
            passwordInput.type = "password";
            }
        });
    });

// Sidebar Toggle
const sidebar = document.querySelector(".dashboard-sidebar");
const navIcon = document.querySelector(".nav-icon");

// Sidebar Toggle on Click
if(navIcon,sidebar){
  navIcon.addEventListener("click", function () {
    sidebar.classList.toggle("icon-only");
    // this.classList.toggle("active");
  });
}

// Dashboard Sidebar menu Toggle on Window Resize
function handleResize() {
  if(navIcon,sidebar){
    if (window.innerWidth < 1200) {
      sidebar.classList.add("icon-only");
    //   navIcon.classList.add("active");
    } else {
      sidebar.classList.remove("icon-only");
    //   navIcon.classList.remove("active");
    }
  }
}
handleResize();
window.addEventListener("resize", handleResize);

})


</script>

<style>

.spinner-border {
  display: inline-block;
  width: 1rem;
  height: 1rem;
  vertical-align: text-bottom;
  border: 0.25em solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  animation: spinner-border 0.75s linear infinite;
}
</style>
