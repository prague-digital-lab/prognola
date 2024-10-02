<template>
  <div>
    <div class="md:flex md:items-center md:justify-between min-h-[50px]">
      <div class="min-w-0 flex-1">
        <h2
          class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight"
        >
          <slot name="title"></slot>
        </h2>
      </div>

      <div class="flex md:ml-4">
        <slot name="controls"></slot>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts"></script>

<style scoped></style>
