import { SwitchDataType } from '@/components/types/SwitchTypes'
import Title from '@/components/Typography/Title'
import Button from '@/components/UI/Button'
import Link from '@/components/UI/Link'
import Switch from '@/components/UI/Switch'
import TextField from '@/components/UI/TextField'
import { At, Password } from '@phosphor-icons/react'
import axios from 'axios'
import { useRouter } from 'next/navigation'
import React, { useContext, useState } from 'react'
import config from '@/config';
import { AppContext } from '@/components/Contexts/AppContext'
import Modal from '@/components/UI/Modal'
import instance from '@/libs/utils/api'

interface AddContextModalProps {
  opened: boolean,
  setOpened: React.Dispatch<React.SetStateAction<boolean>>
}

export default function AddContextModal(props: AddContextModalProps) {
  const router = useRouter();
  const { app, setApp } = useContext(AppContext)

  const [fetching, setFetching] = useState(false)

  const [form, setForm] = useState({
    name: "",
  })
  const [errors, setErrors] = useState<{ name?: string }>()

  async function addcontext() {
    setFetching(true)
    try {
      const response = await instance.post(`/contexts`, {
        name: form.name
      }, { headers: { Authorization: `Bearer ${app.token}` } })
      if (response?.data) {
        setForm({ name: "" })
        props.setOpened(false)
      }
    }
    catch (err: any) {
      if (err.response.data) {
        // alert(err.response.data.message)
        setErrors(err.response.data.errors)
      }
    }
    finally {
      setFetching(false)
    }
  }

  return (
    <Modal opened={props.opened} setOpened={props.setOpened}>
      <div className="flex flex-col h-full pt-4">
        <div className="w-full flex justify-center py-6">
          <Title level={1} className='text-center'>Jaký kontext si přeješ vytvořit?</Title>
        </div>

        <form className='flex-grow flex flex-col' onSubmit={e => {
          e.preventDefault()
          !fetching ? addcontext() : {}
        }}>
          <div className="gap-2 flex flex-col flex-grow h-full">
            <TextField required autoFocus value={form.name} label='Název kontextu' error={errors?.name} setValue={(to) => setForm({ ...form, name: to })} />
          </div>
          <div className="w-full md:pb-4">
            <div className="w-full flex pt-4">
              <div className="flex-1">
                <Link onClick={() => props.setOpened(false)}>Zrušit</Link>
              </div>
              <div className="flex-1">
                <Button type='submit' disabled={fetching}>{fetching ? "Ukládání..." : "Vytvořit"}</Button>
              </div>
            </div>
          </div>
        </form>


      </div>
    </Modal>
  )
}
