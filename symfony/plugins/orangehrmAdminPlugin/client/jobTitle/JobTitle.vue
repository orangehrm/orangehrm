<template>
  <div class="orangehrm-background-container" v-if="currentScreen === 'list'">
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6">Job Title List</oxd-text>
        <div>
          <oxd-button label="Add" type="secondary" @click="onClickAdd" />
        </div>
      </div>
      <oxd-divider class="orangehrm-horizontal-margin" />
      <div>
        <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
          <div v-if="checkedItems.length > 0">
            <oxd-text tag="span">
              {{ checkedItems.length }} Job Title Selected
            </oxd-text>
            <oxd-button
              label="Delete Selected"
              type="label-error"
              @click="onClickDeleteSelected"
              class="orangehrm-horizontal-margin"
            />
          </div>
          <oxd-text tag="span" v-else> {{ total }} Job Title Found</oxd-text>
        </div>
      </div>
      <div class="orangehrm-container">
        <oxd-card-table
          :headers="headers"
          :items="items"
          :selectable="true"
          v-model:selected="checkedItems"
          rowDecorator="oxd-table-decorator-card"
        />
      </div>
      <div class="orangehrm-bottom-container">
        <oxd-pagination
          v-if="showPaginator"
          :length="pages"
          v-model:current="currentPage"
        />
      </div>
    </div>
  </div>

  <SaveJobTitle v-if="currentScreen === 'add'" />

  <EditJobTitle v-if="currentScreen === 'edit'" :editItem="editItem" />
</template>

<script>
import CardTable from "@orangehrm/oxd/core/components/CardTable/CardTable";
import Button from "@orangehrm/oxd/src/core/components/Button/Button";
import Pagination from "@orangehrm/oxd/core/components/Pagination/Pagination";
import Divider from "@orangehrm/oxd/core/components/Divider/Divider";
import Text from "@orangehrm/oxd/core/components/Text/Text";
import SaveJobTitle from "./SaveJobTitle.vue";
import EditJobTitle from "./EditJobTitle.vue";

export default {
  data() {
    return {
      headers: [
        { name: "title", title: "Job Title", style: { flex: 2 } },
        { name: "description", title: "Description", style: { flex: 4 } },
        {
          name: "actions",
          title: "Actions",
          style: { flex: 1 },
          cellType: "oxd-table-cell-actions",
          cellConfig: {
            delete: {
              onClick: this.onClickDelete,
              component: "oxd-icon-button",
              props: {
                name: "trash"
              }
            },
            edit: {
              onClick: this.onClickEdit,
              props: {
                name: "pencil-fill"
              }
            }
          }
        }
      ],
      items: [],
      total: 0,
      pages: 1,
      currentPage: 1,
      pageSize: 5,
      showPaginator: false,
      currentScreen: "list",
      editItem: null,
      checkedItems: []
    };
  },

  watch: {
    currentPage() {
      this.fetchData();
    }
  },

  components: {
    "oxd-card-table": CardTable,
    "oxd-button": Button,
    "oxd-pagination": Pagination,
    "oxd-divider": Divider,
    "oxd-text": Text,
    SaveJobTitle,
    EditJobTitle
  },

  created() {
    this.fetchData();
  },

  methods: {
    onClickAdd() {
      this.currentScreen = "add";
    },
    onClickDeleteSelected() {
      const ids = [];
      this.checkedItems.forEach(index => {
        ids.push(this.items[index].id);
      });
      this.callDelete(ids);
    },
    onClickDelete(item) {
      const id = item.id;
      this.callDelete([id]);
    },
    callDelete(ids) {
      const headers = new Headers();
      headers.append("Content-Type", "application/json");
      headers.append("Accept", "application/json");

      fetch(`${this.global.baseUrl}/api/v1/admin/job-titles`, {
        method: "DELETE",
        headers: headers,
        body: JSON.stringify({
          ids: ids
        })
      }).then(async res => {
        if (res.status === 200) {
          window.location.reload();
          // this.currentPage = 1;
          // this.checkedItems = [];
          // this.fetchData();
        } else {
          console.error(res);
        }
      });
    },
    onClickEdit(item) {
      this.editItem = item;
      console.log(item);
      console.log(this.editItem);
      this.$nextTick();
      this.currentScreen = "edit";
    },
    fetchData() {
      const headers = new Headers();
      headers.append("Content-Type", "application/json");
      headers.append("Accept", "application/json");

      let query = `limit=${this.pageSize}`;
      const offset = this.pageSize * (this.currentPage - 1);
      query = query + `&offset=${offset}`;

      fetch(`${this.global.baseUrl}/api/v1/admin/job-titles?${query}`, {
        method: "GET",
        headers: headers
      }).then(async res => {
        if (res.status === 200) {
          const response = await res.json();
          this.items = response.data;
          this.total = response.meta.total;

          if (this.total > this.pageSize) {
            this.showPaginator = true;
            this.pages = Math.floor(this.total / this.pageSize) + 1;
          } else {
            this.showPaginator = false;
          }
        } else {
          console.error(res);
        }
      });
    }
  }
};
</script>

<style>
.orangehrm-header-container {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: space-between;
  padding: 1.2rem;
}

.orangehrm-container {
  background-color: #e8eaef;
  /*border-radius: 1.2rem;*/
  padding: 0.5rem;
}

.orangehrm-paper-container {
  background-color: white;
  border-radius: 1.2rem;
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
  background-color: #f6f5fb;
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

body {
  margin: 0;
  background-color: #f6f5fb;
}

#app {
  display: flex;
}
</style>
