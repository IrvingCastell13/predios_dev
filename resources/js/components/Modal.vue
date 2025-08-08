<template>

<div class="modal fade show" :id="modalId" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog max-w-950 m-inline-auto" :class="customClass">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-content-block h-full d-flex flex-column">

                        <div class="modal-content-header d-flex flex-wrap align-items-center mb-4">
                            <div class="flex-fill">
                                <h4 class="fw-bold primary"><slot name="title"></slot></h4>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-content-section flex-fill" :class="overflowy ? 'overflow-y-auto' : ''">
                            <slot></slot>
                        </div>
                        <div class="modal-content-footer mt-3">
                            <slot name="footer"></slot>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
    const props = defineProps({
        modalId: {
            type: String,
            required: true
        },
        customClass: {
            type: String,
            default: '' // Clase opcional, por defecto vacía
        },
        overflowy: {
            type: Boolean,
            default: true // Clase opcional, por defecto vacía
        }
    });
</script>

<style lang="scss" scoped>

</style>
