<template>
    <div>
        <h3> quien sos? {{ saludo }}</h3>
    </div>
</template>
<script setup lang="ts">
    import {ref} from 'vue';
    const saludo = ref("Hola Soy Maxi");

</script>