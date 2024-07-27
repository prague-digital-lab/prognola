import { useState, createContext, useEffect, ReactElement, SetStateAction, Dispatch } from "react";

const appDefaults = {
    loggedIn: false,
    token: ""
} as AppTypes


export const AppContext = createContext({
    app: appDefaults,
    setApp: {} as Dispatch<SetStateAction<AppTypes>>,
});

export function AppContextProvider(props: { children: ReactElement | ReactElement[] }) {

    const [app, setApp] = useState(appDefaults)

    useEffect(() => {
        const token = localStorage.getItem("token")
        setApp({ loggedIn: (token) ? true : false, token: token })
    }, []);

    return (
        <AppContext.Provider
            value={{
                app: app,
                setApp: setApp,
            }} >
            {props.children}
        </AppContext.Provider>
    )
}