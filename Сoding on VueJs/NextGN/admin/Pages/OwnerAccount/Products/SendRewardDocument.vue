<template>
  <CModal color="success" :closeOnBackdrop="false" centered :show.sync="isShow">
     <template #header>
        <h6 class="modal-title">Обработать вознаграждение</h6>
     </template>
     <CRow>
            <CCol col="3" class="d-flex align-items-center">
               <label class="mb-0">E-mail</label>
            </CCol>
            <CCol col="9">
               <CInput v-model="email" />
            </CCol>
         </CRow>
     <CRow>
        <CCol col="3" class="d-flex align-items-center">
           <label class="mb-0">Excel Файл</label>
        </CCol>
        <CCol col="9">
          <CInput v-if="isShow" class="mb-0 file__input" @change="handleFileChange" type="file" id="file" />
        </CCol>
     </CRow>
     <template #footer>
        <CButton @click="send" :disabled="isBtnDisabled" color="success">Отправить</CButton>
        <CButton @click="hide" color="light">Отмена</CButton>
     </template>
  </CModal>
</template>
<script>
export default {
name: 'SendRewardDocument',
props: {
  productId: Number
},
data() {
  return {
     isShow: false,
     file: null,
     email: ''
  }
},
computed: {
  isBtnDisabled() {
     return !this.file || this.email.length === 0 || !this.email.includes("@");
  },
},
methods: {
  handleFileChange(fileName, event) {
      const file = event.target.files[0];
      if (file) {
        this.file = file;
      }
  },
  show() {
     this.isShow = true;
  },
  hide() {
     this.isShow = false;
  },
  async send() {
     if (this.isBtnDisabled) {
        return;
     }

     const formData = new FormData();
     formData.append('email', this.email);
     formData.append('file', this.file);
     try {
        await this.$inertia.post(
           `/admin/products/${this.productId}/import`,
           formData,
           { /*preserveScroll: true, forceFormData:true*/ }
        );
        this.hide();
     } catch (e) {
        this.$handleError(e);
     }
  }
},
watch: {
  isShow(value, prevValue) {
    if(value === false) {
      this.file = null
    }
  }
},
mounted() {
  this.email = this.$page.props.user.email;
}
}
</script>
