<template>
	<Toast />
	<div class="mt-0" style="height: 100vh; width: 100vw">
		<Headers @modulos="modulos" :menus="menus" />
		<div class="Containere">
			<home
				@modulos="modulos"
				@showCompany="showCompany"
				@ShowSingleUser="ShowSingleUser"
				v-if="modul.active == $t('apps.configName')"
			/>
			<users
				@ShowSingleUser="ShowSingleUser"
				@message="message"
				v-if="modul.active == 'users'"
			/>
			<new-user
				@message="message"
				@ListUsers="modulos"
				v-if="modul.active == 'NewUser'"
				:SingleUser="SingleUser"
			/>
			<companies
				@message="message"
				@close="modul.active = modul.story"
				v-if="modul.active == 'company'"
				:company="company"
			/>
            <login-register v-if="modul.active == 'Logins'" @message="message"/>
		</div>
	</div>
</template>

<script setup>
import loginRegister from '@/Components/Config/loginRegister.vue'
import companies from '@/Components/Config/company.vue'
import Toast from 'primevue/toast'
import { onMounted, ref } from '@vue/runtime-core';
import home from '@/Components/Config/Home.vue'
import users from '@/Components/Config/user/Index.vue'
import newUser from '@/Components/Config/user/NewUser.vue'
import { useToast } from 'primevue/usetoast';
import { Inertia } from '@inertiajs/inertia';
import Headers from '../../Layouts/header.vue'
import { useI18n } from 'vue-i18n';
const {t} = useI18n()
const toast = useToast();

const SingleUser = ref(Object);

const MostrarDrop = ref(null);

const company = ref([]);

const menus = ref([
    { menu: t('apps.configName')},
    { menu: "Logins" }
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
	active: t('apps.configName'),
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
