import { X } from '@phosphor-icons/react'
import { AnimatePresence, motion } from 'framer-motion'
import React, { ReactNode, useEffect, useState } from 'react'
import { createPortal } from 'react-dom'
import Image from 'next/image'

interface ModalProps {
    children?: ReactNode,
    onClose?: () => void,
    opened: boolean,
    setOpened: React.Dispatch<React.SetStateAction<boolean>>
}

export default function Modal(props: ModalProps) {

    const { opened, setOpened } = props
    const [portal, setPortal] = useState<HTMLElement | null>()

    function close() {
        setOpened(false)
        setTimeout(() => {
            closeExt()
        }, 300)
    }

    function closeExt() {
        if (props.onClose) {
            props.onClose()
        }
    }

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
                            className='bg-white p-2 md:bg-brown-background fixed top-0 left-0 right-0 bottom-0 w-full h-full max-h-screen z-50 flex justify-center items-center'
                        >
                            <motion.div
                                initial={{ opacity: 0, scale: 0.9, rotateX: "10deg", perspective: 800 }}
                                animate={{ opacity: 1, scale: 1, rotateX: "10deg", perspective: 800 }}
                                exit={{ opacity: 0, scale: 0.8, rotateX: "10deg", perspective: 800 }}
                                transition={{ type: "spring", duration: 0.35 }}
                                className='md:max-w-2xl h-full md:h-auto bg-brown-background rounded md:border-white md:border-8 p-2 lg:p-4 w-full max-h-lvh flex flex-col overflow-y-auto relative'
                            >
                                <div className="flex flex-grow-0 justify-between items-center">
                                    <div className="">
                                        <Image
                                            src="/images/gt_icon.svg"
                                            alt="Gentletask icon logo"
                                            width={24}
                                            height={24}
                                        />
                                    </div>
                                    <div className="cursor-pointer" onClick={() => {
                                        close()
                                    }}>
                                        <X width={24} height={24} />
                                    </div>
                                </div>
                                <div className="flex-grow min-h-64  md:px-24 flex-shrink max-h-full overflow-x-visible ">
                                    {props.children}
                                </div>
                            </motion.div>
                        </motion.div>
                    }
                </AnimatePresence>,
                portal
            ) : <></>
    )
}
