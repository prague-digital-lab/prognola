import { CheckCircle, Spinner } from '@phosphor-icons/react'
import { motion } from 'framer-motion'
import React from 'react'

export default function TaskStateIcon({ task }: { task: _Task }) {
    return (
        <>
            {task.status == "done" ?
                <div className="text-blue">
                    <CheckCircle size={24} weight='fill' />
                </div> :
                task.status == "in_progress" ?
                    <motion.div
                        initial={{ rotate: 0 }}
                        animate={{ rotate: 360 }}
                        transition={{ repeat: Infinity, duration: 6, ease: "linear" }}
                        className="text-black-75">
                        <Spinner size={24} />
                    </motion.div>
                    :
                    <div className="text-black-75">
                        <Spinner size={24} />
                    </div>
            }
        </>
    )
}
