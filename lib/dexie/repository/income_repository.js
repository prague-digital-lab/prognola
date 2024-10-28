import { openDatabase } from "~/lib/dexie/db.js";
import { DateTime } from "luxon";

let db = openDatabase();

async function addIncome(income) {
  // console.debug("Storing income:", income);

  let income_object = {
    uuid: income.uuid,
    payment_status: income.payment_status,
    organisation: income.organisation ? income.organisation.uuid : null,
    name: income.name,
    description: income.description,
    paid_at: DateTime.fromISO(income.paid_at).toJSDate(),
    amount: parseInt(income.amount),
  };

  const uuid = db.incomes.add(income_object);
}

export { addIncome };
