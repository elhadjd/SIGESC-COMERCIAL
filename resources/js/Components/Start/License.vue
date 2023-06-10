<template>
   <div class="principal">
        <div class="premium" v-if="OpenLicensePremium">
            <modalLicensesPremium
                @close="OpenLicensePremium = $event"
            />
        </div>
        <div v-else class="cards">
            <div class="card">
                <div>
                    <strong>Plano Premium</strong>
                    <span>{{formatMoney(3500)}}</span>
                    <span>/Mensal</span>
                    <button type="button" @click="OpenLicensePremium = true">Aderir</button>
                </div>
                <div>
                <ul>
                    <li>Todos Modulos Liberado</li>
                    <li>Configuração de usuarios ilimitado</li>
                    <li>Gateway de pagamento</li>
                    <li>Controle de stock</li>
                </ul>
                </div>
            </div>
            <div class="card" :class="choose.plano == 'free' ? 'active' : ''">
                <div>
                <strong>Plano Free</strong>
                <span>{{formatMoney(0)}}</span>
                <span>/15 DIAS</span>
                <button type="button" @click="AddFree('free')">Começar</button>
                </div>
                <div>
                <ul>
                    <li>POS com apenas 10 produtos</li>
                    <li>Só permite criar um usuario</li>
                    <li>Apenas 2 fornecedores</li>
                    <li>Apenas 3 clientes</li>
                </ul>
                </div>
            </div>
        </div>

   </div>
</template>

<script setup>
import { ref } from "vue";
import { useStore } from "vuex";
import modalLicensesPremium from '@/Components/Start/licensesPremium/modal.vue'

const store = useStore()
const choose = ref({
    plano: store.state.Start.start.license,
})

const OpenLicensePremium = ref(false);

const AddFree = ((plan)=>{
    if (choose.value.plano != null) return choose.value.plano = null
    choose.value.plano = plan
    store.commit('Start/StartSaveLicense',choose.value.plano)
})
</script>

<style scoped lang="scss">
@import '../../../assets/Start/cards';
.principal {
    .premium {
        width: 100%;
        height: 100%;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
    }
}
</style>
