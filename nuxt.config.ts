// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },
  css: ["~/assets/scss/app.scss"],
  ssr: false,

  sentry: {
    sourceMapsUploadOptions: {
      org: "prazska-laborator",
      project: "prognola-web",
      authToken: "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",
    },
  },

  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    },
  },

  sanctum: {
    mode: "token",
  },

  runtimeConfig: {
    public: {
      sanctum: {
        baseUrl: "http://localhost:8000",
        redirect: {
          onLogin: "/entry", // Custom route after successful login
          onAuthOnly: "/login",
          onGuestOnly: "/entry",
          onLogout: "/",
        },
        endpoints: {
          login: "/api/login",
          logout: "/api/logout",
          user: "/api/user",
        },
      },
    },
  },

  modules: ["nuxt-auth-sanctum", "@nuxt/image", "@nuxt/fonts", "@sentry/nuxt/module"],
  compatibilityDate: "2024-07-16",
});
