<template>
    <div>
        <h1>Prop</h1>
        <p>{{ nombre }} - {{ edad }}</p>
    </div>
</template>
<script setup lang="ts">
    import { defineProps } from 'vue';
   /*  defineProps(['nombre','edad']) */
   defineProps({
        nombre:{
            type:String,
            required:true
        },
        edad:{
            type:Number,
            required:false
        }
   })
</script>