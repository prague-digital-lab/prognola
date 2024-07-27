import Button from '@/components/UI/Button'
import Modal from '@/components/UI/Modal';
import React, { useContext, useState } from 'react'
import LoginModal from './LoginModal';
import { AppContext } from '@/components/Contexts/AppContext';
import Link from 'next/link';

export default function LoginButton() {

    const { app } = useContext(AppContext)

    const [openModal, setOpenModal] = useState(false)

    return (
        <>
            {app.loggedIn ?
                <Link href={"/dashboard"}>
                    <Button>Přejít&nbsp;do&nbsp;aplikace</Button>
                </Link>
                :
                <Button onClick={() => setOpenModal(true)}>Vyzkoušet&nbsp;zdarma</Button>
            }


            <LoginModal opened={openModal} setOpened={setOpenModal} />

        </>
    )
}
