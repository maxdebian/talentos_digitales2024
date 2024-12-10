<template>
   <v-sheet class="bg-deep-purple pa-12" rounded>
    <v-card class="mx-auto px-6 py-8" max-width="344">
      <!--   @submit="$event => authStore.login(form)" -->
      <v-form   @submit.prevent="result">
      <v-alert
            v-show="errors.length>0"
            :text="errors"
            title="Errors"
            type="error"
            :closable="removeAlert"
          ></v-alert>
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
          Sign In
        </v-btn>
        <v-btn class="mt-4"><router-link to="/register">Register</router-link></v-btn>

      </v-form>
    </v-card>
  </v-sheet>
</template>

<script setup >
import { useAuthStore } from '@/stores/auth';
import { onMounted, ref } from 'vue';
/* import Swal from 'sweetalert2' */
const url =  "http://localhost:9000/";
    const authStore = useAuthStore();
    const form = ref({email:'',password:''});
    /* const form2 = ref(null); */
    //import { urlApiServer } from '@/constApi';
    import axios from 'axios';


    /* const username= ref(null);
    const password = ref(null); */
    const loading = ref(false);
    const token = ref('')
    const errors = ref([])

    const removeAlert = ()=>{
      errors.value = []
     }
    const required = (v)=>{
        return !!v || 'Field is required'
    }
     const result = async()=>{
        errors.value=[]
        const formulario = {
          email:form.value.email,
          password:form.value.password
        }
        try {
         
          //Vue.swal('Hello Vue world!!!');
/*           console.log(formulario);
          console.log(form.value); */



          const res = await axios.post(`${url}api/login`,formulario)
         /*  const res = await axios.post(`${url}api/login`,{
              "email": "admin2212331231@example.com",
              "password": "password"
              }) */
          console.log(res.data)
          
        } catch (error) {
          if(error.response && error.response.status){
            const status = error.response.status;
            if(status==422){
              const errorServer = error.response.data.errors
              // convertimos todos los errores en un array
              console.log(error.response.data.message)
              errors.value = Object.values(errorServer).flat();
            }else if(status==401){
              errors.value = [error.response.data.message]
            }else{
              errors.value = ["Error en el server: "+status];
            }

          }else{
            errors.value=['Error inesperado'];
          }
        }
    }
    onMounted(()=>{
      //Swal.fire('GGGGGGGGGG'); 
      /*  Swal.fire({
  position: "top-end",
  icon: "success",
  title: "Your work has been saved",
  showConfirmButton: false,
  timer: 1500
}); */
 
/*  Swal.fire({
  title: "Custom animation with Animate.css",
  showClass: {
    popup: `
      animate__animated
      animate__fadeInUp
      animate__faster
    `
  },
  hideClass: {
    popup: `
      animate__animated
      animate__fadeOutDown
      animate__faster
    `
  }
}); */


   /*  Swal.fire({
          title: "¿Guardar los cambios?",
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: "Guardar",
          denyButtonText: "No guardar",
        })
        .then((result) => {
          if (result.isConfirmed) {
            Swal.fire("Guardado!", "", "success");
          } else if (result.isDenied) {
            Swal.fire("Los cambios no se han guardado", "", "info");
          }
        });
 */

    })
    /*
    



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