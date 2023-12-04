import axios from 'axios';
import moment from 'moment'
import { ref } from 'vue';
import { useStore } from 'vuex';
export default {
    methods: {
        formatMoney(amount) {
            const vuexData = JSON.parse(localStorage.getItem('vuex'));
            const currency = vuexData.publico.company.currency_company;
            return Intl.NumberFormat("PT-AO", {
                style: 'currency',
                currency: currency.code,
                minimumIntegerDigits: currency.digits
              }).format(amount);
            // console.log(symbol);
            // try {
            //     decimalCount = Math.abs(decimalCount);
            //     decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

            //     const negativeSign = amount < 0 ? "-" : "";

            //     const i = parseInt(
            //         (amount = Math.abs(Number(amount) || 0).toFixed(
            //             decimalCount
            //         ))
            //     ).toString();
            //     const j = i.length > 3 ? i.length % 3 : 0;

            //     return (
            //         negativeSign +
            //         (j ? i.substr(0, j) + thousands : "") +
            //         i.substr(j).replace(/(\d{3})(?=\d)/g, `$1${thousands}`) +
            //         (decimalCount
            //             ? decimal +
            //             Math.abs(amount - i)
            //                 .toFixed(decimalCount)
            //                 .slice(2)
            //             : "") + symbol
            //     );
            // } catch (e) {
            //     console.log(e);
            // }
        },
        /* Method para Formatar Date */
        formatDate(date){
            return moment(date).format("DD-MM-YYYY")
        },

        formatTime(data){
            return moment(data).format('HH:mm:ss')
        },

        formatHourAndTime(data){
            return moment(data).format('DD-MM-Y HH:mm:ss')
        }
    },
};
