import { ref } from 'vue';
export default function(){
    const contador = ref(0);
    const cantidadAIncrementar = ref(0);
    const incrementar = ()=>{
        contador.value++;
    }
    const decrementar = ()=>{
        contador.value--;
    }
    const incrementarPor = (valorAIncrementar:number = 1) =>{
        console.log(valorAIncrementar)
        contador.value = valorAIncrementar;
    }
    return {contador,cantidadAIncrementar,incrementar,decrementar,incrementarPor}
}
