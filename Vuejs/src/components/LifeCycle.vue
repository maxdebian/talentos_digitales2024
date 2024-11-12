<template>
    <div>
        <h1>Life Cycle</h1>
        <h3>Nombre {{ nombre }}</h3>
    </div>
</template>

<script setup lang="ts">
    import { onBeforeMount, onMounted, ref } from 'vue';
    const nombre = ref('');
    onMounted(()=>{
        nombre.value = 'maxi'
        console.log('onMounted')
    })
    onBeforeMount(()=>{
        nombre.value = 'Juan'
        console.log('onBeforeMount')
    })
</script>

<style scoped></style>