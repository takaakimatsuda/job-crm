<script setup>
import { Head } from '@inertiajs/vue3'

// ✅ あなたの共通レイアウトに差し替え
import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import DeleteUserForm from './Partials/DeleteUserForm.vue'

defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
})
</script>

<template>
  <Head title="プロフィール" />

  <!-- AppLayout 側の <main class="max-w-7xl mx-auto p-6"> に入る想定 -->
  <div class="space-y-6">
    <h2 class="text-xl font-bold">プロフィール</h2>

    <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
      <UpdateProfileInformationForm
        :must-verify-email="mustVerifyEmail"
        :status="status"
        class="max-w-xl"
      />
    </div>

    <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
      <UpdatePasswordForm class="max-w-xl" />
    </div>

    <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
      <DeleteUserForm class="max-w-xl" />
    </div>
  </div>
</template>
