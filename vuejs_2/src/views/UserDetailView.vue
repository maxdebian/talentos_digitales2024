<template>
    <div>
        <h1>Detail</h1>
        <div class="card" style="width: 18rem;">
            <img :src="user?.avatar" alt="avatar" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">First Name: {{ user?.first_name }}, Last Name: {{ user?.last_name }}</h5>
                <p class="card-text">Email: {{ user?.email }}</p>
                <button class="btn btn-primary" @click="returnPage">
                    Return
                </button>
                <button @click="goHome">
                    Home
                </button>
            </div>
        </div>        
    </div>
</template>

<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import { useRoute, useRouter } from 'vue-router'
    import type {IUser} from '@/types'
    const user = ref<IUser | null>(null)
    const route = useRoute();
    const router = useRouter();
    //console.log(route.params);
    onMounted(async()=>{
        const result = await fetch(`https://reqres.in/api/users/${route.params.userId}`)
        const {data} = await result.json();
        //console.log(data)
        user.value = data
    })
    const returnPage = ()=>{
        router.back();
    }
    const goHome = ()=>{
        router.push('/')
    }
</script>
