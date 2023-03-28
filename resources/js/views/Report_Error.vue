<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <div class="w-screen h-screen grid">
        <!-- Header -->
        <div class="w-full h-[50px] grid border-b-[1px] border-custom-light bg-white">
            <div class="m-auto flex gap-4">
                <img src="../../assets/logo.png" alt="Rocket Automation Logo" class="h-[35px] my-auto">
                <p class="mt-2 text-[30px] text-custom-blue font-medium">R.O.V.E.R</p>
            </div>
        </div>
        
        <!-- Report Errors Form -->
        <form v-if="!submitted" @submit.prevent="submitError" class="w-[502px] h-[619px] mx-auto mt-[-100px] grid gap-6 justify-items-center bg-white p-6 rounded-[8px] shadow-newdrop relative">
            <div class="h-fit w-full grid gap-6">
                <h1 class="text-center text-[28px] text-custom-gray font-semibold">Error Details</h1>

                <!-- Application ID -->
                <textInput @inputUpdate="inputChange" :inputValue="form.app_id" :id="'app_id'" :label="'Application ID'" :placeholderText="'RFAA000000/RMGA00000'" :height="'48px'" />
            
                <!-- Errors -->
                <div class="w-full h-[250px] grid gap-4 overflow-y-scroll scrollbar">
                     <div v-for="(error, index) in form.errors" :key="index" class="w-full h-fit grid">
                        <label :for="'carrier'+index" class="text-[16px] text-custom-light-gray font-medium">Select Carrier <span class="text-custom-red">*</span></label>
                        <select v-model="error.carrier" data-te-select-init :name="'carrier'+index" :id="'carrier'+index" class="h-[48px] text-custom-gray border-custom-light border-[1px] bg-custom-white rounded-[2px] focus:ring-0 focus:border-custom-light">
                            <option value="aon" class="">AON Edge</option>
                            <option value="beyond">Beyond Flood</option>
                            <option v-if="this.form.app_id.includes('RFAA')" value="cat">Cat Coverage</option>
                            <option value="dual">Dual Flood</option>
                            <option value="flow">Flow Flood</option>
                            <option v-if="this.form.app_id.includes('RFAA')" value="frs">Flood Risk Solutions</option>
                            <option v-if="this.form.app_id.includes('RFAA')" value="hippo">Hippo</option>
                            <option value="neptune">Neptune</option>
                            <option value="palomar">Palomar</option>
                            <option v-if="this.form.app_id.includes('RFAA')" value="pmf">Private Market Flood</option>
                            <option value="sterling">Sterling</option>
                            <option v-if="this.form.app_id.includes('RFAA')" value="superior">Superior Flood</option>
                            <option v-if="this.form.app_id.includes('RFAA')" value="tower">Tower Hill</option>
                            <option value="wright">Wright Flood</option>
                        </select>

                        <label :for="'desc'+index" class="mt-4 text-[16px] text-custom-light-gray font-medium">Error Description <span class="text-custom-red">*</span></label>
                        <textarea v-model="error.desc" :name="'desc'+index" :id="'desc'+index" class="w-full h-[96px] text-[15px] text-custom-gray border-custom-light border-[1px] rounded-[2px] focus:ring-0 focus:border-custom-light"></textarea>
                    
                        <button v-if="form.errors.length > 1" type="button" @click="deleteError(index)" class="flex gap-2 w-fit mt-2 px-4 py-2 text-[12px] text-custom-red font-semibold border-[1px] border-custom-red rounded-[2px] justify-right hover:bg-custom-red hover:text-white"><TrashIcon class="h-[24px] my-auto" /><span class="my-auto">Delete Error</span></button>
                    </div>
                </div>

                <button @click="addError()" type="button" class="flex gap-2 w-fit px-6 py-2 text-[12px] text-custom-gray font-semibold border-[1px] border-custom-gray rounded-[2px] justify-right hover:bg-custom-gray hover:text-white"><PlusIcon class="h-[24px] my-auto" /><span class="my-auto">Add Additional Carrier Error</span></button>
            </div>

            <input type="submit" class="h-fit w-[90%] bottom-12 absolute cursor-pointer bg-custom-blue text-[15px] text-white font-medium py-2 border-l-[4px] border-b-[5px] border-[#7EA9E8] active:border-custom-blue rounded-[4px] shadow-newdrop active:shadow-none">
        </form>

        <!-- Report Submitted Thank You Msg -->
        <div v-else class="w-[702px] h-fit mx-auto mt-[-100px] grid gap-4 bg-white p-6 rounded-[8px] border-[1px] border-custom-light">
            <h2 class="text-center text-[28px] text-custom-blue font-semibold">Error Report Submitted!!</h2>
            <p class="text-[16px] text-custom-gray">Thank you for helping us maintain Rocket Automation rating platforms. The below carrier errors have been submitted for application <span class="text-custom-blue">{{ form.app_id }}</span>:</p>
        
            <ul class="grid ml-8 text-[16px] text-custom-light-gray font-medium list-disc">
                <li v-for="(error, index) in form.errors" :key="index">{{ this.carriers[error.carrier].name }}</li>
            </ul>

            <p class="text-[16px] text-custom-orange">This page will automatically reload in 10 seconds.</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="w-full md:absolute bottom-0 py-2 px-6 border-t border-custom-light text-center text-[14px] text-custom-dark-blue bg-white">
        <p>© 2023 <a target="_blank" href="https://rocketmga.com">ROCKETMGA</a>   |  ALL RIGHTS RESERVED  |  <a target="_blank" href="https://rocketmga.com/privacy-policy/">PRIVACY POLICY</a></p>
    </div>
