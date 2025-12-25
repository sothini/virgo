<script setup lang="ts">
import { defineAsyncComponent } from 'vue'
import type { Component } from 'vue'

const HeaderComp = defineAsyncComponent(async () => {
  const module = (await import('../components/HeaderComp.vue')) as { default: Component }
  return module.default
})


</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <HeaderComp />
    
  </div>
</template>

<style scoped></style>
