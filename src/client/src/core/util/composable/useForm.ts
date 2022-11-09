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

import {ref, computed} from 'vue';
import Form from '@ohrm/oxd/core/components/Form/Form.vue';

type useFormArgs = {
  refName?: string;
};

export default function useForm({refName = 'formRef'}: useFormArgs = {}) {
  // https://v3.vuejs.org/guide/typescript-support.html#typing-template-refs
  const form = ref<InstanceType<typeof Form>>();

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
