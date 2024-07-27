import { AppContext } from '@/components/Contexts/AppContext'
import AppLayout from '@/components/Layouts/AppLayout'
import { useContexts } from '@/libs/hooks/contexts'
import React, { ReactElement, useContext, useEffect, useState } from 'react'
import { NextPageWithLayout } from './_app'
import { useRouter } from 'next/router'

const Guide: NextPageWithLayout = () => {

    const router = useRouter()
    const { app, setApp } = useContext(AppContext)

    return (
        <div className="bg-brown-background min-h-screen flex flex-col">
            <nav className='sticky top-0 z-20'>
                
            </nav>
            <main className='flex-grow'>
                
            </main>
        </div>
    )
}

export default Guide