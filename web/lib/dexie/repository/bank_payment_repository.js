import { openDatabase } from "~/lib/dexie/db.js";
import {DateTime} from "luxon";

let db = openDatabase();

function createBankPaymentObjectFromApiData(data) {
  return {
    uuid: data.uuid,
    bank_account: data.bank_account.uuid,
    amount: parseFloat(data.amount),
    issued_at: DateTime.fromISO(data.issued_at).toJSDate(),
  };
}

async function getBankPayments() {
  return await db.bank_payments.toArray();
}

async function addBankPayment(bank_payment) {
  const uuid = db.bank_payments.add(bank_payment);
}

async function getBankPayment(uuid) {
  return await db.bank_payments.get(uuid);
}

async function updateBankPayment(uuid, bank_payment) {
  return await db.bank_payments.update(uuid, bank_payment);
}

async function deleteBankPayment(uuid) {
  return await db.bank_payments.delete(uuid);
}

async function syncBankPayment(bank_payment) {
  console.debug("Syncing bank payment from API:", bank_payment);

  if (typeof bank_payment.deleted_at !== "undefined") {
    console.debug("Bank payment marked as deleted, deleting it.");
    await deleteBankPayment(bank_payment.uuid);

    return;
  }

  const existing_local_record = getBankPayment(bank_payment.uuid);

  if (existing_local_record === null) {
    console.debug("Bank payment not found in local database, adding it.");
    await addBankPayment(bank_payment);
  } else {
    console.debug("Bank payment found in local database, updating it.");
    await updateBankPayment(bank_payment.uuid, bank_payment);
  }
}

export {
  addBankPayment,
  updateBankPayment,
  deleteBankPayment,
  syncBankPayment,
  createBankPaymentObjectFromApiData,
};
