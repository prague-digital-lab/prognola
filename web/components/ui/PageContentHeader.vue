<template>
  <div>
    <div class="mb-4">
      <div class="mb-2 min-h-[40px] md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
          <slot name="title"></slot>
        </div>

        <div class="flex md:ml-4">
          <slot name="controls"></slot>
        </div>
      </div>

      <slot name="subtitle"></slot>
    </div>
  </div>
</template>

<script setup lang="ts"></script>

<style scoped></style>
