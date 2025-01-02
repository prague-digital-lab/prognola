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

function formatPrice(value) {
    let val = (value / 1).toFixed(0).replace(".", ",");
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

export { sumItemProp, formatDate, formatPrice };