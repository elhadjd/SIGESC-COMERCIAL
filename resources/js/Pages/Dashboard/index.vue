<template>
  <div class="h-screen principal user-select-none w-screen flex justify-center relative">
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
    <!-- <headerTopVue :user="user"/>

    <div style="background-color: var(--newBg)" class="fixed w-[200%] h-[200%] origin-top-left skew-y-6 lg:skew-y-12 rotate-45 transform mt-0 top-28 md:left-0 left-0 md:right-0 md:rotate-45 lg:rotate-12"></div>
    <span class="absolute w-96 left-10 top-10 hidden lg:flex">
        <img src="/app/image/pc.png" alt="">
    </span>
    <div class="absolute flex flex-col space-y-6 left-0 bottom-14 md:bottom-6 px-8 py-2 w-full md:w-1/2 text-white">
        <div class="hidden lg:flex lg:flex-col">
            <h2 class="">Links</h2>
            <div class="flex flex-row space-x-4">
                <strong class="w-22">Youtube:</strong>
                <a href="https://www.youtube.com/@sigescTech/playlists" target="_blank" class="text-white ">https://www.youtube.com/@sigescTech/playlists</a>
            </div>
        </div>
        <div>
            <h2>Contactos</h2>
            <div class="flex flex-row space-x-4">
                <strong class="w-22">{{$t('words.formulary')}}:</strong>
                <a href="https://sisgesc.net/contact" target="_blank" class="text-white ">Formulário de contacto</a>
            </div>
            <div class="flex flex-row space-x-4">
                <strong class="w-22">E-mail:</strong>
                <span>elhadjd73@gmail.com</span>
            </div>
            <div class="flex flex-row space-x-4">
                <strong class="w-22">{{$t('words.phone')}}:</strong>
                <span>9735249725</span>
            </div>
        </div>
    </div>
    <div class="absolute flex flex-col items-center justify-center space-y-4 lg:h-2/3 lg:w-3/6 w-[80%] md:right-10 md:w-3/4 mt-16 md:mt-22 h-2/3 md:h-2/3">
        <h1 class="text-bold">Bem vindo ao sistema</h1>
        <div class="drop-shadow-lg p-3 w-full h-full bg-white rounded ">
            <div class="grid grid-cols-2 gap-4 grid-rows-3 md:grid-cols-3 md:grid-rows-2 h-full">
                <Link
                class="no-underline bg-white hover:drop-shadow-lg rounded-lg drop-shadow-md p-2 flex flex-col justify-center items-center"
                    v-for="app,i in apps"
                    :key="i"
                    :href="route(app.apps.href)">
                    <span class="w-16 h-16 rounded" >
                        <img :src="'/app/image/' + app.apps.image" alt="" />
                    </span>
                    <span class="truncate text-gray-500 text-base font-bold md:text-lg w-full text-center">{{ app.apps.app_translate[0]?.translate }}</span>
                </Link>
            </div>
        </div>
    </div>
    <licenseVue :licenseProps="data.license"/> -->
    <div class="Container">
      <div class="body">
        <Link
          class="box"
          v-for="app,i in props.data.license.app_license"
          :key="i"
          :href="route(app.apps.href)"

        >
          <span :style="`--i:${i};`">
            <img :src="'/app/image/' + app.apps.image" alt="" />
          </span>
          <span class="name_app flex-0 truncate text-center">{{ app.apps.app_translate[0]?.translate }}</span>
        </Link>
      </div>
    </div>
    <newsMessagesVue v-if="news.type != ''" :message="news">
        <Link :href="`/config/Home`"> clica aqui para atualizar</Link>
    </newsMessagesVue>
    <!-- <canvas ref="canvas" id="canvas"></canvas> -->
  </div>
</template>
<script setup>
import headerTopVue from '@/Layouts/headerTop/index.vue'
import { useStore } from "vuex";
import Index from '../../Layouts/index.vue'
import { Link } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import {getImages} from '@/composable/public/getImages'
import { ref, onMounted ,reactive, computed} from 'vue';
import newsMessagesVue from "@/Layouts/news/newsMessages.vue";
import licenseVue from '@/ui/licenseVue.vue'
const store = useStore();
const MostrarDrop = ref(false);

