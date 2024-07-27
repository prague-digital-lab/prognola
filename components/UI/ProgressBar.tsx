import React from 'react'

export default function ProgressBar({progress}:{
    progress: number
}) {
  return (
    <div className='bg-black-10 w-full h-1 rounded overflow-hidden'>
        <div style={{width: `${progress}%`}} className="bg-blue h-1"></div>
    </div>
  )
}
