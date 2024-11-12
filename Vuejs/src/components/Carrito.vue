<template>
    <div>
        <h1>Carrito</h1>
        <p>Resultado {{ contador }}</p>
        <button @click="incrementar">+1</button>
        <button @click="decrementar">-1</button>
    </div>
</template>
<script setup lang="ts">
    import useContador from '@/composables/useContador';
    const {contador,incrementar,decrementar} = useContador()
</script>