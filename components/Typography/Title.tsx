import React, { HTMLAttributes, ReactNode } from 'react'

interface titleProps extends HTMLAttributes<HTMLDivElement> {
    level?: 0 | 1 | 2 | 3 | 4,
    size?: number,
    children?: string | ReactNode
}

export default function Title({ level, className, children }: titleProps) {

    switch (level) {
        case 1: return <h1 className={"text-2xl font-medium " + className}>{children}</h1>
        case 2: return <h2 className={"text-xl font-medium " + className}>{children}</h2>
        case 3: return <h3 className={"text-lg font-medium " + className}>{children}</h3>
        case 4: return <h4 className={"font-medium " + className}>{children}</h4>
        default: return <h1 className={"text-2xl font-medium " + className}>{children}</h1>
    }

}
