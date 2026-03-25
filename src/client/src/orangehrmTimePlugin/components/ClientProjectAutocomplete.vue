<template>
  <oxd-input-field
    v-model="selectedProject"
    type="autocomplete"
    label="Project Name"
    :clear="false"
    :create-options="loadProjects"
  />
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';

export default {
  name: 'ClientProjectAutocomplete',

  props: {
    modelValue: {
      type: Object,
      default: null,
    },
    customerId: {
      type: Number,
      default: null,
    },
  },

  emits: ['update:modelValue'],

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/time/client-projects',
    );

    return {http};
  },

  computed: {
    selectedProject: {
      get() {
        return this.modelValue;
      },
      set(val) {
        this.$emit('update:modelValue', val);
      },
    },
  },

  methods: {
    async loadProjects(searchParam) {
      return new Promise((resolve) => {
        // Prevent loading without customer
        if (!this.customerId) {
          resolve([]);
          return;
        }

        if (searchParam.trim() && searchParam.length < 100) {
          this.http
            .getAll({
              name: searchParam.trim(),
              customerId: this.customerId,
            })
            .then(({data}) => {
              const list = data?.data || [];

              resolve(
                list.map((project) => ({
                  id: project.id,
                  label: project.name,
                  _project: project,
                })),
              );
            });
        } else {
          resolve([]);
        }
      });
    },
  },
};
</script>
