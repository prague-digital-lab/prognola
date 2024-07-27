import React, { ReactNode } from 'react'

interface LinkProps extends React.DetailedHTMLProps<React.HTMLAttributes<HTMLDivElement>, HTMLDivElement> {
  children: ReactNode,
  destructive?: boolean
}

export default function Link(props: LinkProps) {
  return (
    <div {...props} className={`h-12 cursor-pointer font-semibold flex items-center justify-center px-3  duration-75 ${props.destructive ? "text-red" : "text-blue  hover:text-blue-darker"} `}>{props.children}</div>
  )
}
