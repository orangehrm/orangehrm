<template>
  <div class="orangehrm-translation-container">
    <oxd-divider />
    <oxd-grid :cols="2" class="orangehrm-translation-grid">
      <oxd-grid-item class="orangehrm-translation-grid-header">
        <oxd-text type="card-title">{{ $t('admin.source_text') }}</oxd-text>
      </oxd-grid-item>
      <oxd-grid-item class="orangehrm-translation-grid-header">
        <oxd-text type="card-title">Error</oxd-text>
      </oxd-grid-item>
      <oxd-grid-item class="orangehrm-translation-grid-header">
        <oxd-text type="card-title">{{ $t('admin.translated_text') }}</oxd-text>
      </oxd-grid-item>
      <template
        v-for="(langstring, index) in xliffSourceAndTargetValidationErrors"
        :key="index"
      >
        <oxd-grid-item class="orangehrm-translation-grid-text">
          <oxd-text
            class="orangehrm-translation-grid-langstring-header"
            type="card-title"
          >
            {{ $t('admin.source_text') }}
          </oxd-text>
          <oxd-text :title="langstring.source">
            {{ langstring.source }}
          </oxd-text>
        </oxd-grid-item>
        <oxd-grid-item class="orangehrm-translation-grid-text">
          <oxd-text
            :title="langstring.error"
            class="orangehrm-translation-grid-header"
          >
            {{ langstring.error }}
          </oxd-text>
        </oxd-grid-item>
        <oxd-grid-item class="orangehrm-translation-grid-text">
          <oxd-text
            class="orangehrm-translation-grid-langstring-header"
            type="card-title"
          >
            {{ $t('admin.translated_text') }}
          </oxd-text>
          <oxd-input-field
            type="input"
            :placeholder="langstring.target"
            :model-value="langstring.target"
            :rules="rules.langString"
            @update:model-value="onUpdateTranslation($event, index)"
          />
          <oxd-divider class="orangehrm-translation-grid-langstring-header" />
        </oxd-grid-item>
      </template>
    </oxd-grid>
    <oxd-divider />
  </div>
</template>

<script>
import {validLangString} from '@/core/util/validation/rules';

export default {
  props: {
    langstrings: {
      type: Array,
      required: true,
    },
    xliffSourceAndTargetValidationErrors: {
      type: Array,
      required: true,
    },
  },

  emits: ['update:langstrings'],

  setup(props, context) {
    const onUpdateTranslation = (value, index) => {
      const updatedLangstrings = [...props.langstrings];
      updatedLangstrings[index].target = value;
      updatedLangstrings[index].modified = true;
      context.emit('update:langstrings', updatedLangstrings);
    };

    return {
      onUpdateTranslation,
      rules: {
        langString: [validLangString],
      },
    };
  },
};
</script>

<style src="./edit-translation-table.scss" lang="scss" scoped></style>
