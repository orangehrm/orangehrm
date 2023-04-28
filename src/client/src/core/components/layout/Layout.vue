<template>
  <oxd-layout v-bind="$attrs">
    <template v-for="(_, name) in $slots" #[name]="slotData">
      <slot :name="name" v-bind="slotData" />
    </template>
    <template #user-actions>
      <li>
        <a
          href="#"
          role="menuitem"
          class="oxd-userdropdown-link"
          @click="openAboutModel"
        >
          {{ $t('general.about') }}
        </a>
      </li>
      <li>
        <a :href="supportUrl" role="menuitem" class="oxd-userdropdown-link">
          {{ $t('general.support') }}
        </a>
      </li>
      <li v-if="updatePasswordUrl">
        <a
          :href="updatePasswordUrl"
          role="menuitem"
          class="oxd-userdropdown-link"
        >
          {{ $t('general.change_password') }}
        </a>
      </li>
      <li>
        <a :href="logoutUrl" role="menuitem" class="oxd-userdropdown-link">
          {{ $t('general.logout') }}
        </a>
      </li>
    </template>
  </oxd-layout>
  <about v-if="showAboutModel" @close="closeAboutModel"></about>
</template>

<script>
import {provide, readonly, ref} from 'vue';
import About from '@/core/pages/About.vue';
import Layout from '@ohrm/oxd/core/components/Layout/Layout.vue';
import {dateFormatKey} from '@/core/util/composable/useDateFormat';

export default {
  components: {
    about: About,
    'oxd-layout': Layout,
  },
  inheritAttrs: false,
  props: {
    permissions: {
      type: Object,
      default: () => ({}),
    },
    logoutUrl: {
      type: String,
      default: '#',
    },
    homeUrl: {
      type: String,
      default: '#',
    },
    supportUrl: {
      type: String,
      default: '#',
    },
    updatePasswordUrl: {
      type: String,
      default: '#',
    },
    dateFormat: {
      type: Object,
      default: null,
    },
  },
  setup(props) {
    const showAboutModel = ref(false);
    provide('permissions', readonly(props.permissions));
    provide(dateFormatKey, readonly(props.dateFormat));

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
