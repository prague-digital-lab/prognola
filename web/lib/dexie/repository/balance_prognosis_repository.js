import { openDatabase } from "~/lib/dexie/db.js";
import { DateTime } from "luxon";
import { getExpensesByPaidAt } from "~/lib/dexie/repository/expense_repository.js";
import { getIncomesByPaidAt } from "~/lib/dexie/repository/income_repository.js";
import { getCurrentBalance } from "~/lib/dexie/repository/bank_account_repository.js";

let db = openDatabase();

async function countBalancePrognosisFromNow() {
  // Drop old balance prognosis records
  db.balance_prognosis.clear();

  const date_from = DateTime.now().startOf("day");
  const date_to = date_from.plus({ year: 3 }).endOf("day");

  let current_date = date_from;

  let current_balance = await getCurrentBalance();

  let due_from = current_date.minus({ year: 10 }).startOf("day").toJSDate();
  let due_to = current_date.minus({ days: 1 }).endOf("day").toJSDate();

  let due_expenses = await getExpensesByPaidAt(due_from, due_to);
  due_expenses = filterPlanned(due_expenses);
  let due_incomes = await getIncomesByPaidAt(due_from, due_to);
  due_incomes = filterPlanned(due_incomes);

  let due_expenses_sum = sumItemProp(due_expenses, "price");
  let due_incomes_sum = sumItemProp(due_incomes, "amount");

  let due_balance = due_incomes_sum - due_expenses_sum;
  current_balance = current_balance + due_balance;

  console.debug("Due incomes:", due_incomes_sum);
  console.debug("Due expenses:", due_expenses_sum);

  while (current_date <= date_to) {
    let expenses = await getExpensesByPaidAt(
      current_date.startOf("day").toJSDate(),
      current_date.endOf("day").toJSDate(),
    );
    let incomes = await getIncomesByPaidAt(
      current_date.startOf("day").toJSDate(),
      current_date.endOf("day").toJSDate(),
    );

    let expenses_sum = sumItemProp(expenses, "price");
    let incomes_sum = sumItemProp(incomes, "amount");

    current_balance = current_balance + incomes_sum - expenses_sum;

    await addBalancePrognosis(current_date.toJSDate(), current_balance);

    current_date = current_date.plus({ day: 1 });
  }

  console.debug("Balance prognosis counted.");
}

async function getBalancePrognosisByDate(date) {
  return await db.balance_prognosis.where("date").equals(date).first();
}

async function addBalancePrognosis(date, balance) {
  db.balance_prognosis.add({
    date: date,
    balance: balance,
  });
}

function sumItemProp(items, prop_name) {
  return items.reduce(function (a, b) {
    return a + b[prop_name];
  }, 0);
}

function filterPlanned(items) {
  return items.filter((item) => {
    return item.payment_status === "plan" || item.payment_status === "pending";
  });
}

export { countBalancePrognosisFromNow, getBalancePrognosisByDate };
