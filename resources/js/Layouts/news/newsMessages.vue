<template>
  <div class="form">
    <span>
        {{message.message}}
        <!-- 🌟 Grande Novidade a Caminho! 🚀 Em breve, teremos uma empolgante atualização para vocês, nossos incríveis clientes! Estamos trabalhando arduamente para possibilitar que você divulgue seus produtos na internet. Em breve, você terá o mundo ao alcance das mãos. Estamos ansiosos para ver seus negócios brilharem online! 🌐✨ #FiqueAtento #EmBreveOnline -->
        <slot></slot>
    </span>
  </div>
</template>

<script lang="ts" setup>
const props = defineProps({
    message: Object
})
</script>

<style lang="scss" scoped>
.form{
    widows: 100% !important;
    height: auto;
    display: flex;
    justify-content: center;

    >span{
        background-color: rgba(8, 84, 74, 0.934);
        width: 350px;
        padding: 15px;
        font: 12pt arial;
        font-weight: 300;
        border-radius: 10px;
        margin-bottom: 10px;
        color: white;
    }
}
</style>
