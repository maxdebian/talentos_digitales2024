<template>
    <div>
        <table class="table table-striped">
        <thead>
          <th>Nombre</th>
          <th>Estado</th>
          <th>Accion</th>
        </thead>
        <tbody>
          <tr v-for="(tarea,index) in tareas" :key="index">
            <td>{{ tarea.nombre }}</td>
            <td>{{ tarea.completado ? 'Completado' : 'Pendiente' }}</td>
            <td><button @click="eliminarTarea(index)">Borrar</button></td>
          </tr>
        </tbody>
      </table>
    </div>
</template>

<script setup lang="ts">
    import { useTareasStore } from '@/stores/misTareas';
    import { storeToRefs } from 'pinia';
    /* const {tareas} = useTareasStore();
    console.log(tareas); */
    const tareaStore = useTareasStore();
    const {eliminarTarea,obtenerTareas} = tareaStore
    const {tareas,tareasActual,mostrarFormulario} = storeToRefs(tareaStore)  
    obtenerTareas()
</script>