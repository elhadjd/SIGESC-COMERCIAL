<template>
    <modalVue>
        <div class="w-full z-30 user-select-none h-full flex justify-center items-center">
            <div class="w-full lg:w-2/3 md:w-3/4 h-auto bg-white">
                <div class="p-2 md:p-4 w-full flex justify-center border-bottom relative">
                    <h2>{{$t('words.permission')}}</h2>
                    <span @click="$emit('toClose')" class="absolute right-0 top-0 bg-gray border w-10 h-10 flex items-center justify-center cursor-pointer p-2 rounded-full">X</span>
                </div>
                <div v-if="services.length == 0" class="flex justify-center text-gray-200">
                    <loadingVue/>
                </div>
                <div v-else class="w-full md:grid md:grid-cols-2 md:grid-flow-row h-[450px] min-[500px]:h-96 overflow-y-auto">
                    <div :class="`${serviceNumber == service.id && `row-span-6`}`" class="flex flex-col px-4 w-full" v-for="service in services" :key="service.id">
                        <div class="flex p-2 cursor-pointer hover:bg-gray-200 space-x-2 items-center">
                            <span class="flex flex-row">
                                <input class="rounded" @click="(e)=>selectAll(e)" :checked="service.state" type="checkbox" :name="service.name" :id="service.id">
                            </span>
                            <span @click="()=>showService(service.id)" class="flex flex-auto items-center flex-row justify-between">
                                <span >{{service.translate[0].translate}}</span>
                                <FontAwesomeIcon :icon="`fa-solid ${serviceNumber == service.id ? 'fa-chevron-down' : 'fa-chevron-right'}`"/>
                            </span>
                        </div>
                        <div @click="()=>activeUserPermission(permission)" class="ml-10" :class="serviceNumber == service.id ? 'visible':'hidden'" v-for="permission in service.permissions" :key="permission.id">
                            <span class="flex flex-row space-x-2 p-2" v-if="serviceNumber == service.id">
                                <input class="rounded" type="checkbox" :checked="permission.state" :name="permission.name" :id="permission.id">
                                <span class="w-full truncate" >{{$t(`permissions.${String(permission.name).replace(/\s/g, '')}`)}}</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div v-if="stateButtonSave > 0" class="flex justify-end p-2 md:p-4 saveButton border-top">
                    <button @click="()=>savePermission(userProps)">
                        {{$t('words.save')}}
                    </button>
                </div>
            </div>
        </div>
        <progressVue v-if="stateButtonSave == 2"/>
    </modalVue>
</template>

<script lang="ts" setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import modalVue from '@/ui/modal.vue'
import {PermissionsServices} from './services'
import axios from 'axios';
import { onMounted, ref } from 'vue'
import progressVue from '@/Components/confirmation/progress.vue'
import loadingVue from '@/Components/confirmation/loading.vue'
const {
        getServices,
        services,
        serviceNumber,
        showService,
        activeUserPermission,
        savePermission,
        stateButtonSave,
        selectAll
    } = PermissionsServices()
const emits = defineEmits(['toClose'])
const props = defineProps({
    userProps: Number
})

onMounted(async()=>{
    await getServices(props.userProps);
})

</script>

<style scoped lang="scss">
.saveButton{
    button{
        @include botao_normal;
    }
}
</style>
