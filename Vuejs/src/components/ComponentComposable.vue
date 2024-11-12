<template>
    <div>
        <h1>Composable</h1>
        <p>Resultado {{ contador }}</p>
        <input v-model="cantidadAIncrementar">
        <button @click="incrementar">+1</button>
        <button @click="decrementar">-1</button>
        <button @click="incrementarPor(cantidadAIncrementar)">Incrementar por {{ cantidadAIncrementar }}</button>
    </div>
</template>

<script setup lang="ts">
    import useContador from '@/composables/useContador';
    const {contador,cantidadAIncrementar,incrementar,decrementar,incrementarPor} = useContador();
</script>

<style scoped>

</style>