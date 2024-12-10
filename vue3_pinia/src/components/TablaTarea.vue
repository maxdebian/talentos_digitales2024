<template>
    <div>
<!-- 
      <v-btn
         append-icon="mdi-account-circle"
         prepend-icon="mdi-check-circle"
        >
          <template v-slot:prepend>
            <v-icon color="success"></v-icon>
          </template>

          Button

          <template v-slot:append>
            <v-icon color="warning"></v-icon>
          </template>
        </v-btn> -->

        <v-table height="500px">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Estado</th>
            <th>Accion</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(tarea,index) in tareas" :key="index">
            <td>{{ tarea.nombre }}</td>
            <td>{{ tarea.completado ? 'Completado' : 'Pendiente' }}</td>
            <!-- <td><button @click="eliminarTarea(index)">Borrar</button></td> -->
            <td><v-btn :loading="btnState">Borrar</v-btn></td>
          </tr>
        </tbody>
      </v-table>
    </div>
</template>

<script setup lang="ts">
    import { useTareasStore } from '@/stores/misTareas';
    import { storeToRefs } from 'pinia';
    import { ref } from 'vue';
    const btnState = ref(false)
    /* const {tareas} = useTareasStore();
    console.log(tareas); */
    const tareaStore = useTareasStore();
    const {eliminarTarea} = tareaStore

    const {tareas,tareasActual,mostrarFormulario} = storeToRefs(tareaStore)  
</script>