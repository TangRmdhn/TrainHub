/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./index.php", "./views/**/*.php"],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Inter", "sans-serif"],
      },
      colors: {
        brand: {
          light: "#f97316", // orange-500
          DEFAULT: "#ea580c", // orange-600
          dark: "#c2410c", // orange-700
        },
      },
    },
  },
  plugins: [],
};
