<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-text tag="h6">Job Category List</oxd-text>
        <div>
          <oxd-button label="Add" displayType="secondary" @click="onClickAdd" />
        </div>
      </div>
      <oxd-divider class="orangehrm-horizontal-margin" />
      <div>
        <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
          <div v-if="checkedItems.length > 0">
            <oxd-text tag="span">
              {{ checkedItems.length }} Job Category Selected
            </oxd-text>
            <oxd-button
              label="Delete Selected"
              displayType="label-danger"
              @click="onClickDeleteSelected"
              class="orangehrm-horizontal-margin"
            />
          </div>
          <oxd-text tag="span" v-else> {{ total }} Job Category Found</oxd-text>
        </div>
      </div>
      <div class="orangehrm-container">
        <oxd-card-table
          ref="dTable"
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

    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script>
import DeleteConfirmationDialog from "../components/dialogs/DeleteConfirmationDialog";

export default {
  data() {
    return {
      headers: [
        { name: "name", title: "Job Category", style: { "flex-basis": "80%" } },
        {
          name: "actions",
          title: "Actions",
          style: { "flex-shrink": 1 },
          cellType: "oxd-table-cell-actions",
          cellConfig: {
            delete: {
              onClick: this.onClickDelete,
              component: "oxd-icon-button",
              props: {
                name: "trash",
              },
            },
            edit: {
              onClick: this.onClickEdit,
              props: {
                name: "pencil-fill",
              },
            },
          },
        },
      ],
      items: [],
      total: 0,
      pages: 1,
      currentPage: 1,
      pageSize: 5,
      showPaginator: false,
      editItem: null,
      checkedItems: [],
    };
  },

  components: {
    "delete-confirmation": DeleteConfirmationDialog,
  },

  watch: {
    currentPage() {
      this.resetDataTable();
      this.fetchData();
    },
  },

  created() {
    this.fetchData();
  },

  methods: {
    onClickAdd() {
      this.$emit("onAddItem", { viewId:2, payload: null });
    },
    onClickEdit(item) {
      this.$emit("onEditItem", { viewId:3, payload: item });
    },
    onClickDeleteSelected() {
      const ids = this.checkedItems.map((index) => {
        return this.items[index].id;
      });
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === "ok") {
          this.deleteItems(ids);
        }
      });
    },
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === "ok") {
          this.deleteItems([item.id]);
        }
      });
    },

    fetchData() {
      // TODO: Loading
      let query = `limit=${this.pageSize}`;
      const offset = this.pageSize * (this.currentPage - 1);
      query = query + `&offset=${offset}`;
      this.$http
        .get(`api/v1/admin/job-categories?${query}`)
        .then((response) => {
          const { data, meta } = response.data;
          this.items = data;
          this.total = meta.total;

          if (this.total > this.pageSize) {
            this.showPaginator = true;
            this.pages = Math.floor(this.total / this.pageSize) + 1;
          } else {
            this.showPaginator = false;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    deleteItems(items) {
      // TODO: Loading
      if (items instanceof Array) {
        this.$http
          .delete("api/v1/admin/job-categories", {
            data: { ids: items },
          })
          .then(() => {
            this.resetDataTable();
            this.fetchData();
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    resetDataTable() {
      this.$refs.dTable.checkedItems = [];
    },
  },
};
</script>