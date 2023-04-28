<template>
<Toast />
<div class='principal'>
    <header>
        <h1><span>S</span>ISGESC</h1>
    </header>
    <div class='container'>
        <div class="info">
            <div class='content'>
                <div>
                    <div><AiOutlineCheckCircle/></div>
                    <div>
                        <span>Comece em pouco tempo</span>
                        <span>Integre APIs voltadas a desenvolvedores ou opte por soluções com pouco código ou predefinidas.</span>
                    </div>
                </div>
                <div>
                    <div><AiOutlineCheckCircle/></div>
                    <div>
                        <span>Aceite qualquer modelo de negócio</span>
                        <span>E-commerce, assinaturas, plataformas de SaaS, marketplaces e muito mais. Tudo isso em uma plataforma unificada.</span>
                    </div>
                </div>
                <div>
                    <div><AiOutlineCheckCircle/></div>
                    <div>
                        <span>Junte-se a milhões de empresas</span>
                        <span>A Sisgesc tem a confiança de empresas e startups de todos os portes.</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form">
            <form @submit.prevent="submit">

                <header>
                    <span>Identifique-se</span>
                </header>

                <div class='box'>
                    <label htmlFor="email">Email:</label>
                    <input type="email" v-model="form.email" id='email' />
                </div>

                <div class='box'>
                    <label htmlFor="password">Senha:</label>
                    <input type="password" v-model="form.password" id='password' />
                </div>

                <div class='checkbox'>
                    <input type="checkbox" name="checkbox" id="checkbox" />
                    <label htmlFor="checkbox">Permanece connectado por uma semana</label>
                </div>

                <div class='buttons'>
                    <button type='submit'>Entrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "@vue/runtime-core";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
const form = useForm({
    email: null,
    password: null,
    path: window.location.pathname,
});

const toast = useToast();

const connection = ref({
  state: false,
});

const submit = () => {
  if (form.email == null || form.email == '') {
    document.querySelector("#email").style.borderBottom = "1px solid red";
  } else if (form.password == null || form.password == '') {
    document.querySelector("#password").style.borderBottom = "1px solid red";
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

<style scoped lang="scss">
@import '../../assets/login/css/login';
.form{
    height: 150px !important;
    .box{
        margin-bottom: 10px !important;

    }
}
</style>
