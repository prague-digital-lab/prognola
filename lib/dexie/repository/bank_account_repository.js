import { openDatabase } from "~/lib/dexie/db.js";


let db = openDatabase();

async function addBankAccount(bank_account) {
  let bank_account_object = {
    uuid: bank_account.uuid,
    name: bank_account.name,
    current_amount: parseFloat(bank_account.current_amount)
  };

  const uuid = db.bank_accounts.add(bank_account_object);
}

async function getCurrentBalance() {
  let bank_accounts = await db.bank_accounts.toArray()
  console.log(bank_accounts)

  return bank_accounts.reduce((accumulator, current) => accumulator + current.current_amount, 0);
}


export { addBankAccount, getCurrentBalance };
