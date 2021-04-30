<template>
  <oxd-input-field
    type="dropdown"
    label="Employee Name"
    :create-options="loadEmployees"
    :lazyLoad="true"
  />
</template>

<script>
import {APIService} from '@orangehrm/core/util/services/api.service';
export default {
  name: 'employee-dropdown',
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/pim/employees',
    );
    return {
      http,
    };
  },
  methods: {
    async loadEmployees(serachParam) {
      return new Promise(resolve => {
        if (serachParam.trim()) {
          this.http
            .getAll({
              nameOrId: serachParam,
            })
            .then(({data}) => {
              resolve(
                data.data.map(employee => {
                  return {
                    id: employee.empNumber,
                    label: `${employee.firstName} ${employee.lastName}`,
                  };
                }),
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
