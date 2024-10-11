<script>
export default {
  props: ["scan"],

  data: () => {
    return {
      expanded: false,
    };
  },
};
</script>

<template>
  <div class="w-full text-base text-slate-500">
    <div
      class="flex items-center justify-between border-b border-gray-200 bg-white px-5 py-2 hover:bg-gray-hover active:bg-gray-100"
      @click="!expanded ? (expanded = true) : (expanded = false)"
    >
      <div>
        {{ scan.title }}</div>

      <div>
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

<style scoped></style>
