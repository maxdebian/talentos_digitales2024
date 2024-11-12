<template>
    <div>
        <h1>Depositos y Extracciones</h1>    
        <h4>Mis ahorros {{ ahorro }}</h4>
        <button @click="depositar">Depositar 1</button>
        <button @click="extracion" :disabled="status">Extraer 1</button>
        <label for="">Dni</label>
        <input type="text" v-model="dni">
        <h3>Result</h3>
        <p>Computed {{ valueComputed }}</p>        
    </div>
</template>
<script setup lang="ts">
    import {computed, ref, watch, watchEffect} from 'vue';
    const saludoApp = ref<string>("Hola Soy Maxi");
    const ahorro = ref<number>(0)
    const depositar = ()=> ahorro.value += 1
    const dni = ref('');

    const extracion = ()=>{
        if(ahorro.value<=0) return;
        ahorro.value -= 1        
    }
    const valueComputed = computed(()=>{
        return ahorro.value; 
    })
    const status = computed(()=>{
        return false;
    })
    watch(ahorro,(newValue,currentValue)=>{
        console.log(`Ahorro ${ahorro.value}  current value: ${newValue}  old value: ${currentValue}`)
    })
    /* watch([ahorro,nombre],([newValueAhorro,newValueNombre],[currentValueAhorro,currentValueAhorro])=>{
        console.log(`Ahorro ${ahorro.value}  current value: ${newValue}  old value: ${currentValue}`)
    }) */
   /* watchEffect(()=>{
        console.log(`watchEffect dni: ${dni.value} `)
    })  */
   const stopWatch = watchEffect(()=>{
        console.log(`watchEffect dni: ${dni.value} `)
        if(dni.value.length===8){
            stopWatch();
        }
   })

</script>

<style scoped>

</style>