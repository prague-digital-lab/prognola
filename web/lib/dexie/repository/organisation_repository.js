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

async function addOrganisation(organisation) {
  let organisation_object = {
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

  const uuid = db.organisations.add(organisation_object);
}

export { addOrganisation, getOrganisations, getOrganisation, findOrganisation };
