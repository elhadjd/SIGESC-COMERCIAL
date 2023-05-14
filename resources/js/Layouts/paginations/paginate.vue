<template>
 <!-- v-if="loading == 'page'+item.label" -->
    <div class="Content">
        <i v-if="data.current_page>1"  @click="getPages(data.current_page-1)" class="fa fa-chevron-circle-left" aria-hidden="true"></i>
        <div v-for="item in data.links" :key="item.label" @click="data.current_page != item.label ? getPages(item.label):''" v-show="item.label != 'pagination.next' && item.label != 'pagination.previous'">
            <span >{{item.label}}</span>
            <i v-if="loading == 'page'+item.label" class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        </div>
       <i v-if="data.current_page < data.last_page" @click="getPages(data.current_page+1)" class="fa fa-chevron-circle-right" aria-hidden="true"></i>
    </div>
</template>

<script setup>
import axios from "axios"
import { onMounted, ref } from "vue"

const props = defineProps({
    object: Object
})
const emits = defineEmits(['page'])
const data = ref([])
const loading = ref(null)
onMounted(()=>{
    data.value = props.object
})
const getPages = ((page)=>{
    loading.value = 'page'+page
   axios.get(data.value.links[page].url)
   .then((response) => {
        data.value = response.data

        emits('page',data.value)
   }).catch((err) => {
        console.log(err);
        loading.value = null
   }).finally(()=>{
        loading.value = null
   });
})

</script>

<style lang="scss" scoped>
.Content{
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .3rem;
    margin-top: 3px;
    padding: 2px;
    i{
        color: var(--bgButtons);
    }
   >i, div{
        background-color: var(--appBg);
        padding: 0px 10px 0px 10px;
        width: 100%;
        height: 100%;
        display: flex;
        gap: .2rem;
        align-items: center;
        border-radius: 5px;
        font-size: 15px;
        font-weight: 600;
        user-select: none;
        color: var();
        &:hover{
            background-color: var(--bgButtons);
            cursor: pointer;
            color: white;
        }
        >i{
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--bgButtons);
            border-radius: 5px;
            font-size: 15px;
            width: 100%;
            height: 100%;
            &:hover{
                color: white;
            }
        }

    }
}
</style>
