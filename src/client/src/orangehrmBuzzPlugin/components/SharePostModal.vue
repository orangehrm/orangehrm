<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
 -->

<template>
  <post-modal
    :loading="isLoading"
    :title="$t('buzz.share_post')"
    @submit="onSubmit"
    @close="$emit('close', false)"
  >
    <template #header>
      <oxd-buzz-post-input
        v-model="post.text"
        :rules="rules.text"
        :placeholder="$t('buzz.post_placeholder')"
      >
      </oxd-buzz-post-input>
    </template>
    <oxd-text tag="p" class="orangehrm-buzz-share-employee">
      {{ employeeFullName }}
    </oxd-text>
    <oxd-text tag="p" class="orangehrm-buzz-share-date">
      {{ postDate }}
    </oxd-text>
    <oxd-text v-if="data.text" tag="p">
      {{ data.text }}
    </oxd-text>
  </post-modal>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {computed, reactive, toRefs} from 'vue';
import useLocale from '@/core/util/composable/useLocale';
import {APIService} from '@/core/util/services/api.service';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import PostModal from '@/orangehrmBuzzPlugin/components/PostModal';
import BuzzPostInput from '@ohrm/oxd/core/components/Buzz/BuzzPostInput';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  name: 'SharePostModal',

  components: {
    'post-modal': PostModal,
    'oxd-buzz-post-input': BuzzPostInput,
  },

  props: {
    data: {
      type: Object,
      required: true,
    },
  },

  emits: ['close'],

  setup(props, context) {
    const {locale} = useLocale();
    const {jsDateFormat} = useDateFormat();
    const {$tEmpName} = useEmployeeNameTranslate();
    const rules = {
      text: [required, shouldNotExceedCharLength(63535)],
    };
    const http = new APIService(window.appGlobal.baseUrl, 'api/v2/buzz/posts');

    const state = reactive({
      post: {
        text: null,
      },
      isLoading: false,
    });

    const onSubmit = () => {
      state.isLoading = true;
      http
        .create({
          text: state.post.text,
          parentPostId: props.data.id,
        })
        .then(() => context.emit('close', true));
    };

    const employeeFullName = computed(() => {
      return $tEmpName(props.data.employee, {
        includeMiddle: true,
        excludePastEmpTag: false,
      });
    });

    const postDate = computed(() => {
      return formatDate(parseDate(props.data.createdTime), jsDateFormat, {
        locale,
      });
    });

    return {
      rules,
      onSubmit,
      postDate,
      employeeFullName,
      ...toRefs(state),
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-buzz-share {
  &-employee {
    font-size: 1.2rem;
  }
  &-date {
    font-size: 0.75rem;
    margin-bottom: 0.5rem;
    color: $oxd-interface-gray-color;
  }
}
</style>
