<template>
    <div class="nav_da_home_compra">
    <Index class="botao" />
    <nav>
      <div class="d-flex">
        <div v-for="menu in props.menus" :key="menu.menu">
          <li @mouseenter="showDown(menu.menu)" @click="modulos(menu.menu)">
            {{ menu.menu }}
          </li>
          <div v-show="Mostrar == menu.menu">
            <div v-if="user.nivel == 'admin'" class="ListRelatorios">
              <div
                v-for="subMenu, index in menu.subMenu"
                :key="index"
                @click="showModule(subMenu.name)"
              >
                {{ subMenu.name }}
              </div>
            </div>
          </div>
        </div>
        <div class="user_conectado">
          <div class="User dropdown-toggle" @click="stateLogout = !stateLogout">
            <img :src="element.img" alt="user" />
            <div>{{ user.surname }}</div>
          </div>
          <div v-if="stateLogout" class="Usuario">
            <div @click="Sair" class="Sair">
              Sair <i class="fa fa-sign-out"></i>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </div>
</template>

<script setup>
import Index from "./index.vue";
import { onMounted, reactive, ref } from "@vue/runtime-core";
import { useStore } from "vuex";
import { Inertia } from "@inertiajs/inertia";
import {getImages} from '@/composable/public/getImages'
const props = defineProps({
  menus: Array,
});

const emits = defineEmits(["modulos"]);
const Mostrar = ref(null);
const store = useStore();
const user = ref(store.state.publico.user);
const stateLogout = ref(false);
const element = reactive({
    img: '/login/image/' + user.value.image
})
const {getImage} = getImages(element);
const modulos = (event) => {
  const filter = props.menus.filter((menu) => {
    return event === menu.menu;
  });
  if (!filter[0].subMenu) return showModule(filter[0].menu);
  if (event == Mostrar.value) {
    Mostrar.value = null;
  } else {
    Mostrar.value = event;
  }
};

const Sair = () => {
  Inertia.post("/auth/logout");
};

const showModule = (module) => {
  emits("modulos", module);
  Mostrar.value = null;
};

const showDown = (event) => {
  if (Mostrar.value != null) {
    Mostrar.value = event;
  }
};
onMounted(() => {});
</script>

<style>

</style>
