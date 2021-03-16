<template>
  <!-- TODO: Extend from base layout -->
  <div class="container">
   <job-category-list v-if="activeView==1" @on-add-item="changeView" @on-edit-item="changeView"></job-category-list>
   <job-category-single v-if="activeView==2" @on-cancel="changeView" title="Add Job Category" action="Add" :jobCategory="null"></job-category-single>
   <job-category-single v-if="activeView==3" @on-cancel="changeView" title="Edit Job Category" action="Update" :jobCategory="payload"></job-category-single>
  </div>
</template>

<script>
import JobCategoryList from "./views/JobCategoryList.vue";
import JobCategorySingle from "./views/JobCategorySingle.vue";

export default {
  components: {
    "job-category-list": JobCategoryList,
    "job-category-single": JobCategorySingle,
  },
  data() {
    return {
      activeView: 1,
      payload: null
    };
  },
  methods: {
    changeView({ viewId, payload }) {
      this.activeView = viewId;
      this.payload = payload ? payload : null;
    },
  },
};
</script>

<style lang="scss">
.container {
  width: 100vw;
  display: flex;
}

body {
  margin: 0;
  background-color: $oxd-background-pastel-white-color;
}

.orangehrm-header-container {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: space-between;
  padding: 1.2rem;
}

.orangehrm-container {
  background-color: $oxd-interface-gray-lighten-2-color;
  padding: 0.5rem;
}

.orangehrm-paper-container {
  background-color: $oxd-white-color;
  border-radius: $oxd-border-radius;
}

.orangehrm-horizontal-padding {
  padding-left: 1.2rem;
  padding-right: 1.2rem;
}

.orangehrm-vertical-padding {
  padding-top: 1.2rem;
  padding-bottom: 1.2rem;
}

.orangehrm-horizontal-margin {
  margin-left: 1.2rem;
  margin-right: 1.2rem;
}

.orangehrm-background-container {
  background-color: $oxd-background-pastel-white-color;
  padding: 2rem;
  flex: 1;
}

.orangehrm-bottom-container {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: right;
  padding: 1.2rem;
}
</style>
