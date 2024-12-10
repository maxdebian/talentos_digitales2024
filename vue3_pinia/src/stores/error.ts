import { defineStore } from "pinia";
interface IState{
    mensaje:String | null
}
export const useErrorStore = defineStore('error',{
    state:():IState=>({
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