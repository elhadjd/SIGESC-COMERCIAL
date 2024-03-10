import { companyTs } from "@/types/company";
import moment from "moment";

export const HomeServices = (()=>{
    
    // async function expiration(company:companyTs) {
    //     const targetDate = moment(company.license.to, 'YYYY-MM-DD H:mm:ss');
    //     let timeRemaining = targetDate.diff(moment());

    //     const intervalId = setInterval(() => {
    //     timeRemaining -= 1000;
    //     const duration = moment.duration(timeRemaining);

    //     license.value.expiration = duration.months() +" Meses: "+ duration.days() + 'Dias: ' + duration.hours() + 'Horas: ' + duration.minutes() + ':' + duration.seconds();
    //     if (duration._data.months <=0 && duration._data.days <= 2) return license.value.state = true
    //     if (timeRemaining <= 0) {
    //         clearInterval(intervalId);
    //         console.log('Tempo acabou!');
    //     }
    //     }, 1000);
    // }
    return {

    }
})