<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useUserStore } from '../stores/user'

const userStore = useUserStore()
const profile = computed(() => userStore.profile)
const assets = computed(() => userStore.assets)

onMounted(() => {
  if (!userStore.profile) {
    userStore.fetchProfile()
  }
})
</script>

<template>
  <main class="max-w-6xl mx-auto px-4 py-10 space-y-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">Profile</h1>
          <p class="mt-1 text-gray-600">Your account details and balances.</p>
        </div>
        <div v-if="userStore.loading" class="text-sm text-indigo-600">Loading...</div>
      </div>

      <div v-if="userStore.error" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ userStore.error }}
      </div>

      <div v-else class="mt-6 grid gap-6 md:grid-cols-3">
        <div class="md:col-span-2 space-y-4">
          <div class="rounded-lg border border-gray-100 p-4">
            <h2 class="text-lg font-medium text-gray-900">Account</h2>
            <dl class="mt-3 grid gap-3 sm:grid-cols-2">
              <div>
                <dt class="text-sm text-gray-500">Name</dt>
                <dd class="text-base text-gray-900">{{ profile?.name ?? '—' }}</dd>
              </div>
              <div>
                <dt class="text-sm text-gray-500">Email</dt>
                <dd class="text-base text-gray-900">{{ profile?.email ?? '—' }}</dd>
              </div>
              <div>
                <dt class="text-sm text-gray-500">Email Verified</dt>
                <dd class="text-base text-gray-900">
                  {{ profile?.email_verified_at ? 'Verified' : 'Not verified' }}
                </dd>
              </div>
              <div>
                <dt class="text-sm text-gray-500">Member Since</dt>
                <dd class="text-base text-gray-900">
                  {{
                    profile?.created_at ? new Date(profile.created_at).toLocaleDateString() : '—'
                  }}
                </dd>
              </div>
            </dl>
          </div>

          <div class="rounded-lg border border-gray-100 p-4">
            <h2 class="text-lg font-medium text-gray-900">Balances</h2>
            <div class="mt-3 flex items-baseline gap-2">
              <span class="text-3xl font-semibold text-indigo-700">
                {{
                  profile?.balance?.toLocaleString(undefined, { minimumFractionDigits: 2 }) ??
                  '0.00'
                }}
              </span>
              <span class="text-sm text-gray-500">USD</span>
            </div>
          </div>
        </div>

        <div class="rounded-lg border border-gray-100 p-4">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Asset Balances</h2>
            <span class="text-sm text-gray-500">{{ assets.length }} assets</span>
          </div>
          <ul class="mt-4 space-y-3">
            <li
              v-if="assets.length === 0"
              class="rounded-md border border-dashed border-gray-200 p-3 text-sm text-gray-500"
            >
              No assets found.
            </li>
            <li
              v-for="asset in assets"
              :key="asset.id"
              class="rounded-md border border-gray-100 p-3"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ asset.symbol }}</p>
                  <p class="text-xs text-gray-500">Locked: {{ asset.locked_amount }}</p>
                </div>
                <p class="text-base font-semibold text-gray-900">{{ asset.amount }}</p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </main>
</template>
