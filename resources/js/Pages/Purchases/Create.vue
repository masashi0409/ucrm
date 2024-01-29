<script setup>
import { getToday } from '@/common'
import { onMounted, reactive, ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia';


// controllerから渡ってくる
const props = defineProps({
    'customers': Array,
    'items': Array
})

const itemList = ref([]) // itemのlistにquantityを持たせたい

const form = reactive({
    date: null,
    customer_id: null,
    status: true,
    items: []
})

const quantity= ["0", "1", "2", "3"]

onMounted(() => {
    form.date = getToday() // yyyy-mm-dd

    props.items.forEach(item => {
        itemList.value.push({
            id: item.id,
            name: item.name,
            price: item.price,
            quantity: 0
        })
    })
})

const totalPrice = computed(() => {
    let total = 0
    itemList.value.forEach(item => {
        total += item.price * item.quantity
    })
    return total
})

const storePurchase = () => {

    itemList.value.forEach( item => {
        if (item.quantity > 0) {
            form.items.push({
                id: item.id,
                quantity: item.quantity
            })
        }
    })

    console.log(form)

    Inertia.post(route('purchases.store'), form)

    console.log('purchase store')
}
</script>

<template>

    <Head title="Purchase" />

    <form @submit.prevent="storePurchase">
        日付：
        <input type="date" name="date" v-model="form.date"><br>
        会員：
        <select name="customer" v-model="form.customer_id">
            <option v-for="customer in customers" :value="customer.id" :key="customer.id">
                {{ customer.id }} : {{ customer.name }}
            </option>
        </select><br>
        商品・サービス
        <table>
            <thead>
                <tr>
                    <th>id</th>
                    <th>商品名</th>
                    <th>金額</th>
                    <th>数量</th>
                    <th>小計</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in itemList" >
                    <td>{{ item.id }}</td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.price }}</td>
                    <td>
                        <select name="quantity" v-model="item.quantity">
                            <option v-for="q in quantity" :value="q">{{ q }}</option>
                        </select>
                    </td>
                    <td>
                        {{ item.price * item.quantity }}
                    </td>
                </tr>
            </tbody>
        </table>
        合計 {{ totalPrice }} 円<br>
        <button>store</button>
    </form>
</template>