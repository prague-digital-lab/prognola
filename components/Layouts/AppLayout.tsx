import React, { ReactNode } from 'react'
import AppNavigation from '../Navigation/AppNavigation'

interface AppLayoutProps {
    children?: ReactNode | ReactNode[],
    contentWrapper?: boolean
}

export default function AppLayout(props: AppLayoutProps) {

    const { children, contentWrapper = true } = props


    return (
        <div className="bg-brown-background min-h-screen flex flex-col">
            <nav className='sticky top-0 z-20'>
                <AppNavigation />
            </nav>
            <main className="flex-grow flex justify-center">
                <div className={`${contentWrapper ? "max-w-4xl px-4 md:py-4 w-full " : ""}`}>{children}</div>
            </main>
        </div>
    )
}
