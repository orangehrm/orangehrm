<template>
  <div class="orangehrm-login-branding">
    <img :src="imgSrc" alt="company-branding" />
  </div>
</template>

<script>
export default {
  name: 'login-branding',
  props: {
    imgSrc: {
      type: String,
      required: true,
    },
  },
};
</script>

<style src="./login-layout.scss" lang="scss" scoped></style>
