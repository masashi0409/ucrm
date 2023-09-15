<script setup>
import { Link } from '@inertiajs/inertia-vue3';

</script>

<template>
test<br>

<a href="/">aタグ</a><br>
<Link href="/">Link</Link><br>
<Link :href="route('inertia.index')">index</Link><br>
<Link :href="route('inertia.show', { id: 1 })">show</Link><br>
</template>