<template>
    <router-view/>
    <notifications />
</template>

<script>

export default {
    name: "App"
}
</script>

<style>
:root{
  /* --toggle-bg-off: #F70503;
  --toggle-border-off: #F70503; */
}

/**Font from google font */
#app {
  font-family: 'Montserrat', sans-serif;
}
body {
  background-color: #F5F5F5;
}
@media (-webkit-device-pixel-ratio: 1.10) {
    :root {
      zoom: 0.90;
    }
}
/* Chrome, Safari, Edge, Opera */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
/* Firefox */
input[type="number"] {
    -moz-appearance: textfield;
}

input[type="text"]:focus{
  outline: none !important;
  --tw-ring-color: transparent !important;
}
</style>