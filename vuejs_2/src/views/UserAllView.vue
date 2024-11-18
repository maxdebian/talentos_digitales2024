<template>
    <div>
        <h1>User</h1>
        
        <ul class="list-group">
            <li class="list-group-item" v-for="user in users" :key="user.id">
                {{ user.email }} - {{user.first_name}} - {{ user.last_name }}                 
                <router-link :to="`/usuarios-detalle/${user.id}/${user.first_name}`">Show</router-link>
                <router-link :to="{name:'userDetail',params:{userId:user.id,firstName:user.first_name}}">Show 2</router-link>
            </li>
        </ul>
    </div>
</template>
<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import type {IUser} from '@/types'
    const users = ref<IUser[]>([]);
    onMounted(async()=>{
        const result = await fetch('https://reqres.in/api/users/')
        const data = await result.json()
        users.value = data.data
    })
</script>
<style scoped>
ul li{
    list-style: none;
}
</style>