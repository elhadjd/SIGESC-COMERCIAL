import axios from "axios"
import { ref,Ref } from "vue";

export const analisOrders = ((route:string) => {
    const Response = ref([])
    const chartType = ref(null)
    const stateOptions: Ref<number> = ref(0);
    const chartData = ref({
        labels: [],
        datasets:[
            {
            label: null,
            backgroundColor:[
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor:[
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1,
            data: [],
        }]
    })

    const changeChart = ((event)=>{
        chartType.value = event
    })

    const date: object = [
        { name: 'Hoje',filter: 'now'},
        {name: 'Ontem',filter: 'subDay'},
        {name: 'Este mês',filter: 'month'},
        {name: 'Esta semana',filter: 'week'},
        {name: 'Mês passado',filter: 'subMonth'},
        {
            name: 'Trimestral',
            filter: 'firstOfQuarter',
            state: false,
            options: [
                {
                    name: 'Primeiro Trimestro',
                    start: '01-01',
                    end: '03-31'
                },
                {
                    name: 'Segundo Trimestro',
                    start: '04-01',
                    end: '06-30'
                },
                {
                    name: 'Terceiro Trimestro',
                    start: '07-01',
                    end: '09-30'
                },
                {
                    name: 'Quarto Trimestro',
                    start: '10-01',
                    end: '12-31'
                },
            ]
        },
        {
            name: 'Semestral',
            filter: 'firstOfSemester',
            state: false,
            options: [
                {
                    name:'Primeiro Semestro',
                    start: '01-01',
                    end: '05-31'
                },
                {
                    name:'Segundo Semestro',
                    start: '06-01',
                    end: '12-31'
                },
            ]
        },
        {name: 'Anual',filter: 'year'},
    ]

    const handleItemClick = ((item:any,index:number)=>{
        if (item.options) {
            stateOptions.value = stateOptions.value === index ? 0 : index;
        } else {
            filterOption('data/' + item.filter);
        }
    })

    const handleYearInput = ((e:string)=>{
        e.length >=4 && filterOption(`data/${e}`)
    })

    const handleOptionClick = ((filter:any,start:any,end:any)=>{
        filterOption('dataSub/'+filter+'/'+start+'/'+end)
    })

    const forData = () => {
        var datas = []
        var label = []
        for (let index = 0; index <= Response.value.length; index++) {
            if(Response.value[index]?.name){
                datas.push(Response.value[index].total);
                label.push(Response.value[index].name)
            }
            else if (!Response.value[index]?.name && Response.value[index] > 0 ) {
                datas.push(Response.value[index]);
                label.push(index <= 9? '0'+index : index)
            }
        }
        chartData.value.labels = label
        chartData.value.datasets[0].data = datas
        chartType.value = 'bar'
    };

    async function getOrder():Promise<void>{

        chartType.value = ''
       await axios.get(route)
        .then((response) => {
            Response.value = response.data
            chartData.value.datasets[0].label = 'Este mes'
            forData()
        }).catch((err) => {
            console.log(err);
            err.response.data
        }).finally(()=>{
            chartType.value = 'bar'
        });
    }

    async function filterOption(type:string):Promise<void>{
        chartType.value = ''
        return await axios.get(`${route}/${type}`)
        .then((response) => {
            Response.value = response.data
            forData()
            chartData.value.datasets[0].label = type
            chartType.value = 'bar'
        }).catch((err) => {
            return err.response.data
        });
    }

    return {
        getOrder,
        date,
        chartData,
        filterOption,
        chartType,
        forData,
        handleYearInput,
        handleOptionClick,
        handleItemClick,
        stateOptions,
        changeChart
    }

})
