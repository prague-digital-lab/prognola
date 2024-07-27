import { Url } from 'next/dist/shared/lib/router/router'
import Link from 'next/link'
import React, { ReactNode } from 'react'

interface navItemProps extends React.DetailedHTMLProps<React.HTMLAttributes<HTMLLinkElement>, HTMLLinkElement> {
    title: string,
    icon?: ReactNode,
    active?: boolean,
    href: string
}

export default function NavItem(props: navItemProps) {

    const { title, icon, active, href } = props

    return (
        <Link href={href} className={`flex cursor-pointer gap-1 p-2 items-center duration-300 rounded ${active ? "bg-blue-highlight text-blue font-bold" : "text-black-75 hover:text-black-90 "}`}>
            {icon && icon}
            {title}
        </Link>
    )
}
