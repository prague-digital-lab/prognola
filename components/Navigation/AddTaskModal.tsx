import { KeyReturn, X } from '@phosphor-icons/react'
import { AnimatePresence, motion } from 'framer-motion'
import React, { LegacyRef, MutableRefObject, ReactNode, RefObject, useCallback, useContext, useEffect, useRef, useState } from 'react'
import { createPortal } from 'react-dom'
import Image from 'next/image'
import Link from '../UI/Link'
import TextField from '../UI/TextField'
import useOnClickOutside from '../hooks/clickOutside'
import instance from '@/libs/utils/api'
import { AppContext } from '../Contexts/AppContext'

interface ModalProps {
    opened: boolean,
    setOpened: React.Dispatch<React.SetStateAction<boolean>>
}

export default function AddTaskModal(props: ModalProps) {

    const { opened, setOpened } = props
    const { app } = useContext(AppContext)
    const [portal, setPortal] = useState<HTMLElement | null>()

    const innerRef = useRef<any>()
    const inputRef = useRef<any>(null)

    function close() {
        setOpened(false)
    }

    async function addTask() {
        console.log(app)
        if (app.token) {
            close()
            try {
                const response = await instance.post("/tasks", {
                    name: inputRef?.current?.value
                }, { headers: { Authorization: `Bearer ${app.token}` } })

                if (response.data) {
                    console.log(response.data)
                }
            }
            catch (err) {
                alert("Úkol se nepodařilo přidat")
                console.log(err)
            }

        }

    }

    useOnClickOutside(innerRef, () => close())

    useEffect(() => {
        if (opened) {
            document.body.style.overflow = 'hidden';
        }
        else {
            document.body.style.overflow = 'unset';
        }
    }, [opened]);

    useEffect(() => {
        if (document?.getElementById("modals-portal")) {
            setPortal(document.getElementById("modals-portal"))
        }



        function escFunction(ev: React.KeyboardEvent<Element> | any) {
            if (ev.key === "Escape") {
                //Do whatever when esc is pressed
                close()
            }
        }

        document.addEventListener("keydown", escFunction, false)

        return () => {
            document.removeEventListener("keydown", escFunction, false);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [])

    return (
        portal ?
            createPortal(
                <AnimatePresence>
                    {opened &&
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            transition={{ duration: 0.2 }}
                            className='bg-brown-background bg-opacity-75 px-2 md:pt-24 pt-2 backdrop-blur fixed top-0 left-0 right-0 bottom-0 w-full h-full z-50 flex justify-center items-start'
                        >
                            <motion.div
                                ref={innerRef}
                                initial={{ opacity: 0, y: -10, scale: 0.9 }}
                                animate={{ opacity: 1, y: 0, scale: 1 }}
                                exit={{ opacity: 0, y: 10, scale: 0.9 }}
                                transition={{ type: "spring", duration: 0.5 }}
                                className='md:max-w-2xl p-2 lg:p-4 w-full max-h-lvh flex flex-col relative gap-4'
                            >

                                <div onClick={() => setOpened(false)} className="text-red cursor-pointer hover:opacity-75">Zavřít</div>
                                <form onSubmit={e => {
                                    e.preventDefault()
                                    addTask()

                                }}>
                                    <input ref={inputRef} required type='text' autoFocus className='w-full px-4 py-5 bg-white rounded shadow-lg order-2 md:order-1 text-xl' />
                                </form>
                                <div className="flex items-center gap-2 text-black-75 order-1 md:order-2">
                                    <KeyReturn />
                                    enter = pustit to z hlavy 🍃
                                </div>
                            </motion.div>
                        </motion.div>
                    }
                </AnimatePresence>,
                portal
            ) : <></>
    )
}
