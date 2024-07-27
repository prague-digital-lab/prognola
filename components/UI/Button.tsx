import React, { ReactNode } from 'react'

interface ButtonProps extends React.DetailedHTMLProps<React.ButtonHTMLAttributes<HTMLButtonElement>, HTMLButtonElement> {
  children?: string,
  destructive?: boolean
}

export default function Button(props: ButtonProps) {
  return (
    <button {...props} value={props.children} className={`h-12 w-full rounded cursor-pointer font-semibold flex items-center justify-center  duration-75 px-3 ${props.disabled ? "bg-black-10 text-black-50" : props.destructive ? "bg-red text-white" : "bg-blue hover:bg-blue-darker text-white"} `} />
  )
}
