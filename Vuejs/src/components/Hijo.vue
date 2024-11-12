<template>
    <div>
        <h1>Hijo</h1>
        <input type="text" v-model="mensaje">
        <button @click="$emit('enviarMensaje',mensaje)" >Enviar Mensaje</button>
        <!-- <button @click="pruebaEmit('enviarMensaje',mensaje)">Enviar Mensaje</button> -->
    </div>
</template>

<script setup lang="ts">
    import { ref,defineEmits } from 'vue';

    const mensaje = ref('');
/*     const pruebaEmit = defineEmits(['enviarMensaje']); */
</script>