<script setup>
import { onMounted, ref, reactive } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashMessage from '../../Components/FlashMessage.vue'
import Pagination from '@/Components/Pagination.vue'
import axios from 'axios';

const props = defineProps({
    totals: Object
})

onMounted( () =>{
    console.log(props.totals.data)
})

</script>
<template>

</template>