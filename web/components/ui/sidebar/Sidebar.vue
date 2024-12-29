<template>
  <nav
    class="flex flex-1 flex-col divide-y divide-zinc-200 dark:divide-zinc-800"
  >
    <ul role="list" class="space-y-1 py-2">
      <MenuLink
        name="Cash flow"
        :icon="ChartBarIcon"
        :href="'/' + route.params.workspace + '/cashflow'"
      ></MenuLink>
      <MenuLink
        name="Plán"
        :icon="ClockIcon"
        :href="'/' + route.params.workspace + '/now'"
      ></MenuLink>
      <MenuLink
        name="Schránka"
        :icon="InboxIcon"
        :href="'/' + route.params.workspace + '/inbox'"
      ></MenuLink>
    </ul>

    <ul role="list" class="space-y-1 py-2">
      <MenuLink
        name="Příjmy"
        :icon="PlusCircleIcon"
        :href="'/' + route.params.workspace + '/income'"
      ></MenuLink>
      <MenuLink
        name="Výdaje"
        :icon="MinusCircleIcon"
        :href="'/' + route.params.workspace + '/expenses'"
      ></MenuLink>
      <MenuLink
        name="Účty"
        :icon="WalletIcon"
        :href="'/' + route.params.workspace + '/bank_accounts'"
      ></MenuLink>
    </ul>

    <ul role="list" class="space-y-1 py-2">
      <MenuLink
        name="Firmy"
        :icon="UsersIcon"
        :href="'/' + route.params.workspace + '/organisations'"
      ></MenuLink>
    </ul>
  </nav>
</template>

<script setup>
const route = useRoute();

import {
  ChartBarIcon,
  ClockIcon,
  InboxIcon,
  MinusCircleIcon,
  PlusCircleIcon,
  UsersIcon,
  WalletIcon,
} from "@heroicons/vue/24/outline";
import MenuLink from "~/components/ui/sidebar/MenuLink.vue";
</script>

<style scoped></style>
