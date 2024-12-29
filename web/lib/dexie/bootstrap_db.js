import { addExpense } from "~/lib/dexie/repository/expense_repository.js";
import { addIncome } from "~/lib/dexie/repository/income_repository.js";
import { addOrganisation } from "~/lib/dexie/repository/organisation_repository.js";
import { addBankAccount } from "~/lib/dexie/repository/bank_account_repository.js";

function truncateStores(db) {
  console.debug("Truncating old IndexedDB stores.");
  db.expenses.clear();
  db.incomes.clear();
}

async function bootstrapDatabase(db, workspace_url_slug) {
  console.debug("Bootstrapping database:", workspace_url_slug);

  truncateStores(db);

  let expenses = await fetchExpenses(workspace_url_slug);

  for (let expense of expenses) {
    await addExpense(expense);
  }

  let incomes = await fetchIncomes(workspace_url_slug);

  for (let income of incomes) {
    await addIncome(income);
  }

  let organisations = await fetchOrganisations(workspace_url_slug);

  for (let organisation of organisations) {
    await addOrganisation(organisation);
  }

  let bank_accounts = await fetchBankAccounts(workspace_url_slug);

  for (let bank_account of bank_accounts) {
    await addBankAccount(bank_account);
  }

  console.debug("Bootstrapping database completed.");
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

export default bootstrapDatabase;
