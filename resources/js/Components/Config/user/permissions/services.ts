import { serviceMessage } from "@/composable/public/messages";
import { UserTypeScript } from "@/types/userTypeScript";
import axios from "axios";
import { ref } from "vue";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";
interface permission_userTypeScript{
    permission_id: number,
    user_id: number
}
interface PermissionTypeScript{
    service_id: number,
    id: number,
    name: string,
    state: boolean,
    permission_user: permission_userTypeScript[]
}
interface serviceTranslate {
    local: string,
    translate: string,
    id: string,
    service_id: number
}
interface ServiceTypeScript{
    state: boolean,
    name:string,
    id: number,
    translate:serviceTranslate[]
    permissions:PermissionTypeScript[],
}
export const PermissionsServices = (()=>{
    const serviceNumber = ref(0)
    const store = useStore()
    const {t} = useI18n()
    const services = ref<ServiceTypeScript[]>([])
    const {showMessage} = serviceMessage()
    const stateButtonSave = ref<number>(0)
    const getServices = (async(userId:number)=>{

        await axios.get(`getPermissions/${userId}`)
        .then((response) => {
            if(response.data.message) return showMessage(response.data.message,response.data.type)
            services.value = response.data
        }).catch((err) => {
            showMessage(t('message.serverError'))
            console.log(err);
        }).finally(()=>{
            checkServicesActive(true)
        });
    })
    
    const checkServicesActive = ((type?:boolean)=>{
        services.value.forEach(service => {
            if(type){
                let newService = service.permissions.filter(permission => {
                    return permission.permission_user.length
                });
                service.permissions.forEach(permission => {
                    if(permission.permission_user.length){
                        permission.state = true
                    }else{
                        permission.state = false
                    }
                });
                if(newService.length){
                    service.state = true
                }else{
                    service.state = false
                }
            }else{
                let newService = service.permissions.filter(permission => {
                    return permission.state == true
                });
                if(newService.length){
                    service.state = true
                }else{
                    service.state = false
                }
            }
        });
    })

    const selectAll = ((e:any)=>{
        const filter = services.value.find((service)=>{
            return service.id == e.target.id
        })

        filter.permissions.forEach(permission => {
            if (e.target.checked) {
                permission.state = true
            }else{
                permission.state = false
            }
        });
        stateButtonSave.value = 1
    })

    const showService = ((number: number)=>{
        if(serviceNumber.value === number) {
            serviceNumber.value = 0
        }else{
            serviceNumber.value = number
        }
    })

    const activeUserPermission = ((permissionProps: PermissionTypeScript)=>{
        const findService = services.value.find((service: ServiceTypeScript)=>service.id == permissionProps.service_id)

        let permissionInService = findService.permissions.find((permission)=>{
            return permission.id == permissionProps.id
        })
        

        if(permissionInService.state){
            permissionInService.state = false;
            updateService(findService,permissionInService)
        }else{
            permissionInService.state = true
            updateService(findService,permissionInService)
        }
    })

    const  updateService = (serviceProps: ServiceTypeScript,permissionProps:PermissionTypeScript)=>{
        serviceProps.permissions.forEach((element:PermissionTypeScript)=> {
            if(element.id == permissionProps.id){
                element = permissionProps
            }
        });
        stateButtonSave.value = 1
        checkServicesActive()
    }

    const savePermission = (async(userId: number)=>{
        if(stateButtonSave.value == 0) return
        stateButtonSave.value = 2
        await axios.post(`savePermission/${userId}`,services.value)
        .then((response) => {
            if(response.data.message){
                showMessage(response.data.message,response.data.type)
                services.value = response.data.data.services
                store.commit("publico/saveUser", response.data.data.user);
                checkServicesActive(true)
            }
        }).catch((err) => {
            showMessage(t('message.serverError'))
            if(err.response.data.message) showMessage(err.response.data.message,'info')
            console.log(err);
        }).finally(()=>{
            stateButtonSave.value = 0
        });
    })

    return {
        getServices,
        services,
        serviceNumber,
        showService,
        activeUserPermission,
        savePermission,
        stateButtonSave,
        selectAll
    }
})

