<template>
    <div>
        <button :type="type" :disabled="habilitarDesabilitar">{{ valorBotton }}</button>
    </div>
</template>
<script setup lang="ts">
    import { defineProps } from 'vue';
    
    defineProps(['valorBotton','type','habilitarDesabilitar'])
</script>
<style scoped></style>