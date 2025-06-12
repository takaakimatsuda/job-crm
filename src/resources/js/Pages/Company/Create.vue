<template>
  <div class="max-w-2xl mx-auto p-6 bg-white shadow rounded">
    <h1 class="text-2xl font-bold mb-6">企業を登録</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">企業名</label>
        <input
          v-model="form.name"
          type="text"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
          required
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">ステータス</label>
        <input
          v-model="form.status"
          type="text"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">希望度（1〜5）</label>
        <input
          v-model="form.hope_level"
          type="number"
          min="1"
          max="5"
          class="mt-1 block w-24 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">メモ</label>
        <textarea
          v-model="form.memo"
          rows="4"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
        ></textarea>
      </div>

      <div class="pt-4">
        <button
          type="submit"
          class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none"
        >
          登録
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  status: '',
  hope_level: '',
  memo: '',
})

const submit = () => {
  form.post('/companies')
}
</script>
