<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'

// ここでは仮の履歴データ
const recentUpdates = [
  { id: 1, company: '株式会社テスト', type: '面談', date: '2025-07-09', content: '採用担当と初回面談を実施' },
  { id: 2, company: 'Remote Dreams LLC', type: 'メール', date: '2025-07-08', content: '履歴書を送付しました' },
  { id: 3, company: 'Tech Innovators Inc.', type: '電話', date: '2025-07-07', content: '次回面接日程を確認しました' },
]
</script>

<template>
  <Head title="ダッシュボード" />
  <AppLayout>
    <div class="px-10 py-8 bg-gray-100 min-h-screen">
      <!-- サマリー -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-xl shadow">
          <p class="text-sm text-gray-500">登録企業数</p>
          <p class="text-2xl font-bold">12社</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow">
          <p class="text-sm text-gray-500">面談数</p>
          <p class="text-2xl font-bold">3件</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow">
          <p class="text-sm text-gray-500">内定獲得</p>
          <p class="text-2xl font-bold">1社</p>
        </div>
      </div>

      <!-- タグ -->
      <div class="mb-8">
        <h2 class="text-lg font-semibold mb-2">タグで絞り込み</h2>
        <div class="flex flex-wrap gap-2">
          <span class="bg-gray-200 text-sm text-gray-700 px-3 py-1 rounded-full cursor-pointer">React</span>
          <span class="bg-gray-200 text-sm text-gray-700 px-3 py-1 rounded-full cursor-pointer">PHP</span>
          <span class="bg-gray-200 text-sm text-gray-700 px-3 py-1 rounded-full cursor-pointer">リモート</span>
          <span class="bg-gray-200 text-sm text-gray-700 px-3 py-1 rounded-full cursor-pointer">出社</span>
          <span class="bg-gray-200 text-sm text-gray-700 px-3 py-1 rounded-full cursor-pointer">スタートアップ</span>
        </div>
      </div>

      <!-- タイムライン -->
      <div>
        <h2 class="text-lg font-semibold mb-4">最近の更新履歴</h2>
        <ul class="relative border-l border-gray-300 pl-4">
          <li v-for="item in recentUpdates" :key="item.id" class="mb-8 ml-4">
            <div class="absolute w-3 h-3 bg-blue-500 rounded-full -left-1.5 top-1"></div>
            <time class="text-sm text-gray-500">{{ item.date }}</time>
            <p class="text-md font-semibold text-gray-900 mt-1">{{ item.company }}（{{ item.type }}）</p>
            <p class="text-sm text-gray-700 mt-1">{{ item.content }}</p>
          </li>
        </ul>
      </div>
    </div>
  </AppLayout>
</template>
