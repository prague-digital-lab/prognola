import { CaretDown } from '@phosphor-icons/react'
import { motion } from 'framer-motion'
import React, { ReactElement, useState } from 'react'

export default function Accordion(props:{
    title?: ReactElement,
    children?: ReactElement
}) {

    const [opened, setOpened] = useState(false)

    return (
        <div className="bg-white rounded">
            <button onClick={() => setOpened(!opened)} className='bg.white px-3 py-2 w-full flex justify-between'>
                <div className="">{props.title}</div>
                <motion.div
                animate={{rotate: opened ? 180: 0}}
                className="">
                <CaretDown size={24} />
                </motion.div>
            </button>
            {opened &&
            <>{props.children}</>
            }
        </div>

    )
}
