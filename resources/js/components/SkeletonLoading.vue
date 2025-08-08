<template>
<!-- Skeleton completo del layout -->

<div v-if="isLoading" class="container-fluid">
    <div class="row">
        <!-- Sidebar Skeleton -->
       <Nav />

        <!-- Main Content Skeleton -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
            <!-- Navbar Skeleton -->
            <div class="d-flex justify-content-end align-items-center py-3 border-bottom">
                <Header />
            </div>

            <!-- Tabla Skeleton (Encabezados y Filas) -->
            <div class="mt-4 skeleton" style="width: 100px; height: 30px; border-radius: 20px;"></div>
            <div class="table-responsive" style="margin-top: 20px;">
                <table class="table">
                    <thead>
                    <tr>
                        <th v-for="i in 8" :key="'header-' + i">
                        <div class="skeleton" style="width: 60%; height: 20px;"></div>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="i in 3" :key="'row-' + i">
                        <td v-for="j in 8" :key="'cell-' + j">
                        <div class="skeleton table-placeholder"></div>
                        </td>
                    </tr>
                    <tr v-for="i in 3" :key="'row-' + (i + 3)">
                        <td v-for="j in 8" :key="'cell-' + j">
                        <div class="skeleton table-placeholder"></div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>


</template>

<script setup>
import { computed } from 'vue';
import { useLoadingStore } from '../stores/loading';
import Header from './Skeleton/HederSkeleton.vue';
import Nav from './Skeleton/NavSkeleton.vue';

// Escucha el estado de carga desde el store de Pinia
const loadingStore = useLoadingStore();
const isLoading = computed(() => loadingStore.isLoading);
</script>

<style >
.skeleton {
    background: #e0e0e0;
    border-radius: 4px;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% {
        background-color: #e0e0e0;
    }
    50% {
        background-color: #f0f0f0;
    }
    100% {
        background-color: #e0e0e0;
    }
}

.skeleton-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.placeholder-nav, .sidebar-skeleton {
    height: 15px;
    margin-bottom: 10px;
}

.table-placeholder {
    height: 20px;
    width: 100%;
    border-radius: 4px;
    margin-bottom: 15px;
}

/* Sidebar ocupa todo el alto */
.sidebar {
    height: 100vh; /* Ocupa todo el viewport */
    position: fixed; /* Fijo en la pantalla */
    top: 0;
    left: 0;
}

.content {
    margin-left: 16.66%; /* Deja espacio para el sidebar */
}
</style>

