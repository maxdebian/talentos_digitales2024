
<template>
  <div>

    <v-toolbar>

    <v-app-bar-nav-icon></v-app-bar-nav-icon>
      <v-toolbar-title>Tareas</v-toolbar-title>

      <v-spacer></v-spacer>
      <v-btn>Login</v-btn>
      <v-btn>Register</v-btn>

    </v-toolbar>
    <Product></Product>


 <!--      <Navbar></Navbar>
      <Formulario></Formulario>
      <TablaTarea></TablaTarea> -->

  </div>  
</template>
<script setup lang="ts">
   
    import Navbar from './components/Navbar.vue'
    import TablaTarea from './components/TablaTarea.vue'
    import Formulario from './components/Formulario.vue';
    import Product from './components/Product.vue';
</script>

<style scoped>

</style>
