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
  <div class="orangehrm-post-filters">
    <oxd-button
      :label="mostRecentLabel"
      icon-name="clock-history"
      :display-type="isActiveRecent ? 'label-warn' : 'text'"
      :class="
        isActiveRecent || !isMobile ? 'orangehrm-post-filters-button' : ''
      "
      @click="filterMostRecent"
    />
    <oxd-button
      :label="mostLikedLabel"
      icon-name="heart-fill"
      :display-type="isActiveLike ? 'label-warn' : 'text'"
      :class="
        isActiveLike || !isMobile
          ? 'orangehrm-post-filters-button-like'
          : 'orangehrm-post-filters-button-like-mobile'
      "
      @click="filterMostLiked"
    />
    <oxd-button
      :label="mostCommentedLabel"
      icon-name="chat-dots-fill"
      :display-type="isActiveComment ? 'label-warn' : 'text'"
      :class="
        isActiveComment || !isMobile ? 'orangehrm-post-filters-button' : ''
      "
      @click="filterMostCommented"
    />
  </div>
</template>
<script>
export default {
  name: 'PostFilters',

  props: {
    isMobile: {
      type: Boolean,
      default: false,
    },
  },

  emits: ['updatePriority'],

  data() {
    return {
      isActiveRecent: true,
      isActiveLike: false,
      isActiveComment: false,
    };
  },

  computed: {
    mostRecentLabel() {
      if (this.isActiveRecent || !this.isMobile) {
        return this.$t('buzz.most_recent_posts');
      } else {
        return '';
      }
    },
    mostLikedLabel() {
      if (this.isActiveLike || !this.isMobile) {
        return this.$t('buzz.most_liked_posts');
      } else {
        return '';
      }
    },
    mostCommentedLabel() {
      if (this.isActiveComment || !this.isMobile) {
        return this.$t('buzz.most_commented_posts');
      } else {
        return '';
      }
    },
  },

  methods: {
    filterMostRecent() {
      this.isActiveRecent = true;
      this.isActiveLike = false;
      this.isActiveComment = false;
      const value = 'most_recent';
      this.$emit('updatePriority', value);
    },
    filterMostLiked() {
      this.isActiveRecent = false;
      this.isActiveLike = true;
      this.isActiveComment = false;
      const value = 'most_likes';
      this.$emit('updatePriority', value);
    },
    filterMostCommented() {
      this.isActiveRecent = false;
      this.isActiveLike = false;
      this.isActiveComment = true;
      const value = 'most_comments';
      this.$emit('updatePriority', value);
    },
  },
};
</script>
<style lang="scss" scoped src="./post-filters.scss"></style>
