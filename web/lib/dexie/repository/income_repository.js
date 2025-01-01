import { openDatabase } from "~/lib/dexie/db.js";
import { DateTime } from "luxon";

let db = openDatabase();

function createIncomeObjectFromApiData(income) {
  return {
    uuid: income.uuid,
    payment_status: income.payment_status,
    organisation: income.organisation ? income.organisation.uuid : null,
    name: income.name,
    description: income.description,
    paid_at: DateTime.fromISO(income.paid_at).toJSDate(),
    amount: parseInt(income.amount),
  };
}

async function getIncomesByPaidAt(date_from, date_to) {
  return await db.incomes
    .where("paid_at")
    .between(date_from, date_to)
    .toArray();
}

async function getIncomesByOrganisation(organisation_uuid) {
  return await db.incomes
    .where("organisation")
    .equals(organisation_uuid)
    .reverse()
    .sortBy("paid_at");
}

async function addIncome(income) {
  const uuid = db.incomes.add(income);
}

async function getIncome(uuid) {
  return await db.incomes.get(uuid);
}

async function updateIncome(uuid, income_object) {
  return await db.incomes.update(uuid, income_object);
}

async function deleteIncome(uuid) {
  return await db.incomes.delete(uuid);
}

async function syncIncome(income) {
  console.debug("Syncing income from API:", income);

  if (typeof income.deleted_at !== "undefined") {
    console.debug("Income marked as deleted, deleting it.");
    await deleteIncome(income.uuid);

    return;
  }

  const existing_local_record = await getIncome(income.uuid);
  console.log("found existing", existing_local_record);

  if (typeof existing_local_record === 'undefined') {
    console.debug("Income not found in local database, adding it.");
    await addIncome(income);
  } else {
    console.debug("Income found in local database, updating it.");
    await updateIncome(income.uuid, income);
  }
}

export {
  addIncome,
  getIncomesByPaidAt,
  getIncomesByOrganisation,
  createIncomeObjectFromApiData,
  syncIncome,
};
