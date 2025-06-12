<script setup>
import { Head } from '@inertiajs/vue3'

// Laravel側から渡されたデータを受け取る
defineProps({
    companies: Array
})
</script>

<template>
    <Head title="企業一覧" />

    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">企業一覧</h1>
        <a
            href="/companies/create"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
        >
            新規登録
        </a>

        <table class="table-auto w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border">ID</th>
                    <th class="px-4 py-2 border">企業名</th>
                    <th class="px-4 py-2 border">ステータス</th>
                    <th class="px-4 py-2 border">希望度</th>
                    <th class="px-4 py-2 border">メモ</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="company in companies" :key="company.id">
                    <td class="px-4 py-2 border">{{ company.id }}</td>
                    <td class="px-4 py-2 border">{{ company.name }}</td>
                    <td class="px-4 py-2 border">{{ company.status }}</td>
                    <td class="px-4 py-2 border">{{ company.hope_level }}</td>
                    <td class="px-4 py-2 border">{{ company.memo }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
