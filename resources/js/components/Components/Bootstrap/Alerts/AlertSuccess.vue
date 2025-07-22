<template>
    <div class="alert alert-success d-flex align-items-center text-center" role="alert">

        <div class="text-center">
            {{ text }}
        </div>
        <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
    </div>
</template>

<script setup>
   const props = defineProps({
        text: {
            type: String,
            required: true
        },

    });
</script>
