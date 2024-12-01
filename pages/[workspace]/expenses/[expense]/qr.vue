<template>
  <div v-if="loaded">
    <page-content-header>
      <template v-slot:title>
        <heading>QR platba pro výdaj {{ expense.description }}</heading>
      </template>
    </page-content-header>

    <div v-if="accounts.length > 0" class="grid grid-cols-2">
      <div class="dark:text-zinc-300">
        <div class="mb-4">
          <label class="block">Částka</label>
          <input
            v-model="price"
            class="rounded-md dark:border dark:border-zinc-800 dark:bg-zinc-900"
          />
        </div>


        <div class="mb-4">
          <label class="block">Variabilní symbol</label>
          <input
            v-model="variable_symbol"
            class="rounded-md dark:border dark:border-zinc-800 dark:bg-zinc-900"
          />
        </div>

        <div class="mb-4">
          <label class="block">Zpráva pro příjemce</label>
          <input
            v-model="message"
            class="rounded-md dark:border dark:border-zinc-800 dark:bg-zinc-900"
          />
        </div>

        <div class="mb-4">
          <label class="block">Bankovní účet</label>
          <select
            v-model="counter_bank_account"
            class="rounded-md dark:border dark:border-zinc-800 dark:bg-zinc-900"
          >
            <option v-for="account in accounts" :value="account">
              {{ account.account_number }}/{{ account.bank_number }}
            </option>
          </select>
        </div>
      </div>

      <div class="flex justify-end">
        <qrcode-vue
          :value="qr_value"
          class="mb-4"
          :size="300"
          level="H"
          render-as="svg"
        />
      </div>
    </div>

    <div v-else class="dark:text-zinc-300">
      Organizace daného výdaje nemá uložen žádný bankovní účet.
    </div>
  </div>
</template>

<script setup>
import PageContentHeader from "~/components/ui/PageContentHeader.vue";
import Heading from "~/components/ui/Heading.vue";
import QrcodeVue from "qrcode.vue";
import { CountryCode, IBANBuilder } from "ibankit";

const loaded = ref(false);

const expense = ref(null);

const price = ref(null);
const variable_symbol = ref(null);
const message = ref(null);
const counter_bank_account = ref(null);

const accounts = ref([]);
const qr_value = ref(null);

async function fetchData() {
  const client = useSanctumClient();
  const route = useRoute();

  const { data } = await useAsyncData("expense", () =>
    client(
      "/api/" + route.params.workspace + "/expenses/" + route.params.expense,
      {
        method: "GET"
      }
    )
  );

  expense.value = data.value;
  price.value = expense.value.price;
  variable_symbol.value = expense.value.variable_symbol;
  message.value = expense.value.description;

  const { data: counter_bank_accounts } = await useAsyncData(
    "counter_bank_accounts",
    () =>
      client("/api/" + route.params.workspace + "/counter_bank_accounts", {
        method: "GET",
        query: {
          organisation: expense.value.organisation.uuid
        }
      })
  );

  console.log("Setting accounts");

  accounts.value = counter_bank_accounts.value;

  if (counter_bank_accounts.value.length > 0) {
    console.log("Choosing default counter_bank_account");

    counter_bank_account.value = counter_bank_accounts.value[0];
  }

  loaded.value = true;
}

onMounted(async () => {
  await fetchData();
});

watch(counter_bank_account, (newX, oldX) => {
  updateQrCode();
});
watch(variable_symbol, (newX, oldX) => {
  updateQrCode();
});

watch(price, (newX, oldX) => {
  updateQrCode();
});
watch(message, (newX, oldX) => {
  updateQrCode();
});

function updateQrCode() {
  if (
    counter_bank_account.value === undefined ||
    counter_bank_account.value === null
  ) {
    return;
  }
  const zeroPad = (num, places) => String(num).padStart(places, "0");

  if (counter_bank_account.value.account_number.includes("-")) {
    let account_number_arr =
      counter_bank_account.value.account_number.split("-");

    let brach_number_leading = zeroPad(account_number_arr[0], 6);
    let bank_number_leading = zeroPad(account_number_arr[1], 10);

    let iban = new IBANBuilder()
      .countryCode(CountryCode.CZ)
      .accountNumber(bank_number_leading)
      .branchCode(brach_number_leading)
      .bankCode(counter_bank_account.value.bank_number)
      .build()
      .toString();

    qr_value.value =
      "SPD*1.0*ACC:" +
      iban +
      "*AM:" +
      price.value +
      "*X-VS:" +
      variable_symbol.value +
      "*CC:CZK*MSG:" +
      message.value;
  } else {
    let bank_number_leading = zeroPad(
      counter_bank_account.value.account_number,
      10
    );

    let iban = new IBANBuilder()
      .countryCode(CountryCode.CZ)
      .accountNumber(bank_number_leading)
      .branchCode("000000")
      .bankCode(counter_bank_account.value.bank_number)
      .build()
      .toString();

    qr_value.value =
      "SPD*1.0*ACC:" +
      iban +
      "*AM:" +
      price.value +
      "*X-VS:" +
      variable_symbol.value +
      "*CC:CZK*MSG:" +
      message.value;
  }

  // return expense.value.amount;
}
</script>

<style scoped></style>
