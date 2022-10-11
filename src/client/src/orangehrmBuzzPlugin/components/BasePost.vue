<template>
  <!-- TODO: Placeholder component -->
  <oxd-sheet class="orangehrm-buzz-post">
    <div class="orangehrm-buzz-post-profile">
      <img
        alt="profile picture"
        class="employee-image"
        :src="`../pim/viewPhoto/empNumber/${employee.empNumber}`"
      />
      <oxd-text type="card-title">
        {{ employeeFullName }}
      </oxd-text>
    </div>
    <oxd-divider></oxd-divider>
    <oxd-text type="card-title">{{ content }}</oxd-text>
  </oxd-sheet>
</template>

<script>
import {computed} from 'vue';
import Sheet from '@ohrm/oxd/core/components/Sheet/Sheet';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  name: 'BasePost',

  components: {
    'oxd-sheet': Sheet,
  },

  props: {
    postId: {
      type: Number,
      required: true,
    },
    content: {
      type: String,
      required: true,
    },
    employee: {
      type: Object,
      required: true,
    },
  },

  setup(props) {
    const {$tEmpName} = useEmployeeNameTranslate();

    const employeeFullName = computed(() => {
      return $tEmpName(props.employee, {
        includeMiddle: true,
        excludePastEmpTag: false,
      });
    });

    return {
      employeeFullName,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-buzz-post {
  height: 150px;
  margin-bottom: 1rem;
  &-profile {
    display: flex;
    align-items: center;
    & img {
      width: 40px;
      height: 40px;
      display: flex;
      flex-shrink: 0;
      margin-right: 1rem;
      border-radius: 100%;
      justify-content: center;
      box-sizing: border-box;
    }
  }
}
</style>
