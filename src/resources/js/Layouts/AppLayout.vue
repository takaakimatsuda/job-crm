<script setup>
import { Head, Link } from '@inertiajs/vue3'
</script>

<template>
  <Head title="Job CRM" />

  <div class="min-h-screen bg-gray-100 text-gray-800">
    <!-- Header -->
    <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold">Job CRM</h1>
      <nav>
        <Link href="/companies" class="mr-4">企業一覧</Link>
        <Link href="/dashboard">ダッシュボード</Link>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto p-6">
      <slot />
    </main>
  </div>
</template>
