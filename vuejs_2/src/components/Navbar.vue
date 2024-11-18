<template>
    <div>
        <nav>
            <!-- <RouterLink to="/">Home</RouterLink>
            <RouterLink to="/about">About</RouterLink> -->
            <!-- <a href="/">Home</a>
            <a href="/about">About</a> -->
            <router-link to="/">Home</router-link>
            <router-link to="/counter">Counter</router-link>
            <router-link :to="{name:'about'}">About</router-link>
            <router-link :to="{name:'users'}">User List</router-link>
        </nav>
    </div>
</template>

<script setup lang="ts">
</script>