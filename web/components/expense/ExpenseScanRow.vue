<template>
  <div class="w-full text-base text-slate-500">
    <div
      class="mb-2 flex items-center justify-between rounded-md border border-gray-200 bg-white px-5 py-2 shadow-sm hover:bg-gray-hover active:bg-gray-100 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800"
      @click="!expanded ? (expanded = true) : (expanded = false)"
    >
      <div class="flex items-center">
        <DocumentIcon class="me-2 h-4 w-4 text-gray-700" />
        {{ scan.title }}
        <a
          :href="scan.file_url"
          title="Otevřít v novém panelu"
          target="_blank"
          class="ms-2 inline-block rounded-md p-1 hover:bg-gray-100 active:bg-gray-200 dark:hover:bg-zinc-900"
        >
          <LinkIcon class="h-4 w-4" />
        </a>
      </div>

      <div class="flex items-center">
        <a
          @click="deleteScan"
          class="me-2 inline-block rounded-md p-1 hover:bg-gray-100 active:bg-gray-200 dark:hover:bg-zinc-900"
        >
          <TrashIcon class="h-4 w-4" />
        </a>

        <div v-if="expanded">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="m4.5 15.75 7.5-7.5 7.5 7.5"
            />
          </svg>
        </div>
        <div v-else>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="m19.5 8.25-7.5 7.5-7.5-7.5"
            />
          </svg>
        </div>
      </div>
    </div>

    <div v-if="expanded" class="mt-3 px-5 py-3">
      <object
        :data="scan.file_url"
        type="application/pdf"
        class="h-[800px] w-full"
      >
        <p>Unable to display PDF file. <a href="">Download</a>instead.</p>
      </object>
    </div>
  </div>
</template>

<script setup>
import { DocumentIcon, TrashIcon } from "@heroicons/vue/24/outline";
import { LinkIcon } from "@heroicons/vue/16/solid";

const expanded = ref(false);
const props = defineProps(["scan"]);
const emit = defineEmits(["scan-deleted"]);

async function deleteScan() {
  if (!confirm("Opravdu chcete odstranit tuto přílohu?")) {
    return;
  }

  const client = useSanctumClient();
  const route = useRoute();

  const { data } = await useAsyncData("expense", () =>
    client("/api/" + route.params.workspace + "/scans/" + props.scan.uuid, {
      method: "DELETE",
    }),
  );

  emit("scan-deleted");
}
</script>

<style scoped></style>