const props = defineProps({
  data: Object,
  user: Object,
});

const hasRole = computed(()=>{
    return props.user.roles?.name
});

const apps = ref(props.data.license.app_license)

const canvas = ref(null);
const ctx = ref(null);
const mouse = { x: 0, y: 0 };
const spots = ref([]);
let hue = 0;
const news = ref({
    message: '',
    type: ''
})
const element = reactive({
    img: '/login/image/' + props.user.image
})

const {getImage} = getImages(element);
const Sair = () => {
  Inertia.post("/auth/logout");
};

onMounted(async() => {
    apps.value.sort().reverse()
    if (!props.data.currency_company) {
        news.value = {
            message: `Prezado ${props.data.name} ,Esperamos que esta mensagem o encontre bem. Informamos que realizamos uma atualização em nosso sistema para aprimorar a experiência do usuário.
                por favor, verifique e atualize o tipo de moeda no formulário da sua empresa.
                Agradecemos pela sua colaboração e compreensão. Se tiver alguma dúvida ou precisar de assistência, não hesite em entrar em contato conosco.
                Atenciosamente,
                Leonardo
                Administrador SIGESC`,
            type: 'currency',
        }
    }
    if(props.user.user_language.code) {
        const locale = localStorage.getItem('locale');
        if(locale){
            if(locale != props.user.user_language.code){
                localStorage.setItem('locale',props.user.user_language.code)
                location.reload()
            }
        }
        else{
            localStorage.setItem('locale',props.user.user_language.code)
            location.reload()
        }
    }
    // canvas.value = canvas.value;
    // ctx.value = canvas.value.getContext('2d');
    // canvas.value.width = window.innerWidth;
    // canvas.value.height = window.innerHeight;

    // canvas.value.addEventListener('mousemove', handleMouseMove);
    // window.addEventListener('resize', handleWindowResize);
    store.commit("publico/saveCompany", props.data);
    store.commit("publico/saveUser", props.user);
    // animate();te
});


const handleMouseMove = (event) => {
  mouse.x = event.clientX;
  mouse.y = event.clientY;

  for (let i = 0; i < 2; i++) {
    spots.value.push(createParticle(mouse.x, mouse.y));
  }
};

const handleWindowResize = () => {
  canvas.value.width = window.innerWidth;
  canvas.value.height = window.innerHeight;
};

const createParticle = (x, y) => {
  return {
    x,
    y,
    size: Math.random() * 8 + 0.1,
    speedX: Math.random() * 3 - 1.5,
    speedY: Math.random() * 3 - 1.5,
    color: `hsl(${hue}, 100%, 50%)`,
    ctx: ctx.value,
    update() {
      this.x += this.speedX;
      this.y += this.speedY;
      if (this.size > 0.01) this.size -= 0.01;
    },
    draw() {
      this.ctx.fillStyle = this.color;
      this.ctx.beginPath();
      this.ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
      this.ctx.fill();
    },
  };
};

const animate = () => {
  ctx.value.clearRect(0, 0, canvas.value.width, canvas.value.height);

  for (let i = 0; i < spots.value.length; i++) {
    spots.value[i].update();
    spots.value[i].draw();

    for (let j = i + 1; j < spots.value.length; j++) {
      const dx = spots.value[i].x - spots.value[j].x;
      const dy = spots.value[i].y - spots.value[j].y;
      const distance = Math.sqrt(dx * dx + dy * dy);

      if (distance < 40) {
        ctx.value.beginPath();
        ctx.value.strokeStyle = spots.value[i].color;
        ctx.value.lineWidth = (spots.value[i].size + spots.value[j].size) / 6;
        ctx.value.moveTo(spots.value[i].x, spots.value[i].y);
        ctx.value.lineTo(spots.value[j].x, spots.value[j].y);
        ctx.value.stroke();
      }
    }

    if (spots.value[i].size <= 0.01) {
      spots.value.splice(i, 1);
      i--;
    }
  }

  hue++;
  requestAnimationFrame(animate);
};


</script>




<style lang="scss" scoped>
@import "../../../assets/deshboard/css/home";
</style>
