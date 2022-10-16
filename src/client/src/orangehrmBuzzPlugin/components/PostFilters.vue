<template>
  <div class="orangehrm-post-filters">
    <oxd-button
      :label="isActiveRecent || !isMobile ? 'Most Recent Posts' : ''"
      icon-name="clock-history"
      :display-type="isActiveRecent ? 'label-warn' : 'text'"
      :class="
        isActiveRecent || !isMobile ? 'orangehrm-post-filters-button' : ''
      "
      @click="filterMostRecent"
    />
    <oxd-button
      :label="isActiveLike || !isMobile ? 'Most Liked Posts' : ''"
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
      :label="isActiveComment || !isMobile ? 'Most Commented Posts' : ''"
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

  methods: {
    filterMostRecent() {
      this.isActiveRecent = true;
      this.isActiveLike = false;
      this.isActiveComment = false;
      // TODO - add filters
    },
    filterMostLiked() {
      this.isActiveRecent = false;
      this.isActiveLike = true;
      this.isActiveComment = false;
      // TODO - add filters
    },
    filterMostCommented() {
      this.isActiveRecent = false;
      this.isActiveLike = false;
      this.isActiveComment = true;
      // TODO - add filters
    },
  },
};
</script>
<style lang="scss" scoped>
.orangehrm-post-filters {
  display: flex;
  justify-content: space-between;
  margin-top: 0.5rem;
  margin-bottom: 1.5rem;
  &-button {
    width: 60rem;
    transition: width 400ms ease-in-out;
    &-like {
      width: 60rem;
      margin-left: 0.5rem;
      margin-right: 0.5rem;
      &-mobile {
        margin-left: 0.5rem;
        margin-right: 0.5rem;
      }
    }
  }
}
::v-deep(.oxd-button) {
  font-weight: 700;
}
</style>
