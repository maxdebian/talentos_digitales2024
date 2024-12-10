<template>
  <v-data-table-server
    v-model:items-per-page="itemsPerPage"
    :headers="headers"
    :items="serverItems"
    :items-length="totalItems"
    :loading="loading"
    item-value="name"
    @update:options="loadItems"
  ></v-data-table-server>
</template>

<script setup>
    import axios from 'axios';
    import { onMounted, ref } from 'vue';
    import { VDataTable } from 'vuetify/components';
    const products = ref([]);
    const url =  "http://localhost:9000/";
    const itemsPerPage = ref();
    const headers = ref();
    onMounted(async()=>{
        const result = await axios.get(url + "api/products");
        console.log(result)
        itemsPerPage.value =  result.data.meta.per_page
        //headers.value = result.data.meta.data
        if(result.status==200){
            products.value = await result.data.data; 
        } 
    })
</script>