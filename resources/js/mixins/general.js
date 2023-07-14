import axios from 'axios';
import moment from 'moment'
export default {
    methods: {
        /* Method para formatar money */
        formatMoney(amount, decimalCount = 2, decimal = ",", thousands = ".", symbol = "kz") {
            try {
                decimalCount = Math.abs(decimalCount);
                decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

                const negativeSign = amount < 0 ? "-" : "";

                const i = parseInt(
                    (amount = Math.abs(Number(amount) || 0).toFixed(
                        decimalCount
                    ))
                ).toString();
                const j = i.length > 3 ? i.length % 3 : 0;

                return (
                    negativeSign +
                    (j ? i.substr(0, j) + thousands : "") +
                    i.substr(j).replace(/(\d{3})(?=\d)/g, `$1${thousands}`) +
                    (decimalCount
                        ? decimal +
                        Math.abs(amount - i)
                            .toFixed(decimalCount)
                            .slice(2)
                        : "") + symbol
                );
            } catch (e) {
                console.log(e);
            }
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
