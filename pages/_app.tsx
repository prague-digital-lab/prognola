import { AppContextProvider } from '@/components/Contexts/AppContext'
import '../styles/globals.css'
import type { AppProps } from 'next/app'
import Head from 'next/head'
import { NextPage } from 'next/types'
import { ReactElement, ReactNode } from 'react'

export type NextPageWithLayout<P = {}, IP = P> = NextPage<P, IP> & {
  getLayout?: (page: ReactElement) => ReactNode
}

type AppPropsWithLayout = AppProps & {
  Component: NextPageWithLayout
}


function MyApp({ Component, pageProps }: AppPropsWithLayout) {

  // Use the layout defined at the page level, if available
  const getLayout = Component.getLayout ?? ((page) => page)

  return (
    <AppContextProvider>
      <>
        {getLayout(
          <>
            <Head>
              <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
              <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png" />
              <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png" />
              <meta name="theme-color" content="#F2F0E8" />
              <link rel="icon" href="/favicon/favicon.ico" />
              <link rel="manifest" href="/favicon/site.webmanifest" />
            </Head>

            <Component className="font-inter" {...pageProps} />
            <div id="modals-portal"></div>
          </>
        )
        }
      </>
    </AppContextProvider>
  )
}

export default MyApp
