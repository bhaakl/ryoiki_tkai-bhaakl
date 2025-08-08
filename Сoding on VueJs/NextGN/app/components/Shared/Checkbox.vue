<template>
   <div @keydown="onKeyDown" class="input-checkbox" tabindex="0">
      <label>
         <input type="checkbox" :checked="shouldBeChecked" :value="value" @change="updateInput" ref="input" />
         <div class="checkbox"></div>
         <span>{{ label }}</span>
      </label>
   </div>
</template>

<script>
export default {
   name: 'Checkbox',
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
         if (this.modelValue instanceof Array) {
            return this.modelValue.includes(this.value);
         }

         return this.modelValue;
      },
   },
   methods: {
      updateInput(event) {
         let isChecked = event.target.checked;

         if (this.modelValue instanceof Array) {
            let newValue = [...this.modelValue];

            if (isChecked) {
               newValue.push(this.value);
            } else {
               newValue.splice(newValue.indexOf(this.value), 1);
            }

            this.$emit('change', newValue);
         } else {
            this.$emit('change', isChecked);
         }
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
