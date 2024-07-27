import { AppContext } from '@/components/Contexts/AppContext'
import AppLayout from '@/components/Layouts/AppLayout'
import { useContexts } from '@/libs/hooks/contexts'
import React, { ReactElement, useContext, useEffect, useState } from 'react'
import { NextPageWithLayout } from './_app'
import { useRouter } from 'next/router'
import { useTasks } from '@/libs/hooks/tasks'
import WelcomerTitle from '@/components/Blocks/Dashboard/WelcomerTitle'
import Head from 'next/head'
import Title from '@/components/Typography/Title'
import LoadingSkeleton from '@/components/UI/LoadingSkeleton'
import { Plus } from '@phosphor-icons/react'
import { motion } from 'framer-motion'
import AddContextModal from '@/components/Blocks/Contexts/AddContextModal'
import EditContextModal from '@/components/Blocks/Contexts/EditContextModal'
import Image from 'next/image'

const Dashboard: NextPageWithLayout = () => {

    const router = useRouter()
    const { app, setApp } = useContext(AppContext)
    const { data, isLoading, error, mutate } = useContexts(app.token)

    const [addModalOpened, setAddModalOpened] = useState(false)
    const [editModalData, setEditModalData] = useState<{ name: string, uuid: string }>()

    useEffect(() => {
        if (!addModalOpened) {
            mutate()
        }
    }, [addModalOpened]);

    useEffect(() => {
        if (!editModalData) {
            mutate()
        }
    }, [editModalData]);

    return (
        <div className='md:pt-12'>
            <Head>
                <title>Gentletask</title>
            </Head>


            <div className="w-full">
                <div className="flex gap-2 items-baseline">
                    <Title level={1}>Kontexty</Title>
                    <button onClick={() => setAddModalOpened(true)} className="flex items-center duration-150 gap-1 text-blue hover:text-blue-darker active:scale-95">
                        <Plus weight='bold' />
                        <div className="font-medium">Nový kontext</div>
                    </button>
                </div>
                <div className="mt-4 w-full rounded bg-green-light border border-black-10 p-4 relative">
                    <p className='font-medium'>Kontexty pomáhají sdružovat podobné úkoly.</p>
                    <p className=' text-black-90 text-sm'>Může to být třeba typ činnosti, který často provádíte, (telefonování, psaní, programování), nebo místo, kde úkoly chcete splnit, (doma, v kanceláři, v terénu).</p>
                    {/* <Image src={"/images/plant.png"} alt="Gentletask plant decoration" width={60} height={80} className="absolute top-0 bottom-0 right-0 object-center -z-10" /> */}
                </div>
                <div className="mt-4 w-full">
                    {isLoading ? <div className='flex-col gap-2 w-full grid grid-cols-1 md:grid-cols-3'>
                        <LoadingSkeleton width={"100%"} height={48} />
                        <LoadingSkeleton width={"100%"} height={48} />
                        <LoadingSkeleton width={"100%"} height={48} />
                        <LoadingSkeleton width={"100%"} height={48} />
                        <LoadingSkeleton width={"100%"} height={48} />
                        <LoadingSkeleton width={"100%"} height={48} />
                    </div> :
                        <div
                            className='flex-col gap-2 w-full grid grid-cols-1 md:grid-cols-3'>
                            {data?.map((context: { name: string, uuid: string }, i:number) => (
                                <button onClick={() => setEditModalData(context)} key={i} className="h-12 w-full border duration-100 border-white active:border-black-25 bg-white rounded px-4 flex items-center justify-between font-medium">{context?.name}</button>
                            ))}
                        </div>
                    }


                    {error && "Error"}

                    <AddContextModal opened={addModalOpened} setOpened={setAddModalOpened} />
                    <EditContextModal opened={editModalData ? true : false} setOpened={() => setEditModalData(undefined)} context={editModalData ?? {name: "", uuid: ""}} />
                </div>

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