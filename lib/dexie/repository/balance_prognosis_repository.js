import { openDatabase } from "~/lib/dexie/db.js";
import { DateTime } from "luxon";
import { getExpensesByPaidAt } from "~/lib/dexie/repository/expense_repository.js";
import { getIncomesByPaidAt } from "~/lib/dexie/repository/income_repository.js";

let db = openDatabase();

async function countBalancePrognosisFromNow() {
  // Drop old balance prognosis records
  db.balance_prognosis.clear();

  const date_from = DateTime.now().startOf("day")
  const date_to = date_from.plus({ "year": 3 }).endOf("day")

  let current_date = date_from;

  let current_balance = 0;

  while (current_date <= date_to) {
    let expenses = await getExpensesByPaidAt(current_date.startOf("day").toJSDate(), current_date.endOf("day").toJSDate());
    let incomes = await getIncomesByPaidAt(current_date.startOf("day").toJSDate(), current_date.endOf("day").toJSDate());

    let expenses_sum = sumItemProp(expenses, "price");
    let incomes_sum = sumItemProp(incomes, "amount");

    current_balance = current_balance + incomes_sum - expenses_sum;

    await addBalancePrognosis(current_date.toJSDate(), current_balance);

    current_date = current_date.plus({ "day": 1 });
  }

  console.debug("Balance prognosis counted.");
}

async function getBalancePrognosisByDate(date) {
  return await db.balance_prognosis
    .where("date")
    .equals(date)
    .first();
}

async function addBalancePrognosis(date, balance) {
  db.balance_prognosis.add({
    date: date,
    balance: balance
  });
}

function sumItemProp(items, prop_name) {
  return items.reduce(function(a, b) {
    return a + b[prop_name];
  }, 0);
}

export { countBalancePrognosisFromNow , getBalancePrognosisByDate};
