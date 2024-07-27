import { motion } from 'framer-motion'
import React from 'react'

export default function LoadingSkeleton(props:{
    width: number|string, 
    height: number|string
}) {


    return (
        <motion.div
        initial={{opacity: 0}}
        animate={{opacity: 1}}
        transition={{repeat: Infinity, repeatType: "reverse", duration: 0.2}}
        style={{width: props.width, height: props.height}} className='rounded bg-black-10'>

        </motion.div>
    )
}
