<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const page = usePage()

const sidebarOpen = ref(true)
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const userMenuOpen = ref(false)
const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

// ダミーユーザー（実際は props で受け取る）
const user = {
  name: 'matsudatakaaki@example.com',
}

// 現在のページ名を動的に表示
const pageTitle = computed(() => {
  const url = page.url
  if (url.startsWith('/companies/create')) return '企業登録'
  if (url.startsWith('/companies')) return '企業一覧'
  if (url.startsWith('/dashboard')) return 'ダッシュボード'
  return 'Job CRM'
})
</script>

<template>
  <Head title="Job CRM" />

  <div class="min-h-screen flex bg-gray-100 text-gray-800 relative">
    <!-- Sidebar -->
    <Transition name="slide-fade">
      <div
        v-if="sidebarOpen"
        class="w-64 bg-white shadow-md h-full fixed z-40 transition-all duration-300"
      >
        <div class="p-6 text-xl font-bold border-b flex justify-between items-center">
          Job CRM
          <button @click="toggleSidebar" class="text-sm text-gray-500 hover:text-gray-800">
            ◀︎
          </button>
        </div>
        <nav class="mt-4 space-y-2 px-4">
          <Link href="/dashboard" class="block px-2 py-2 rounded hover:bg-gray-100">ダッシュボード</Link>
          <Link href="/companies" class="block px-2 py-2 rounded hover:bg-gray-100">企業一覧</Link>
        </nav>
      </div>
    </Transition>

    <!-- Collapsed Sidebar Button -->
    <div
      v-if="!sidebarOpen"
      class="w-6 h-full bg-white shadow-md fixed z-30 flex items-start justify-center pt-6"
    >
      <button @click="toggleSidebar" class="text-sm text-gray-500 hover:text-gray-800">▶︎</button>
    </div>

    <!-- Main Content -->
    <div
      :class="[sidebarOpen ? 'ml-64' : 'ml-6']"
      class="flex-1 transition-all duration-300 relative"
    >
      <header class="bg-white shadow px-6 py-4 flex justify-between items-center relative z-20">
        <!-- 現在のページ名を表示 -->
        <h1 class="text-xl font-bold">
          {{ pageTitle }}
        </h1>

        <!-- ユーザーメニュー -->
        <div class="relative z-30">
          <button @click="toggleUserMenu" class="text-gray-600 hover:text-gray-800">
            <!-- ユーザーアイコン -->
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="w-8 h-8"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5.121 17.804A9 9 0 1118.88 6.196 9 9 0 015.121 17.804zM12 14a4 4 0 100-8 4 4 0 000 8zm0 2c-2.21 0-4.2 1.045-5.48 2.705A7.956 7.956 0 0012 20a7.956 7.956 0 005.48-1.295A6.97 6.97 0 0012 16z"
              />
            </svg>
          </button>

          <!-- ドロップダウン -->
          <div
            v-show="userMenuOpen"
            class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-lg z-40"
          >
            <div class="px-4 py-2 border-b text-sm text-gray-700">{{ user.name }}</div>
            <Link href="/profile" class="block px-4 py-2 text-sm hover:bg-gray-100">プロフィール</Link>
            <form method="POST" action="/logout" class="block">
              <button
                type="submit"
                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
              >
                ログアウト
              </button>
            </form>
          </div>
        </div>
      </header>

      <main class="max-w-7xl mx-auto p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  opacity: 0;
  transform: translateX(-100%);
}
</style>
