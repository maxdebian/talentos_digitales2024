import { defineStore } from "pinia";
import { ref } from "vue";

export const useTareasStore = defineStore('tarea',()=>{
   
        const tareas = ref([{nombre:'Aprender Laravel',completado:false}])
        const tareasActual = ref(null)
        const mostrarFormulario = ref(false)    
       
        const crearTarea = (nombre:string)=>{
            tareas.value.push({nombre,completado:false});
            /* localStorage.setItem('tareas',JSON.stringify(this.tareas)) */
        }
        const obtenerTareas = ()=>{
          /*   const data = localStorage.getItem('tareas')
            if(data){
                console.log(JSON.parse(data))
            } */
        }
        const eliminarTarea = (index:number) => {
            tareas.value = tareas.value.filter((elment,indexFilter)=>indexFilter!=index)
        }
    //persist:true
    return {tareas,tareasActual,mostrarFormulario,crearTarea,obtenerTareas,eliminarTarea}
},{
    persist:true
})