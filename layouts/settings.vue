<template>
  <div class="cursor-default text-base">
    <!-- Static sidebar for desktop -->
    <div
      class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col"
    >
      <!-- Sidebar component, swap this element with another sidebar if you like -->
      <div
        class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-4 pb-4"
      >
        <div class="flex h-16 shrink-0 items-center">
          <nuxt-link
            :href="'/' + route.params.workspace + '/cashflow'"
            class="active:bg-gray-100 flex cursor-pointer items-center rounded-md px-2 py-2 pb-1 pt-1 duration-100 hover:bg-gray-hover"
          >
            <arrow-left-icon
              class="me-2 inline-block h-4 w-4 shrink-0 group-hover:text-indigo-600"
            />

            <p class="inline-block cursor-default font-medium text-gray-600">
              Nastavení firmy
            </p>
          </nuxt-link>
        </div>
        <nav class="flex flex-1 flex-col px-2">
          <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
              <ul role="list" class="-mx-2 space-y-1">
                <li v-for="item in navigation" :key="item.name">
                  <NuxtLink
                    :href="item.href"
                    :active-class="'hover:bg-gray-200 bg-gray-200 text-indigo-600'"
                    class="group flex items-center gap-x-3 rounded-md p-2 text-base leading-4 text-gray-500 duration-100 hover:bg-gray-200/60 hover:text-indigo-600"
                  >
                    <component
                      :is="item.icon"
                      class="h-4 w-4 shrink-0 group-hover:text-indigo-600"
                      :active-class="'text-indigo-600'"
                    />
                    {{ item.name }}
                  </NuxtLink>
                </li>
              </ul>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <div class="lg:pl-72">
      <!-- Top navbar -->

      <main class="py-10">
        <div class="px-4 sm:px-10 lg:px-[70px]">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import {
  ArrowLeftIcon, BellIcon,
  ChartBarIcon,
  InboxIcon,
} from "@heroicons/vue/20/solid";

useHead({
  title: "Prognola",
  bodyAttrs: {
    class: "bg-gray-50",
  },
});

const route = useRoute();

const navigation = [
  {
    name: "Zasílání dokladů",
    href: "/" + route.params.workspace + "/settings",
    icon: InboxIcon,
    current: false,
  },
  {
    name: "API",
    href: "/" + route.params.workspace + "/settings/api",
    icon: ChartBarIcon,
    current: false,
  },
  {
    name: "Oznámení",
    href: "/" + route.params.workspace + "/settings/notifications",
    icon: BellIcon,
    current: false,
  },
];

const { user } = useSanctumAuth();

const workspaces = ref("");
const active_workspace = ref("");
const active_workspace_url_slug = ref("");

onMounted(async () => {
  await loadAvailableWorkspaces();

  if (workspaces.value.length === 0) {
    await navigateTo("/create_workspace");
    return;
  }

  findActiveWorkspace(route.params.workspace);
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

function findActiveWorkspace(url_slug) {
  let active_url_slug = url_slug;

  let workspace_by_slug = workspaces.value.find(
    (x) => x.url_slug === active_url_slug,
  );

  if (workspace_by_slug === undefined) {
    showError("Nenalezeno");
  }

  active_workspace.value = workspace_by_slug;
  active_workspace_url_slug.value = active_url_slug;
}

const sidebarOpen = ref(false);

watch(active_workspace_url_slug, async (newUrlSlug, oldUrlSlug) => {
  if (newUrlSlug === oldUrlSlug || oldUrlSlug === "") {
    return;
  }

  console.log("Changing workspace to: " + newUrlSlug);

  location.href = "/" + newUrlSlug + "/cashflow";
});

async function submitLogout() {
  const { logout } = useSanctumAuth();

  await logout();
}
</script>
