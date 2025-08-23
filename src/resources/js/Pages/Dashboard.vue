<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  statusCounts: Object,
  recentUpdates: Array,
})

// 総企業数を集計（statusCounts の合計）
const totalCompanies = computed(() =>
  Object.values(props.statusCounts).reduce((sum, v) => sum + v, 0)
)

// 「内定獲得」件数は、status = '内定' のカウントを想定
const offerCount = computed(() => props.statusCounts['内定'] ?? 0)
</script>

<template>
  <Head title="ダッシュボード" />
  <AppLayout>
    <div class="px-10 py-8 bg-gray-100 min-h-screen">
      <!-- サマリー -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- 登録企業数 -> /companies -->
        <Link href="/companies" class="block" aria-label="企業一覧へ">
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-md cursor-pointer transition">
            <p class="text-sm text-gray-500">登録企業数</p>
            <p class="text-2xl font-bold">{{ totalCompanies }}社</p>
          </div>
        </Link>

        <!-- 面接中 -> /companies?status=面接中 -->
        <Link
          :href="route('companies.index', { status: '面接中' })"
          class="block"
          aria-label="面接中の企業一覧へ"
        >
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-md cursor-pointer transition">
            <p class="text-sm text-gray-500">面接中</p>
            <p class="text-2xl font-bold">{{ statusCounts['面接中'] ?? 0 }}社</p>
          </div>
        </Link>

        <!-- 内定獲得 -> /companies?status=内定 -->
        <Link
          :href="route('companies.index', { status: '内定' })"
          class="block"
          aria-label="内定獲得の企業一覧へ"
        >
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-md cursor-pointer transition">
            <p class="text-sm text-gray-500">内定獲得</p>
            <p class="text-2xl font-bold">{{ offerCount }}社</p>
          </div>
        </Link>
      </div>

      <!-- タイムライン -->
      <div>
        <h2 class="text-lg font-semibold mb-4">最近の更新履歴</h2>
        <ul class="relative border-l border-gray-300 pl-4">
          <li v-for="item in recentUpdates" :key="item.id" class="mb-8 ml-4">
            <div class="absolute w-3 h-3 bg-blue-500 rounded-full -left-1.5 top-1"></div>
            <time class="text-sm text-gray-500">{{ item.date }}</time>
            <p class="text-md font-semibold text-gray-900 mt-1">
              {{ item.company_name }}（{{ item.type }}）
            </p>
            <p class="text-sm text-gray-700 mt-1">{{ item.memo }}</p>
          </li>
        </ul>
      </div>
    </div>
  </AppLayout>
</template>
