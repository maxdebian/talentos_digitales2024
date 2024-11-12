<template>
    <div>
        <h1>Fetch</h1>
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
    import { onBeforeMount, onMounted, ref } from 'vue';
    const posts = ref([]);
   /*  onBeforeMount(()=>{
        //fetch('https://jsonplaceholder.typicode.com/posts').then(respuesta => respuesta.json())
        fetch('https://jsonplaceholder.typicode.com/posts')
            .then(respuesta => console.log(respuesta.json()))       
    }) */
   onBeforeMount(async ()=>{
        const result = await fetch('https://jsonplaceholder.typicode.com/posts');
        /* alert('completo') */
        if(result.ok){
            const postsData = await result.json();
            posts.value = postsData;
            //console.log(postsData)

        }else{
            alert('Error HTTP '+result.status)
        }
   })
   onMounted(()=>{
    fetch('https://jsonplaceholder.typicode.com/posts/1')
        .then((response) => response.json())
        .then((json) => console.log(json));
   })
</script>