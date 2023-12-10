<template>
    <Link
        :href="route(`dashboard`)"
        @mouseenter="state = true"
        @mouseout="state = false"
        class="Container"
    >
        <div class="menu-one" v-if="state">
            <span></span>
            <span></span>
        </div>

        <div class="menu-two" v-else>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
    </Link>
</template>
<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import { Link } from "@inertiajs/vue3";
import { useI18n } from 'vue-i18n';
import { useStore } from "vuex";
const {locale} = useI18n()
const state = ref(false);
const store = useStore()

</script>

<style scoped lang="scss">
.Container {
    position: absolute;
    width: 100%;
    height: 100% !important;
    padding: 12px;
    display: flex;
    align-content: center;
    justify-content: center;
    cursor: pointer;
    .menu-two {
        width: 100%;
        height: 100%;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-template-rows: repeat(3, 1fr);
        transition: 0.5s;
        gap: 0.1rem;
        span {
            background-color: white;
        }
    }
    .menu-one {
        transition: 0.5s;
        width: 100%;
        height: 100%;
        display: grid;
        grid-template-columns: 1fr;
        grid-template-rows: repeat(2, 1fr);
        align-items: center;
        span {
            width: 70%;
            height: 30%;
            background-color: white;
            border-radius: 10px 0px 0px 10px;
        }
        > span:nth-child(1) {
            transform: rotate(-35deg);
        }
        > span:nth-child(2) {
            transform: rotate(35deg);
        }
    }
}
</style>
