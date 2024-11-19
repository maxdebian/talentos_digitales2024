<template>
    <div v-if="mensaje">
        <div class="alert alert-danger" role="alert">
              {{ mensaje }}
              <button @click="errorStore.borrarMensaje()">Cerrar</button>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { useErrorStore } from '@/stores/error';
    import { storeToRefs } from 'pinia';
    const errorStore = useErrorStore();
    const {mensaje} = storeToRefs(errorStore)
</script>