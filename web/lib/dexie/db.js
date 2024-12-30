// db.js
import Dexie from "dexie";

function openDatabase() {
  let workspace_url_slug = localStorage.getItem("active_workspace_url_slug");

  let store_name = "store-" + workspace_url_slug;
  console.debug("Opening IndexedDB database:", store_name);

  const db = new Dexie(store_name);
  db.version(3).stores({
    expenses: "++uuid, paid_at, received_at, organisation, payment_status",
    incomes: "++uuid, paid_at, organisation",
    organisations: "++uuid, name",
    balance_prognosis: "++date",
    bank_accounts: "++uuid",
    options: "++key",
  });

  return db;
}

export { openDatabase };
