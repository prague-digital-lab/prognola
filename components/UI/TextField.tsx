import { DetailedHTMLProps, InputHTMLAttributes, ReactNode, useState } from "react"

interface TextFieldProps extends DetailedHTMLProps<InputHTMLAttributes<HTMLInputElement>, HTMLInputElement> {
    value: string,
    setValue: (to: string) => void,
    label?: string,
    type?: string,
    icon?: ReactNode,
    name?: string,
    error?: string
}


export default function TextField(props: TextFieldProps) {

    const [blurred, setBlurred] = useState(false)

    return (
        <div className="relative">
            <div className="text-black-50 z-0 absolute top-0 left-0 bottom-0 flex items-center justify-center w-12 h-full pointer-events-none">
                {props?.icon}
            </div>

            {(props.value.length > 0) &&
                <label className={`absolute top-2 ${props.icon ? "left-10": "left-3"} text-xs text-black-75 pointer-events-none`}>{props.label}</label>
            }

            <input {...props} onBlur={() => setBlurred(true)} onFocus={() => setBlurred(false)} required={props?.required} name={props?.name} className={`w-full ${props.label && props.value.length > 0 ? "pt-4 pb-2" : "pt-3 pb-3"} ${props.icon ? "pl-10" : "pl-3"} pr-2 ${(props.error && blurred) ? "border-red focus:ring-red":"border-black-25 focus:border-blue focus:ring-blue"} focus:ring-opacity-25 focus:ring-4 focus:outline-none border rounded placeholder:text-black-50`} type={props?.type ?? "text"} placeholder={props.label} value={props.value} onChange={(e) => props.setValue(e.target.value)} />
            {(props.error && blurred) &&
            <div className="text-red font-medium  text-sm">
                {props.error}
            </div>
            }
        </div>
    )
}