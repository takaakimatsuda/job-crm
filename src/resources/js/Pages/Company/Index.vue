<script setup>
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  companies: Array,
  filters: Object,
  availableTags: Array,
})

const searchName = ref(props.filters?.name || '')
const searchStatus = ref(props.filters?.status || '')
const searchHopeLevel = ref(props.filters?.hope_level || '')
const selectedTags = ref(props.filters?.tags || [])
const showScrollTop = ref(false)

const toggleTag = (tagName) => {
  const index = selectedTags.value.indexOf(tagName)
  if (index > -1) {
    selectedTags.value.splice(index, 1)
  } else {
    selectedTags.value.push(tagName)
    selectedTags.value = [...new Set(selectedTags.value)] // ← 重複排除
  }
  applySearch()
}

const applySearch = () => {
  router.get(
    '/companies',
    {
      name: searchName.value,
      status: searchStatus.value,
      hope_level: searchHopeLevel.value,
      tags: selectedTags.value,
    },
    {
      preserveState: true,
      replace: true,
      preserveScroll: true
    }
  )
}

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
      <!-- タグフィルタ -->
      <div class="flex flex-wrap gap-2 mb-4">
        <button
          v-for="tag in availableTags"
          :key="tag.id"
          @click="toggleTag(tag.name)"
          :class="[
            'px-3 py-1 rounded-full text-sm font-medium border transition',
            selectedTags.includes(tag.name)
              ? 'bg-blue-100 text-blue-700 border-blue-400'
              : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'
          ]"
        >
          {{ tag.name }}
        </button>
      </div>

      <!-- 選択中のタグ表示 -->
      <div v-if="selectedTags.length" class="mb-2 text-sm text-gray-600">
        選択中: {{ selectedTags.join(', ') }}
      </div>

      <!-- 検索フォーム -->
      <div class="bg-gray-100 p-4 rounded-lg space-y-4">
        <div class="flex flex-wrap gap-4 items-center">
          <input
            v-model="searchName"
            type="text"
            placeholder="企業名で検索"
            class="px-3 py-2 border border-gray-300 rounded"
          />
          <select v-model="searchStatus" class="px-3 py-2 border border-gray-300 rounded">
            <option value="">ステータス</option>
            <option value="未応募">未応募</option>
            <option value="面接中">面接中</option>
            <option value="内定">内定</option>
            <option value="辞退">辞退</option>
          </select>
          <select v-model="searchHopeLevel" class="px-3 py-2 border border-gray-300 rounded">
            <option value="">希望度</option>
            <option v-for="i in 5" :key="i" :value="i">{{ i }} ★</option>
          </select>
          <div class="ml-auto">
            <a
              href="/companies/create"
              class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
            >新規登録</a>
          </div>
        </div>
      </div>

      <!-- カード一覧 -->
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
              <p class="text-sm text-gray-500 mt-1">ステータス: <span class="font-medium">{{ company.status }}</span></p>
              <p class="text-sm text-gray-500 mt-1">メモ: <span class="text-gray-700">{{ company.memo || '（なし）' }}</span></p>
            </div>
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

    <!-- トップへ戻るボタン -->
    <button
      v-show="showScrollTop"
      @click="scrollToTop"
      class="fixed bottom-6 right-6 bg-blue-500 text-white p-3 rounded-full shadow-lg hover:bg-blue-600 transition-opacity duration-300"
    >↑</button>
  </AppLayout>
</template>
