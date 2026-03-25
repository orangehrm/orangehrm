<template>
  <oxd-input-field
    v-model="selectedEmployee"
    type="autocomplete"
    :label="$t('general.employee_name')"
    :clear="false"
    :create-options="loadEmployees"
  />
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';

export default {
  name: 'ClientEmployeeAutocomplete',

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

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/time/client-employees',
    );

    return {http};
  },

  computed: {
    selectedEmployee: {
      get() {
        return this.modelValue;
      },
      set(val) {
        this.$emit('update:modelValue', val);
      },
    },
  },

  methods: {
    async loadEmployees(searchParam) {
      return new Promise((resolve) => {
        // Prevent search without customer
        if (!this.customerId) {
          resolve([]);
          return;
        }

        if (searchParam.trim() && searchParam.length < 100) {
          this.http
            .getAll({
              nameOrId: searchParam.trim(),
              customerId: this.customerId,
            })
            .then(({data}) => {
              const list = data?.data || [];

              resolve(
                list.map((employee) => ({
                  id: employee.empNumber,
                  label: `${employee.firstName} ${employee.middleName} ${employee.lastName}`,
                  _employee: employee,
                  isPastEmployee: !!employee.terminationId,
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
