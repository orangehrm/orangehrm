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
  <oxd-sheet class="orangehrm-buzz-create-post">
    <div class="orangehrm-buzz-create-post-header">
      <div class="orangehrm-buzz-create-post-profile-image">
        <img
          alt="profile picture"
          class="employee-image"
          :src="`../pim/viewPhoto/empNumber/1`"
        />
      </div>
      <div class="orangehrm-buzz-create-post-header-text">
        <oxd-form @submitValid="onSubmit">
          <oxd-buzz-post-input
            v-model="post"
            :rules="rules"
            :placeholder="$t('buzz.post_placeholder')"
          >
            <oxd-button type="submit" :label="$t('buzz.post')" />
          </oxd-buzz-post-input>
        </oxd-form>
      </div>
    </div>
    <oxd-divider />
    <div class="orangehrm-buzz-create-post-actions">
      <oxd-glass-button
        icon="cameraglass"
        :label="$t('buzz.share_photos')"
        @click="onClickSharePhotos"
      ></oxd-glass-button>
      <oxd-glass-button
        icon="videoglass"
        :label="$t('buzz.share_video')"
        @click="onClickShareVideos"
      ></oxd-glass-button>
    </div>
  </oxd-sheet>
  <share-video-modal
    v-if="showVideoModal"
    :text="post"
    :employee="employee"
    @close="onCloseVideoModal"
  ></share-video-modal>
</template>

<script>
import {ref} from 'vue';
import {
  required,
  shouldNotExceedCharLength,
} from '@/core/util/validation/rules';
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import GlassButton from '@ohrm/oxd/core/components/Button/GlassButton';
import BuzzPostInput from '@ohrm/oxd/core/components/Buzz/BuzzPostInput';
import ShareVideoModal from '@/orangehrmBuzzPlugin/components/ShareVideoModal';

export default {
  name: 'CreatePost',

  components: {
    'oxd-sheet': Sheet,
    'oxd-glass-button': GlassButton,
    'oxd-buzz-post-input': BuzzPostInput,
    'share-video-modal': ShareVideoModal,
  },

  props: {
    employee: {
      type: Object,
      default: null,
    },
  },

  setup() {
    const post = ref(null);
    const showVideoModal = ref(false);
    const rules = [required, shouldNotExceedCharLength(63535)];

    const onSubmit = () => {
      // todo
    };

    const onClickSharePhotos = () => {
      // todo
    };

    const onClickShareVideos = () => {
      showVideoModal.value = true;
    };

    const onCloseVideoModal = () => {
      showVideoModal.value = false;
    };

    return {
      post,
      rules,
      onSubmit,
      showVideoModal,
      onCloseVideoModal,
      onClickSharePhotos,
      onClickShareVideos,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-buzz-create-post {
  margin: 0.5rem 0 1rem 0;
  &-header {
    display: flex;
    &-text {
      width: 100%;
    }
  }
  &-actions {
    display: flex;
    align-items: center;
    justify-content: space-around;
  }
  &-profile-image {
    & img {
      width: 50px;
      height: 50px;
      display: flex;
      flex-shrink: 0;
      border-radius: 100%;
      justify-content: center;
      box-sizing: border-box;
      margin-right: 0.5rem;
    }
  }
}
</style>
