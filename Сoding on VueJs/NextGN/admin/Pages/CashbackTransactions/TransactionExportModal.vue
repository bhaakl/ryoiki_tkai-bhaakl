<template>
  <CModal color="success" :closeOnBackdrop="false" centered :show.sync="isShow">
    <template #header>
      <h6 class="modal-title">Выгрузить начисления Cashback</h6>
    </template>
    <CRow>
      <CCol col="3" class="d-flex align-items-center">
        <label class="mb-0">E-mail</label>
      </CCol>
      <CCol col="9">
        <CInput v-model="email"/>
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
  name: 'TransactionExportModal',
  data() {
    return {
      isShow: false,
      email: ''
    }
  },
  computed: {
    isBtnDisabled() {
      return this.email.length === 0 || !this.email.includes("@");
    },
  },
  methods: {
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
      try {
        await this.$inertia.post(
          '/admin/transactions/cashback/export',
          formData,
        );
        this.hide();
      } catch (e) {
        this.$handleError(e);
      }
    }
  },
  mounted() {
    this.email = this.$page.props.user.email;
  }
}
</script>
