<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, onUnmounted } from 'vue'

defineProps({
    companies: Array
})

const showScrollTop = ref(false)

const onScroll = () => {
  showScrollTop.value = window.scrollY > 200
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => {
  window.addEventListener('scroll', onScroll)
})
onUnmounted(() => {
  window.removeEventListener('scroll', onScroll)
})
</script>

<template>
  <AppLayout>
    <Head title="企業一覧" />

    <div class="p-6 space-y-6">
      <h1 class="text-2xl font-bold">企業一覧</h1>

      <a
        href="/companies/create"
        class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
      >
        新規登録
      </a>

      <!-- カードリスト -->
      <div class="space-y-4">
        <a
          v-for="company in companies"
          :key="company.id"
          :href="`/companies/${company.id}`"
          class="block bg-white border border-gray-200 rounded-lg shadow-md p-4 transition hover:shadow-lg hover:bg-gray-50"
        >
          <div class="flex justify-between items-start">
        <div>
          <h2 class="text-xl font-semibold text-gray-800">{{ company.name }}</h2>
          <p class="text-sm text-gray-500 mt-1">
            ステータス:
            <span class="font-medium">{{ company.status }}</span>
          </p>
          <p class="text-sm text-gray-500 mt-1">
            メモ:
            <span class="text-gray-700">{{ company.memo || '（なし）' }}</span>
          </p>
        </div>

        <!-- 希望度スター -->
        <div class="flex items-center">
          <template v-for="i in 5" :key="`star-${company.id}-${i}`">
            <svg
          class="w-5 h-5"
          :class="i <= company.hope_level ? 'text-yellow-400' : 'text-gray-300'"
          fill="currentColor"
          viewBox="0 0 24 24"
            >
          <path
            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.95a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.95c.3.921-.755 1.688-1.538 1.118l-3.371-2.448a1 1 0 00-1.175 0l-3.371 2.448c-.783.57-1.838-.197-1.538-1.118l1.287-3.95a1 1 0 00-.364-1.118L2.22 9.377c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.95z"
          />
            </svg>
          </template>
        </div>
          </div>
        </a>
      </div>
    </div>

    <!-- スクロールトップボタン -->
    <button
      v-show="showScrollTop"
      @click="scrollToTop"
      class="fixed bottom-6 right-6 bg-blue-500 text-white p-3 rounded-full shadow-lg hover:bg-blue-600 transition-opacity duration-300"
    >
      ↑
    </button>
  </AppLayout>
</template>

<style scoped>
button[v-show] {
  transition: opacity 0.3s ease-in-out;
}
</style>
