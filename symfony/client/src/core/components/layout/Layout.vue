<template>
  <oxd-layout v-bind="$attrs">
    <template v-for="(_, name) in $slots" v-slot:[name]="slotData">
      <slot :name="name" v-bind="slotData" />
    </template>
    <template v-slot:user-actions>
      <li>
        <a
          href="#"
          role="menuitem"
          class="oxd-userdropdown-link"
          @click="openAboutModel"
        >
          About
        </a>
      </li>
      <li>
        <a :href="supportUrl" role="menuitem" class="oxd-userdropdown-link">
          Support
        </a>
      </li>
      <li>
        <a
          :href="updatePasswordUrl"
          role="menuitem"
          class="oxd-userdropdown-link"
        >
          Change Password
        </a>
      </li>
      <li>
        <a :href="logoutUrl" role="menuitem" class="oxd-userdropdown-link">
          Logout
        </a>
      </li>
    </template>
  </oxd-layout>
  <about v-if="showAboutModel" @close="closeAboutModel"></about>
</template>

<script>
import {provide, readonly, ref} from 'vue';
import About from '@/core/pages/About.vue';
import Layout from '@orangehrm/oxd/src/core/components/Layout/Layout.vue';

export default {
  inheritAttrs: false,
  components: {
    about: About,
    'oxd-layout': Layout,
  },
  props: {
    permissions: {
      type: Object,
    },
    logoutUrl: {
      type: String,
    },
    supportUrl: {
      type: String,
    },
    updatePasswordUrl: {
      type: String,
    },
  },
  setup(props) {
    const showAboutModel = ref(false);
    provide('permissions', readonly(props.permissions));

    const openAboutModel = () => {
      showAboutModel.value = true;
    };

    const closeAboutModel = () => {
      showAboutModel.value = false;
    };

    return {
      showAboutModel,
      openAboutModel,
      closeAboutModel,
    };
  },
};
</script>
