<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */
 -->

<template>
  <div class="orangehrm-comment-wrapper">
    <profile-image :employee="data.employee"></profile-image>
    <div class="orangehrm-post-comment">
      <oxd-form v-if="edit" @submit-valid="onSubmit">
        <oxd-input-field
          v-model="comment"
          v-autofocus
          :rules="rules"
          @keydown.esc.stop="onCancelComment"
        />
        <oxd-text tag="span">{{ $t('buzz.press_esc_to') }}&nbsp;</oxd-text>
        <oxd-text
          tag="span"
          class="orangehrm-post-comment-action --cancel"
          @click="onCancelComment"
        >
          {{ $t('general.cancel') }}
        </oxd-text>
      </oxd-form>
      <div v-else class="orangehrm-post-comment-area">
        <oxd-text tag="p" class="orangehrm-post-comment-employee">
          {{ employeeFullName }}
        </oxd-text>
        <oxd-text
          tag="span"
          :class="{
            'orangehrm-post-comment-text': true,
            '--truncate': readMore === false,
          }"
        >
          {{ comment }}
        </oxd-text>
        <oxd-text
          v-show="!readMore"
          tag="span"
          class="orangehrm-post-comment-readmore"
          @click="onClickReadMore"
        >
          {{ $t('buzz.read_more') }}
        </oxd-text>
        <oxd-text tag="p" class="orangehrm-post-comment-datetime">
          {{ dateTime }}
        </oxd-text>
        <div
          v-if="data.comment.numOfLikes > 0"
          class="orangehrm-post-comment-stats"
        >
          <oxd-icon
            name="heart-fill"
            class="orangehrm-post-comment-stats-icon"
          />
          <oxd-text tag="p" class="orangehrm-post-comment-stats-text">
            {{ data.comment.numOfLikes }}
          </oxd-text>
        </div>
      </div>
      <div v-if="!edit" class="orangehrm-post-comment-action-area">
        <oxd-text
          tag="p"
          :class="{
            'orangehrm-post-comment-action': true,
            '--liked': data.comment.liked === true,
          }"
          @click="onClickLike"
        >
          {{ $t('buzz.like') }}
        </oxd-text>
        <oxd-text
          v-if="data.permission.canUpdate"
          tag="p"
          class="orangehrm-post-comment-action"
          @click="onClickEdit"
        >
          {{ $t('general.edit') }}
        </oxd-text>
        <oxd-text
          v-if="data.permission.canDelete"
          tag="p"
          class="orangehrm-post-comment-action"
          @click="onClickDelete"
        >
          {{ $t('performance.delete') }}
        </oxd-text>
      </div>
    </div>
  </div>
</template>

<script>
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import {computed, reactive, toRefs} from 'vue';
import useLocale from '@/core/util/composable/useLocale';
import {APIService} from '@/core/util/services/api.service';
import useAutoFocus from '@/core/util/composable/useAutoFocus';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useDateFormat from '@/core/util/composable/useDateFormat';
import ProfileImage from '@/orangehrmBuzzPlugin/components/ProfileImage';
import useBuzzAPIs from '@/orangehrmBuzzPlugin/util/composable/useBuzzAPIs';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import {OxdIcon} from '@ohrm/oxd';

export default {
  name: 'PostComment',

  components: {
    'oxd-icon': OxdIcon,
    'profile-image': ProfileImage,
  },

  directives: {...useAutoFocus()},

  props: {
    postId: {
      type: Number,
      required: true,
    },
    data: {
      type: Object,
      required: true,
    },
  },

  emits: ['edit', 'delete', 'like'],

  setup(props, context) {
    let loading = false;
    const {locale} = useLocale();
    const {jsDateFormat, jsTimeFormat} = useDateFormat();
    const {$tEmpName} = useEmployeeNameTranslate();
    const rules = [required, shouldNotExceedCharLength(65530)];
    const state = reactive({
      edit: false,
      comment: props.data.comment.text,
      readMore: new String(props.data.comment.text).length < 500,
    });

    const {updatePostComment, updateCommentLike} = useBuzzAPIs(
      new APIService(window.appGlobal.baseUrl, ''),
    );

    const onSubmit = () => {
      updatePostComment(
        props.postId,
        props.data.comment.id,
        state.comment,
      ).then(() => {
        state.edit = false;
        context.emit('edit', props.data.comment.id);
      });
    };

    const onClickEdit = () => {
      state.edit = true;
    };

    const onClickLike = () => {
      if (loading) return;
      loading = true;
      updateCommentLike(props.data.comment.id, props.data.comment.liked).then(
        () => {
          loading = false;
          context.emit('like', props.data.comment.id);
        },
      );
    };

    const onClickDelete = () => {
      context.emit('delete', props.data.comment.id);
    };

    const onClickReadMore = () => {
      state.readMore = !state.readMore;
    };

    const onCancelComment = () => {
      state.comment = props.data.comment.text;
      state.edit = false;
    };

    const dateTime = computed(() => {
      const {createdDate, createdTime} = props.data.comment;
      const utcDate = parseDate(
        `${createdDate} ${createdTime} +00:00`,
        'yyyy-MM-dd HH:mm xxx',
      );

      return formatDate(utcDate, `${jsDateFormat} ${jsTimeFormat}`, {
        locale,
      });
    });

    const employeeFullName = computed(() => {
      return $tEmpName(props.data.employee, {
        includeMiddle: true,
        excludePastEmpTag: false,
      });
    });

    return {
      rules,
      dateTime,
      onSubmit,
      onClickLike,
      onClickEdit,
      onClickDelete,
      onClickReadMore,
      onCancelComment,
      employeeFullName,
      ...toRefs(state),
    };
  },
};
</script>

<style src="./post-comment.scss" lang="scss" scoped></style>
