<template>
    <div>
        counter: {{ counterStore.counter }}
        <button @click="counterStore.increment">+1</button>
        <button @click="counterStore.decrement">-1</button>
        count: {{ piniCounterStore.count }}
        <button @click="piniCounterStore.increment">+1</button>
        {{ piniCounterStore.doubleCount }}
    </div>
</template>
<script setup lang="ts">
    //import { ref } from 'vue';
    import { useCounter } from '@/stores/myCounter';
    import { useCounterStore } from '@/stores/counter';
    const counterStore = useCounter(10)
    const piniCounterStore = useCounterStore(); 
   /*  const counter = ref(0)
    const increment = () =>{
        counter.value++
    }
    const decrement = () =>{
        counter.value--
    } */
</script>
