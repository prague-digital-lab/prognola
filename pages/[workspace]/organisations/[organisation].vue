<template>
  <Head>
    <Title>{{ title }}</Title>
  </Head>

  <div v-if="loaded">
    <div class="mb-4 h-screen md:flex md:justify-between">
      <div class="me-5 w-full">
        <div class="flex justify-between">
          <input
            type="text"
            class="mb-3 w-full border-none bg-transparent p-0 text-2xl font-bold leading-7 text-gray-900 focus:ring-0 sm:truncate sm:tracking-tight"
            placeholder="Název"
            v-model="input_name"
            v-on:blur="updateName"
          />

          <div>
            <!--            <organisation-options :organisation="organisation"/>-->
          </div>
        </div>

        <!--        <p class="mb-5 text-base text-gray-500">{{ organisation.type }}</p>-->
        <p
          class="mb-5 text-base text-gray-500"
          v-if="
            organisation.expenses.length === 0 &&
            organisation.incomes.length === 0
          "
        >
          Organizace
        </p>
        <p
          class="mb-5 text-base text-gray-500"
          v-if="
            organisation.expenses.length > 0 &&
            organisation.incomes.length === 0
          "
        >
          Dodavatel
        </p>
        <p
          class="mb-5 text-base text-gray-500"
          v-if="
            organisation.expenses.length === 0 &&
            organisation.incomes.length > 0
          "
        >
          Zákazník
        </p>
        <p
          class="mb-5 text-base text-gray-500"
          v-if="
            organisation.expenses.length > 0 && organisation.incomes.length > 0
          "
        >
          Dodavatel a zákazník
        </p>

        <textarea
          v-model="input_internal_note"
          class="mb-5 w-full border-none bg-transparent p-0 text-base text-slate-700 focus:ring-0"
          placeholder="Přidat popis..."
          v-on:blur="updateInternalNote"
        ></textarea>

        <p class="mb-2 text-gray-700">Fakturační údaje</p>
        <div class="mb-4 rounded-md border border-gray-200 p-5">
          <p class="mb-2 text-red-500">
            Údaje momentálně nelze upravit. Funkci připravujeme.
          </p>

          <p>IČ: {{ organisation.ic }}</p>
          <p>DIČ: {{ organisation.dic }}</p>
          <p>Ulice: {{ organisation.street }}</p>
          <p>Město: {{ organisation.city }}</p>
          <p>PSČ: {{ organisation.postal }}</p>
          <p>Země: {{ organisation.country }}</p>
        </div>

        <div class="mb-2 flex justify-between">
          <div>
            <p class="text-base text-gray-600">Výdaje</p>
          </div>
          <div>
            <button-secondary @click="openModal"
              >+ přidat výdaj
            </button-secondary>
          </div>
        </div>

        <expense-create-modal
          ref="modal_create"
          :default_organisation_uuid="organisation.uuid"
          @expense-created="fetchData"
        />

        <div
          class="mb-4 divide-y divide-slate-200 rounded border border-slate-200"
        >
          <expense-row
            :expense="expense"
            v-for="expense in organisation.expenses"
          ></expense-row>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import ExpenseCreateModal from "~/components/ui/modals/expense_create_modal/ExpenseCreateModal.vue";
import ButtonSecondary from "~/components/ui/ButtonSecondary.vue";

definePageMeta({
  layout: "default",
  middleware: ["sanctum:auth", "sanctum:verified"],
});

const modal_create = useTemplateRef('modal_create');

function openModal() {
  modal_create.value.openModal();
}
</script>

<script>
export default {
  data() {
    return {
      route: null,
      loaded: false,

      organisation: null,
      input_name: "",
      input_internal_note: "",
    };
  },

  mounted() {
    this.route = useRoute();

    this.fetchData();
  },

  computed: {
    title() {
      return this.organisation
        ? this.organisation.name + " - Prognola"
        : "Detail organizace - Prognola";
    },
  },

  methods: {
    async fetchData() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("organisation", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/organisations/" +
            route.params.organisation,
          {
            method: "GET",
          },
        ),
      );

      this.organisation = data.value;
      this.input_name = data.value.name;
      this.input_internal_note = data.value.internal_note;

      this.loaded = true;
    },

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace(".", ",");
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    },

    async updateName() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("organisation", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/organisations/" +
            route.params.organisation,
          {
            method: "PATCH",
            body: {
              name: this.input_name,
            },
          },
        ),
      );
    },

    async updateInternalNote() {
      const client = useSanctumClient();
      const route = useRoute();

      const { data } = await useAsyncData("organisation", () =>
        client(
          "/api/" +
            route.params.workspace +
            "/organisations/" +
            route.params.organisation,
          {
            method: "PATCH",
            body: {
              internal_note: this.input_internal_note,
            },
          },
        ),
      );
    },
  },
};
</script>
