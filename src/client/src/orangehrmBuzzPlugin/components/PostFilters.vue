<template>
  <div class="orangehrm-post-filters">
    <oxd-button
      :label="mostRecentLabel"
      icon-name="clock-history"
      :display-type="activeRecent"
      :class="mostRecentClass"
      @click="filterMostRecent"
    />
    <oxd-button
      :label="mostLikedLabel"
      icon-name="heart-fill"
      :display-type="activeLike"
      :class="mostLikedClass"
      @click="filterMostLiked"
    />
    <oxd-button
      :label="mostCommentedLabel"
      icon-name="chat-dots-fill"
      :display-type="activeComment"
      :class="mostCommentedClass"
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
    mostRecentClass() {
      if (this.isActiveRecent || !this.isMobile) {
        return 'orangehrm-post-filters-button';
      } else {
        return '';
      }
    },
    mostLikedClass() {
      if (this.isActiveLike || !this.isMobile) {
        return 'orangehrm-post-filters-button-like';
      } else {
        return 'orangehrm-post-filters-button-like-mobile';
      }
    },
    mostCommentedClass() {
      if (this.isActiveComment || !this.isMobile) {
        return 'orangehrm-post-filters-button';
      } else {
        return '';
      }
    },
    activeRecent() {
      if (this.isActiveRecent) {
        return 'label-warn';
      } else {
        return 'text';
      }
    },
    activeLike() {
      if (this.isActiveLike) {
        return 'label-warn';
      } else {
        return 'text';
      }
    },
    activeComment() {
      if (this.isActiveComment) {
        return 'label-warn';
      } else {
        return 'text';
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
