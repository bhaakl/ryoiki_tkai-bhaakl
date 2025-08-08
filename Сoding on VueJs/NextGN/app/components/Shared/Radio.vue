<template>
   <div @keydown="onKeyDown" class="input-radio" tabindex="0">
      <label>
         <input type="radio" :checked="shouldBeChecked" :value="value" @change="updateInput" ref="input" />
         <div class="checkbox"></div>
         <span>{{ label }}</span>
      </label>
   </div>
</template>

<script>
export default {
   name: 'Radio',
   model: {
      prop: 'modelValue',
      event: 'change',
   },
   props: {
      label: String,
      value: {
         default: null,
      },
      modelValue: {
         default: false,
      },
   },
   computed: {
      shouldBeChecked() {
         return this.modelValue == this.value;
      },
   },
   methods: {
      updateInput() {
         this.$emit('change', this.value);
      },
      onKeyDown(e) {
         if (e.keyCode === 13) {
            e.preventDefault();
            this.$refs.input.click();
         }
      },
   },
};
</script>
