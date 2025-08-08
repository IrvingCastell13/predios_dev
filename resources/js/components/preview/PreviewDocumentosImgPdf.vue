<template>
    <div v-if="extension !== 'pdf'" style="cursor: pointer;" @click="showDocument">
        <img :src="cleanUrl" alt="" :style="{ width: props.width, height: props.height, borderRadius: '20px' }">
    </div>
    <canvas v-else ref="pdfCanvas" @click="showDocument"></canvas>

    <showDoc :url="url" />
</template>

<script setup>

    import { onMounted } from 'vue';
    import { abrirModal } from '../../Utilities';

    import showDoc from './showDoc.vue';
    import { ref } from 'vue';
import { computed } from 'vue';

    const props = defineProps({
        extension: String,
        url: String,
        width: {
            type: String,
            default: '154px'
        },
        height: {
            type: String,
            default: '116px'
        }
    });


    const pdfCanvas = ref(null);

    const showDocument = (doc) => {

        let fileContainer = document.getElementById("fileContainer");

        if (props.extension === "pdf") {
            fileContainer.innerHTML = `<iframe src="${props.url}#toolbar=0" width="100%" height="500px"></iframe>`;
            // fileContainer.innerHTML = `<iframe src="/storage/track_archivos/${props.url}#toolbar=0" width="100%" height="500px"></iframe>`;
        } else {
            fileContainer.innerHTML = `<img src="${props.url}" class="img-fluid" alt="Imagen">`;
            // fileContainer.innerHTML = `<img src="/storage/track_archivos/${props.url}" class="img-fluid" alt="Imagen">`;
        }
        abrirModal("modalVerDoc");

    }

    const cleanUrl = computed(() => {
        return props.url?.replaceAll('&amp;', '&') ?? ''
    })


    onMounted(async () => {


            if(props.extension === 'pdf'){

                const loadingTask = pdfjsLib.getDocument(props.url);
                const pdf = await loadingTask.promise;
                const page = await pdf.getPage(1); // Obtener la primera página

                const scale = 1.5;
                const viewport = page.getViewport({ scale });

                const canvas = pdfCanvas.value;
                const context = canvas.getContext("2d");

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                canvasContext: context,
                viewport: viewport,
                };
                await page.render(renderContext).promise;
            }


});
</script>

<style  scoped>
canvas {
    border: 1px solid #ccc;
    width: 154px;
    height: 116px;
    object-fit: cover;
    cursor: pointer;
    border-radius: 5px;
  }
</style>
