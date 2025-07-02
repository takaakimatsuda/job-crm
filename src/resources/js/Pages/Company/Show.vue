<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
  company: Object
})
</script>

<template>
  <AppLayout>
    <!-- 戻るリンク（サイドバーと揃えた位置） -->
    <div class="mt-4 mb-2 ml-6">
      <Link href="/companies" class="text-sm text-blue-600 hover:underline inline-flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        企業一覧に戻る
      </Link>
    </div>

    <!-- 企業詳細情報カード -->
    <div class="px-10 py-8 border-b bg-white">
      <h1 class="text-3xl font-bold mb-2">{{ company.name }}</h1>
      <p class="text-gray-600 mb-1">ステータス：{{ company.status }}</p>
      <p class="mb-1">
        希望度：
        <span v-for="n in company.hope_level" :key="n">⭐️</span>
      </p>
      <p v-if="company.tags?.length" class="mb-2">
        タグ：
        <span
          v-for="tag in company.tags"
          :key="tag"
          class="inline-block text-xs bg-gray-200 text-gray-800 px-2 py-1 rounded-full mr-2"
        >
          {{ tag }}
        </span>
      </p>
      <p class="mb-1">担当者：{{ company.contact_person || '未設定' }}</p>
      <p class="mb-1">
        メール：
        <a :href="`mailto:${company.email}`" class="text-blue-600 underline">
          {{ company.email || '未設定' }}
        </a>
      </p>
      <p class="mb-1">
        電話番号：
        <a :href="`tel:${company.phone}`" class="text-blue-600 underline">
          {{ company.phone || '未設定' }}
        </a>
      </p>
      <p class="mb-1">
        Webサイト：
        <a
          v-if="company.website_url"
          :href="company.website_url"
          target="_blank"
          class="text-blue-600 underline"
        >
          {{ company.website_url }}
        </a>
        <span v-else>未設定</span>
      </p>
      <p class="mt-4 text-gray-700 whitespace-pre-line">
        メモ：{{ company.memo || 'なし' }}
      </p>
    </div>

    <!-- 履歴一覧 -->
    <div class="px-10 py-8 bg-gray-100">
      <h2 class="text-xl font-bold mb-4">履歴</h2>
      <div v-if="company.interactions.length > 0">
        <div
          v-for="interaction in [...company.interactions].sort((a, b) => b.interaction_date.localeCompare(a.interaction_date))"
          :key="interaction.id"
          class="bg-white border border-gray-200 rounded-xl p-5 mb-4 shadow-md"
        >
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
              <span class="text-lg">💬</span>
              <h3 class="text-md font-semibold text-gray-900">{{ interaction.type }}</h3>
            </div>
            <div class="text-sm text-gray-500">{{ interaction.interaction_date }}</div>
          </div>
          <p class="text-sm text-gray-700 mt-2 whitespace-pre-line">
            {{ interaction.memo }}
          </p>
        </div>
      </div>
      <div v-else class="text-gray-400 text-sm">履歴はまだ登録されていません。</div>
    </div>

    <!-- 新規履歴登録 -->
    <div class="px-10 pb-10 bg-gray-100">
      <button
        class="mt-4 px-6 py-3 bg-black text-white rounded hover:bg-gray-800 transition"
        @click="() => console.log('履歴追加ボタンがクリックされました')"
      >
        ＋ 新規履歴を登録
      </button>
    </div>
  </AppLayout>
</template>
