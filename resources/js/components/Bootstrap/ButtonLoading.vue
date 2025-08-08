<template>
    <div>
        <button v-if="!isButtonLoading" type="submit" :id="id" :class="clases">{{ text }}</button>
        <button v-else :class="clases" disabled>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            {{ text }}
        </button>

    </div>
</template>

<script setup>
    import { computed } from 'vue';
    import { useLoadingStore } from '../../stores/loading';

    const loadingStore = useLoadingStore();

    const isButtonLoading = computed(() => loadingStore.buttonLoading[props.id]);

    const props = defineProps(['text', 'clases', 'id'])
</script>

<style lang="scss" scoped>

</style>
