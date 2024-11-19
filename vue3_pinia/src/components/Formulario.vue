<template>
    <div>
        <form @submit.prevent="procesarformulario" ref="formularioRef">
            <Error/>
            <label for="">Nombre</label>
            <input type="text" v-model="nombre" required :rules="rules">
        </form>
    </div>
</template>
<script setup lang="ts">
    import { ref } from 'vue';
    import { useTareasStore } from '@/stores/misTareas';
    import Error from './Error.vue';

    const nombre = ref('');
    const {crearTarea} = useTareasStore();
    const procesarformulario = /* async */()=>{
        /* const { valid } = await formularioRef.value!.validate();
        console.log(valid); */
        
        crearTarea(nombre.value)
    }
    const formularioRef = ref(null) 
    const rules = [
        (valor:string) => !!valor || 'Valor Requerido'
    ]
</script>
