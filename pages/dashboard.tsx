import { AppContext } from '@/components/Contexts/AppContext'
import AppLayout from '@/components/Layouts/AppLayout'
import { useContexts } from '@/libs/hooks/contexts'
import React, { ReactElement, useContext, useEffect, useState } from 'react'
import { NextPageWithLayout } from './_app'
import { useRouter } from 'next/router'
import { useTasks } from '@/libs/hooks/tasks'
import WelcomerTitle from '@/components/Blocks/Dashboard/WelcomerTitle'
import Head from 'next/head'
import { format } from 'date-fns'
import Title from '@/components/Typography/Title'
import { CaretDown, CheckCircle, CircleNotch, Spinner, SpinnerGap } from '@phosphor-icons/react'
import Link from '@/components/UI/Link'
import TaskSmCard from '@/components/Cards/TaskSmCard'
import { motion } from 'framer-motion'
import LoadingSkeleton from '@/components/UI/LoadingSkeleton'
import Accordion from '@/components/UI/Accordion'
import TaskStateIcon from '@/components/Blocks/Dashboard/TaskStateIcon'
import inflectionGenerator from '@/libs/utils/inflectionGenerator'
import Image from 'next/image'
import NextLink from "next/link"

const Dashboard: NextPageWithLayout = () => {

    const router = useRouter()
    const { app, setApp } = useContext(AppContext)
    const { data, isLoading, error } = useTasks(app.token)
    const tasks = data as Array<_Task>

    const [enableRedirect, setEnableRedirect] = useState(false)

    useEffect(() => {
        if (error?.response?.status == 401 && enableRedirect == true) {
            console.log(error.response.status)
            setApp({ loggedIn: false, token: null })
            localStorage.removeItem("token")
            setEnableRedirect(false)
            router.push("/")
        }
    }, [error]);

    useEffect(() => {
        setEnableRedirect(true)
    }, []);

    const taskWithoutDoneAnotherDay = tasks && tasks?.filter((task) => task.status != "done" || (format(new Date(task?.done_at ?? 0), 'yyyy-MM-dd')) == format(new Date(), 'yyyy-MM-dd'))
    const easyTasks = taskWithoutDoneAnotherDay?.filter((task) => (task.estimated_seconds && task?.estimated_seconds < 900))
    const mediumTasks = taskWithoutDoneAnotherDay?.filter((task) => (task.estimated_seconds && task?.estimated_seconds >= 900 && task?.estimated_seconds < 3600))
    const hardTasks = taskWithoutDoneAnotherDay?.filter((task) => (task.estimated_seconds && task?.estimated_seconds >= 3600))

    const todayWorked = tasks && tasks.filter((task) => (format(new Date(task?.worked_at ?? 0), 'yyyy-MM-dd')) == format(new Date(), 'yyyy-MM-dd') || (task.status == "in_progress")).sort((a, b) => a.status == "done" ? -1 : b.status == "in_progress" ? -1 : 1)
    const taskDoneBefore = tasks && tasks?.filter((task) => (task.status == "done") && (format(new Date(task?.done_at ?? 0), 'yyyy-MM-dd')) != format(new Date(), 'yyyy-MM-dd'))

    return (
        <div className="relative">
            <div className='relative z-10 md:pt-4'>
                <Head>
                    <title>Gentletask</title>
                </Head>
                <Accordion title={<div className="flex items-center gap-2">
                    <h1 className='font-medium text-lg'>Dnešek</h1>
                    <div className="flex items-center gap-1">
                        {todayWorked?.map((task, i) => (
                            <div key={i + "_worked-icon"} className="">
                                <TaskStateIcon task={task} />
                            </div>
                        ))}
                    </div>
                </div>}>
                    <div className="px-4 pb-4">
                        {todayWorked?.map((task, i) => (
                            <div key={i + "_worked"} className="flex items-start gap-2">
                                <TaskStateIcon task={task} />
                                <div className={`w-28 flex-shrink-0 font-medium ${task.status == "done" ? "text-blue font-semibold" : "text-black-75"}`}>{task.status == "done" ? "Dokončeno" : task.status == "in_progress" ? "Probíhá" : "Rozpracováno"}</div>
                                <NextLink href={`/task/${task?.uuid}`} className={`font-medium cursor-pointer`}>{task?.name}</NextLink>
                            </div>
                        ))}
                    </div>
                </Accordion>

                <div className="py-4 gap-2">
                    <p className='font-light text-xl md:text-2xl md:w-2/3'>{inflectionGenerator("Michal")}, tohle jsou všechny Tvé úkoly, seskupené podle obtížnosti</p>
                    <button className='text-blue hover:text-blue-darker font-medium mt-4'>Vybrat kontext, projekt nebo oblast...</button>
                </div>
                {isLoading &&
                    <div className="">
                        <div><LoadingSkeleton width={100} height={16} /></div>
                        <div className="mt-4 grid grid-cols-2 md:grid-cols-4 gap-2">
                            {(Array.from(Array(10).keys()))?.map((task, i) => (
                                <div key={i + "_skeleton"} className="grid grid-cols-1">
                                    <LoadingSkeleton width={"100%"} height={96} />
                                </div>
                            ))}
                        </div>
                    </div>
                }
                {easyTasks?.length > 0 &&
                    <div className="">
                        <h2 className='font-semibold'>Snadné</h2>
                        <div className="mt-4 grid grid-cols-2 md:grid-cols-4 gap-2">
                            {easyTasks?.map((task, i) => (
                                <div key={i + "_task-easy"} className="w-full grid grid-cols-1">
                                    <TaskSmCard task={task} />
                                </div>
                            ))}
                        </div>
                    </div>
                }
                {mediumTasks?.length > 0 &&
                    <div className="mt-4">
                        <h2 className='font-semibold'>Střední</h2>
                        <div className="mt-4 grid grid-cols-2 md:grid-cols-4 gap-2">
                            {mediumTasks?.map((task, i) => (
                                <div key={i + "_task-medium"} className="w-full grid grid-cols-1">
                                    <TaskSmCard task={task} />
                                </div>
                            ))}
                        </div>
                    </div>
                }
                {hardTasks?.length > 0 &&
                    <div className="mt-4">
                        <h2 className='font-semibold'>Soustředění</h2>
                        <div className="mt-4 grid grid-cols-2 md:grid-cols-4 gap-2">
                            {hardTasks?.map((task, i) => (
                                <div key={i + "_task-hard"} className="w-full grid grid-cols-1">
                                    <TaskSmCard task={task} />
                                </div>
                            ))}
                        </div>
                    </div>
                }
                {taskDoneBefore?.length > 0 &&
                    <div className="mt-4">
                        <h2 className='font-semibold'>Dokončeno dříve</h2>
                        <div className="mt-4 grid grid-cols-2 md:grid-cols-4 gap-2">
                            {taskDoneBefore?.map((task, i) => (
                                <div key={i + "_task-done"} className="w-full grid grid-cols-1">
                                    <TaskSmCard task={task} gray />
                                </div>
                            ))}
                        </div>
                    </div>
                }
                {error && "Error"}


            </div>
            <div className='fixed -right-12 md:right-0 md:left-0 top-24 h-screen z-0 w-2/3 md:w-full'>
                <Image src="/images/plant.png" blurDataURL='/images/plant.png' alt={'Gentletask plant'} fill className='object-contain object-right-top' />
            </div>
        </div>

    )
}

export default Dashboard

Dashboard.getLayout = function getLayout(page: ReactElement) {
    return (
        <AppLayout>
            {page}
        </AppLayout>
    )
}