<template>
    <v-sheet class="bg-deep-purple pa-12" rounded>
     <v-card class="mx-auto px-6 py-8" max-width="644">
         <!-- @submit="$event => authStore.register(form)" -->
       <v-form
       @submit.prevent="result"
       >
       <v-alert v-if="errors.length>0"
            :text="errors"
            title="Errors"
            type="error"
            :closable="removeAlert"
          ></v-alert>
         <v-text-field
           v-model="form.name"
           :readonly="loading"
           :rules="[required]"
           class="mb-2"
           label="Name"
           clearable
         ></v-text-field>
         <v-text-field
           v-model="form.email"
           :readonly="loading"
           :rules="[required]"
           class="mb-2"
           label="Email"
           clearable
         ></v-text-field>
 
         <v-text-field
         type="password"
           v-model="form.password"
           :readonly="loading"
           :rules="[required]"
           label="Password"
           placeholder="Enter your password"
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
           Send
         </v-btn>
 
       </v-form>
      
      
      
     </v-card>
   </v-sheet>
</template>

<script setup>
 import { useAuthStore } from '@/stores/auth';
 import { onMounted, ref } from 'vue';
 
     const authStore = useAuthStore();
     const form = ref({name:'',email:'',password:''});
     //import { urlApiServer } from '@/constApi';
     /* import axios from 'axios'; */
 
 
     /* const username= ref(null);
     const password = ref(null); */
     const loading = ref(false);
     const token = ref('')
      const errors = ref([])
  /*   const removeError = ()=>{
      errors.value=[];
    } */
     const required = (v)=>{
         return !!v || 'Field is required'
     }
     const removeAlert = ()=>{
      errors.value = []
     }
    const result = async()=>{
        errors.value=[];
        try {
          const res = await axios.post(`http://localhost:9000/api/register`,form.value)
          console.log(res.data)
        } catch (error) {
          if(error.response && error.response.status){
            const status = error.response.status;
            if(status==422){
              const errorServer = error.response.data.errors
              // convertimos todos los errores en un array
              console.log(error.response.data.message)
              errors.value = Object.values(errorServer).flat();
            }else{
              errors.value = ["Error en el server: "+status];
            }

          }else{
            errors.value=['Error inesperado'];
          }
                 
        }
        
        /*          const res = await axios.post(`http://localhost:9000/api/register`,{
             "name": "testmax2",
             "email":"test2@testvue.com",
             "password": "password"
             })

         console.log(res.data) */
     } /*
     onMounted(()=>{
     })
     
 
 
 
     const testLogin = async()=>{
         const params2 = {
             headers:{
                 'Authorization':'Bearer '+token.value
             }
         }
         const res = await axios.get(`${urlApiServer}/auth/authenticated_user`,params2)
         console.log(res.data)
     } */
 </script>