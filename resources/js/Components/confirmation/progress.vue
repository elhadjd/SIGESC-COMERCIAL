<template>
    <span>
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
    </span>
</template>

<style lang="scss" scoped>
span{
    position: fixed;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: var(--bgButtons);
    width:150px;
    height: 40px;
    bottom: 5px;
    right: 5px;
    z-index: 1080;
    >i{
        font-size: 17px;
        color: white !important;
    }
}
</style>
