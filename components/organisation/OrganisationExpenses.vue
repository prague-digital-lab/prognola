<template>
  <div>
    <div class="mb-2 flex justify-between">
      <div>
        <p class="text-base text-gray-600 dark:text-zinc-400">Výdaje</p>
      </div>
      <div>
        <select
          v-model="date_type"
          v-if="date_type"
          @change="changeDateType"
          class="me-2 h-[30px] rounded-md border border-gray-200 py-0 text-sm focus:ring-transparent dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-300"
        >
          <option value="paid_at">Datum platby</option>
          <option value="received_at">Datum přijetí</option>
        </select>

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
      <expense-row
        :expense="expense"
        v-for="expense in expenses"
        :key="expense.uuid"
      ></expense-row>
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

const date_type = ref("paid_at");

function openModal() {
  modal_create.value.openModal();
}

onMounted(() => {
  if (localStorage.getItem("date_type") !== null) {
    date_type.value = localStorage.getItem("date_type");
  }

  fetchData();
});

async function fetchData() {
  if (date_type.value === "received_at") {
    let expenses_data = await getExpensesByOrganisation(
      props.organisation.uuid,
      "received_at",
    );

    expenses.value = expenses_data.filter((item) => {
      return item.payment_status !== "plan";
    });
  } else {
    expenses.value = await getExpensesByOrganisation(
      props.organisation.uuid,
      "paid_at",
    );
  }
}

function changeDateType(event) {
  localStorage.setItem("date_type", event.target.value);

  date_type.value = event.target.value;
  fetchData();
}
</script>

<style scoped></style>
