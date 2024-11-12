<template>
    <div>
        <h1>Inputs</h1>
        <br>

        <div class="mb-3">
            <label for="Nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" v-model="nombre" id="Nombre" placeholder="name@example.com">
        </div>
        <div class="mb-3">
            <label class="form-label">Example textarea</label>
            <textarea class="form-control" rows="3" :value="textArea"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Example checkbox</label>
            <br>
            <label for="">Maxi</label>
            <input type="checkbox" v-model="checkboxModel" value="Maxi" id="">
            <br>
            <label for="">Maria</label>
            <input type="checkbox" v-model="checkboxModel" value="Maria" id="">
            <br>
            <label for="">Juan</label>
            <input type="checkbox" v-model="checkboxModel" value="Juan" id="">
        </div>
        <div class="mb-3">
            <label class="form-label">Example radio button</label>
            <br>
            <label for="">Maxi</label>
            <input type="radio" v-model="radioModel" value="Maxi" id="">
            <br>
            <label for="">Maria</label>
            <input type="radio" v-model="radioModel" value="Maria" id="">
            <br>
            <label for="">Juan</label>
            <input type="radio" v-model="radioModel" value="Juan" id="">
        </div>
        <div class="mb-3">
            <label class="form-label">Example select</label>
            <br>
            <select v-model="selectModel" id="">
                <option value="0">Select language</option>
                <option value="1">Laravel</option>
                <option value="2">VueJs</option>
                <option value="3">Javascript</option>
                <option value="4">GitHub</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Example select Multiple</label>
            <br>
            
            <select class="form-select" v-model="selectMultipleModel" multiple aria-label="Multiple select example">
                <option v-for="option in options" :key="option.id" :value="option.id">{{ option.description }}</option>
            </select>
        </div>
      <!--        <div class="mb-3">
            <label class="form-label">Example textarea</label>
        </div>
        <div class="mb-3">
            <label class="form-label">Example textarea</label>
        </div>
        <div class="mb-3">
            <label class="form-label">Example textarea</label>
        </div> -->


        <h3>Resultados</h3>
        <p>{{ nombre }}</p>
        <p>{{ checkboxModel }}</p>
        <p>{{ radioModel }}</p>
        <p>{{ selectModel }}</p>
        <p>{{ selectMultipleModel }}</p>

    </div>
</template>
<script setup lang="ts">
    import { ref } from 'vue';

    const nombre = ref<string>('');
    const textArea = ref<string>('Soy un text area');
    const checkboxModel = ref<string[]>([]);
    const radioModel = ref<string>('Juan');
    const selectModel = ref<number>(0);
    const selectMultipleModel = ref<number[]>([]);
    const options = ref([
        {
          id:1,
          description:'Laravel'  
        },
        {
          id:2,
          description:'VueJs'  
        },
        {
          id:3,
          description:'JavaScript'  
        },
        {
          id:4,
          description:'GitHub'  
        }
    ])
</script>
<style scoped>

</style>