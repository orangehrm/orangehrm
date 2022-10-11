<template>
  <!-- TODO: Placeholder component -->
  <oxd-sheet class="orangehrm-buzz-create-post">
    <oxd-form @submitValid="onSubmit">
      <oxd-buzz-post-input
        v-model="post"
        :rules="rules"
        :placeholder="$t('buzz.post_placeholder')"
      >
        <oxd-button type="submit" :label="$t('buzz.post')" />
      </oxd-buzz-post-input>
    </oxd-form>
    <oxd-divider />
    <div class="orangehrm-buzz-create-post-actions">
      <oxd-glass-button
        icon="cameraglass"
        :label="$t('buzz.share_photos')"
      ></oxd-glass-button>
      <oxd-glass-button
        icon="videoglass"
        :label="$t('buzz.share_video')"
      ></oxd-glass-button>
    </div>
  </oxd-sheet>
</template>

<script>
import {ref} from 'vue';
import {required} from '@/core/util/validation/rules';
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import GlassButton from '@ohrm/oxd/core/components/Button/GlassButton';
import BuzzPostInput from '@ohrm/oxd/core/components/Buzz/BuzzPostInput';

export default {
  name: 'CreatePost',

  components: {
    'oxd-sheet': Sheet,
    'oxd-glass-button': GlassButton,
    'oxd-buzz-post-input': BuzzPostInput,
  },

  props: {
    employee: {
      type: Object,
      default: null,
    },
  },

  setup() {
    const post = ref(null);
    const rules = [required];

    const onSubmit = () => {
      // todo
    };

    return {
      post,
      rules,
      onSubmit,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-buzz-create-post {
  margin: 0.5rem 0 1rem 0;
  &-actions {
    display: flex;
    align-items: center;
    justify-content: space-around;
  }
}
</style>
