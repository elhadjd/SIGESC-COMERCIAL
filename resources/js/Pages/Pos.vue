<template>
    <opining v-if="session.caixa.state != 'Aberto' && $store.state.Controlo.state" :session_id="session.id" @openControl="openControl"/>
    <Menu :session="session"/>
</template>

<script setup>
import Menu from '@/Components/pos/menuPos.vue'
import useEventsBus from '@/Eventbus'
import { onMounted,ref,watch } from 'vue'
import opining from '@/Components/pos/opining.vue'
import { useStore } from 'vuex';

const store = useStore();
const {bus} = useEventsBus();

watch(()=>bus.value.get('EncerrarSessao'), (payload) => {
    store.commit('CloseCash',false)
})
const props = defineProps({
    session: Object
})
const session = ref(props.session)

const openControl = ((session_open)=>{
    session.value = session_open
})

</script>

