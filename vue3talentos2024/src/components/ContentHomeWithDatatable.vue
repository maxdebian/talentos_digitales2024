<template>
  <div>
    <h1>Vdatatable</h1>
    <v-data-table 
      v-model:items-per-page="itemsPerPage" 
      :items="products"
      item-value="id"
      :headers="headers"  
      class="elevation-1">

    </v-data-table>
    <!-- <v-pagination
      v-model="page"
      :length="4"
      rounded="circle"
    ></v-pagination> -->
  </div>   
</template>


<script setup lang="ts">
    import axios from 'axios';
    import { onMounted, ref } from 'vue';
    import { VDataTable } from 'vuetify/components';
    const products = ref([]);
    const url =  "http://localhost:9000/";
    const itemsPerPage = ref();
    const headers = ref();
    onMounted(async()=>{
        const result = await axios.get(url+"api/products");
        //console.log(result)
        itemsPerPage.value =  result.data.meta.per_page
        headers.value = [
          {
            text:"id",
            align:'start',
            sortable:true,
            value:'id'
          },
          {text:"Description",value:'description'},
          {text:"Image",value:'image'},
          {text:"Price",value:'price'},
          {text:"ProductStock",value:'productStock'},
          {text:"Enabled",value:'enabled'},
        ]
        
        result.data.meta.data
        if(result.status==200){
            products.value = await result.data.data; 
        } 
    })
</script>