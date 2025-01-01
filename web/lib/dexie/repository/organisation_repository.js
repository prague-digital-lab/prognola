import { openDatabase } from "~/lib/dexie/db.js";

let db = openDatabase();

async function getOrganisations() {
  return await db.organisations.toArray();
}

async function getOrganisation(uuid) {
  return await db.organisations.get(uuid);
}

async function findOrganisation(uuid) {
  return await db.organisations.get(uuid);
}

function createOrganisationObjectFromApiData(organisation) {
  return {
    uuid: organisation.uuid,
    name: organisation.name,
    type: organisation.type,
    street: organisation.street,
    city: organisation.city,
    postal: organisation.postal,
    country: organisation.country,
    ic: organisation.ic,
    dic: organisation.dic,
    internal_note: organisation.internal_note,
  };
}

async function addOrganisation(organisation) {
  const uuid = db.organisations.add(organisation);
}

async function updateOrganisation(uuid, organisation) {
  return await db.organisations.update(uuid, organisation);
}

async function deleteOrganisation(uuid) {
  return await db.organisations.delete(uuid);
}

async function syncOrganisation(organisation) {
  console.debug("Syncing organisation from API:", organisation);

  if (typeof organisation.deleted_at !== "undefined") {
    console.debug("Organisation marked as deleted, deleting it.");
    await deleteOrganisation(organisation.uuid);

    return;
  }

  const existing_local_record = await getOrganisation(organisation.uuid);

  if (typeof existing_local_record === "undefined") {
    console.debug("Organisation not found in local database, adding it.");
    await addOrganisation(organisation);
  } else {
    console.debug("Organisation found in local database, updating it.");
    await updateOrganisation(organisation.uuid, organisation);
  }
}

export {
  addOrganisation,
  getOrganisations,
  updateOrganisation,
  getOrganisation,
  findOrganisation,
  syncOrganisation,
  createOrganisationObjectFromApiData,
};
