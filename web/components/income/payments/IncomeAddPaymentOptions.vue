<template>
  <div class="top-16 z-20 w-56 text-left">
    <Menu as="div" class="relative inline-block text-left">
      <div>
        <MenuButton
          class="rounded-md px-2 py-1 text-sm text-gray-400 duration-100 hover:bg-gray-100 active:bg-gray-200"
          >+ přidat platbu
        </MenuButton>
      </div>

      <transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <MenuItems
          class="absolute right-0 z-20 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
        >
          <div class="px-1 py-1">
            <MenuItem v-slot="{ active }">
              <button
                :class="[
                  active ? 'bg-gray-hover duration-100' : 'text-gray-900',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                ]"
                @click="$emit('open_pair_modal')"
              >
                Spárovat s existující platbou
              </button>
            </MenuItem>
            <!--            <MenuItem v-slot="{ active }">-->
            <!--              <button-->
            <!--                :class="[-->
            <!--                  active ? 'bg-gray-hover duration-100' : 'text-gray-900',-->
            <!--                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',-->
            <!--                ]"-->
            <!--              >-->
            <!--                Vytvořit novou platbu-->
            <!--              </button>-->
            <!--            </MenuItem>-->
          </div>
        </MenuItems>
      </transition>
    </Menu>
  </div>
</template>

<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";

defineProps(["income"]);
</script>
