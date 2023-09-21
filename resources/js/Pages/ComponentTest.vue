<script setup>
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const emitTest = e => console.log(e)
</script>

<template>
<GuestLayout>
  <InputLabel>タイトル</InputLabel>
  <TextInput modelValue="初期値" @update:modelValue="emitTest"></TextInput>
</GuestLayout>
</template>