import {
  addExpense,
  createExpenseObjectFromApiData,
  syncExpense,
} from "~/lib/dexie/repository/expense_repository.js";
import {
  addIncome,
  createIncomeObjectFromApiData,
  syncIncome,
} from "~/lib/dexie/repository/income_repository.js";
import {
  addOrganisation,
  createOrganisationObjectFromApiData,
  syncOrganisation,
} from "~/lib/dexie/repository/organisation_repository.js";
import {
  addBankAccount,
  createBankAccountObjectFromApiData,
  syncBankAccount,
} from "~/lib/dexie/repository/bank_account_repository.js";
import {
  getLastSyncedAt,
  setLastSyncedAt,
} from "~/lib/dexie/repository/options_repository.js";
import { DateTime } from "luxon";
import {
  addBankPayment,
  createBankPaymentObjectFromApiData,
} from "~/lib/dexie/repository/bank_payment_repository.js";

function truncateStores(db) {
  console.debug("Truncating old IndexedDB stores.");
  db.expenses.clear();
  db.incomes.clear();
}

async function bootstrapDatabase(db, workspace_url_slug) {
  console.debug("Bootstrapping database:", workspace_url_slug);

  const last_synced_at = await getLastSyncedAt();
  console.debug("Last synced at:", last_synced_at);

  if (last_synced_at === null) {
    await bootstrapDatabaseFull(db, workspace_url_slug);

    await setLastSyncedAt(new Date());
  } else {
    await bootstrapDatabaseIncremental(workspace_url_slug, last_synced_at);

    await setLastSyncedAt(new Date());
  }
}

async function bootstrapDatabaseFull(db, workspace_url_slug) {
  console.debug("Full bootstrap of database: ", workspace_url_slug);

  truncateStores(db);

  let expenses = await fetchExpenses(workspace_url_slug);

  for (let expense of expenses) {
    let expense_object = createExpenseObjectFromApiData(expense);
    await addExpense(expense_object);
  }

  let incomes = await fetchIncomes(workspace_url_slug);

  for (let income of incomes) {
    let income_object = createIncomeObjectFromApiData(income);
    await addIncome(income_object);
  }

  let organisations = await fetchOrganisations(workspace_url_slug);

  for (let organisation of organisations) {
    let organisation_object = createOrganisationObjectFromApiData(organisation);
    await addOrganisation(organisation_object);
  }

  let bank_accounts = await fetchBankAccounts(workspace_url_slug);

  for (let bank_account of bank_accounts) {
    let bank_account_object = createBankAccountObjectFromApiData(bank_account);
    await addBankAccount(bank_account_object);
  }

  let bank_payments = await fetchBankPayments(workspace_url_slug);

  for (let bank_payment of bank_payments) {
    let bank_payment_object = createBankPaymentObjectFromApiData(bank_payment);
    console.log(bank_payment_object);
    await addBankPayment(bank_payment_object);
  }

  console.debug("Full bootstrap of database completed.");
}

async function bootstrapDatabaseIncremental(
  workspace_url_slug,
  last_synced_at,
) {
  console.debug("Running incremental database bootstrap.");

  const client = useSanctumClient();

  const date = DateTime.fromJSDate(last_synced_at).toISO();

  const data = await client("/api/" + workspace_url_slug + "/bootstrap", {
    method: "GET",
    query: {
      date_from: date,
    },
  });

  const expenses = data.expenses;

  for (let expense of expenses) {
    let expense_object = createExpenseObjectFromApiData(expense);
    await syncExpense(expense_object);
  }

  const incomes = data.incomes;

  for (let income of incomes) {
    let income_object = createIncomeObjectFromApiData(income);
    await syncIncome(income_object);
  }

  const organisations = data.organisations;

  for (let organisation of organisations) {
    let organisation_object = createOrganisationObjectFromApiData(organisation);
    await syncOrganisation(organisation_object);
  }

  const bank_accounts = data.bank_accounts;

  for (let bank_account of bank_accounts) {
    let bank_account_object = createBankAccountObjectFromApiData(bank_account);
    await syncBankAccount(bank_account_object);
  }

  console.debug("Incremental database bootstrap completed.");
}

async function fetchExpenses(workspace_url_slug) {
  const client = useSanctumClient();

  console.debug("Fetching expenses from API.");

  const data = await client("/api/" + workspace_url_slug + "/expenses", {
    method: "GET",
  });

  return data.data;
}

async function fetchIncomes(workspace_url_slug) {
  const client = useSanctumClient();

  console.debug("Fetching incomes from API.");

  const data = await client("/api/" + workspace_url_slug + "/incomes", {
    method: "GET",
  });

  return data.data;
}

async function fetchOrganisations(workspace_url_slug) {
  const client = useSanctumClient();

  console.debug("Fetching organisations from API.");

  return await client("/api/" + workspace_url_slug + "/organisations", {
    method: "GET",
  });
}

async function fetchBankAccounts(workspace_url_slug) {
  const client = useSanctumClient();

  return await client("/api/" + workspace_url_slug + "/bank_accounts", {
    method: "GET",
  });
}

async function fetchBankPayments(workspace_url_slug) {
  const client = useSanctumClient();

  return await client("/api/" + workspace_url_slug + "/bank_payments", {
    method: "GET",
  });
}

export { bootstrapDatabase, bootstrapDatabaseFull };
