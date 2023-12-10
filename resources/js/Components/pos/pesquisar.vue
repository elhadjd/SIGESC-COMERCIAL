<template>
    <div class="search">
        <span class="p-input-icon-left w-100">
            <i class="pi pi-search" />
            <input
            type="search"
            v-model="$store.state.PesquisarProduto"
            @mouseenter="$store.state.PesquisarProduto = '',AutoFocus()"
            ref="PesquisarProduto"
            name="PesquisarProduto"
            :placeholder="$t('words.search')"
            id="PesquisarProduto"
            class="w-100 p-0"/>
        </span>
    </div>
</template>

<script setup>
import { computed, watch , ref } from "@vue/runtime-core";
import {mapMutations, mapState, useStore } from "vuex"

const store = useStore()

const Watche = computed(()=>{
    return store.state.PesquisarProduto
})



const PesquisarProduto = ref(null)

const AutoFocus = (()=>{
    PesquisarProduto.value.focus()
})

watch(Watche, (novo)=>{
    return AutoFocus()
})

</script>

<style lang="scss" scoped>
.search{
    width: 100% !important;
    input{
        width: 100% !important;
        border: none !important;
        outline: none !important;
        box-shadow: 0 0 0 0;
        padding: 3px 15px 3px 30px !important;
        border-bottom: 1px solid #610246;
        border-radius: 3px;
        color:rgba(0, 0, 0, 0.535);
    }
}

</style>
