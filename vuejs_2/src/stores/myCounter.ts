import { defineStore } from "pinia";
import { ref } from "vue";

export const useCounter = defineStore('myCounter',()=>{
    //setup
    const counter = ref(0);
    
    //get

    //actions
    const increment = ()=>{
        counter.value++
    }
    const decrement = () =>{
        counter.value--
    }
    return {counter,increment,decrement}
})