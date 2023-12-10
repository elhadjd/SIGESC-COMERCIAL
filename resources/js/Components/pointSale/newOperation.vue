<template>
  <div class="principal">
    <div class="Modal">
      <div class="Header">
        <h3>{{`${$t('words.new')} ${$t('words.expenses')}`}}</h3>
      </div>
     <form @submit.prevent="SaveOperation">
      <div class="Container">
        <div>
          <textarea v-model="form.subject"></textarea>
        </div>
        <div>
          <div>
            <span>{{ !form.user?.surname ? user.name : form.user?.surname}}</span>
            <span>Tipo Gasto</span>
          </div>

          <div>
            <input ref="inputRef" v-model="form.amount" type="text" :placeholder="$t('words.amount')" />
          </div>
        </div>
      </div>

      <div class="Footer">
        <div class="buttons">
          <button @click="$emit('close')" class="Descartar">{{$t('words.close')}}</button>
          <button type="submit" class="save">{{$t('words.save')}}</button>
        </div>
      </div>
      </form>
    </div>
  </div>
</template>
<script setup>
import { computed, onMounted, ref } from "vue";
import { useCurrencyInput } from "vue-currency-input";
import { useStore } from "vuex";

const props = defineProps({
  data:{
    default: () => Object
  }
});

const {inputRef} = useCurrencyInput({
    currency: 'AOA'
});

const store = useStore();

const user = computed(() => store.state.publico.user);

const emits = defineEmits(['message','update','close']);

const form = ref({
  amount: 0,
  subject: null,
  user_id: props.data.user ? props.data.user.id : user.value.id,
});

function SaveOperation() {
    axios.post('SaveOperation',{...form.value}).then((response) => {
        emits('message',response.data.message,response.data.type)
        emits('update',response.data.data)
    }).catch((err) => {
        console.log(err);
    });
}

onMounted(() => {
  if (props.data) {
    form.value = props.data;
  }
});

</script>

<style lang="scss" scoped>
.principal {
  @include modal;
    display: flex;
    justify-content: center;
    align-items: center;
  .Modal {
    margin-top: 0px !important;
    border-radius: 6px;
    height: 300px !important;
        .Header {
            height: 70px !important;
            display: flex;
            justify-content: center;
            h3 {
                font-weight: 600 !important;
            }
        }
        form{
            height: 230px !important;
            .Container {
                height: 170px;
                display: flex !important;
                flex-direction: column !important;
                > div {
                    display: flex !important;
                    width: 100% !important;
                    textarea {
                    width: 100% !important;
                    height: 60px;
                    max-width: 100%;
                    max-height: 60px !important;
                    box-shadow: 0 0 0 0;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    margin-bottom: 30px;
                    }
                }
                > div:nth-child(2) {
                    width: 100% !important;
                    display: flex !important;
                    justify-content: space-between !important;
                    > div:nth-child(1) {
                        display: flex;
                        flex-direction: column;
                        span {
                            margin-bottom: 5px;
                        }
                    }
                    > div:nth-child(2) {
                        width: 50% !important;
                        input {
                            width: 100%;
                        }
                    }
                }
            }
            .Footer{
                height: 70px;
                display: flex;
                align-items: center;
                >div{
                    display: flex;
                    flex-direction: row;
                }
            }
        }
    }
}
</style>
