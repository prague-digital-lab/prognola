function sumItemProp(items, prop_name) {
    return items.reduce(function (a, b) {
        return a + b[prop_name];
    }, 0);
}

export { sumItemProp };