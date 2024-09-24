<template>
  <div class="">
    <div
        class="sticky bg-gray-900/10 text-white rounded-2xl mt-5 mx-[180px] px-5 py-3 border border-gray-800/80 backdrop-blur-md">
      <div class="w-screen">
        <a>Prognola</a>
      </div>
    </div>

    <main class="py-10 text-gray-200 mt-5">
      <slot/>
    </main>
  </div>
</template>

<script setup>
import {ref} from 'vue'
import {
  CalendarIcon,
  ChartPieIcon,
  DocumentDuplicateIcon,
  FolderIcon,
  HomeIcon,
  UsersIcon,
} from '@heroicons/vue/24/outline'

const navigation = [
  {name: 'Nástěnka', href: '/', icon: HomeIcon, current: true},
  {name: 'Tržby', href: '/invoices', icon: UsersIcon, current: false},
  {name: 'Projects', href: '#', icon: FolderIcon, current: false},
  {name: 'Calendar', href: '#', icon: CalendarIcon, current: false},
  {name: 'Documents', href: '#', icon: DocumentDuplicateIcon, current: false},
  {name: 'Reports', href: '#', icon: ChartPieIcon, current: false},
]
const teams = [
  {id: 1, name: 'Heroicons', href: '#', initial: 'H', current: false},
  {id: 2, name: 'Tailwind Labs', href: '#', initial: 'T', current: false},
  {id: 3, name: 'Workcation', href: '#', initial: 'W', current: false},
]
const userNavigation = [
  {name: 'Your profile', href: '#'},
  {name: 'Sign out', href: '#'},
]

useHead({
  bodyAttrs: {
    class: 'bg-grad-dark'
  },
})

const sidebarOpen = ref(false)
</script>

<style lang="scss">
.bg-grad-dark {
  background: rgb(0, 0, 0);
  background: linear-gradient(90deg, rgb(28, 28, 28) 0%, rgba(18, 18, 18, 1) 65%, rgba(0, 0, 0, 1) 100%);
}
</style>