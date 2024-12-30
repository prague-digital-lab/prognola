import { openDatabase } from "~/lib/dexie/db.js";

let db = openDatabase();

async function getLastSyncedAt() {
  return await getOptionByKey("last_synced_at");
}

async function setLastSyncedAt(value) {
  await setOptionByKey("last_synced_at", value);
}

async function getOptionByKey(key) {
  const option = await db.options.get(key);

  if (typeof option !== "undefined") {
    return option.value;
  } else {
    return null;
  }
}

async function setOptionByKey(key, value) {
  await db.options.put({ key: key, value: value });
}

export { getLastSyncedAt, setLastSyncedAt };
