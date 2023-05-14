<template>
  <div class="MenuFaturacao bg-white">
    <div v-if="$store.state.form_faturacao">
     <Modal-confirm />
    </div>
    <Menu />
  </div>
</template>

<script>
import Menu from "./Home.vue";
import ModalConfirm from "./ModalConfirm.vue";
export default {
  setup() {},
  components: { ModalConfirm, Menu },
};
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
}
.MenuFaturacao {
  width: 100vw;
  height: 100vh;
}
</style>
