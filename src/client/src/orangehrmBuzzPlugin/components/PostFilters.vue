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
  name: 'PostFIlters',

  props: {
    isMobile: {
      type: Boolean,
      default: false,
    },
  },

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
    },
    filterMostLiked() {
      this.isActiveRecent = false;
      this.isActiveLike = true;
      this.isActiveComment = false;
    },
    filterMostCommented() {
      this.isActiveRecent = false;
      this.isActiveLike = false;
      this.isActiveComment = true;
    },
  },
};
</script>
<style lang="scss" scoped src="./post-filters.scss"></style>
