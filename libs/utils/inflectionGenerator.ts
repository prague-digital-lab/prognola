


export default function inflectionGenerator(fName: string) {

    const firstName = fName
    const lastLetter = firstName?.slice(-1)
    const semiLastLetter = firstName?.slice(-2, -1)
    let result = firstName

    const forbiddenNames = ["Ingrid", "Dagmar", "Arion", "Marion", "Manon", "Orion"]

    if (!forbiddenNames.includes(firstName)) {
        switch (lastLetter) {
            case "a": case "u":
                let work = firstName.slice(0, -1)
                result = work + "o"
                break;
            case "r":
                if (semiLastLetter != "a" && semiLastLetter != "e" && semiLastLetter != "i" && semiLastLetter != "o" && semiLastLetter != "u") {
                    let repl = firstName.slice(0, -1)
                    result = repl + "ře"
                    break
                }
                else if (semiLastLetter != "e") {
                    result = firstName + "e"
                    break
                }
                else {
                    result = firstName
                    break
                }
            case "b": case "d": case "f": case "l": case "m": case "l": case "m": case "n": case "t": case "v":
                result = firstName + "e"
                break;
            case "c": case "j": case "s": case "x": case "z": case "č": case "ř":
                result = firstName + "i"
                break;
            case "g": case "h": case "k": case "p":
                if (semiLastLetter == "ě") {
                    let repl = firstName.slice(0, -3)
                    result = repl + "ňku"
                    break
                }
                if (semiLastLetter == "e") {
                    let repl = firstName.slice(0, -2)
                    result = repl + "ku"
                    break
                }
                result = firstName + "u"
                break;
            default:
                result = firstName
        }
    }


    return result

}