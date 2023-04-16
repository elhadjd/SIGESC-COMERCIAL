<template>
	<Toast />
	<div class="mt-0" style="height: 100vh; width: 100vw">
		<Headers @modulos="modulos" :menus="menus" />
		<div class="Containere">
			<home
				@modulos="modulos"
				@showCompany="showCompany"
				@ShowSingleUser="ShowSingleUser"
				v-if="modul.active == 'Configurações'"
			/>
			<users
				@ShowSingleUser="ShowSingleUser"
				@message="message"
				v-if="modul.active == 'users'"
			/>
			<new-user
				@message="message"
				@Users="modul.active = 'users'"
				v-if="modul.active == 'NewUser'"
				:SingleUser="SingleUser"
			/>
			<License
				@message="message"
				v-if="modul.active == 'Liseças'"
				@close="modul.active = modul.story"
			/>
			<Company
				@message="message"
				@close="modul.active = modul.story"
				v-if="modul.active == 'company'"
				:company="company"
			/>
		</div>
	</div>
</template>

<script setup>
import Company from '@/components/config/company.vue'
import Toast from 'primevue/toast'
import Index from '@/layouts/index.vue'
import { onMounted, ref } from '@vue/runtime-core';

import home from '@/Components/Config/Home.vue'
import users from '@/Components/Config/user/Index.vue'
import newUser from '@/Components/Config/user/NewUser.vue'
import { useToast } from 'primevue/usetoast';
// import License from '@/components/Config/license/index.vue'
import { Inertia } from '@inertiajs/inertia';
import Headers from '../Layouts/header.vue'

const toast = useToast();

const SingleUser = ref(Object);

const MostrarDrop = ref(null);

const company = ref([]);

const menus = ref([
    { menu: "Configurações" },
    { menu: "Liseças" }
]);

const message = (message, tipo) => {
	toast.add({
		severity: tipo,
		summary: "Messagem",
		detail: message,
		life: 5000,
	});
};

const modul = ref({
	active: "Configurações",
	story: "",
});

const modulos = (event) => {
	MostrarDrop.value = null;
	modul.value.story = modul.value.active;
	modul.value.active = event;
};

const Sair = () => {
	Inertia.post("/sair");
};

const DropShow = (evento) => {
	if (evento == MostrarDrop.value) {
		MostrarDrop.value = null;
	} else {
		MostrarDrop.value = evento;
	}
};


const ShowSingleUser = (event) => {
	SingleUser.value = event;
	modul.value.story = modul.value.active;
	modul.value.active = "NewUser";
};

const showCompany = (event) => {
	company.value = event;
	modul.value.story = modul.value.active;
	modul.value.active = "company";
};
</script>

<style>
.Containere{
    height: 92% !important;
}
</style>
