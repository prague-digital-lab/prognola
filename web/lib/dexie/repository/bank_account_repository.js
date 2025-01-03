import { openDatabase } from "~/lib/dexie/db.js";
import {DateTime} from "luxon";

let db = openDatabase();

function createBankAccountObjectFromApiData(data) {
  return {
    uuid: data.uuid,
    name: data.name,
    current_amount: parseFloat(data.current_amount),
    account_number: data.account_number,
    bank_number: data.bank_number,
    bank: data.bank,
    synced_at: data.synced_at
        ? DateTime.fromISO(data.synced_at).toJSDate()
        : null,
  };
}

async function getBankAccounts() {
  return await db.bank_accounts.toArray();
}

async function addBankAccount(bank_account) {
  const uuid = db.bank_accounts.add(bank_account);
}

async function getBankAccount(uuid) {
  return await db.bank_accounts.get(uuid);
}

async function updateBankAccount(uuid, bank_account) {
  return await db.bank_accounts.update(uuid, bank_account);
}

async function deleteBankAccount(uuid) {
  return await db.bank_accounts.delete(uuid);
}

async function getCurrentBalance() {
  let bank_accounts = await db.bank_accounts.toArray();

  return bank_accounts.reduce(
    (accumulator, current) => accumulator + current.current_amount,
    0,
  );
}

async function syncBankAccount(bank_account) {
  console.debug("Syncing bank_account from API:", bank_account);

  if (typeof bank_account.deleted_at !== "undefined") {
    console.debug("Bank account marked as deleted, deleting it.");
    await deleteBankAccount(bank_account.uuid);

    return;
  }

  const existing_local_record = await getBankAccount(bank_account.uuid);

  if (typeof existing_local_record === 'undefined') {
    console.debug("Bank account not found in local database, adding it.");
    await addBankAccount(bank_account);
  } else {
    console.debug("Bank account found in local database, updating it.");
    await updateBankAccount(bank_account.uuid, bank_account);
  }
}

export {
  getBankAccounts,
  addBankAccount,
  getCurrentBalance,
  getBankAccount,
  updateBankAccount,
  deleteBankAccount,
  syncBankAccount,
  createBankAccountObjectFromApiData,
};
