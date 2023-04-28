<template>
	<div class="Container">
        <header>
            <h1><span>S</span>ISGESC</h1>
        </header>
		<form @submit.prevent="submitForm" class="form">
			<div class="Header">
                <div>
                    <font-awesome-icon icon="fa-regular fa-building" />
                    <span>Empresa</span>
                    <div class="step"></div>
                </div>
                <div>
                    <font-awesome-icon icon="fa-regular fa-user" />
                    <span>Perfil</span>
                    <div :class="$store.state.start.step >= 1 ? 'step' : ''"></div>
                </div>
                <div>
                    <font-awesome-icon icon="fa-solid fa-certificate" />
                    <span>License</span>
                    <div :class="$store.state.start.step >= 2 ? 'step' : ''"></div>
                </div>
            </div>

			<div class="Content">
				<company v-if="$store.state.start.step == 0" />
				<License v-else-if="$store.state.start.step == 1"/>
				<Admin v-else/>
			</div>

			<div class="Footer">
                <button v-if="$store.state.start.step > 0" @click="back" class="Descartar">Voltar</button>
                <button  type="submit">{{$store.state.start.step > 2 ? 'Concluir' : 'Avançar'}}</button>
			</div>
		</form>
	</div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import company from "./company.vue";
import License from "./License.vue";
import Admin from "./Admin.vue";
import { ref } from "vue";
import { useStore } from 'vuex';

const store = useStore()

const submitForm = (()=>{
    if (store.state.start.step > 2) {
        saveForm()
    }else{
        nextStep()
    }
})

const back = (()=>{
    store.state.start.step --
})
const nextStep = (()=>{
    store.state.start.step ++
})

</script>

<style scoped lang="scss">
@import '../../../assets/Start/index';
</style>
