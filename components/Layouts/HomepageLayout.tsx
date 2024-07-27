import React, { ReactNode } from 'react'

export default function HomepageLayout({ children }: {
    children: ReactNode
}) {
    return (
        <div className='bg-white min-h-screen'>
            {children}
        </div>
    )
}
