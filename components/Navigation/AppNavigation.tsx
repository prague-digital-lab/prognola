import React, { useEffect, useState } from 'react'
import Image from 'next/image'
import NavItem from './NavItem'
import { CalendarCheck, CalendarHeart, CardsThree, CheckSquare, CheckSquareOffset, DiscordLogo, House, List, ListPlus, PresentationChart, Question, Sparkle, Tag, UserCircle } from "@phosphor-icons/react"
import AddTaskModal from './AddTaskModal'
import { usePathname } from 'next/navigation'
import Link from 'next/link'

export default function AppNavigation() {

    const pathname = usePathname()

    const [addTaskOpened, setAddTaskOpened] = useState(false)

    const [scrollPosition, setScrollPosition] = useState(0)
    const handleScroll = () => {
        const position = window.scrollY
        setScrollPosition(position)
    };

    useEffect(() => {
        window.addEventListener('scroll', handleScroll, { passive: true })

        return () => {
            window.removeEventListener('scroll', handleScroll)
        };
    }, []);


    return (
        <div className={`w-full flex justify-between items-center px-4 py-2 bg-brown-background bg-opacity-75 backdrop-blur border-b duration-150 ${scrollPosition > 20 ? "border-black-25" : "border-transparent"}`}>
            <Link href={"/dashboard"} className="order-1">
                <Image
                    src="/images/gt_logo.svg"
                    alt="Gentletask logo"
                    className='hidden lg:flex'
                    width={110}
                    height={24}
                    priority
                />
                <Image
                    src="/images/gt_icon.svg"
                    alt="Gentletask icon"
                    className='lg:hidden'
                    width={24}
                    height={24}
                    priority
                />
            </Link>
            <div className="order-3 lg:order-2">
                <div className="lg:hidden">
                    <List size={24} />
                </div>
                <div className="lg:flex flex-row gap-1 hidden">
                    <NavItem active={pathname == "/dashboard"} icon={<CalendarHeart />} title={'Dnešek'} href="/dashboard" />
                    <NavItem active={pathname == "/what-now"} icon={<Sparkle />} title={'Co teď?'} href="/dashboard" />
                    <NavItem active={pathname == "/planing"} icon={<ListPlus />} title={'Plánování'} href="/dashboard" />
                    <NavItem active={pathname == "/projects"} icon={<CardsThree />} title={'Projekty'} href="/dashboard" />
                    <NavItem active={pathname == "/contexts"} icon={<Tag />} title={'Kontext'} href="/contexts" />
                    <NavItem active={pathname == "/stats"} icon={<PresentationChart />} title={'Statistiky'} href="/dashboard" />
                    <NavItem active={pathname == "/my-account"} icon={<UserCircle />} title={'Můj účet'} href="/dashboard" />
                </div>

            </div>
            <div className="flex gap-1 order-2 lg:order-3">
                <div onClick={() => setAddTaskOpened(true)} className="h-8 px-4 flex items-center justify-center border border-black-25 hover:border-black-50 duration-100 hover:text-black-75 cursor-pointer rounded text-black-50">+ Přidat úkol</div>
            </div>
            <AddTaskModal opened={addTaskOpened} setOpened={setAddTaskOpened} />
        </div>
    )
}
