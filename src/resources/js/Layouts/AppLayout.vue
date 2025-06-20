<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const sidebarOpen = ref(true)
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}
</script>

<template>
  <Head title="Job CRM" />

  <div class="min-h-screen flex bg-gray-100 text-gray-800">
    <!-- Sidebar Wrapper -->
    <Transition name="slide-fade">
      <div v-if="sidebarOpen" class="w-64 bg-white shadow-md h-full fixed z-50 transition-all duration-300">
        <!-- Sidebar content -->
        <div class="p-6 text-xl font-bold border-b flex justify-between items-center">
          Job CRM
          <button @click="toggleSidebar" class="text-sm text-gray-500 hover:text-gray-800">
            ◀︎
          </button>
        </div>
        <nav class="mt-4 space-y-2 px-4">
          <Link href="/companies" class="block px-2 py-2 rounded hover:bg-gray-100">企業一覧</Link>
          <Link href="/dashboard" class="block px-2 py-2 rounded hover:bg-gray-100">ダッシュボード</Link>
        </nav>
      </div>
    </Transition>

    <!-- Sidebar collapsed trigger -->
    <div
      v-if="!sidebarOpen"
      class="w-6 h-full bg-white shadow-md fixed z-50 flex items-start justify-center pt-6"
    >
      <button @click="toggleSidebar" class="text-sm text-gray-500 hover:text-gray-800">
        ▶︎
      </button>
    </div>

    <!-- Main Content -->
    <div :class="[sidebarOpen ? 'ml-64' : 'ml-6']" class="flex-1 transition-all duration-300">
      <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Job CRM</h1>
        <!-- モバイル用ボタン（必要に応じて） -->
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
