import * as Sentry from "@sentry/nuxt";

Sentry.init({
  // If set up, you can use your runtime config here
  // dsn: useRuntimeConfig().public.sentry.dsn,
  dsn: "https://e576359b7ce1dc9c9994707518553800@o4505390391754752.ingest.us.sentry.io/4508224870285312",
  integrations: [Sentry.replayIntegration()],
  // Tracing
  // We recommend adjusting this value in production, or using a tracesSampler for finer control.
  tracesSampleRate: 0, //  Capture 100% of the transactions
  // Set 'tracePropagationTargets' to control for which URLs distributed tracing should be enabled
  tracePropagationTargets: ["localhost", /^https:\/\/yourserver\.io\/api/],
  // Session Replay
  replaysSessionSampleRate: 0, // This sets the sample rate at 10%. You may want to change it to 100% while in development and then sample at a lower rate in production.
  replaysOnErrorSampleRate: 0, // If you're not already sampling the entire session, change the sample rate to 100% when sampling sessions where errors occur.
});
