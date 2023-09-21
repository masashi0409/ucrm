<script setup>
import { Link } from '@inertiajs/inertia-vue3'
import { ref } from 'vue'

const title = ref('')
const content = ref('')
</script>

<template>
test<br>

<a href="/">aタグ</a><br>
<Link href="/">Link</Link><br>
<Link :href="route('inertia.index')">index</Link><br>
<Link :href="route('inertia.show', { id: 1 })">show</Link><br>

<div class="mb-8"></div>

<div>
  store<br>
  <input type="text" name="title" v-model="title">{{ title }}<br>
  <input type="text" name="content" v-model="content">{{ content }}<br>
  <Link as="button" method="post" :href="route('inertia.store')" :data="{ title: title, content: content }">save</Link>
</div>
</template>