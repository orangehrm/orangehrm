<!--
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
 -->

<template>
  <oxd-input-group
    class="oxd-input-field-bottom-space"
    :message="message"
    :classes="classes"
  >
    <oxd-label :label="label" :class="classes.label" />
    <oxd-color-input
      v-bind="$attrs"
      :disabled="disabled"
      :has-error="hasError"
      :model-value="modelValue"
      dropdown-position="left"
      @update:model-value="$emit('update:modelValue', $event)"
    />
  </oxd-input-group>
</template>

<script>
import {toRef, nextTick, computed} from 'vue';
import {OxdColorInput, OxdLabel, useField} from '@ohrm/oxd';

export default {
  name: 'InlineColorInput',
  components: {
    'oxd-label': OxdLabel,
    'oxd-color-input': OxdColorInput,
  },
  inheritAttrs: false,
  props: {
    label: {
      type: String,
      default: null,
      required: false,
    },
    rules: {
      type: Array,
      default: () => [],
      required: false,
    },
    required: {
      type: Boolean,
      default: false,
      required: false,
    },
    modelValue: {
      type: String,
      default: null,
      required: false,
    },
    disabled: {
      type: Boolean,
      default: false,
      required: false,
    },
  },
  emits: ['update:modelValue'],
  setup(props, context) {
    const disabled = toRef(props, 'disabled');
    const modelValue = toRef(props, 'modelValue');
    const initialValue = modelValue.value;

    const onReset = async () => {
      context.emit('update:modelValue', initialValue);
      await nextTick();
    };

    const {hasError, message} = useField({
      fieldLabel: props.label ?? '',
      rules: props.rules,
      modelValue,
      onReset,
      disabled,
    });

    const classes = computed(() => ({
      label: {
        'oxd-input-field-required': props.required,
      },
      message: {
        'oxd-input-field-error-message': hasError,
      },
      wrapper: {
        'orangehrm-color-input-wrapper': true,
      },
    }));

    return {
      classes,
      message,
      hasError,
    };
  },
};
</script>

<style lang="scss" scoped>
::v-deep(.oxd-input-group__label-wrapper) {
  display: none;
}
::v-deep(.orangehrm-color-input-wrapper) {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
::v-deep(.oxd-color-input) {
  padding: 2px;
  flex-shrink: 0;
}
.oxd-input-field-bottom-space {
  margin-bottom: 1rem;
}
</style>
