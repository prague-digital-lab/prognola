<template>
  <div class="flex flex-col justify-between min-h-screen">
    <main class="container mx-auto">
      <Menu></Menu>

      <div class="px-5">
        <slot />
      </div>
    </main>

    <Footer></Footer>
  </div>
</template>

<script setup>
import {
  CalendarIcon,
  ChartPieIcon,
  DocumentDuplicateIcon,
  FolderIcon,
  HomeIcon,
  UsersIcon,
} from "@heroicons/vue/24/outline";
import Menu from "~/components/guest/Menu.vue";
import Footer from "~/components/guest/Footer.vue";

const navigation = [
  { name: "Nástěnka", href: "/", icon: HomeIcon, current: true },
  { name: "Tržby", href: "/invoices", icon: UsersIcon, current: false },
  { name: "Projects", href: "#", icon: FolderIcon, current: false },
  { name: "Calendar", href: "#", icon: CalendarIcon, current: false },
  { name: "Documents", href: "#", icon: DocumentDuplicateIcon, current: false },
  { name: "Reports", href: "#", icon: ChartPieIcon, current: false },
];
const teams = [
  { id: 1, name: "Heroicons", href: "#", initial: "H", current: false },
  { id: 2, name: "Tailwind Labs", href: "#", initial: "T", current: false },
  { id: 3, name: "Workcation", href: "#", initial: "W", current: false },
];
const userNavigation = [
  { name: "Your profile", href: "#" },
  { name: "Sign out", href: "#" },
];

useHead({
  bodyAttrs: {
    class: "bg-grad-dark",
  },
  meta: [{ name: "theme-color", content: "#000b32" }],
  titleTemplate: (titleChunk) => {
    return titleChunk
      ? `${titleChunk} | Prognola`
      : "Prognola: Plánujte a řiďte firemní finance s lehkostí.";
  },
});

onMounted(() => {});
</script>

<style lang="scss">
.bg-grad-dark {
  background: rgb(0, 0, 0);
  background: linear-gradient(
    90deg,
    rgb(0, 11, 50) 0%,
    rgba(18, 18, 18, 1) 65%,
    rgba(0, 0, 0, 1) 100%
  );
}
</style>
