<template>
   <v-sheet class="bg-deep-purple pa-12" rounded>
    <v-card class="mx-auto px-6 py-8" max-width="344">
  
     <!--  <v-form   @submit.prevent="$event => authStore.login(form)"> -->
      <v-form   @submit.prevent="result">
     <!--  <v-alert
            v-show="errors.length>0"
            :text="errors"
            title="Errors"
            type="error"
            :closable="removeAlert"
          ></v-alert> -->
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
        <v-btn><router-link to="/register">Register</router-link></v-btn>

      </v-form>
    </v-card>
  </v-sheet>
</template>

<script setup lang="ts">
import {useRouter} from "vue-router"
import { useAuthStore } from '@/stores/auth';
  import { onMounted, ref } from 'vue';
  const router = useRouter()
  const authStore = useAuthStore();
  const form = ref({email:'',password:''});
  const loading = ref(false)
  const errors = ref([])
  const required = (v)=>{
        return !!v || 'Field is required'
    }
  const result = async ()=>{
    const res = await authStore.login(form.value)
   /*  console.log(res) */
    
     if(res){
      //Swal.fire('Login Successfully')
      router.push('/')
    }else{
      //Swal.fire('Error')
      router.push('/login2');
    } 
  }
  
/*   onMounted(()=>{
    
  }) */
</script>