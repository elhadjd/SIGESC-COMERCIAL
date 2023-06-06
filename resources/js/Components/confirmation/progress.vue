<template>
    <span class="Modal">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
        <!-- <img src="/login/image/refresh.gif" width="50"> -->
    </span>
</template>

<style lang="scss" scoped>
.Modal{
    position: absolute;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgba(0, 0, 0, 0.183);
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: 1080;
    >i{
        font-size: 25px;
        color: var(--bgButtons) !important;
    }
}
</style>
