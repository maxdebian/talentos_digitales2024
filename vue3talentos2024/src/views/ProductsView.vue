<template>
     <v-container>
        <v-row>
            <v-col lg="4" v-for="product in products" :key="product.id">

                <v-card
                    class="mx-auto"
                    width="400"
                >
                    <template v-slot:title>
                    <span class="font-weight-black"><v-img height="200" :src="baseURLBackend+product.image"></v-img></span>
                    </template>

                    <v-card-text class="bg-surface-light pt-4">
                        Description {{ product.description }} Precio {{ product.price }}
                    </v-card-text>
                    <v-card-actions>
                        <!-- <v-btn color="red" @click="getProductId(product.id)">See More...</v-btn> -->
                        <router-link :to="{name:'productById',params:{id:product.id}}">See More...</router-link>
                    </v-card-actions>
                </v-card>

            </v-col>
        </v-row>
        
    </v-container>
</template>
<script setup lang="ts">
    import { useProductStore } from '@/stores/product';
    import { onMounted, ref } from 'vue';
    import { baseURLBackend } from '@/constant';
    import Product from '@/components/Product/Product.vue'
    const productStore = useProductStore();
    const products = ref([]);
    const getProductId = async (id:number)=>{
        //const prod =productStore.products 
        const product = productStore.getProductById(id)
        console.log(product)
        //console.log(productStore.products.filter(element => element.id == id),id)
        /* const product = productStore.getProductById(id); */
        
    }
    onMounted(async()=>{
        if(productStore.products == undefined || productStore.products == null ) await productStore.getProducts();
        products.value = productStore.products
/*         console.log(products.value); */
    })
  
</script>
