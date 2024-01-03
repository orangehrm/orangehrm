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
  <oxd-sheet class="orangehrm-buzz-create-post">
    <div class="orangehrm-buzz-create-post-header">
      <profile-image :employee="employee"></profile-image>
      <div class="orangehrm-buzz-create-post-header-text">
        <oxd-form @submit-valid="onSubmit">
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
  <share-photo-modal
    v-if="showPhotoModal"
    :text="post"
    :employee="employee"
    @close="onClosePhotoModal"
  ></share-photo-modal>
</template>

<script>
import {ref} from 'vue';
import {OxdBuzzPostInput, OxdGlassButton, OxdSheet} from '@ohrm/oxd';
import useToast from '@/core/util/composable/useToast';
import {APIService} from '@/core/util/services/api.service';
import {shouldNotExceedCharLength} from '@/core/util/validation/rules';
import ProfileImage from '@/orangehrmBuzzPlugin/components/ProfileImage';
import ShareVideoModal from '@/orangehrmBuzzPlugin/components/ShareVideoModal';
import SharePhotoModal from '@/orangehrmBuzzPlugin/components/SharePhotoModal';

export default {
  name: 'CreatePost',

  components: {
    'oxd-sheet': OxdSheet,
    'profile-image': ProfileImage,
    'oxd-glass-button': OxdGlassButton,
    'oxd-buzz-post-input': OxdBuzzPostInput,
    'share-video-modal': ShareVideoModal,
    'share-photo-modal': SharePhotoModal,
  },

  props: {
    employee: {
      type: Object,
      required: true,
    },
  },

  emits: ['refresh'],

  setup(_, context) {
    const post = ref(null);
    const {saveSuccess} = useToast();
    const showVideoModal = ref(false);
    const showPhotoModal = ref(false);
    const rules = [shouldNotExceedCharLength(65530)];
    const http = new APIService(window.appGlobal.baseUrl, '/api/v2/buzz/posts');

    const onSubmit = () => {
      if (post.value === null || String(post.value).trim() === '') return;
      http
        .create({
          type: 'text',
          text: post.value,
        })
        .then(() => {
          saveSuccess();
          post.value = null;
          context.emit('refresh');
        });
    };

    const onClickSharePhotos = () => {
      showPhotoModal.value = true;
    };

    const onClickShareVideos = () => {
      showVideoModal.value = true;
    };

    const onCloseVideoModal = ($event) => {
      showVideoModal.value = false;
      if ($event) {
        saveSuccess();
        context.emit('refresh');
      }
    };

    const onClosePhotoModal = ($event) => {
      showPhotoModal.value = false;
      if ($event) {
        saveSuccess();
        context.emit('refresh');
      }
    };

    return {
      post,
      rules,
      onSubmit,
      showVideoModal,
      showPhotoModal,
      onCloseVideoModal,
      onClosePhotoModal,
      onClickSharePhotos,
      onClickShareVideos,
    };
  },
};
</script>

<style src="./create-post.scss" lang="scss" scoped></style>
