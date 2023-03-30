<template>
  <div>
    <Toast />
    <ConfirmDialog></ConfirmDialog>
  </div>
</template>

<script>
import ConfirmDialog from "primevue/confirmdialog";
import { mapState } from "vuex";
export default {
  data: function () {
    return {
      modal: this.$store.state.MostrarModal,
    };
  },
  components: {
    ConfirmDialog,
  },
  methods: {
    confirm1() {
      this.$confirm.require({
        message: "Tens uma fatura para salvar",
        header: "Confirmation",
        icon: "pi pi-exclamation-triangle",
        accept: () => {
          this.$toast.add({
            severity: "success",
            summary: "Confirmada",
            detail: "Fatura salva com sucesso",
            life: 3000,
          });
          // Salvar os dados no Banco de Dado
          axios.post("/").then((Response) => {

          });
        },
        reject: () => {
          this.$toast.add({
            severity: "error",
            summary: "Rejeitada",
            detail: "Fatura rejeitada",
            life: 3000,
          });

          // Eliminar todos produtos desse Array no Banco de Dado
          axios.get("/").then((Response) => {

          });
        },
      });
    },
  },
  watch: {
    novo_valor() {
      if (this.$store.state.prodFaturacao.length > 0) {
        this.confirm1();
        this.$store.state.MostrarModal = false;
      } else {
        return false;
      }
    },
  },
  computed: {
    ...mapState({
      novo_valor: (state) => state.MostrarModal,
    }),
  },
};
</script>

<style lang="scss">
.p-dialog {
  padding: 20px;
  background-color: white;
}
</style>
