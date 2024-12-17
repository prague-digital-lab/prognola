import { openDatabase } from "~/lib/dexie/db.js";
import { DateTime } from "luxon";

let db = openDatabase();

async function getExpensesByPaidAt(date_from, date_to) {
  return await db.expenses
    .where("paid_at")
    .between(date_from, date_to)
    .toArray();
}

async function getExpensesByOrganisation(
  organisation_uuid,
  sort_by = "paid_at",
) {
  return await db.expenses
    .where("organisation")
    .equals(organisation_uuid)
    .reverse()
    .sortBy(sort_by);
}

export async function getExpensesByPaymentStatus(payment_status) {
  return await db.expenses
    .where("payment_status")
    .equals(payment_status)
    .toArray();
}

async function updateExpense(uuid, expense) {
  let expense_object = createExpenseObject(expense);

  return await db.expenses.update(uuid, expense_object);
}

async function updateExpenseFromLocalObject(uuid, expense_object) {
  return await db.expenses.update(uuid, expense_object);
}

async function updateExpenseOrganisation(uuid, expense) {
  let expense_object = createExpenseObject(expense);

  return await db.expenses.update(uuid, expense_object);
}

async function getExpense(uuid) {
  return await db.expenses.get(uuid);
}

async function deleteExpense(uuid) {
  return await db.expenses.delete(uuid);
}

function createExpenseObject(expense) {
  return {
    uuid: expense.uuid,
    payment_status: expense.payment_status,
    organisation: expense.organisation ? expense.organisation.uuid : null,
    description: expense.description,
    variable_symbol: expense.variable_symbol,
    received_at: expense.received_at
      ? DateTime.fromISO(expense.received_at).toJSDate()
      : null,
    paid_at: DateTime.fromISO(expense.paid_at).toJSDate(),
    price: parseInt(expense.price),
  };
}

async function addExpense(expense) {
  // console.debug("Storing expense:", expense);

  let expense_object = createExpenseObject(expense);

  const uuid = db.expenses.add(expense_object);
}

export {
  deleteExpense,
  getExpensesByPaidAt,
  updateExpenseFromLocalObject,
  getExpense,
  addExpense,
  updateExpense,
  getExpensesByOrganisation,
};
