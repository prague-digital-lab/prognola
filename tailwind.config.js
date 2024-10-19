/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./components/**/*.{js,vue,ts}",
    "./layouts/**/*.vue",
    "./pages/**/*.vue",
    "./plugins/**/*.{js,ts}",
    "./app.vue",
    "./error.vue",
  ],
  theme: {
    fontSize: {
      sm: "0.80rem",
      base: "0.85rem",
      xl: "1.17rem",
      "2xl": "1.263rem",
      "3xl": "1.453rem",
      "4xl": "1.741rem",
      "5xl": "2.052rem",
    },
    extend: {
      fontFamily: {
        custom: ["Inter"],
      },
      colors: {
        'gray-hover': '#fafafa',
      }
    },
  },
  plugins: [require("@tailwindcss/forms")],
};
