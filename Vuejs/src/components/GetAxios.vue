<template>
    <div>
        <h1>Axios</h1>
        <table class="table">
            <thead>
                <tr>
                <th scope="col">UserId</th>
                <th scope="col">Id</th>
                <th scope="col">Title</th>
                <th scope="col">Body</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="post in posts" :key="post.id">
                    <th scope="row">{{ post.userId }}</th>
                    <td>{{ post.id }}</td>
                    <td>{{ post.title }}</td>
                    <td>{{ post.body }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script setup lang="ts">
    import axios from 'axios';
    import { onBeforeMount, ref } from 'vue';
    const url = 'https://jsonplaceholder.typicode.com/posts';
    const posts = ref([]);
    /* onBeforeMount(()=>{
        axios.get(url)
            .then(resp => console.log(resp.data))
            .catch(error => console.log(error))
            .finally('finish')
    }) */
   onBeforeMount(async()=>{
        const result = await axios.get(url)
        if(result.status===200){
            posts.value = result.data;
            console.log(result)    
        }else{
            alert('Error HTTP '+result.status)
        }
   })

</script>