import React, { ChangeEvent, useLayoutEffect, useRef } from 'react'

interface TextareaProps extends React.DetailedHTMLProps<React.TextareaHTMLAttributes<HTMLTextAreaElement>, HTMLTextAreaElement> {
disableEnter?: boolean
}

export default function AutosizeTextarea(props: TextareaProps) {

    const textbox = useRef<any>(null)

    function adjustHeight() {
        if(textbox?.current) {
            textbox.current.style.height = "inherit";
            textbox.current.style.height = `${textbox.current.scrollHeight}px`;
        }
    }
  
    useLayoutEffect(adjustHeight, []);
  
    function handleKeyDown(e:any) {
        if (e.keyCode == 13 && props.disableEnter)
            {
                // prevent default behavior
                e.preventDefault();
            }
      adjustHeight()
    }


    return (
        <textarea ref={textbox} style={{resize: "none"}} onKeyDown={(e) => handleKeyDown(e)} {...props} />
    )
}
