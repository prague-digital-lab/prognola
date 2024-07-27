import AutosizeTextarea from '@/components/AutosizeTextarea'
import { AppContext } from '@/components/Contexts/AppContext'
import { useDebouncedCallback } from '@/components/hooks/useDebouncedCallback'
import Title from '@/components/Typography/Title'
import { useTask } from '@/libs/hooks/task'
import instance from '@/libs/utils/api'
import { ArrowBendLeftDown, ArrowBendUpLeft, DotsThreeVertical } from '@phosphor-icons/react'
import { motion } from 'framer-motion'
import Head from 'next/head'
import { useRouter } from 'next/router'
import React, { useContext, useEffect, useState } from 'react'

export default function Task() {

    const router = useRouter()
    const uuid = router?.query?.uuid as string

    const { app, setApp } = useContext(AppContext)
    const { data, isLoading, error } = useTask(uuid, app.token)

    const task = data as _Task | undefined

    const [form, setForm] = useState({
        name: task?.name,
        description: task?.description
    })

    async function updateTask() {
        try {
            const response = await instance.patch(`/tasks/${task?.uuid}`, form, { headers: { Authorization: `Bearer ${app.token}` } })
            if (response.data) {

            }
        }
        catch (err) {
            console.log(err)
            alert("Nepodařilo se upravit úkol")
        }
      
    }

    const waitAndUpdate = useDebouncedCallback(() => updateTask(), 2000)

    useEffect(() => {
        if(form.name != task?.name || form.description != task?.description) {
            waitAndUpdate()
        }
    }, [form]);

    return (
        <div className='bg-white min-h-screen'>
            <Head>
                <title>Úkol: {task?.name} | Gentletask</title>
            </Head>
            <motion.div
            initial={{scale: 0.98, opacity: 0}}
            animate={{scale:1, opacity: 1}}
            transition={{duration:0.2}}
            className="w-full flex justify-center md:p-12">
                <div className="w-full max-w-4xl bg-white md:border-brown-background md:border-4 rounded">
                    <div className="flex justify-between items-center">
                        <button onClick={() => router.back()} className="flex items-center gap-2 text-black-90 hover:text-black duration-150 p-4 cursor-pointer">
                            <ArrowBendUpLeft size={24} />
                            <p className='font-medium'>Zpět</p>
                        </button>
                        <div className="text-black-90 hover:text-black duration-75 p-4 cursor-pointer">
                            <DotsThreeVertical size={24} />
                        </div>
                    </div>
                    <div className="px-4">
                        <div className="flex items-start gap-4">
                            <div className="w-8 h-8 border border-black-25 flex-shrink-0 rounded cursor-pointer hover:border-black-50 duration-150"></div>
                            <AutosizeTextarea disableEnter defaultValue={task?.name} onChange={(e) => setForm({...form, name: e.target.value})} className='text-xl font-medium w-full' rows={1} />
                        </div>
                        <div className="mt-6"></div>
                        <AutosizeTextarea defaultValue={task?.description} onChange={(e) => setForm({...form, description: e.target.value})} placeholder="Poznámky k úkolu" className='w-full' />
                    </div>
                </div>
            </motion.div>
        </div>
    )
}
