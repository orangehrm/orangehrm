/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

import {ref, computed} from 'vue';
import {OxdForm} from '@ohrm/oxd';

type useFormArgs = {
  refName?: string;
};

export default function useForm({refName = 'formRef'}: useFormArgs = {}) {
  // https://v3.vuejs.org/guide/typescript-support.html#typing-template-refs
  const form = ref<InstanceType<typeof OxdForm>>();

  const submit = () => form.value?.onSubmit(new Event('submit'));
  const reset = () => form.value?.onReset();
  const validate = () => form.value?.validate();

  const invalid = computed(() => form.value?.isFromInvalid);
  const errorbag = computed(() => form.value?.errorbag);

  return {
    reset,
    submit,
    validate,
    [refName]: form,
    errorbag,
    invalid,
  };
}
