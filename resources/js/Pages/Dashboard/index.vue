<template>
  <div class="principal">
    <header class="header">
      <div class="LOGO">
        <img :src="'/company/image/' + props.data.image" alt="" />
      </div>
      <div @click="MostrarDrop = !MostrarDrop" class="user">
        <img :src="element.img" alt="user" />
        <span>{{ props.user.surname }}</span>
        <div v-show="MostrarDrop" class="logOut">
          <div @click="Sair" class="Sair">
            Sair <i class="fa fa-sign-out"></i>
          </div>
        </div>
      </div>
    </header>
    <div class="Container">
      <div class="Content">
        <Link
          v-for="app in props.data.license.app_license"
          :key="app.id"
          :href="route(app.apps.name)"
        >
          <div>
            <img :src="'/app/image/' + app.apps.image" alt="" />
          </div>
          <span class="name_app">{{ app.apps.name }}</span>
        </Link>
      </div>
    </div>
  </div>
</template>
<script setup>
import { onMounted, reactive, ref } from "@vue/runtime-core";
import { useStore } from "vuex";
import Index from '../../Layouts/index.vue'
import { Link } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import {getImages} from '@/composable/public/getImages'

const store = useStore();
const MostrarDrop = ref(false);
const props = defineProps({
  data: Object,
  user: Object,
});
const element = reactive({
    img: '/login/image/' + props.user.image
})
const {getImage} = getImages(element);
const Sair = () => {
  Inertia.post("/auth/logout");
};

onMounted(() => {
  store.commit("publico/saveCompany", props);
});
</script>




<style lang="scss" scoped>
.principal {
  width: 100vw;
  height: 100vh;
  background: radial-gradient(
      at 100% 0%,
      var(--app_color) 0px,
      transparent 50%
    ),
    radial-gradient(at 7% 13%, var(--app_hover) 0px, transparent 50%),
    radial-gradient(at 96% 94%, var(--app_hover) 0px, transparent 50%),
    radial-gradient(at 3% 96%, var(--app_color) 0px, transparent 50%);
  display: grid;
  grid-template-columns: 1fr;
  grid-template-rows: 60px 1fr;
  margin: 0;
  padding: 0;
  user-select: none;
  .header {
    width: 100%;
    display: flex;
    justify-content: space-between;
    padding: 3px 10px 3px 10px;
    .LOGO {
      padding: 0px 10px 0px 10px;
      display: flex;
      justify-content: center;
      align-items: center;
      img {
        width: 55px;
        height: 50px;
        border-radius: 5px;
      }
    }
    .user {
      display: flex;
      align-items: center;
      gap: 1rem;
      position: relative;
      cursor: pointer;
      &:hover {
        opacity: 0.9;
      }
      img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
      }
      span {
        color: white;
        font-size: 15px;
        font-weight: bold;
      }
      .logOut {
        position: absolute;
        top: 48px;
        color: white;
        padding: 0px 10px 0px 10px;
        display: flex;
        justify-content: flex-end;
        width: 100%;
      }
    }
  }
  .Container {
    background-color: transparent;
    display: flex;
    justify-content: center;
    .Content {
      width: 85%;
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      grid-template-rows: 1fr;
      margin-top: 20px;
      gap: 0.2rem;
      a {
        width: 100%;
        height: 120px;
        list-style: none;
        text-decoration: none;
        display: grid;
        grid-auto-columns: 1fr;
        grid-template-rows: 70% 30%;
        align-items: center;
        justify-content: center;
        color: var(--appBg);
        border-radius: 10px;
        &:hover {
          background-color: var(--buttons-hover);
          color: var(--text-form);
          img {
            transition: 0.2s;
            transition-property: all;
            width: 82px;
            height: 72px;
          }
        }
        > div {
          display: flex;
          justify-content: center;
          img {
            width: 80px;
            height: 70px;
            border-radius: 5px;
            box-shadow: rgb(100, 100, 111, 0.2) 10px 10px 20px 0px;
          }
        }
        span {
          display: flex;
          font-size: 15px;
          font-weight: bold;
          justify-content: center;
          text-shadow: 0 1px 1px rgb(0 0 0 / 50%);
        }
      }
    }
  }
  @media screen and (max-width: 900px) {
    .Content {
      display: grid;
      grid-template-columns: repeat(5, 1fr) !important;
      grid-template-rows: 130px 130px !important;
    }
  }
  @media screen and (max-width: 650px) {
    .Content {
      display: grid;
      grid-template-columns: repeat(4, 1fr) !important;
    }
  }
  @media screen and (max-width: 500px) {
    .Content {
      display: grid;
      grid-template-columns: repeat(3, 1fr) !important;
    }
  }
  @media screen and (max-width: 360px) {
    .Content {
      display: grid;
      grid-template-rows: 130px 130px 130px !important;
      grid-template-columns: repeat(2, 1fr) !important;
    }
  }
}
h1 {
  position: relative;
  color: #714b67;
  font-size: 4rem;
  letter-spacing: 5px;
  border-bottom: 15px solid #714b67;
  line-height: 1.4;
  font-family: consolas;
  text-transform: uppercase;
  &::before {
    content: attr(data-text);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    color: #00686e;
    border-bottom: 16px solid #00686e;
    animation: lod 4s linear infinite;
    overflow: hidden;
  }
  @keyframes lod {
    0% {
      width: 0%;
    }
    100% {
      width: 100%;
    }
  }
}
</style>
