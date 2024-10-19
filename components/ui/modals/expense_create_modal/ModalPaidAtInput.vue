<template>
  <div class="inline-block">
    <Popover v-slot="{ open }" class="relative">
      <div class="">
        <PopoverButton
          :class="open ? 'bg-gray-200' : ''"
          class="me-2 cursor-default rounded border border-gray-200 px-2 py-1 text-sm text-gray-800 shadow-sm hover:bg-gray-100 focus-visible:outline-0 active:bg-gray-200"
        >
          <div class="flex items-center text-gray-800">
            {{ paid_at ? formatDate(paid_at) : "-" }}
          </div>
        </PopoverButton>
      </div>

      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="translate-y-1 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-1 opacity-0"
      >
        <PopoverPanel v-slot="{ close }" class="absolute top-[37px] z-50">
          <div
            class="overflow-hidden rounded-lg bg-white p-3 shadow-lg ring-1 ring-black/5"
          >
            <p class="ms-2 text-base text-gray-500">Datum úhrady</p>
            <VueDatePicker
              :model-value="paid_at.toJSDate()"
              @update:model-value="setDate"
              inline
              auto-apply
            />
          </div>
        </PopoverPanel>
      </transition>
    </Popover>
  </div>
</template>

<script setup>
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { Popover, PopoverButton, PopoverPanel } from "@headlessui/vue";
import { DateTime } from "luxon";

const paid_at = defineModel();

onMounted(() => {
  if (paid_at.value === null) {
    paid_at.value = DateTime.now().startOf("day");
  }
});

function formatDate(date) {
  return date.toFormat("d.M.yyyy");
}

function setDate(date) {
  paid_at.value = DateTime.fromJSDate(date);
}
</script>

<style scoped></style>
