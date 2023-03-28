<template>
    <div class="w-full grid gap-4 mt-4 md:mt-8">
        <div class="hidden w-full md:grid grid-cols-5 px-4 border-b-[1px] pb-[2px] text-[16px] text-custom-light-gray font-medium">
            <p>Application ID</p>
            <p>Carrier</p>
            <p>Error Type</p>
            <p>Error Source</p>
            <p>Assigned To</p>
        </div>

        <div class="w-full h-[550px] overflow-y-scroll scrollbar">
            <div v-for="(taskList, index) in tasks" :key="index" :class="index >= 1 ? 'mt-2' : ''" class="w-full h-fit grid gap-2">
                <p class="text-[16px] text-custom-gray font-medium">{{ today == taskList.date ? 'Today' : taskList.date && yesterday == taskList.date ? 'Yesterday' : taskList.date }}</p>
                <div @click="gotoTask(task.app_id, task.carrier.toString())" v-for="(task, index) in taskList.tasks" :key="index" class="w-full h-[48px] grid grid-cols-5 px-4 text-[16px] text-custom-gray bg-white border-[1px] border-custom-light rounded-[4px] hover:inner-border-2 cursor-pointer">
                    <p class="my-auto">{{ task.app_id }}</p>
                    <p class="my-auto">{{ carriers[task.carrier.toString()].name }}</p>
                    <p :class="task.type == 'API' ? 'text-custom-blue' : ''" class="my-auto">{{ task.type }}</p>
                    <p :class="task.source == 'MGA' ? 'text-custom-red' : 'text-custom-blue'" class="my-auto">{{ task.source == 'MGA' ? 'RocketMGA' : 'RocketAutomation' }}</p>
                    <p class="my-auto">{{ task.assigned }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import carriers from '../../../assets/carriers.json'

export default {
    name: "Normal",
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