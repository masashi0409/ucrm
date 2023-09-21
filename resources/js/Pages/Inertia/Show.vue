<script setup>
import { router } from '@inertiajs/vue3'

defineProps({
  id: String,
  blog: Object
})

const deleteConfirm = id => {
  console.log('delete cofirm')
  router.delete(`/inertia/${id}`, {
    onBefore: () => confirm('本当に削除しますか？')
  })
}
</script>

<template>
  <div>Inertia Show</div>
  <div>id:{{ id }}</div>
  <button @click="deleteConfirm(blog.id)">削除</button>
</template>