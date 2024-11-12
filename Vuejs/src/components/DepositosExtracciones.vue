<template>
    <div>
        <h1>Depositos y Extracciones</h1>    
        <h4 :class="asignarClase">Mis ahorros {{ ahorro }}</h4>
        <button @click="depositar(100)">Depositar 100</button>
        <button @click="extracion(100)" :disabled="status">Extraer 100</button>
    
    </div>
</template>
<script setup lang="ts">
    import {computed, ref} from 'vue';
    const saludoApp = ref<string>("Hola Soy Maxi");
    const ahorro = ref<number>(0)
    //const status = ref<boolean>(true)
    //const asignarClase = ref('rojo');
    const depositar = (cantidad:number)=>{
        ahorro.value += cantidad
        //asignarClase.value = 'verde'
        //status.value = !status.value
    }
    const extracion = (cantidad:number)=>{
        if(ahorro.value<=0){
            //status.value = !status.value
            //asignarClase.value = 'rojo' 
            return;
        }

        ahorro.value -= cantidad
        
        //status.value = !status.value
    }
    const asignarClase = computed(():string=>{
        if(ahorro.value>0) return 'verde';
        return 'rojo';
    })
    const status = computed(()=>{
        if(ahorro.value===0) return true;
        return false;
    })
 
</script>

<style scoped>
.verde{
    color: green;
}
.rojo{
    color: red;
}
</style>