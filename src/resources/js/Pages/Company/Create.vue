<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const form = useForm({
  name: '',
  status: '',
  hope_level: 0,
  tags: [],
  contact_person: '',
  email: '',
  phone: '',
  website_url: '',
  memo: ''
})

const hoverRating = ref(0)
const newTag = ref('')

const setRating = (value) => form.hope_level = value
const setHover = (value) => hoverRating.value = value
const clearHover = () => hoverRating.value = 0

function addTag() {
  const tag = newTag.value.trim()
  if (tag && !form.tags.includes(tag)) {
    form.tags.push(tag)
  }
  newTag.value = ''
}

function removeTag(index) {
  form.tags.splice(index, 1)
}

function submit() {
  form.post('/companies')
}
</script>

<template>
  <AppLayout title="企業登録">
    <div class="max-w-3xl mx-auto p-6 space-y-6">
      <!-- 戻るリンク -->
      <div class="mt-4 mb-2 ml-1">
        <Link href="/companies" class="text-sm text-blue-600 hover:underline inline-flex items-center">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          企業一覧に戻る
        </Link>
      </div>

      <h1 class="text-2xl font-bold">企業新規登録</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- 企業名 -->
        <div>
          <label class="block mb-1">企業名</label>
          <input v-model="form.name" type="text" class="w-full border p-2 rounded" />
          <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
        </div>

        <!-- ステータス -->
        <div>
          <label class="block mb-1">選考ステータス</label>
          <select v-model="form.status" class="w-full border p-2 rounded">
            <option value="">選択してください</option>
            <option value="未応募">未応募</option>
            <option value="応募済">応募済</option>
            <option value="面接中">面接中</option>
            <option value="内定">内定</option>
            <option value="辞退">辞退</option>
          </select>
          <div v-if="form.errors.status" class="text-red-500 text-sm">{{ form.errors.status }}</div>
        </div>

        <!-- 希望度（星） -->
        <div>
          <label class="block mb-1">希望度</label>
          <div class="flex space-x-1">
            <template v-for="i in 5" :key="i">
              <svg
                @click="setRating(i)"
                @mouseover="setHover(i)"
                @mouseleave="clearHover"
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 cursor-pointer transition-transform hover:scale-125"
                :class="[(hoverRating || form.hope_level) >= i ? 'text-yellow-400' : 'text-gray-300']"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.216 3.736a1 1 0 00.95.69h3.925c.969 0 1.371 1.24.588 1.81l-3.178 2.31a1 1 0 00-.364 1.118l1.216 3.736c.3.921-.755 1.688-1.538 1.118l-3.178-2.31a1 1 0 00-1.175 0l-3.178 2.31c-.783.57-1.838-.197-1.538-1.118l1.216-3.736a1 1 0 00-.364-1.118L2.292 9.163c-.783-.57-.38-1.81.588-1.81h3.925a1 1 0 00.95-.69l1.216-3.736z"
                />
              </svg>
            </template>
          </div>
          <div v-if="form.errors.hope_level" class="text-red-500 text-sm">{{ form.errors.hope_level }}</div>
        </div>

        <!-- タグ -->
        <div>
          <label class="block mb-1">タグ</label>
          <input
            v-model="newTag"
            @keydown.enter.prevent="addTag"
            type="text"
            class="w-full border p-2 rounded mb-2"
            placeholder="タグを入力してEnterで追加"
          />
          <div class="flex flex-wrap gap-2">
            <span
              v-for="(tag, index) in form.tags"
              :key="index"
              class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center gap-2"
            >
              {{ tag }}
              <button type="button" @click="removeTag(index)" class="text-blue-500 hover:text-red-500">×</button>
            </span>
          </div>
          <div v-if="form.errors.tags" class="text-red-500 text-sm mt-1">{{ form.errors.tags }}</div>
        </div>

        <!-- 担当者名 -->
        <div>
          <label class="block mb-1">担当者名</label>
          <input v-model="form.contact_person" type="text" class="w-full border p-2 rounded" />
          <div v-if="form.errors.contact_person" class="text-red-500 text-sm">{{ form.errors.contact_person }}</div>
        </div>

        <!-- メール -->
        <div>
          <label class="block mb-1">メールアドレス</label>
          <input v-model="form.email" type="email" class="w-full border p-2 rounded" />
          <div v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</div>
        </div>

        <!-- 電話 -->
        <div>
          <label class="block mb-1">電話番号</label>
          <input v-model="form.phone" type="tel" class="w-full border p-2 rounded" />
          <div v-if="form.errors.phone" class="text-red-500 text-sm">{{ form.errors.phone }}</div>
        </div>

        <!-- URL -->
        <div>
          <label class="block mb-1">会社URL</label>
          <input v-model="form.website_url" type="url" class="w-full border p-2 rounded" />
          <div v-if="form.errors.website_url" class="text-red-500 text-sm">{{ form.errors.website_url }}</div>
        </div>

        <!-- メモ -->
        <div>
          <label class="block mb-1">メモ</label>
          <textarea v-model="form.memo" class="w-full border p-2 rounded"></textarea>
          <div v-if="form.errors.memo" class="text-red-500 text-sm">{{ form.errors.memo }}</div>
        </div>

        <!-- 登録ボタン -->
        <button type="submit" class="bg-black text-white px-6 py-2 rounded hover:bg-gray-800">
          登録
        </button>
      </form>
    </div>
  </AppLayout>
</template>
