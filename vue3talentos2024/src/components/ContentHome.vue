<template>
    <v-container>
        <v-row>
            <v-col lg="4" v-for="product in products" :key="product.id">

                <v-card
                    class="mx-auto"
                    width="400"
                >
                    <template v-slot:title>
                    <span class="font-weight-black"><v-img height="200" :src="url+product.image"></v-img></span>
                    </template>

                    <v-card-text class="bg-surface-light pt-4">
                        {{ product.description }} {{ product.price }}
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="red">See More...</v-btn>
                    </v-card-actions>
                </v-card>

            </v-col>
          <!--   description
            image -->
        </v-row>
    </v-container> 

</template>
<script setup lang="ts">
    import axios from 'axios';
    import { onMounted, ref } from 'vue';
    const products = ref([]);
    const url =  "http://localhost:9000/";
/*     const title = ref('Computadora de Escritorio')
    const description = ref('description');
    const image = ref("https://reqres.in/img/faces/1-image.jpg") 
 */
    onMounted(async()=>{
        const result = await axios.get(url + "api/products");
        if(result.status==200){
            products.value = await result.data.data;  
        } 
    })
</script>