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
import { useStore } from "vuex";
import Index from '../../Layouts/index.vue'
import { Link } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import {getImages} from '@/composable/public/getImages'
import { ref, onMounted ,reactive} from 'vue';
import newsMessagesVue from "@/Layouts/news/newsMessages.vue";
const store = useStore();
const MostrarDrop = ref(false);

const props = defineProps({
  data: Object,
  user: Object,
});


console.log(props);

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
onMounted(() => {
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
    // canvas.value = canvas.value;
    // ctx.value = canvas.value.getContext('2d');
    // canvas.value.width = window.innerWidth;
    // canvas.value.height = window.innerHeight;

    // canvas.value.addEventListener('mousemove', handleMouseMove);
    // window.addEventListener('resize', handleWindowResize);
    store.commit("publico/saveCompany", props);
    // animate();
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
