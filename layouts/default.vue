<template>
  <div class="cursor-default text-base">
    <TransitionRoot as="template" :show="sidebarOpen">
      <Dialog class="relative z-50 lg:hidden" @close="sidebarOpen = false">
        <TransitionChild
          as="template"
          enter="transition-opacity ease-linear duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="transition-opacity ease-linear duration-300"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-zinc-900/80" />
        </TransitionChild>

        <div class="fixed inset-0 flex">
          <TransitionChild
            as="template"
            enter="transition ease-in-out duration-300 transform"
            enter-from="-translate-x-full"
            enter-to="translate-x-0"
            leave="transition ease-in-out duration-300 transform"
            leave-from="translate-x-0"
            leave-to="-translate-x-full"
          >
            <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">
              <TransitionChild
                as="template"
                enter="ease-in-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in-out duration-300"
                leave-from="opacity-100"
                leave-to="opacity-0"
              >
                <div
                  class="absolute left-full top-0 flex w-16 justify-center pt-5"
                >
                  <button
                    type="button"
                    class="-m-2.5 p-2.5"
                    @click="sidebarOpen = false"
                  >
                    <span class="sr-only">Close sidebar</span>
                    <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                  </button>
                </div>
              </TransitionChild>
              <!-- Sidebar component, swap this element with another sidebar if you like -->
              <div
                class="flex grow flex-col overflow-y-auto bg-white px-6 pb-4"
              >
                <div class="flex shrink-0 items-center">
                  <nuxt-link href="/">
                    <img
                      class="h-8 w-auto"
                      src="/img/logo_horizontal_sm.webp"
                      alt="Prognola"
                    />
                  </nuxt-link>
                </div>
                <nav class="flex flex-1 flex-col">
                  <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                      <ul role="list" class="-mx-2 space-y-1">
                        <li v-for="item in navigation" :key="item.name">
                          <NuxtLink
                            :href="item.href"
                            :class="[
                              item.current
                                ? 'bg-zinc-50 font-medium text-indigo-600'
                                : 'text-zinc-700 hover:bg-zinc-50 hover:text-indigo-600',
                              'group flex gap-x-3 rounded-md p-2 text-base font-semibold leading-6',
                            ]"
                          >
                            <component
                              :is="item.icon"
                              :class="[
                                item.current
                                  ? 'text-indigo-600'
                                  : 'text-zinc-400 group-hover:text-indigo-600',
                                'h-6 w-6 shrink-0',
                              ]"
                              aria-hidden="true"
                            />
                            {{ item.name }}
                          </NuxtLink>
                        </li>
                      </ul>
                    </li>
                    <li>
                      <!--                      <div class="text-xs font-semibold leading-6 text-zinc-400">Your teams</div>-->
                      <!--                      <ul role="list" class="-mx-2 mt-2 space-y-1">-->
                      <!--                        <li v-for="team in teams" :key="team.name">-->
                      <!--                          <NuxtLink :href="team.href"-->
                      <!--                                    :class="[team.current ? 'bg-zinc-50 text-indigo-600' : 'text-zinc-700 hover:bg-zinc-50 hover:text-indigo-600', 'group flex gap-x-3 rounded-md p-2 text-base font-semibold leading-6']">-->
                      <!--                            <span-->
                      <!--                                :class="[team.current ? 'border-indigo-600 text-indigo-600' : 'border-zinc-200 text-zinc-400 group-hover:border-indigo-600 group-hover:text-indigo-600', 'flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border bg-white text-[0.625rem] font-medium']">{{-->
                      <!--                                team.initial-->
                      <!--                              }}</span>-->
                      <!--                            <span class="truncate">{{ team.name }}</span>-->
                      <!--                          </NuxtLink>-->
                      <!--                        </li>-->
                      <!--                      </ul>-->
                    </li>
                    <li class="mt-auto">
                      <!--                      <NuxtLink href="#"-->
                      <!--                                class="group -mx-2 flex gap-x-3 rounded-md p-2 text-base font-semibold leading-6 text-zinc-700 hover:bg-zinc-50 hover:text-indigo-600">-->
                      <!--                        <Cog6ToothIcon class="h-6 w-6 shrink-0 text-zinc-400 group-hover:text-indigo-600"-->
                      <!--                                       aria-hidden="true"/>-->
                      <!--                        Settings-->
                      <!--                      </NuxtLink>-->
                    </li>
                  </ul>
                </nav>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Static sidebar for desktop -->
    <div
      class="hidden lg:fixed lg:inset-y-0 lg:z-30 lg:flex lg:w-60 lg:flex-col"
    >
      <!-- Sidebar component, swap this element with another sidebar if you like -->
      <div
        class="m-3 flex grow flex-col overflow-y-auto rounded-xl border border-zinc-200 bg-white pb-4 tracking-wide dark:border-zinc-800 dark:bg-zinc-900"
      >
        <div
          class="flex shrink-0 items-center border-b border-zinc-200 px-2 py-3 dark:border-zinc-800"
        >
          <nuxt-link
            :href="'/' + route.params.workspace + '/cashflow'"
            class="flex w-full cursor-default items-center rounded-md py-1 pe-3 ps-2 duration-100 hover:bg-zinc-200/50 active:hover:bg-zinc-200/80 dark:hover:bg-zinc-800 dark:active:hover:bg-zinc-700/80"
          >
            <nuxt-img
              src="/img/logo_prazska_laborator.png"
              width="20"
              class="me-4 ms-1"
            />

            <p class="text-[15px] text-zinc-800 dark:text-zinc-200">Prognola</p>
          </nuxt-link>
        </div>

        <sidebar></sidebar>
      </div>
    </div>

    <!-- Top navbar and content -->
    <div class="lg:pl-60">
      <div
        class="sticky top-0 z-40 flex h-12 shrink-0 items-center gap-x-4 border-b border-zinc-200 bg-zinc-50 px-4 sm:gap-x-6 sm:px-6 lg:px-4 dark:border-zinc-800 dark:bg-black"
      >
        <button
          type="button"
          class="-m-2.5 p-2.5 text-zinc-700 lg:hidden"
          @click="sidebarOpen = true"
        >
          <span class="sr-only">Open sidebar</span>
          <Bars3Icon class="h-6 w-6" aria-hidden="true" />
        </button>

        <!-- Separator -->
        <div class="h-6 w-px bg-zinc-200 lg:hidden" aria-hidden="true" />

        <Menu as="div" class="relative">
          <MenuButton class="flex items-center">
            <span class="hidden lg:flex lg:items-center">
              <span
                class="text-base leading-6 text-zinc-700 dark:text-zinc-500"
                aria-hidden="true"
                >{{ active_workspace.name }}</span
              >
              <ChevronDownIcon
                class="ml-2 h-5 w-5 text-zinc-400"
                aria-hidden="true"
              />
            </span>
          </MenuButton>
          <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
          >
            <MenuItems
              class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-zinc-900/5 focus:outline-none dark:bg-zinc-900 dark:ring-zinc-700"
            >
              <MenuItem v-slot="{ active }" v-for="workspace in workspaces">
                <div
                  @click="chooseWorkspace(workspace.url_slug)"
                  :class="[
                    active ? 'bg-zinc-50' : '',
                    'block px-3 py-1 text-base leading-6 text-zinc-900 dark:text-zinc-400',
                  ]"
                >
                  {{ workspace.name }}
                </div>
              </MenuItem>
            </MenuItems>
          </transition>
        </Menu>

        <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
          <form class="relative flex flex-1" action="#" method="GET">
            <label for="search-field" class="sr-only">Search</label>
            <!--            <MagnifyingGlassIcon class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-zinc-400"-->
            <!--                                 aria-hidden="true"/>-->
            <!--            <input id="search-field"-->
            <!--                   class="block h-full w-full border-0 py-0 pl-8 pr-0 text-zinc-900 placeholder:text-zinc-400 focus:ring-0 sm:text-base"-->
            <!--                   placeholder="Vyhledávání" type="search" name="search"/>-->
          </form>
          <div class="flex items-center gap-x-4 lg:gap-x-6">
            <!-- Profile dropdown -->
            <Menu as="div" class="relative">
              <MenuButton class="-m-1.5 flex items-center p-1.5">
                <span class="sr-only">Open user menu</span>
                <!--                <img-->
                <!--                  class="h-6 w-6 rounded-full bg-zinc-50"-->
                <!--                  :src="user.profile_photo_url"-->
                <!--                  alt=""-->
                <!--                />-->
                <span class="hidden lg:flex lg:items-center">
                  <span
                    class="ml-4 text-base leading-6 text-zinc-700 dark:text-zinc-500"
                    aria-hidden="true"
                    >{{ user.name }}</span
                  >
                  <ChevronDownIcon
                    class="ml-2 h-5 w-5 text-zinc-400"
                    aria-hidden="true"
                  />
                </span>
              </MenuButton>
              <transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
              >
                <MenuItems
                  class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-zinc-900/5 focus:outline-none"
                >
                  <MenuItem v-slot="{ active }">
                    <NuxtLink
                      :href="'/' + route.params.workspace + '/settings'"
                      :class="[
                        active ? 'bg-zinc-50' : '',
                        'block px-3 py-1 text-base leading-6 text-zinc-900',
                      ]"
                    >
                      Nastavení firmy
                    </NuxtLink>
                  </MenuItem>

                  <MenuItem v-slot="{ active }">
                    <NuxtLink
                      href="/"
                      :class="[
                        active ? 'bg-zinc-50' : '',
                        'block px-3 py-1 text-base leading-6 text-zinc-900',
                      ]"
                    >
                      Úvod webu
                    </NuxtLink>
                  </MenuItem>

                  <a
                    @click="submitLogout"
                    :class="[
                      'block cursor-pointer px-3 py-1 text-base leading-6 text-zinc-900',
                    ]"
                  >
                    Odhlásit se
                  </a>
                </MenuItems>
              </transition>
            </Menu>
          </div>
        </div>
      </div>

      <main class="py-10">
        <div class="px-4 sm:px-4" v-if="!loading_workspace">
          <slot />
        </div>

        <div
          class="flex flex-col items-center justify-center space-y-10 px-4 h-[600px]"
          v-else
        >
          <div class="block">
            <nuxt-img src="/img/logo_prazska_laborator.png" width="80" />
          </div>

          <div>
            <p class="text-zinc-700 dark:text-zinc-200">Probíhá synchronizace dat {{active_workspace.name}}.</p>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import {
  Dialog,
  DialogPanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import {
  Bars3Icon,
  ChevronDownIcon,
  XMarkIcon,
} from "@heroicons/vue/24/outline";

import Sidebar from "~/components/ui/sidebar/Sidebar.vue";

import { openDatabase } from "~/lib/dexie/db.js";
import bootstrapDatabase from "~/lib/dexie/bootstrap_db.js";

useHead({
  title: "Prognola",
  bodyAttrs: {
    class: "bg-zinc-50 dark:bg-black",
  },
});

const route = useRoute();

const { user } = useSanctumAuth();

const workspaces = ref("");
const active_workspace = ref("");
const active_workspace_url_slug = ref("");

const loading_workspace = ref(true);

onMounted(async () => {
  await loadAvailableWorkspaces();

  if (workspaces.value.length === 0) {
    await navigateTo("/create_workspace");
    return;
  }

  initializeWorkspace(route.params.workspace);
});

async function loadAvailableWorkspaces() {
  // Load available workspaces
  const client = useSanctumClient();

  const { data } = await useAsyncData("workspaces", () =>
    client("/api/workspaces", {
      method: "GET",
    }),
  );

  workspaces.value = data.value;
}

async function chooseWorkspace(url_slug) {
  initializeWorkspace(url_slug);

  await navigateTo("/" + url_slug + "/cashflow");
}

async function initializeWorkspace(url_slug) {
  loading_workspace.value = true;

  let active_url_slug = url_slug;

  let workspace_by_slug = workspaces.value.find(
    (x) => x.url_slug === active_url_slug,
  );

  if (workspace_by_slug === undefined) {
    showError("Nenalezeno");
  }

  localStorage.setItem("active_workspace_url_slug", active_url_slug);

  let db = openDatabase();
  await bootstrapDatabase(db, route.params.workspace);

  active_workspace.value = workspace_by_slug;
  active_workspace_url_slug.value = active_url_slug;

  loading_workspace.value = false;
}

const sidebarOpen = ref(false);

async function submitLogout() {
  const { logout } = useSanctumAuth();

  await logout();
}
</script>

<style>
body {
  font-family: "Nunito", sans-serif;
}

.spectral {
  font-family: "Inter", sans-serif;
}
</style>
