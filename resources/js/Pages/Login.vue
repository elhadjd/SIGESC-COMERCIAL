<template>
  <Toast />
  <Data v-if="connection.state" @closeFormData="connection.state = false" />
  <div v-else class="container-login100">
    <div class="wrap-login100">
      <div
        class="login100-form-title"
        style="background-image: url(/login/image/bg-01.jpg)"
      >
        <span class="login100-form-title-1"> Identifique-se </span>
      </div>

      <form @submit.prevent="submit" class="login100-form validate-form">
        <div
          class="wrap-input100 validate-input m-b-26"
          data-validate="O email e obligatorio"
        >
          <span class="label-input100">Email</span>
          <input
            class="input100"
            type="text"
            id="email"
            v-model="form.email"
            name="username"
            placeholder="Digite seu email"
          />
          <span class="focus-input100"></span>
        </div>

        <div
          class="wrap-input100 validate-input m-b-18"
          data-validate="A senha e obligatorio"
        >
          <span class="label-input100">Senha</span>
          <input
            class="input100"
            v-model="form.password"
            id="senha"
            type="password"
            name="pass"
            placeholder="Digite a sua senha"
          />
          <span class="focus-input100"></span>
        </div>

        <div class="flex-sb-m w-full p-b-30">
          <div class="contact100-form-checkbox">
            <input
              class="input-checkbox100"
              id="ckb1"
              type="checkbox"
              name="remember-me"
            />
            <label class="label-checkbox100" for="ckb1"> Lembre-me </label>
          </div>

          <div @click="databases" class="SelectDatabase">
            <span>Select Database</span>
          </div>
        </div>

        <div class="container-login100-form-btn">
          <button class="login100-form-btn" name="submit">Entrar</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "@vue/runtime-core";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import Data from "../components/Data/database.vue";
const form = useForm({
    email: null,
    password: null,
    path: window.location.pathname,
});

const toast = useToast();

const connection = ref({
  state: false,
  database: null,
});

const databases = () => {
  connection.value.state = true;
};

onMounted(() => {
  if (document.cookie.length > 0) {
    let c_start = document.cookie.indexOf("database" + "=");
    if (c_start != -1) {
      c_start = c_start + "database".length + 1;
      let c_end = document.cookie.indexOf(";", c_start);
      if (c_end == -1) {
        c_end = document.cookie.length;
      }
      connection.value.database = unescape(
        document.cookie.substring(c_start, c_end)
      );
    } else {
      connection.value.state = true;
    }
  } else {
    console.log("o");
  }
});

const submit = () => {
  if (form.email == null) {
    document.querySelector("#email").style.borderBottom = "1px solid red";
  } else if (form.password == null) {
    document.querySelector("#senha").style.borderBottom = "1px solid red";
  } else {
    form.post("/auth/logar", {
        onSuccess: (Response) => {
            toast.add({
                severity: "error",
                summary: "menssagem de erro",
                detail: Response.props.erro,
                life: 5000,
            });
        },
    });
  }
};
</script>

<style scoped>
@import url("../../assets/login/css/login.css");
.SelectDatabase {
  font: 12pt arial;
  font-weight: 600;
  color: #714b67;
  padding: 2px;
}
.SelectDatabase:hover {
  color: #017e84;
  cursor: pointer;
}
</style>
