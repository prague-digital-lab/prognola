<template>
  <div class="border-t border-zinc-700 bg-black p-10">
    <div class="container mx-auto text-white">
      <div class="grid grid-cols-4 gap-x-5 mx-10">
        <div class="text-base text-zinc-400">
          <h2 class="mb-4 text-xl text-white">Prognola</h2>

          <div class="flex flex-col space-y-4">
            <nuxt-link href="/cs/plan"></nuxt-link>
          </div>
        </div>

        <div class="text-base text-zinc-400">
          <h2 class="mb-4 text-xl text-white">Produkt</h2>

          <div class="flex flex-col space-y-2">
            <nuxt-link href="/cs/plan">Plánování</nuxt-link>
            <nuxt-link href="/cs/incomes-and-expenses">Příjmy a výdaje</nuxt-link>
            <nuxt-link href="/cs/changelog">Novinky v aplikaci</nuxt-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts"></script>

<style scoped></style>
