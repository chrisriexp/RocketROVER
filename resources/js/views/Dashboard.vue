<template>
    <NavBar :active="'dashboard'" :role="role" class="z-0 absolute" />

    <profileMenu class="z-20 absolute" />

    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <!-- Notification Popup -->
    <div v-if="notificationPopup" class="grid w-screen h-screen z-50 fixed bg-[#3F3F3F] bg-opacity-[26%]">
        <div class="h-[524px] w-[343px] md:h-[571px] md:w-[707px] grid mx-auto mt-8 md:m-auto bg-white rounded-[4px] md:rounded-[8px] shadow-newdrop">
            <div class="h-[69px] w-full grid grid-cols-2 px-6 bg-custom-bg rounded-tr-[4px] md:rounded-tr-[8px] rounded-tl-[4px] md:rounded-tl-[8px] border-b-[1px] border-custom-light">
                <p class="text-[20px] md:text-[24px] font-medium my-auto">Notifications</p>
                <div class="h-fit flow-root my-auto text-custom-gray">
                    <XCircleIcon @click="notificationPopup = false" class="h-[24px] float-right cursor-pointer hover:text-custom-red" />
                </div>
            </div>

            <!-- All Notifications -->
            <div class="h-full w-full grid gap-3 p-6 overflow-y-scroll scrollbar relative">
                <div v-for="(notification, index) in notifications" :key="index" @click="gotoAgency(true, notification.id, notification.app_id, notification.carrier)" class="w-full h-[66px] md:h-[48px] flex gap-4 px-4 bg-custom-bg border-[1px] border-custom-light rounded-[4px] cursor-pointer">
                    <div class="h-[8px] w-[8px] my-auto bg-custom-blue rounded-full"></div>
                    <div class="my-auto w-full h-fit grid grid-cols-2 md:grid-cols-3 text-[16px] text-custom-gray">
                        <div class="w-full md:col-span-2 grid md:grid-cols-2">
                            <p class="my-auto">{{ notification.app_id }}</p>
                            <p :class="notification.source == 'MGA' ? 'text-custom-red' : 'text-custom-blue'" class="my-auto">{{ notification.source == 'MGA' ? 'RocketMGA' : 'RocketAutomation' }}</p>
                        </div>
                        <div class="my-auto w-full h-fit flow-root">
                            <p class="float-right text-[28px] text-custom-gray font-medium">{{ notification.errors <= 9 ? `0${notification.errors}` : notification.errors }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sm:ml-64 mt-24 md:mt-[72px] px-4 md:px-10 h-fit w-full md:w-[84%] bg-custom-bg z-10 absolute">
        <!-- Header -->
        <div class="w-fit grid">
            <h1 class="text-[22px] md:text-[32px] text-custom-gray font-medium">Welcome to Rocket <span class="text-custom-blue">R.O.V.E.R</span></h1>
            <p class="text-[14px] md:text-[16px] text-custom-light-gray font-medium">(Rocket Operations Verification & Error Resolution)</p>
            <p class="mt-2 text-[14px] md:text-[16px] text-custom-light-gray">Here is some important information for you,</p>
        </div>

        <div class="w-full grid md:grid-cols-3 gap-6 md:gap-12 mt-4 md:mt-8 relative">
            <!-- Notificiations -->
            <div class="grid md:col-span-2 border-[1px] border-custom-light rounded-[8px] bg-white shadow-newdrop">
                <div class="h-[78px] w-full grid grid-cols-2 px-6">
                    <p class="h-fit text-[24px] text-custom-gray font-medium my-auto">Notifications</p>

                    <div class="my-auto h-fit flow-root text-custom-blue">
                        <p @click="notificationPopup = true" class="float-right flex gap-2 cursor-pointer">View <span class="hidden md:block">all</span> <ArrowRightCircleIcon class="h-[24px]" /></p>
                    </div>
                </div>

                <!-- Newest Notifications -->
                <div class="hidden w-full md:grid mt-[-30px] gap-4 p-6">
                    <div v-for="(notification, index) in quickNotifications" :key="index" @click="gotoAgency(true, notification.id, notification.app_id, notification.carrier)" :class="notification.read ? 'bg-white' : 'bg-custom-bg'" class="w-full h-[48px] flex gap-4 border-[1px] border-custom-light rounded-[4px] px-4 cursor-pointer">
                        <div :class="notification.read? 'bg-white' : 'bg-custom-blue'" class="h-[8px] w-[8px] my-auto rounded-full"></div>
                        <div class="my-auto w-full h-fit grid grid-cols-3 text-[16px] text-custom-gray">
                            <p class="my-auto">{{ notification.app_id }}</p>
                            <p :class="notification.source == 'MGA' ? 'text-custom-red' : 'text-custom-blue'" class="my-auto">{{ notification.source == 'MGA' ? 'RocketMGA' : 'RocketAutomation' }}</p>
                            <div class="my-auto w-full h-fit flow-root">
                                <p class="float-right text-[28px] text-custom-gray font-medium">{{ notification.errors <= 9 ? `0${notification.errors}` : notification.errors }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics -->
            <div class="grid gap-2 md:gap-6 w-full h-fit bg-white p-4 md:p-6 shadow-newdrop rounded-[8px]">
                <p class="text-[20px] md:text-[24px] text-custom-gray font-medium">Analytics</p>

                <div class="h-[56px] grid grid-cols-3 mt-4 md:mt-0 px-4 bg-white border-[1px] border-custom-light rounded-[4px]">
                    <p class="col-span-2 text-[18px] text-custom-light-gray my-auto">Today's Errors</p>
                    <div class="flow-root my-auto">
                        <p class="text-[28px] md:text-[32px] text-custom-gray font-medium float-right">{{ today <= 9 ? `0${today}` : today }}</p>
                    </div>
                </div>

                <div class="h-[56px] grid grid-cols-3 mt-4 md:mt-0 px-4 bg-white border-[1px] border-custom-light rounded-[4px]">
                    <p class="col-span-2 text-[18px] text-custom-light-gray my-auto">Pending Errors</p>
                    <div class="flow-root my-auto">
                        <p class="text-[28px] md:text-[32px] text-custom-gray font-medium float-right">{{ pending <= 9 ? `0${pending}` : pending }}</p>
                    </div>
                </div>

                <div class="h-[56px] grid grid-cols-3 mt-4 md:mt-0 px-4 bg-white border-[1px] border-custom-light rounded-[4px]">
                    <p class="col-span-2 text-[18px] text-custom-light-gray my-auto">Fixed Errors</p>
                    <div class="flow-root my-auto">
                        <p class="text-[28px] md:text-[32px] text-custom-gray font-medium float-right">{{ fixed <= 9 ? `0${fixed}` : fixed }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agents Quick View -->
        <div class="w-full grid mt-8 mb-12 border-[1px] border-custom-light rounded-[8px] bg-white shadow-newdrop">
            <div class="h-[77px] w-full flex md:grid md:grid-cols-2 px-6">
                <p class="h-fit w-full text-[20px] md:text-[24px] text-custom-gray font-semibold my-auto">Recent Errors</p>

                <router-link to="/tasks" class="w-[50%] md:w-full my-auto h-fit flow-root text-custom-blue">
                    <p class="float-right flex gap-2 cursor-pointer"><span class="hidden md:block">View all</span> <ArrowRightCircleIcon class="h-[24px]" /></p>
                </router-link>
            </div>

            <div class="w-full grid gap-4 md:gap-0 p-6">
                <div class="hidden w-full md:grid grid-cols-5 px-4 border-b-[1px] pb-[2px] text-[16px] text-custom-light-gray font-medium">
                    <p>Application ID</p>
                    <p>Carrier</p>
                    <p>Error Type</p>
                    <p>Error Source</p>
                    <p>Assigned To</p>
                </div>
                
                <!-- Desktop View -->
                <div class="hidden w-full mt-6 md:grid gap-2">
                    <div v-for="(task, index) in tasks" :key="index" @click="gotoAgency(false, null, task.app_id, task.carrier)" class="w-full h-[48px] grid grid-cols-5 px-4 border-[1px] border-custom-light rounded-[4px] text-[16px] text-custom-gray font-medium cursor-pointer hover:inner-border-2 inner-border-custom-light">
                        <p class="my-auto truncate mr-6">{{ task.app_id }}</p>
                        <p class="my-auto truncate mr-6">{{ carriers[task.carrier].name }}</p>
                        <p :class="task.type == 'API' ? 'text-custom-blue' : ''" class="my-auto">{{ task.type }}</p>
                        <p :class="task.source == 'MGA' ? 'text-custom-red' : 'text-custom-blue'" class="my-auto truncate mr-6">{{ task.source == "MGA" ? "RocketMGA" : "RocketAutomation" }}</p>
                        <p class="my-auto truncate mr-6">{{ task.assigned }}</p>
                    </div>
                </div>

                <!-- Mobile View -->
                <div v-for="(agent, index) in agents" :key="index" @click="gotoAgency(agent.rocket_id, false)" :class="index % 2 == 0? 'bg-custom-bg' : ''" class="h-fit w-full flex md:hidden p-4 border-[1px] border-custom-light rounded-[4px] text-[14px]">
                    <div class="w-full grid gap-2 text-custom-light-gray">
                        <p>Agency Name</p>
                        <p>Principal Agent</p>
                        <p>Phone</p>
                        <p>Email</p>
                        <p>Stage</p>
                    </div>

                    <div class="w-full grid gap-2 text-custom-gray font-medium">
                        <p class="truncate">{{ agent.agency_name }}</p>
                        <p class="truncate">{{ agent.agent_name }}</p>
                        <input v-model="agent.phone" disabled v-mask="'(###) ###-####'" :class="index % 2 == 0? 'bg-custom-bg' : 'bg-white'" class="my-auto h-fit truncate">
                        <p class="truncate">{{ agent.email }}</p>
                        
                        <p v-if="agent.appointed" class="text-custom-red truncate">Agency Appointed</p>
                        <p v-else-if="agent.approved" class="text-custom-red  truncate">Agency in Training</p>
                        <p v-else-if="agent.completed" class="text-custom-red truncate">Agency Under Review</p>
                        <p v-else-if="agent.stage == 'agency'" class="text-custom-red truncate">Agency Information</p>
                        <p v-else-if="agent.stage == 'carrier'" class="text-custom-red truncate">Carrier Information</p>
                        <p v-else-if="agent.stage == 'entity'" class="text-custom-red truncate">Entity Information</p>
                        <p v-else-if="agent.stage == 'eo'" class="text-custom-red truncate">E&O Information</p>
                        <p v-else-if="agent.stage == 'agreement'" class="text-custom-red truncate">Agreement</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
    
<script>
import NavBar from '../components/navbar.vue'
import profileMenu from '../components/profileMenu.vue'
import loading from '../components/loading.vue'

import { ArrowRightCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline'
import carriers from '../../assets/carriers.json'

export default {
    name: "Dashboard",
    data(){
        return {
            role: "",
            type: "",
            loading: true,
            notificationPopup: false,
            quickNotifications: [],
            notifications: [],
            today: '',
            pending: '',
            fixed: '',
            tasks: [],
            carriers: carriers
        }
    },
    async created(){
        this.role = this.$route.meta.role
        this.type = this.$route.meta.type
        console.log(this.quickNotifications)

        await axios.get('/api/tasks/dashboard')
        .then(response => {
            this.quickNotifications = response.data.quick_notifications
            this.notifications = response.data.unread_notifications
            this.today = response.data.today
            this.pending = response.data.pending
            this.fixed = response.data.fixed
            this.tasks = response.data.tasks
        })

        this.loading = false
    },
    methods: {
        async gotoAgency(notification, id, app_id, carrier){
            if(notification){
                await axios.get('/api/notification/read/'+id)
            }

            this.$router.push({name: 'Task', params:{app_id: app_id, carrier: carrier}})
        }
    },
    components: {
        NavBar,
        profileMenu,
        loading,
        ArrowRightCircleIcon,
        XCircleIcon
    }
}
</script>