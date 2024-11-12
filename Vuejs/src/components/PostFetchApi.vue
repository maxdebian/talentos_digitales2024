<template>
    <div>
        <h1>Fetch</h1>
       
    </div>
</template>

<script setup lang="ts">
import { onBeforeMount } from 'vue';

    //const result = await fetch('https://jsonplaceholder.typicode.com/posts');
    onBeforeMount(()=>{
        const url = 'https://jsonplaceholder.typicode.com/posts';
        const texto = {name:'Maxi'}
        const post = {
            method:'post',
            body:JSON.stringify(texto),
            headers:{
                'Content-type':'application/json'
            }
        }
        fetch(url,post)
            .then(resp => console.log(resp.json()))

    })
</script>