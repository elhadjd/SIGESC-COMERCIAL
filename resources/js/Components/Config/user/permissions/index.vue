<template>
<div class="flex flex-col space-y-2 p-4 w-full h-full">
    <div class="">
        <span>{{$t('words.permission')}}:</span>
        <strong>{{user.permissions.length}}</strong>
    </div>
    <div class="button_added">
        <button type="button" @click="modalState = true" class="">{{$t('words.added')}}</button>
    </div>
    <permissionListVue :userProps="user.id" v-if="modalState" @toClose="modalState = false" :key="componentKey"/>
</div>
</template>

<script lang="ts" setup>
import permissionListVue from "@/Components/Config/user/permissions/permissionList.vue";
import { computed, onMounted, ref } from "vue";
import { useStore } from "vuex";
const modalState = ref(false)
const componentKey = ref(0)
const store = useStore()
const props = defineProps({
    user: Number,
})
const userAndCompany = computed(()=>store.getters['publico/public'])
</script>

<style scoped lang="scss">
.button_added{
    button{
        @include botao_normal;
    }
}
</style>
