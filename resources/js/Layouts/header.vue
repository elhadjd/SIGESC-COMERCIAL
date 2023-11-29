<template>
    <div class="nav_da_home_compra">
        <div class="botao">
            <Index />
        </div>
        <nav>
            <div class="nave">
                <div :class="stateMenu ? 'show-menu' : ''" class="menus">
                    <span class="open-menu">
                        <font-awesome-icon v-if="stateMenu" @click="stateMenu = false" icon="fa-solid fa-chevron-left" />
                        <font-awesome-icon v-else @click="stateMenu = true" icon="fa-solid fa-chevron-right" />
                    </span>
                    <div v-for="menu in props.menus" :key="menu.menu">
                        <li @mouseenter="showDown(menu.menu)" @click="modulos(menu.menu)">
                            {{ menu.menu }}
                        </li>
                        <div v-show="Mostrar == menu.menu">
                            <div v-if="user?.nivel == 'admin'" class="ListRelatorios">
                                <div
                                    v-for="subMenu, index in menu.subMenu"
                                    :key="index"
                                    @click="showModule(subMenu.name)">
                                    {{ subMenu.name }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="user_conectado">
                    <div class="User dropdown-toggle" @click="stateLogout = !stateLogout">
                        <img :src="element.img" alt="user" />
                        <div>{{ user?.surname }}</div>
                    </div>
                    <div v-if="stateLogout" class="user">
                        <div @click="Sair" class="Sair" :class="!stateMenu ? 'text-white' : ''">
                        Sair <i class="fa fa-sign-out"></i>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</template>

<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import Index from "./index.vue";
import { onMounted, reactive, ref } from "@vue/runtime-core";
import { useStore } from "vuex";
import { Inertia } from "@inertiajs/inertia";
import {getImages} from '@/composable/public/getImages'
import { Link } from "@inertiajs/vue3";
const props = defineProps({
  menus: Array,
});

const stateMenu = ref(false)

const emits = defineEmits(["modulos"]);
const Mostrar = ref(null);
const store = useStore();
const user = ref(store.state.publico?.user);
const stateLogout = ref(false);
const element = reactive({
    img: '/login/image/' + user?.value?.image
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
    stateMenu.value = true
    emits("modulos", module);
    Mostrar.value = null;
};

const showDown = (event) => {
  if (Mostrar.value != null) {
    Mostrar.value = event;
  }
};

// window.addEventListener("DOMContentLoaded", function() {
//   document.getElementById("openMenu").addEventListener("click", function() {
//     var menu = document.querySelector(".menus");
//     menu.classList.toggle("show-menu");
//   });
// });

</script>
