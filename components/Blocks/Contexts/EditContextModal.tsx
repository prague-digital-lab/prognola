import { SwitchDataType } from '@/components/types/SwitchTypes'
import Title from '@/components/Typography/Title'
import Button from '@/components/UI/Button'
import Link from '@/components/UI/Link'
import Switch from '@/components/UI/Switch'
import TextField from '@/components/UI/TextField'
import { At, Password } from '@phosphor-icons/react'
import axios from 'axios'
import { useRouter } from 'next/navigation'
import React, { useContext, useEffect, useState } from 'react'
import config from '@/config';
import { AppContext } from '@/components/Contexts/AppContext'
import Modal from '@/components/UI/Modal'
import instance from '@/libs/utils/api'

interface EditContextModalProps {
  opened: boolean,
  setOpened: React.Dispatch<React.SetStateAction<boolean>>,
  context: { uuid: string, name: string }
}

export default function EditContextModal(props: EditContextModalProps) {
  const router = useRouter();
  const { app, setApp } = useContext(AppContext)

  const [fetching, setFetching] = useState(false)

  const [form, setForm] = useState({
    name: props.context.name,
  })
  const [errors, setErrors] = useState<{ name?: string }>()

  async function editContext() {
    setFetching(true)
    try {
      const response = await instance.patch(`/contexts/${props.context.uuid}`, {
        name: form.name,
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

  async function deleteContext() {
      setFetching(true)
      try {
        const response = await instance.delete(`/contexts/${props.context.uuid}`, { headers: { Authorization: `Bearer ${app.token}` } })
        if (response) {
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

  useEffect(() => {
    setForm({ name: props.context.name })
  }, [props.context]);

  return (
    <Modal opened={props.opened} setOpened={props.setOpened}>
      <div className="flex flex-col h-full pt-4">
        <div className="w-full flex justify-center py-6">
          <Title level={1} className='text-center'>Chceš nějak upravit tento kontext?</Title>
        </div>

        <form className='flex-grow flex flex-col' onSubmit={e => {
          e.preventDefault()
          if(!fetching) {
            if (form.name.length > 0) {
              editContext()
            }
            else {
              deleteContext()
            }
          }
        }}>
          <div className="flex flex-col flex-grow h-full">
            <TextField autoFocus value={form.name} label='Název kontextu' error={errors?.name} setValue={(to) => setForm({ ...form, name: to })} />
            <p className='text-sm text-black-75 ml-1 mt-1'>{(form.name.length > 0) ? "Pro smazání kontextu vymažte toto pole" : `${props.context.name} se po klepnutí na „Odstranit“ nenávratně smaže`}</p>
          </div>
          <div className="w-full md:pb-4">
            <div className="w-full flex pt-4">
              <div className="flex-1">
                <Link onClick={() => {
                  props.setOpened(false)
                }}>Zrušit</Link>
              </div>
              <div className="flex-1">
                <Button type='submit' destructive={form.name.length === 0} disabled={fetching}>{fetching ? "Ukládání..." : form.name.length == 0 ? "Odstranit" : "Uložit"}</Button>
              </div>
            </div>
          </div>
        </form>


      </div>
    </Modal>
  )
}
