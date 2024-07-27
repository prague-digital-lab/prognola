import { CheckCircle, Spinner } from '@phosphor-icons/react'
import { formatDistance, intervalToDuration } from 'date-fns'
import React from 'react'
import ProgressBar from '../UI/ProgressBar'
import { motion } from 'framer-motion'
import { cs } from 'date-fns/locale'
import Link from 'next/link'

export default function TaskSmCard({ task, gray }: {
    task: _Task,
    gray?: boolean
}) {
    return (
        <Link href={`/task/${task.uuid}`} className={`${task.status == "done" ? `${gray ? "bg-gray-medium border-gray-medium" : "bg-blue border-blue"} text-white` : "bg-white border-white"} rounded p-2 text-sm font-medium min-h-24 flex flex-col justify-between active:border-black-25 duration-75 border`}>
            <p className="max-h-10 overflow-hidden text-ellipsis text-left">{task.name}</p>
            <div className="w-full">

                {task.status == "done" ?
                    <div className="flex gap-1 items-center justify-between w-full text-left">{task.worked_seconds && `${formatDistance(0, task.worked_seconds * 1000, { locale: cs })}`}<CheckCircle size={26} weight='fill' /></div>
                    :
                    <div className="grid grid-cols-1 gap-1 w-full">
                        {(task.worked_seconds && task.estimated_seconds) &&
                            <div className="">
                                <ProgressBar progress={task.worked_seconds / task.estimated_seconds*100} />
                            </div>
                        }
                        <div className="flex gap-1 items-center justify-between w-full text-left">{!task.estimated_seconds ? "Chybí odhad" : task.worked_seconds ? task.worked_seconds>task.estimated_seconds ? "brzy hotové": `ještě ${formatDistance(0, (task.estimated_seconds - task.worked_seconds) * 1000, { locale: cs })}` : `${task?.estimated_seconds / 60} min`}

                            {task.status == "in_progress" && <motion.div
                                initial={{ rotate: 0 }}
                                animate={{ rotate: 360 }}
                                transition={{ repeat: Infinity, duration: 6, ease: "linear" }}
                                className="text-black-75">
                                <Spinner size={24} />
                            </motion.div>}
                        </div>
                    </div>

                }
            </div>
        </Link>
    )
}
