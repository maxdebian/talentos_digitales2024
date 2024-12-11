<template>
    <v-sheet class="bg-deep-purple pa-12" rounded>
     <v-card class="mx-auto px-6 py-8" max-width="644">

       <!-- <v-form @submit.prevent="result()"> -->
       <v-form @submit.prevent="productStore.storeProduct(form)">
      <!--  <v-alert v-if="errors.length>0"
            :text="errors"
            title="Errors"
            type="error"
            :closable="removeAlert"
          ></v-alert> -->
         <v-text-field
           v-model="form.description"
           :readonly="loading"
           :rules="[required]"
           class="mb-2"
           label="Description"
           clearable
         ></v-text-field>

         <v-file-input id="fileInput" label="File input"></v-file-input>


         <v-text-field
           v-model="form.cost_price"
           :readonly="loading"
           :rules="[required]"
           class="mb-2"
           label="Cost Price"
           clearable
         ></v-text-field>
         <v-text-field
           v-model="form.increase"
           :readonly="loading"
           :rules="[required]"
           class="mb-2"
           label="Increase"
           clearable
         ></v-text-field>
         <v-text-field
           v-model="form.stock"
           :readonly="loading"
           :rules="[required]"
           class="mb-2"
           label="Stock"
           clearable
         ></v-text-field>
 
        
         <br>
 
         <v-btn
           :disabled="!form"
           :loading="loading"
           color="success"
           size="large"
           type="submit"
           variant="elevated"
           block
         >
           Save
         </v-btn>
 
       </v-form>
      
      
      
     </v-card>
   </v-sheet>
</template>
<script setup lang="ts">
    import axios from 'axios';
    import { ref } from 'vue';
    import { useAuthStore } from '@/stores/auth';
    import { useProductStore } from '@/stores/product';
    const productStore = useProductStore()
    const authStore = useAuthStore()
    
    const form = ref({
        description:'',
        product_image:'',
        cost_price:'',
        increase:'',
        stock:''
    });
    const errors = ref([])




    const required = (v)=>{
        return !!v || 'Field is required'
      }
      const removeAlert = ()=>{
        errors.value = []
      }

   /*    const result = async()=>{
        errors.value=[];
        try {
          const res = await axios.post(`product/store`,      {
            description: form.value.description,
            product_image: 'Image',
            cost_price: form.value.cost_price,
            increase: form.value.increase,
            stock: form.value.stock 
        }, {
            headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${authStore.authToken}`
            }
  }
        
        )
          console.log(res)
        } catch (error) {

        }
     }  */


</script>