import React, { useContext } from 'react'
import Image from 'next/image'
import NavItem from './NavItem'
import { ArrowSquareIn, ArrowSquareRight, CalendarHeart, DiscordLogo, House, Lock, LockOpen, MonitorArrowUp, Question, SignIn, User, UserCheck, UserCircleCheck } from "@phosphor-icons/react"
import { AppContext } from '../Contexts/AppContext'

export default function HomeNavigation() {

    const { app } = useContext(AppContext)

    return (
        <div className="w-full flex justify-between items-center p-4 md:p-6 absolute top-0 z-20">
            <div className="">
                <Image
                    src="/images/gt_logo.svg"
                    alt="Gentletask logo"
                    width={110}
                    height={24}
                    priority
                />
            </div>
            <div className="flex gap-1">
                <NavItem active icon={<House />} title={'Úvod'} href='/' />
                <NavItem icon={<Question />} title={'Tvůrci'} href='/' />
                <NavItem icon={<DiscordLogo />} title={'Komunita'} href='/' />
                {app.loggedIn ? <NavItem icon={<User />} title={'Přihlášen'} href='/dashboard' /> : <NavItem icon={<SignIn />} title={'Přihlásit se'} href='/' />}
            </div>
        </div>
    )
}
