<template>
  <div>
    <main class="container mx-auto md:px-[120px]">
      <div
          class="sticky w-full bg-gray-900/50 text-white rounded-2xl mt-5 px-5 py-3 border border-gray-800/80 backdrop-blur-3xl mb-[100px]">
        <div class="flex justify-between">
          <div>
            <nuxt-link href="/">Prognola</nuxt-link>
          </div>

          <div v-if="!authenticated">
            <nuxt-link href="/login" class="bg-gray-800 rounded text-gray-200 px-2.5 py-1.5 text-sm font-medium me-2">
              Přihlásit se
            </nuxt-link>
            <nuxt-link href="/register" class="bg-gray-200 rounded text-gray-800 px-2.5 py-1.5 text-sm font-medium">
              Vyzkoušet
            </nuxt-link>
          </div>

          <div v-if="authenticated && !verified">
            <a @click="logout" class="bg-gray-800 rounded text-gray-200 px-2.5 py-1.5 text-sm font-medium me-2">
              Odhlásit se</a>

            <nuxt-link href="/entry"
                       class="bg-gray-200 rounded text-gray-800 px-2.5 py-1.5 text-sm font-medium">Ověřit email
            </nuxt-link>
          </div>

          <div v-if="authenticated && verified">
            <a @click="logout" class="bg-gray-800 rounded text-gray-200 px-2.5 py-1.5 text-sm font-medium me-2">
              Odhlásit se</a>

            <nuxt-link href="/entry"
                       class="bg-gray-200 rounded text-gray-800 px-2.5 py-1.5 text-sm font-medium">
              Přejít do aplikace
            </nuxt-link>
          </div>
        </div>
      </div>

      <div class="px-5">
        <slot/>
      </div>
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

onMounted(() => {

})

const {user} = useSanctumAuth();

const authenticated = computed(() => {
  return user.value !== null
})

const verified = computed(() => {
  return authenticated && user.value.email_verified_at
})

async function logout() {
  const {logout} = useSanctumAuth();

  await logout();
}

const sidebarOpen = ref(false)
</script>

<style lang="scss">
.bg-grad-dark {
  background: rgb(0, 0, 0);
  background: linear-gradient(90deg, rgb(0, 11, 50) 0%, rgba(18, 18, 18, 1) 65%, rgba(0, 0, 0, 1) 100%);
}
</style>