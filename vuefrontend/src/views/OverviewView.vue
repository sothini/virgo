<script setup lang="ts">
import { defineAsyncComponent } from 'vue'
import type { Component } from 'vue'

const HeaderComp = defineAsyncComponent(async () => {
  const module = (await import('../components/HeaderComp.vue')) as { default: Component }
  return module.default
})

const ProfileComp = defineAsyncComponent(async () => {
  const module = (await import('../components/ProfileComp.vue')) as { default: Component }
  return module.default
})

const UserOrderComp = defineAsyncComponent(async () => {
  const module = (await import('../components/UserOrderComp.vue')) as { default: Component }
  return module.default
})

const OrderComp = defineAsyncComponent(async () => {
  const module = (await import('../components/OrderComp.vue')) as { default: Component }
  return module.default
})

</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <HeaderComp />
    <ProfileComp />
    <UserOrderComp />
    <OrderComp />
  </div>
</template>

<style scoped></style>
