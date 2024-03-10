<template>
<div class="absolute w-full px-2 text-white md:px-4 py-2 md:px-6 bottom-0 md:space-x-6 flex flex-col md:flex-row md:items-center md:justify-end  bg-transparent " :class="license.state ? 'expired' : 'version'">
    <FontAwesomeIcon class="rounded-full w-4 text-white bg-green-500 p-1" icon="fa-solid fa-check"/>
    <div class="flex flex-row md:text-gray-600 lg:text-white space-x-4">
        <strong>{{`${$t('words.client')} ${$t('words.active')}`}}</strong>
        <span>{{licenseProps.to}}</span>
    </div>
    <div class="flex flex-row md:text-gray-600 space-x-4">
        <strong>{{$t('words.time')}}</strong>
        <span>{{license.expiration}}</span>
    </div>
</div>
</template>

<script lang="ts" setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import moment from "moment";
import { onMounted, ref } from "vue";
import { useI18n } from "vue-i18n";
const {t} = useI18n()
const license = ref({
    state: false,
    expiration: '0',
})
const props = defineProps({
    licenseProps: Object
})

onMounted(async() => {
    await expiration()
})
async function expiration() {
    const targetDate = moment(props.licenseProps.to, 'YYYY-MM-DD H:mm:ss');
    let timeRemaining = targetDate.diff(moment());
    const intervalId = setInterval(() => {
    timeRemaining -= 1000;
    const duration = moment.duration(timeRemaining);

    license.value.expiration = duration.months() +t('words.month') + ': '+ duration.days() + ' '+ t('words.day') + ': ' + duration.hours() + t('words.hour') +': ' + duration.minutes() + ':' + duration.seconds();
    if (duration._data.months <=0 && duration._data.days <= 2) return license.value.state = true
    if (timeRemaining <= 0) {
        clearInterval(intervalId);
        console.log('Tempo acabou!');
    }
    }, 1000);
}
</script>

<style scoped lang="scss">

</style>
