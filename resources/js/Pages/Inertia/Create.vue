<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

defineProps({
  errors: Object
})

const form = reactive({
  title: null,
  content: null
})

const submitFunction = () => {
  router.post('/inertia', form)
}
</script>

<template>
<form @submit.prevent="submitFunction">
  title: <input type="text" name="title" v-model="form.title"><br>
  content: <input type="text" name="content" v-model="form.content"><br>
  <div v-if="errors.title" style="color:rgb(175, 57, 57)">{{ errors.title }}</div>
  <div v-if="errors.content" style="color:rgb(175, 57, 57)">{{ errors.content }}</div>
  <button>送信</button>
</form>
</template>