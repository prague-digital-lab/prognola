<script>
export default {
  props: ["expense"],

  data: () => {
    return {
      expanded: false,
      organisation_name_filter: null,

      organisations: [],
      selected_organisation: null,
    };
  },

  mounted() {
    if (this.expense.organisation_id) {
      this.selected_organisation = this.expense.organisation;
    }

    this.loadOrganisations();
  },

  methods: {
    expand() {
      this.expanded = true;
    },

    close() {
      this.expanded = false;
    },

    async selectOrganisation(organisation) {
      this.expanded = false;
      this.selected_organisation = organisation;

      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("expense", () =>
        client(
          "/api/" + route.params.workspace + "/expenses/" + this.expense.uuid,
          {
            method: "PATCH",
            body: {
              organisation_uuid: organisation.uuid,
            },
          },
        ),
      );
    },

    async loadOrganisations() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("organisations", () =>
        client("/api/" + route.params.workspace + "/organisations", {
          method: "GET",
        }),
      );

      this.organisations = data.value;
    },
  },

  computed: {
    filtered_organisations() {
      return this.organisations.filter((organisation) => {
        return (
          !this.organisation_name_filter ||
          organisation.name
            .toLowerCase()
            .indexOf(this.organisation_name_filter.toLowerCase()) > -1
        );
      });
    },
  },
};
</script>

<template>
  <div class="relative">
    <p
      class="text-xs mb-7 rounded px-1 py-1 text-gray-800 hover:bg-gray-100"
      @click="expanded ? close() : expand()"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="me-2 inline-block size-5"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"
        />
      </svg>

      {{
        selected_organisation ? selected_organisation.name : "zvolit organizaci"
      }}
    </p>

    <Transition>
      <div
        v-if="expanded"
        class="absolute left-[-200px] top-[-5px] w-[197px] scroll-auto rounded border border-slate-200 bg-white px-1 py-1 shadow"
      >
        <input
          class="mb-2 w-full rounded border-gray-200 py-1 text-gray-700 focus:border-gray-200 focus:ring-0"
          type="text"
          placeholder="Název organizace..."
          v-model="organisation_name_filter"
        />

        <div class="max-h-[50vh] overflow-auto">
          <p
            class="px-2 py-1 text-base text-gray-700 hover:bg-gray-100"
            v-for="organisation in filtered_organisations"
            @click="selectOrganisation(organisation)"
          >
            {{ organisation.name }}
          </p>

          <p
            class="px-2 py-1 text-base text-gray-500"
            v-if="filtered_organisations.length === 0"
          >
            žádná organizace
          </p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped></style>