</template>

<script>
import loading from '../components/loading.vue'
import textInput from '../components/textInput.vue'

import carriers from '../../assets/carriers.json'

import { PlusIcon, TrashIcon } from '@heroicons/vue/24/outline'

export default {
    name: "Report Error",
    data(){
        return {
            loading: false,
            submitted: false,
            form: {
                app_id: "",
                errors: [
                    {
                        carrier: "",
                        desc: ""
                    }
                ]
            },
            carriers: carriers
        }
    },
    methods: {
        inputChange(id, value, errors){
            this.form[id] = value
        },
        addError(){
            this.form.errors.push({
                carrier: "",
                desc: ""
            })
        },
        deleteError(index){
            this.form.errors.splice(index, 1)
        },
        async submitError(){
            this.loading = true

            // Check Fields
            if(!this.form.app_id){
                this.$alert({
                    title: 'Application Error',
                    text: 'Please enter an application ID.',
                    type: 'warn'
                })

                this.loading = false
                return
            }

            if(!this.form.app_id.includes('RFAA') && !this.form.app_id.includes('RMGA')){
                this.$alert({
                    title: 'Application Error',
                    text: 'Please enter a valid application ID.',
                    type: 'warn'
                })

                this.loading = false
                return
            }

            let valid = true

            this.form.errors.forEach((error, index) => {
                if(!error.carrier){
                    this.$alert({
                        title: "Carrier Report Error",
                        text: "Please select a carrier for error "+(index + 1).toString(),
                        type: 'warn'
                    })

                    valid = false
                } else if(!error.desc){
                    this.$alert({
                        title: "Carrier Report Error",
                        text: "Please enter a description for error "+(index + 1).toString(),
                        type: 'warn'
                    })

                    valid = false
                }
            })

            if(!valid){
                this.loading = false
            } else {
                await axios.post('/api/task/add', this.form)
                .then(response => {
                    this.$alert({
                        title: 'Error Reported',
                        text: 'Error report for application id '+this.form.app_id+' submitted successfully.',
                        type: 'success'
                    })

                    this.loading = false
                    this.submitted = true

                    setTimeout(() => {
                        location.reload()
                    }, 10000)
                })
            }
        }
    },
    components: {
        loading,
        textInput,
        PlusIcon,
        TrashIcon
    }
}
</script>