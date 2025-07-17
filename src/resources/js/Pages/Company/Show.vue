<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const { company } = defineProps({
  company: Object
})

const formVisible = ref(false)
const editingId = ref(null)

const form = ref({
  interaction_date: new Date().toISOString().slice(0, 10),
  type: '',
  memo: ''
})

const editForm = useForm({
  interaction_date: '',
  type: '',
  memo: ''
})

const submit = () => {
  router.post(`/companies/${company.id}/interactions`, form.value, {
    onSuccess: () => {
      formVisible.value = false
      form.value = {
        interaction_date: new Date().toISOString().slice(0, 10),
        type: '',
        memo: ''
      }
    }
  })
}

const startEdit = (interaction) => {
  editingId.value = interaction.id
  editForm.interaction_date = interaction.interaction_date
  editForm.type = interaction.type
  editForm.memo = interaction.memo
}

const cancelEdit = () => {
  editingId.value = null
  editForm.reset()
}

const submitEdit = (interactionId) => {
  editForm.put(`/interactions/${interactionId}`, {
    onSuccess: () => {
      editingId.value = null
    }
  })
}

const deleteInteraction = (interactionId) => {
  if (confirm('この履歴を削除してもよろしいですか？')) {
    router.delete(`/interactions/${interactionId}`)
  }
}
</script>

<template>
  <AppLayout>
    <!-- 戻るリンク -->
    <div class="mt-4 mb-2 ml-6">
      <Link href="/companies" class="text-sm text-blue-600 hover:underline inline-flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        企業一覧に戻る
      </Link>
    </div>

    <!-- 企業詳細 -->
    <div class="relative px-10 py-8 border-b bg-white">
      <Link
        :href="`/companies/${company.id}/edit`"
        class="absolute top-4 right-4 px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition"
      >
        編集する
      </Link>

      <h1 class="text-3xl font-bold mb-2">{{ company.name }}</h1>
      <p class="text-gray-600 mb-1">ステータス：{{ company.status }}</p>
      <p class="mb-1">
        希望度：<span v-for="n in company.hope_level" :key="n">⭐️</span>
      </p>
      <p v-if="company.tags?.length" class="mb-2">
        タグ：
        <span v-for="tag in company.tags" :key="tag.id" class="inline-block text-xs bg-gray-200 text-gray-800 px-2 py-1 rounded-full mr-2">{{ tag.name }}</span>
      </p>
      <p class="mb-1">担当者：{{ company.contact_person || '未設定' }}</p>
      <p class="mb-1">
        メール：<a :href="`mailto:${company.email}`" class="text-blue-600 underline">{{ company.email || '未設定' }}</a>
      </p>
      <p class="mb-1">
        電話番号：<a :href="`tel:${company.phone}`" class="text-blue-600 underline">{{ company.phone || '未設定' }}</a>
      </p>
      <p class="mb-1">
        Webサイト：
        <a v-if="company.website_url" :href="company.website_url" target="_blank" class="text-blue-600 underline">{{ company.website_url }}</a>
        <span v-else>未設定</span>
      </p>
      <p class="mt-4 text-gray-700 whitespace-pre-line">メモ：{{ company.memo || 'なし' }}</p>
    </div>

    <!-- 履歴一覧 -->
    <div class="px-10 py-8 bg-gray-100">
      <h2 class="text-xl font-bold mb-4">履歴</h2>
      <div v-if="company.interactions.length > 0">
        <div v-for="interaction in [...company.interactions].sort((a, b) => b.interaction_date.localeCompare(a.interaction_date))" :key="interaction.id" class="bg-white border border-gray-200 rounded-xl p-5 mb-4 shadow-md relative">
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
              <span class="text-lg">💬</span>
              <h3 class="text-md font-semibold text-gray-900">{{ interaction.type }}</h3>
            </div>
            <div class="text-sm text-gray-500">
              {{
                new Date(interaction.interaction_date).toLocaleString('ja-JP', {
                  year: 'numeric',
                  month: '2-digit',
                  day: '2-digit',
                  hour: '2-digit',
                  minute: '2-digit',
                  hour12: false,
                  timeZone: 'Asia/Tokyo'
                })
              }}
            </div>
          </div>

          <div v-if="editingId === interaction.id" class="mt-2 space-y-2">
            <input v-model="editForm.interaction_date" type="date" class="border rounded px-2 py-1 w-full" />
            <select v-model="editForm.type" class="border rounded px-2 py-1 w-full">
              <option value="電話">電話</option>
              <option value="面談">面談</option>
              <option value="メール">メール</option>
            </select>
            <textarea v-model="editForm.memo" rows="3" class="border rounded px-2 py-1 w-full"></textarea>
            <div class="flex gap-2 mt-2">
              <button @click="submitEdit(interaction.id)" class="px-3 py-1 bg-blue-600 text-white rounded">保存</button>
              <button @click="cancelEdit" class="px-3 py-1 bg-gray-300 rounded">キャンセル</button>
            </div>
          </div>

          <div v-else class="mt-2 text-sm text-gray-700 whitespace-pre-line">
            {{ interaction.memo }}
            <div class="absolute bottom-3 right-4 flex gap-2">
              <!-- 編集アイコン -->
              <button @click="startEdit(interaction)" class="text-gray-500 hover:text-blue-600 transition" title="編集">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
              </button>
              <!-- 削除アイコン -->
              <button @click="() => deleteInteraction(interaction.id)" class="text-gray-500 hover:text-red-500 transition" title="削除">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="text-gray-400 text-sm">履歴はまだ登録されていません。</div>
    </div>

    <!-- 新規履歴登録フォーム -->
    <div class="px-10 pb-10 bg-gray-100">
      <button v-if="!formVisible" class="mt-4 px-6 py-3 bg-black text-white rounded hover:bg-gray-800 transition" @click="formVisible = true">
        ＋ 新規履歴を登録
      </button>

      <div v-else class="bg-white border p-6 rounded-xl shadow-md mt-4">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">日付</label>
          <input type="date" v-model="form.interaction_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">種別</label>
          <select v-model="form.type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option disabled value="">選択してください</option>
            <option>電話</option>
            <option>面談</option>
            <option>メール</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">メモ</label>
          <textarea v-model="form.memo" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
        </div>
        <div class="flex gap-3">
          <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500" @click="submit">登録する</button>
          <button class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400" @click="formVisible = false">キャンセル</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>