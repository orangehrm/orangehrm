<!--
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
 -->

<template>
  <oxd-input-field
    type="autocomplete"
    :clear="false"
    :label="$t('time.project')"
    :create-options="loadProjects"
  >
    <template #option="{data}">
      <span>
        {{ data._customer ? `${data._customer} - ` : '' }}
        {{ data.label }}
      </span>
    </template>
  </oxd-input-field>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
export default {
  name: 'ProjectAutocomplete',
  props: {
    onlyAllowed: {
      type: Boolean,
      required: false,
      default: true,
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/time/projects',
    );
    return {
      http,
    };
  },
  methods: {
    async loadProjects(serachParam) {
      return new Promise(resolve => {
        if (serachParam.trim()) {
          this.http
            .getAll({
              name: serachParam.trim(),
              onlyAllowed: this.onlyAllowed,
              model: 'detailed',
            })
            .then(({data}) => {
              resolve(
                data.data.map(project => {
                  return {
                    id: project.id,
                    label: project.name,
                    _customer: project.customer?.name,
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

<style scoped>
::v-deep(.oxd-autocomplete-wrapper) {
  min-width: 150px;
}
</style>
