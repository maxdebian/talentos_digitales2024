<template>
    <div>
       <!--  <h4>Content {{ props.numero }}</h4> -->
        <slot></slot>
        <h4>Content <slot name="contetTitle"></slot></h4>
        <slot name="table"></slot>
    </div>
</template>
<script setup lang="ts">
/*     import { defineProps } from 'vue';
  const props = defineProps(['numero']) */

</script>

