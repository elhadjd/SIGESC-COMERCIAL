<template>
    <div class="flex justify-center logo md:w-48 flex-col relative">
        <h1 class="static text-3xl md:text-3xl font-bold ">
            <span>S</span>
            <span>IGESC</span>
        </h1>
        <span class="absolute ml-0 mt-7 truncate w-full text-gray-400 font-sm text-sm w-auto">Solução para a sua empresa</span>
    </div>
    <!-- <div class="absolute w-1/2 h-2/3 bg-white rounded right-0 mt-4">

    </div> -->
    <!-- <div style="background-color: var(--app_hover)" class="fixed w-full h-full origin-bottom mt-28 -rotate-45 left-28 md:origin-top-left md:skew-y-12 md:transform md:mt-0 md:left-0 md:right-0 md:rotate-12"></div> -->
</template>

<script>

</script>

<style scoped lang="scss">
.logo{
    h1{
        >span:nth-child(1){
            color: var(--app_color);
        }
        >span:nth-child(2){
            color: var(--bgButtons);
        }
    }
}
</style>
