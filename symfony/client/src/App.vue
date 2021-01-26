<template>
  <Label label="Input" />
  <Input label="Initial Value" />
</template>

<script lang="ts">
import {Options, Vue} from 'vue-class-component';
import Label from '@orangehrm/oxd/core/components/Label/Label.vue';
import Input from '@orangehrm/oxd/core/components/Input/Input.vue';

@Options({
  components: {
    Label,
    Input,
  },
})
export default class App extends Vue {}
</script>

<style lang="scss">
@import url('https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap');

$orangehrm-font-family: $oxd-font-family !default;

#app {
  font-family: $orangehrm-font-family;
}
</style>
