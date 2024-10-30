// db.js
import Dexie from "dexie";

function openDatabase() {
  console.debug("Opening IndexedDB database.");

  let workspace_url_slug = localStorage.getItem("active_workspace_url_slug");

  let store_name = "store-" + workspace_url_slug;

  const db = new Dexie(store_name);
  db.version(3).stores({
    expenses: "++uuid, paid_at, organisation",
    incomes: "++uuid, paid_at, organisation",
    organisations: "++uuid, name",
    balance_prognosis: "++date",
  });

  return db;
}

export { openDatabase };
