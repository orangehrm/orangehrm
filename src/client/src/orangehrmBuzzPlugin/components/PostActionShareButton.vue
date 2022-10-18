<template>
  <oxd-icon-button class="" name="share-fill" :with-container="true" />
</template>
<script>
export default {
  name: 'ShareButton',

  props: {
    postId: {
      type: Number,
      required: true,
    },
    buzzUserId: {
      type: Number,
      required: true,
    },
  },
};
</script>
