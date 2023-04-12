<template>
    <div class="w-full grid gap-4 mt-4 md:mt-8 mb-12 p-4 md:p-0 bg-white md:bg-transparent rounded-[4px] shadow-newdrop md:shadow-none">
        <div class="hidden w-full md:grid grid-cols-6 px-4 border-b-[1px] pb-[2px] text-[16px] text-custom-light-gray font-medium">
            <p>Application ID</p>
            <p>Carrier</p>
            <p>Product</p>
            <p>Error Type</p>
            <p>Error Source</p>
            <p>Completed Tests</p>
        </div>

        <div class="w-full h-[550px] overflow-y-scroll">
            <div v-for="(taskList, index) in tasks" :key="index" :class="index >= 1 ? 'mt-2' : ''" class="w-full h-fit grid gap-2">
                <p class="text-[16px] text-custom-gray font-medium">{{ today == taskList.date ? 'Today' : taskList.date && yesterday == taskList.date ? 'Yesterday' : taskList.date }}</p>
                <!-- Desktop View -->
                <div @click="gotoTask(task.app_id, task.carrier.toString())" v-for="(task, index) in taskList.tasks" :key="index" class="hidden w-full h-[48px] md:grid grid-cols-6 px-4 text-[16px] text-custom-gray bg-white border-[1px] border-custom-light rounded-[4px] hover:inner-border-2 cursor-pointer">
                    <p class="my-auto truncate">{{ task.app_id }}</p>
                    <p class="my-auto truncate">{{ task.product == 'FLOOD' ? carriers[task.carrier].name : carriers[task.carrier.substring(3)].name }}</p>
                    <p :class="task.product == 'FLOOD' ? 'text-custom-blue' : ''" class="my-auto truncate">{{ task.product }}</p>
                    <p :class="task.type == 'API' ? 'text-custom-blue' : ''" class="my-auto truncate">{{ task.type }}</p>
                    <p :class="task.source == 'MGA' ? 'text-custom-red' : 'text-custom-blue'" class="my-auto truncate">{{ task.source == 'MGA' ? 'RocketMGA' : 'RocketAutomation' }}</p>
                    <img :src="`/images/${task.tests}.png`" alt="Testing Progress" class="my-auto truncate">
                </div>

                <!-- Mobile View -->
                <div @click="gotoTask(task.app_id, task.carrier.toString())" v-for="(task, index) in taskList.tasks" :key="index" :class="index % 2 == 0? 'bg-custom-bg' : ''" class="md:hidden grid grid-cols-2 text-[14px] p-2 border-[1px] border-custom-light rounded-[2px]">
                    <div class="w-full h-fit grid gap-2 text-custom-light-gray">
                        <p>Application ID</p>
                        <p>Carrier</p>
                        <p>Product</p>
                        <p>Error Type</p>
                        <p>Error Source</p>
                        <p>Completed Tests</p>
                    </div>

                    <div class="w-full h-fit grid gap-2 text-custom-gray font-medium">
                        <p class="truncate">{{ task.app_id }}</p>
                        <p class="truncate">{{ task.product == 'FLOOD' ? carriers[task.carrier].name : carriers[task.carrier.substring(3)].name }}</p>
                        <p :class="task.product == 'FLOOD' ? 'text-custom-blue' : ''" class="truncate">{{ task.product }}</p>
                        <p :class="task.type == 'API' ? 'text-custom-blue' : ''" class="truncate">{{ task.type }}</p>
                        <p :class="task.source == 'MGA' ? 'text-custom-red' : 'text-custom-blue'" class="truncate">{{ task.source == 'MGA' ? 'RocketMGA' : 'RocketAutomation' }}</p>
                        <img :src="`/images/${task.tests}.png`" alt="Testing Progress" class="my-auto">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import carriers from '../../../assets/carriers.json'

export default {
    name: "Testing",
    data(){
        return {
            today: "",
            yesterday: "",
            carriers: carriers
        }
    },
    async created(){
        this.moment = moment
        const date = new Date
        const yesterday = new Date(date)
        yesterday.setDate(yesterday.getDate() - 1)

        this.today = moment(date).format('MM/DD/YYYY')
        this.yesterday = moment(yesterday).format('MM/DD/YYYY')
    },
    methods: {
        gotoTask(id, name){
            this.$router.push({name: "Task", params: {app_id: id, carrier: name}})
        }
    },
    props: {
        tasks: Array
    }
}
</script>