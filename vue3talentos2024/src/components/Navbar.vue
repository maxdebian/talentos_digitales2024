
<template>
      <v-toolbar
            color="#008D36"
            dark
            prominent
        >
            <v-app-bar-nav-icon></v-app-bar-nav-icon>

            <v-toolbar-title>Talentos 2024 <span v-if="authStore.authUser">User: {{ authStore.user }}</span></v-toolbar-title>

            <v-spacer></v-spacer>

            <template v-slot:append>
                    <v-btn><RouterLink to="/">Home</RouterLink></v-btn>
                    <v-btn><RouterLink to="/about">About</RouterLink></v-btn>
                    <v-btn><RouterLink to="/login">Login</RouterLink></v-btn>
                    <v-btn><RouterLink to="/login2">Login2</RouterLink></v-btn>
                    <v-btn><RouterLink to="/register">Register</RouterLink></v-btn>
                    <v-btn><RouterLink to="/register2">Register2</RouterLink></v-btn>
                    <v-btn v-if="authStore.authUser" @click="logout">Logout</v-btn>
                    
        <!--             
                 <v-icon icon="$cancel" />
                
                 <v-btn icon="mdi-menu"></v-btn>
                <v-btn icon="mdi-magnify"></v-btn>
                <v-btn icon="mdi-dots-vertical"></v-btn>  -->
            </template>

    </v-toolbar>
<!--     <div>
       
    </div> -->
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import {useRouter} from "vue-router"
const authStore = useAuthStore();
const router = useRouter()
const logout = async () => {
    const res = await authStore.logout()
    router.push('/');
} 


</script>
