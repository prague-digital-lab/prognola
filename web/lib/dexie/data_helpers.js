import {DateTime} from "luxon";

function sumItemProp(items, prop_name) {
    return items.reduce(function (a, b) {
        return a + b[prop_name];
    }, 0);
}

function formatDate(date) {
    let formatted = DateTime.fromJSDate(date);

    return formatted.toFormat("d.M.yyyy");
}

export { sumItemProp, formatDate };