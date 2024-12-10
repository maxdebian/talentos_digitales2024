<template>
    <div>
        <v-container>
            <v-row>
               <!--  3 + 3 + 3 +3 = 12
                4 + 4 + 4 -->
                <v-col lg="4" v-for="product in contentsProducts" :key="product.id" class="mb-4">
                    <v-card height="600">
                        <v-card-title>{{ product.description }}</v-card-title>
                        <v-card-text><v-img class="mx-auto" width="400" :src="url+product.image" alt=""></v-img></v-card-text>
                        <v-card-subtitle>Stock: {{ product.productStock }} and Price: {{ product.price }}</v-card-subtitle>
                        <v-card-actions>

                            <v-btn :disabled="!product.enabled && product.productStock>0">Add Cart</v-btn>
                            <v-btn color="success">Return</v-btn>
                            <v-btn color="warning" outlined>Home</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
     
<!--             <v-card v-for="product in contentsProducts" :key="product.id" class="mb-4">
                <v-card-title>{{ product.description }}</v-card-title>
                <v-card-text><v-img class="mx-auto" width="400" :src="url+product.image" alt=""></v-img></v-card-text>
                <v-card-subtitle>Stock: {{ product.productStock }} and Price: {{ product.price }}</v-card-subtitle>
                <v-card-actions>

                    <v-btn :disabled="!product.enabled && product.productStock>0">Add Cart</v-btn>
                    <v-btn color="success">Return</v-btn>
                    <v-btn color="warning" outlined>Home</v-btn>
                </v-card-actions>
            </v-card> -->
           <!--  <v-card>
                <v-card-title>{{ title }}</v-card-title>
                <v-card-subtitle>{{ description }}</v-card-subtitle>
                <v-card-text><img :src="image" alt=""></v-card-text>
                <v-card-actions>
                    <v-btn color="success">Return</v-btn>
                    <v-btn color="warning" outlined>Home</v-btn>
                </v-card-actions>
            </v-card> -->
        </v-container>
    </div>
</template>

<script setup lang="ts">
    import axios from 'axios';
    import { onMounted, ref } from 'vue';
    const url = "http://localhost:9000/";
    const contentsProducts = ref();
   /*  const title = ref('Computadora de Escritorio')
    const description = ref('description');
    const image = ref("https://reqres.in/img/faces/1-image.jpg")  */

    onMounted(async()=>{
/*         const result = await fetch(url);
        if(result.status==200){
            const data = result.json();
            console.log(data);
        } */
        const result = await axios.get(`${url}api/products`)
        if(result.status==200){
            /* console.log(result) */
            contentsProducts.value = result.data.data
        }

    })



</script>