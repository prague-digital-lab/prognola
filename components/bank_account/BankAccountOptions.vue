<script>
export default {
  props: ["bank_account"],

  data: () => {
    return {
      expanded: false,
    };
  },

  methods: {
    expand() {
      this.expanded = true;
    },

    close() {
      this.expanded = false;
    },
  },
};
</script>

<template>
  <div class="relative">
    <p
      class="mb-3 rounded px-1 py-1 text-sm text-gray-700 hover:bg-gray-100"
      @click="expanded ? close() : expand()"
    >
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
          d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
        />
      </svg>
    </p>

    <Transition>
      <div
        v-if="expanded"
        class="absolute left-[-150px] top-[-5px] w-[147px] rounded border border-slate-200 bg-white px-1 py-1 shadow"
      >
        <nuxt-link
          :href="'/bank_accounts/' + bank_account.id + '/import-kb-csv'"
          v-if="bank_account.bank === 'komercni_banka_csv'"
        >
          <div class="w-full p-2 text-sm text-slate-600 hover:bg-gray-100">
            Importovat transakce z CSV
          </div>
        </nuxt-link>
      </div>
    </Transition>
  </div>
</template>

<style scoped></style>
