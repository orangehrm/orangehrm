<template>
  <oxd-icon-button class="" name="chat-text-fill" :with-container="true" />
</template>
<script>
export default {
  name: 'CommentButton',

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
