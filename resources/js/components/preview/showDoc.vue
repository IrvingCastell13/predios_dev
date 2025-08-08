<template>
       <Modal :modalId="'modalVerDoc'" >
            <template #title>
                Vista Previa
            </template>
            <div id="fileContainer"></div>
       </Modal>
</template>

<script setup>
import Modal from '../Modal.vue';
import { cerrarModal } from '../../Utilities';
</script>

