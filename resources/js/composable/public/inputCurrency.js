import { ref } from "vue";

export const InputCurrency = ((number,form)=>{

    const format = (n)=>{
        return new Intl.NumberFormat('PT-AO', {
        style: 'currency',
        currency: 'AOA',
      }).format(n);
    }

    const type = ref('text');

    const numberStr = ref(format(number.value));

    const onInput = ({ target }) => {
        if (target.value != '' ) {
        number.value = parseInt(target.value);
        }
    };
    const onFocus = () => {
        if (number.value != '' ) {
        numberStr.value = number.value;
        type.value = 'number';
        }
    };
    const onBlur = () => {
        if (number.value != '' ) {
            type.value = 'text';
            numberStr.value = format(number.value);
            form.value.salary = number.value
        }
    };
    return {onBlur,onFocus,onInput,numberStr,type}
})
