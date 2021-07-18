<template>
  <oxd-layout>
    <template v-for="(_, name) in $slots" v-slot:[name]="slotData">
      <slot :name="name" v-bind="slotData" />
    </template>
  </oxd-layout>
</template>

<script>
import {provide, readonly} from 'vue';
import Layout from '@orangehrm/oxd/src/core/components/Layout/Layout.vue';

export default {
  components: {
    'oxd-layout': Layout,
  },
  props: {
    permissions: {
      type: Object,
    },
  },
  setup(props) {
    provide('permissions', readonly(props.permissions));
  },
};
</script>
