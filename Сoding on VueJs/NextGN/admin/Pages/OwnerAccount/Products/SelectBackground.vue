<template>
   <CRow form class="form-group mb-4">
      <CCol col="3" tag="label" class="col-form-label">
         Выбор фона превью
      </CCol>
      <CCol col="9">
         <div class="backgrounds">
            <div v-for="color in colors" :key="color" class="bg-el">
               <div class="bg">
                  <label class="bg-item">
                     <input v-model="selectedColor" type="radio" :value="color" />
                     <span :style="`background-color: ${color}`">
                        <svg class="ico ico-mono-check">
                           <use xlink:href="@app/assets/img/sprite-mono.svg#ico-mono-check"></use>
                        </svg>
                     </span>
                  </label>
               </div>
            </div>
         </div>
      </CCol>
   </CRow>
</template>

<script>
export default {
   name: 'ProductsSelectBackground',
   props: {
      value: {
         type: String,
         default: null,
      },
   },
   data() {
      return {
         colors: ['#DCE5EF', '#FFE2C7', '#DCF4E9', '#FFECEF', '#E4F2FB', '#FFF5DB', '#EFF1F3', '#EFF9E8', '#D1F2EC'],
         selectedColor: null,
      };
   },
   methods: {
      randomColor() {
         const randomIndex = Math.floor(Math.random() * this.colors.length);
         return this.colors[randomIndex];
      },
   },
   watch: {
      selectedColor() {
         this.$emit('input', this.selectedColor);
      },
   },
   beforeMount() {
      this.selectedColor = this.value || this.randomColor();
   },
};
</script>

<style scoped>
.backgrounds {
   display: flex;
   flex-wrap: wrap;
   width: 100%;
   justify-content: flex-start;
}

.bg-el {
   padding: 5px;
}

.bg input {
   display: none;
}

.bg-item {
   cursor: pointer;
   position: relative;
   display: inline-block;
}

.bg-item input:checked + span {
   border-color: #2eb85c;
}

.bg-item input:checked + span .ico {
   opacity: 1;
}

.bg-item span {
   display: block;
   width: 52px;
   min-width: 52px;
   max-width: 52px;
   height: 52px;
   min-height: 52px;
   max-height: 52px;
   border-radius: 50%;
   overflow: hidden;
   border: 3px solid rgba(0, 0, 0, 0);
}

.ico-mono-check {
   width: 1.14em;
   height: 1em;
   fill: #2eb85c;
}

.bg-item .ico,
.bg-item span {
   transition: all 0.25s ease-in-out;
}

.bg-item .ico {
   position: absolute;
   left: 50%;
   top: 50%;
   transform: translate(-50%, -50%);
   font-size: 9px;
   color: #2eb85c;
   z-index: 1;
   opacity: 0;
}

.bg-item:hover span {
   border-color: #2eb85c;
}
</style>
