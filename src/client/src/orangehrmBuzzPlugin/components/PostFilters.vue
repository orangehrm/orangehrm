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
  <div class="orangehrm-post-filters">
    <oxd-button
      icon-name="clock-history"
      class="orangehrm-post-filters-button"
      :label="mostRecentButtonLabel"
      :display-type="mostRecentButtonType"
      @click="$emit('updatePriority', 'share.createdAtUtc')"
    />
    <oxd-button
      icon-name="heart-fill"
      class="orangehrm-post-filters-button"
      :label="mostLikesButtonLabel"
      :display-type="mostLikesButtonType"
      @click="$emit('updatePriority', 'share.numOfLikes')"
    />
    <oxd-button
      icon-name="chat-dots-fill"
      class="orangehrm-post-filters-button"
      :label="mostCommentsButtonLabel"
      :display-type="mostCommentsButtonType"
      @click="$emit('updatePriority', 'share.numOfComments')"
    />
  </div>
</template>

<script>
export default {
  name: 'PostFilters',

  props: {
    filter: {
      type: String,
      required: true,
    },
    mobile: {
      type: Boolean,
      default: false,
    },
  },

  emits: ['updatePriority'],

  computed: {
    isMostRecent() {
      return this.filter === 'share.createdAtUtc';
    },
    isMostLikes() {
      return this.filter === 'share.numOfLikes';
    },
    isMostComments() {
      return this.filter === 'share.numOfComments';
    },
    mostRecentButtonType() {
      return this.isMostRecent ? 'label-warn' : 'text';
    },
    mostLikesButtonType() {
      return this.isMostLikes ? 'label-warn' : 'text';
    },
    mostCommentsButtonType() {
      return this.isMostComments ? 'label-warn' : 'text';
    },
    mostRecentButtonLabel() {
      if (this.mobile) {
        return this.isMostRecent ? this.$t('buzz.most_recent_posts') : '';
      }
      return this.$t('buzz.most_recent_posts');
    },
    mostLikesButtonLabel() {
      if (this.mobile) {
        return this.isMostLikes ? this.$t('buzz.most_liked_posts') : '';
      }
      return this.$t('buzz.most_liked_posts');
    },
    mostCommentsButtonLabel() {
      if (this.mobile) {
        return this.isMostComments ? this.$t('buzz.most_commented_posts') : '';
      }
      return this.$t('buzz.most_commented_posts');
    },
  },
};
</script>

<style lang="scss" scoped src="./post-filters.scss"></style>
