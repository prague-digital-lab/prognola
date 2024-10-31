<template>
  <div>
    <div class="mb-2 flex justify-between">
      <div>
        <p class="text-base text-gray-600 dark:text-zinc-400">Výdaje</p>
      </div>
      <div>
        <button-secondary @click="openModal">+ přidat výdaj</button-secondary>
      </div>
    </div>

    <expense-create-modal
      ref="modal_create"
      :default_organisation_uuid="props.organisation.uuid"
      @expense-created="fetchData"
    />

    <div
      class="mb-4 divide-y divide-slate-200 rounded border border-slate-200 dark:divide-zinc-800 dark:border-zinc-800"
    >
      <expense-row :expense="expense" v-for="expense in expenses"></expense-row>
    </div>
  </div>
</template>

<script setup>
import ExpenseCreateModal from "~/components/ui/modals/expense_create_modal/ExpenseCreateModal.vue";
import ButtonSecondary from "~/components/ui/ButtonSecondary.vue";
import { getExpensesByOrganisation } from "~/lib/dexie/repository/expense_repository.js";

const props = defineProps(["organisation"]);

const modal_create = useTemplateRef("modal_create");

const expenses = ref([]);

function openModal() {
  modal_create.value.openModal();
}

onMounted(() => {
  fetchData();
});

async function fetchData() {
  const client = useSanctumClient();
  const route = useRoute();

  expenses.value = await getExpensesByOrganisation(props.organisation.uuid)
}
</script>

<style scoped></style>
