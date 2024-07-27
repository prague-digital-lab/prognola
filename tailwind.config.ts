import type { Config } from "tailwindcss";

const config: Config = {
  content: [
    "./pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    fontFamily: {
      "inter": ["Inter", "ui-sans-serif"]
    },
    borderRadius: {
      DEFAULT: "8px",
    },
    colors: {
      blue: {
        DEFAULT: "#2563EB",
        darker: "#144CC8",
        light: "#E2F3FF",
        highlight: "#B6E0FF50",
      },
      brown: {
        background: "#F2F0E8",
      },
      green: {
        DEFAULT: "#6D9329",
        success: "#4FCC02",
        light: "#D9E9CF"
      },
      red: {
        DEFAULT: "#D50000"
      },
      black: {
        "10": "#00000010",
        "25": "#00000025",
        "50": "#00000050",
        "75": "#00000075",
        "90": "#00000090",
        DEFAULT: "#000000"
      },
      gray: {
        medium: "#B6B6B6"
      },
      white: "#ffffff",
      transparent: "#00000000"
    },
    extend: {
      backgroundImage: {
        "gradient-radial": "radial-gradient(var(--tw-gradient-stops))",
        "gradient-conic":
          "conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))",
      },
      boxShadow: {
        'DEFAULT': '0 2px 8px 0px rgb(0 0 0 / 0.1)',
      }
    },
  },
  plugins: [],
};
export default config;
