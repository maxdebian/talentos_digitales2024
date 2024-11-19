import { defineStore } from "pinia";

export const useErrorStore = defineStore('error',{
    state:()=>({
        mensaje:null
    }),
    actions:{
        borrarMensaje(){
            this.mensaje = null
        },
        cargarMensaje(mensaje:string){
            this.mensaje = mensaje
        }

    } 
})