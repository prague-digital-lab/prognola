import React, { ReactNode } from 'react'

export default function TinyNote(props:{children?: ReactNode}) {
  return (
    <p className='text-xs text-black-50'>{props.children}</p>
  )
}
