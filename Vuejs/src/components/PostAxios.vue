<template>
    <div>
        <h1>Post Axios</h1>
    </div>
</template>
<script setup lang="ts">
    import axios from 'axios';
    import { onBeforeMount } from 'vue';
    const url = 'https://jsonplaceholder.typicode.com/posts';
    onBeforeMount(()=>{
        const post = {
            title:'Mi Titulo',
            body:'Estamos probando el post'
        }
        axios.post(url,post).then(res => console.log(res))
    })
</script>
