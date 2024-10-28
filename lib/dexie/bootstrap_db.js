import { addExpense } from "~/lib/dexie/repository/expense_repository.js";
import { addIncome } from "~/lib/dexie/repository/income_repository.js";

function truncateStores(db) {
  console.debug("Truncating old IndexedDB stores.");
  db.expenses.clear();
  db.incomes.clear();
}

async function bootstrapDatabase(db, workspace_url_slug) {
  console.debug("Bootstrapping database.");

  truncateStores(db);

  let expenses = await fetchExpenses(workspace_url_slug);

  for (let expense of expenses) {
    await addExpense(expense);
  }

  let incomes = await fetchIncomes(workspace_url_slug);

  for (let income of incomes) {
    await addIncome(income);
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

export default bootstrapDatabase;
