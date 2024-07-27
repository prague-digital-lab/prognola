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

interface LoginModalProps {
  opened: boolean,
  setOpened: React.Dispatch<React.SetStateAction<boolean>>
}

export default function LoginModal(props: LoginModalProps) {
  const router = useRouter();
  const { app, setApp } = useContext(AppContext)

  const [fetching, setFetching] = useState(false)
  const [switchData, setSwitchData] = useState({
    selected: "register", options: [
      { label: "Mám účet", value: "login" },
      { label: "Registrovat se", value: "register" }
    ]
  })

  const [loginForm, setLoginForm] = useState({
    email: "",
    password: "",
  })

  const [registerForm, setRegisterForm] = useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: ""
  })
  const [registerErrors, setRegisterErrors] = useState<{ email?: string, name?: string, password?: string }>()

  async function login(email?: string, password?: string) {
    setFetching(true)
    try {
      const response = await axios.post(`${config.API_HOST}/login`, {
        email: email ?? loginForm.email,
        password: password ?? loginForm.password
      })
      if (response?.data?.token) {
        setLoginForm({ email: "", password: "" })
        props.setOpened(false)
        localStorage.setItem("token", response.data.token)
        setApp({ loggedIn: true, token: response?.data?.token })
        router.push("/dashboard")
      }
    }
    catch (err) {
      alert("Zadané údaje jsou nesprávné. Zkontrolujte, jestli je e-mail a heslo zadáno správně.")
    }
    finally {
      setFetching(false)
    }
  }

  async function register() {
    setFetching(true)
    try {
      const response = await axios.post(`${config.API_HOST}/register`, registerForm)
      if (response?.data) {
        props.setOpened(false)
        // localStorage.setItem("token", response.data.token)
        // setApp({ ...app, token: response?.data?.token })
        // router.push("/dashboard")
        await login(registerForm.email, registerForm.password)
        setRegisterForm({ email: "", password: "", password_confirmation: "", name: "" })
      }
    }
    catch (err: any) {
      console.log(err)
      if (err.response.data) {
        // alert(err.response.data.message)
        setRegisterErrors(err.response.data.errors)
      }
    }
    finally {
      setFetching(false)
    }
  }

  return (
    <Modal opened={props.opened} setOpened={props.setOpened}>
      <div className="flex flex-col h-full pt-4">
        <Switch data={switchData} setData={setSwitchData} />
        <div className="w-full flex justify-center py-6">
          {(switchData.selected == "login") && <Title level={1} className='text-center'>Přihlaste se zpět ke svému účtu.</Title>}
          {(switchData.selected == "register") && <Title level={1} className='text-center'>Zaregistrujte se. Už se na vás těšíme.</Title>}
        </div>

        {(switchData.selected == "login") &&
          <form className='flex-grow flex flex-col' onSubmit={e => {
            e.preventDefault()
            !fetching ? login() : {}
          }}>
            <div className="gap-2 flex flex-col w-full flex-grow h-full">
              <TextField required type='email' value={loginForm.email} label='E-mail' icon={<At />} setValue={(to) => setLoginForm({ ...loginForm, email: to })} />
              <TextField required minLength={8} value={loginForm.password} type='password' label='Heslo' icon={<Password />} setValue={(to) => setLoginForm({ ...loginForm, password: to })} />
            </div>
            <div className="w-full pt-4 md:pb-4">
              <div className="w-full flex">
                <div className="flex-1">
                  <Link onClick={() => props.setOpened(false)}>Zavřít</Link>
                </div>
                <div className="flex-1">
                  <Button type='submit' disabled={fetching}>{fetching ? "Ověřování..." : "Přihlásit se"}</Button>
                </div>
              </div>
            </div>
          </form>
        }

        {(switchData.selected == "register") &&
          <form className='flex-grow flex flex-col' onSubmit={e => {
            e.preventDefault()
            !fetching ? register() : {}
          }}>
            <div className="gap-2 flex flex-col flex-grow h-full">
              <TextField required value={registerForm.name} label='Jméno' error={registerErrors?.name} setValue={(to) => setRegisterForm({ ...registerForm, name: to })} />
              <TextField required type='email' error={registerErrors?.email} value={registerForm.email} label='E-mail' setValue={(to) => setRegisterForm({ ...registerForm, email: to })} />
              <TextField required minLength={8} error={registerErrors?.password ?? (registerForm.password.length > 0 && registerForm.password.length < 8 ? "Heslo musí mít alespoň 8 znaků" : undefined)} value={registerForm.password} type='password' label='Heslo' setValue={(to) => setRegisterForm({ ...registerForm, password: to })} />
              <TextField required minLength={8} error={(registerForm.password != registerForm.password_confirmation && registerForm.password_confirmation.length > 0) ? "Hesla se neshodují" : undefined} value={registerForm.password_confirmation} type='password' label='Heslo znovu' setValue={(to) => setRegisterForm({ ...registerForm, password_confirmation: to })} />
            </div>
            <div className="w-full md:pb-4">
              <div className="w-full flex pt-4">
                <div className="flex-1">
                  <Link onClick={() => props.setOpened(false)}>Zavřít</Link>
                </div>
                <div className="flex-1">
                  <Button type='submit' disabled={fetching}>{fetching ? "Ověřování..." : "Vytvořit účet"}</Button>
                </div>
              </div>
            </div>
          </form>
        }

      </div>
    </Modal>
  )
}
