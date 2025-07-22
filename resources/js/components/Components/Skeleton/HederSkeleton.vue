<template>

<div class="dashboard-header-links">
        <div class="d-flex">
            <div class="skeleton skeleton-avatar me-3"></div>
            <div class="skeleton skeleton-avatar me-3"></div>
            <div class="skeleton skeleton-avatar me-3"></div>
            <div class="skeleton skeleton-avatar me-3"></div>
        </div>
        <div class="mx-2 skeleton" style="width: 100px; height: 30px; border-radius: 20px;"></div>
    </div>



</template>

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

