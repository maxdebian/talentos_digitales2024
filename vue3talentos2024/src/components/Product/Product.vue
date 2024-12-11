<template>
    <v-container>
        <v-card
                    class="mx-auto"
                    width="400"
                >
                
                 <template v-slot:title>
                    <span class="font-weight-black"><v-img height="200" :src="baseURLBackend+product?.image"></v-img></span>
                    </template>

                    <v-card-text class="bg-surface-light pt-4">
                        <p>Description: {{ product?.description }}</p>
                        <p>Price: $ {{ product?.price }}</p>
                        <p>Stock: {{ product?.productStock }}</p> 
                    </v-card-text> 
                    <v-card-actions>
                        <v-btn color="info">Add Cart</v-btn>
                        <v-btn color="green" @click="back">Return...</v-btn>
                    </v-card-actions>
                </v-card>
    </v-container>
</template>
<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import {useRoute, useRouter} from 'vue-router';
    import { useProductStore } from '@/stores/product';
    import { baseURLBackend} from '@/constant';
    const product = ref();
    const productStore = useProductStore()
    const router = useRouter()
    onMounted(()=>{
        const {params} = useRoute();
        product.value = productStore.getProductById(parseInt(params.id))
        console.log(product) 
    })
    const back = ()=>{
        router.push('/products')
    }
</script>