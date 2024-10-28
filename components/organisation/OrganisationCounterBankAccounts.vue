<template>
  <div>
    <div class="mb-2 flex justify-between">
      <div>
        <p class="text-base text-gray-600 dark:text-zinc-400">
          Bankovní účty organizace
        </p>
      </div>
      <div>
        <!--        <button-secondary @click="openModal">+ přidat výdaj</button-secondary>-->
      </div>
    </div>

    <!--    <expense-create-modal-->
    <!--      ref="modal_create"-->
    <!--      :default_organisation_uuid="props.organisation.uuid"-->
    <!--      @expense-created="fetchData"-->
    <!--    />-->

    <div
      class="mb-4 divide-y divide-slate-200 rounded border border-slate-200 dark:divide-zinc-800 dark:border-zinc-800"
    >
      <counter-bank-account-row
        :counter_bank_account="counter_bank_account"
        v-for="counter_bank_account in counter_bank_accounts"
      ></counter-bank-account-row>
    </div>
  </div>
</template>

<script setup>
import ExpenseCreateModal from "~/components/ui/modals/expense_create_modal/ExpenseCreateModal.vue";
import ButtonSecondary from "~/components/ui/ButtonSecondary.vue";
import organisation from "~/pages/[workspace]/organisations/[organisation].vue";

const props = defineProps(["organisation"]);

const modal_create = useTemplateRef("modal_create");

const counter_bank_accounts = ref([]);

function openModal() {
  modal_create.value.openModal();
}

onMounted(() => {
  fetchData();
});

async function fetchData() {
  const client = useSanctumClient();
  const route = useRoute();

  const { data } = await useAsyncData("counter_bank_accounts", () =>
    client("/api/" + route.params.workspace + "/counter_bank_accounts", {
      method: "GET",
      query: {
        organisation: props.organisation.uuid,
      },
    }),
  );

  counter_bank_accounts.value = data.value;
}
</script>

<style scoped></style>
