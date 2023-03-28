/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./index.html",
    "./resources/**/*.{vue,js,ts,jsx,tsx}",
    "./node_modules/flowbite/**/*.js",
    "./node_modules/tw-elements/dist/js/**/*.js"
  ],
  theme: {
    extend: {
      fontFamily: {
        'montserrat': ['Montserrat', 'sans-serif']
      },
      colors: {
        'custom-blue': '#5080C7',
        'custom-red': '#F70503',
        'custom-green': '#31C48D',
        'custom-gray': '#4c4c4c',
        'custom-light-blue': '#EFF3F6',
        'custom-light-blue-bg': '#E8F0FE',
        'custom-dark-blue': '#172340',
        'custom-orange': '#F59C4A',
        'custom-yellow': '#F5F06E',
        'custom-bg': '#F5F5F5',
        'custom-light': '#DEDEDE',
        'custom-light-gray': '#939498'
      },
      boxShadow: {
        'newdrop': 'rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;',
      },
    },
    screens: {
      'sm': '640px',
      // => @media (min-width: 640px) { ... }

      'md': '768px',
      // => @media (min-width: 768px) { ... }

      'lg': '1024px',
      // => @media (min-width: 1024px) { ... }

      'xl': '1280px',
      // => @media (min-width: 1280px) { ... }

      '2xl': '1536px',
      // => @media (min-width: 1536px) { ... }
    },
  },
  plugins: [
    require("@tailwindcss/forms"),
    require("tailwindcss-inner-border"),
    require('flowbite/plugin'),
    require("tw-elements/dist/plugin")
  ],
}