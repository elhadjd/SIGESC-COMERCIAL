<template>
    <div class="list">
        <div class="Main">
            <div v-for="item in Invoices.list.data" :key="item.id" class="form-Content" @click="$emit('showInvoice',item)">
                <div class="Header">
                    <img :src="`/clientes/image/${item.client?.image}`">
                    <span>{{formatDate(item.DateOrder)}}</span>
                </div>
                <div class="name">
                    {{item.client?.surname}}
                </div>
                <div class="Footer">
                    <span>{{formatMoney(item.TotalInvoice)}}</span>
                    <span :class="item.state">
                        <span>{{item.state}}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    Invoices: Object
})

const emits = defineEmits(['showInvoice'])
</script>

<style scoped lang="scss">
.list{
    height: 90%;
    overflow-y: auto;
    @include scroll;
    @include listForm(100%,120px,4,3,3,3,3);
    .Footer,
    .Header{
        width: 100%;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        img{
            width: 25px;
            height: 25px;
            border-radius: 50%;
        }
    }
    .name{
        width: 100%;
        max-width: 25ch;
        display: -webkit-box;
        align-items: center;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .Footer{
        @include state;
        
    }
}
</style>