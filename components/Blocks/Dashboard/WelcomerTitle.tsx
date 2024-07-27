import inflectionGenerator from '@/libs/utils/inflectionGenerator'
import React from 'react'

export default function WelcomerTitle(props: {
    name: string
}) {

    return (
        <h1 className='font-light text-xl md:text-2xl'>{titleGenerator(props.name)}</h1>
    )
}

function titleGenerator(name: string) {

    const time = new Date()

    if (time.getHours() < 9) {
        return `Dobré ráno 🌤️ ${inflectionGenerator(name)}, jak bys rád začal svůj den?`
    }
    else if (time.getHours() < 12) {
        return `Dobré dopoledne 🌤️, ${inflectionGenerator(name)}, jak bys ho rád využil?`
    }
    else if (time.getHours() < 18) {
        return `Dobré odpoledne ☀️, ${inflectionGenerator(name)}, co zkusit něco z následující nabídky?`
    }
    else if (time.getHours() < 20) {
        return `Přejeme pěkný večer, ${inflectionGenerator(name)}, vyber si, o co máš zájem...`
    }
    else if (time.getHours() < 24) {
        return `${inflectionGenerator(name)}, možná Ti něco nedá spát, co zkusit třeba něco z tohohle? 😊`
    }
}
