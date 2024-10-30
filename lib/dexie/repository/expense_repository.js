import { openDatabase } from "~/lib/dexie/db.js";
import { DateTime } from "luxon";

let db = openDatabase();

async function getExpensesByPaidAt(date_from, date_to) {
  return await db.expenses
    .where("paid_at")
    .between(date_from, date_to)
    .toArray();
}

async function getExpensesByOrganisation(organisation_uuid) {
  return await db.expenses
    .where("organisation")
    .equals(organisation_uuid)
    .reverse()
    .sortBy('paid_at')
}

async function updateExpense(uuid, expense) {
  let expense_object = createExpenseObject(expense);

  return await db.expenses
    .update(uuid, expense_object);
}

function createExpenseObject(expense) {
  return {
    uuid: expense.uuid,
    payment_status: expense.payment_status,
    organisation: expense.organisation ? expense.organisation.uuid : null,
    description: expense.description,
    paid_at: DateTime.fromISO(expense.paid_at).toJSDate(),
    price: parseInt(expense.price)
  };

}

async function addExpense(expense) {
  // console.debug("Storing expense:", expense);

  let expense_object = createExpenseObject(expense);

  const uuid = db.expenses.add(expense_object);
}

export { getExpensesByPaidAt, addExpense, updateExpense, getExpensesByOrganisation };
