<template>
    <!-- Loading -->
    <div id="loading" :class="loading ? '' : 'hidden'" class="w-screen h-screen grid bg-[#3F3F3F] bg-opacity-[26%] justify-items-center z-50 fixed">
        <loading class="m-auto" />
    </div>

    <div class="w-full h-full md:w-screen md:h-screen grid md:grid-cols-2 bg-white">
        <div class="grid relative bg-custom-light-blue-bg bg-opacity-30">
            <!--Background Image-->
            <div class="hidden md:block absolute top-0 right-0 z-0">
                <img src="../../assets/1.svg" alt="Abstract Background Image" class="opacity-70">
            </div>

            <div class="z-10 grid w-fit h-fit mx-auto gap-4 md:gap-10 px-6 md:px-0">
                <!--Rocket Automation Logo-->
                <img src="../../assets/logo.png" alt="Rocket Automation Logo" class="mx-auto h-[38px] mt-24 md:mt-36">

                <!-- Instructions -->
                <div class="grid gap-10 w-fit h-fit mx-auto">
                    <ul class="w-fulls md:w-[600px] grid gap-2 ml-6 md:ml-4 list-disc text-[12px] md:text-[16px] text-custom-light-gray font-medium">
                        <li>Update Product Type if this is/these are not (a) flood quote error(s).</li>
                        <li>Enter Application ID shown in Rocket Automation or Rocket MGA.</li>
                        <li>Please enter all carrier errors related to this application.</li>
                        <li>Upload relevant images related to each error. You can also paste images into the error description box to upload them</li>
                        <li>Provide information relevant to the quote or error.</li>
                    </ul>
                </div>
                
                <!--Login Illustration-->
                <img src="../../assets/report-error.png" alt="Login Illustration" class="mx-auto">
            </div>
        </div>

        <div class="grid relative">
            <!-- Error Submission Form -->
            <form v-if="!submitted" @submit.prevent="submitError" class="h-fit grid gap-6 justify-items-center px-6 md:px-0">
                <!-- Header -->
                <h1 class="mt-10 text-[28px] text-custom-gray font-semibold">Error Details</h1>

                <div class="w-full md:w-[422px] h-fit grid gap-4">
                    <!-- Product Type -->
                    <div class="w-full h-[40px] grid grid-cols-2 text-[16px] text-custom-gray font-medium border-[1px] border-custom-light rounded-[2px]">
                        <div @click="floodProduct = false" :class="!floodProduct ? 'bg-custom-gray text-white' : ''" class="w-full h-full grid cursor-pointer">
                            <p class="m-auto">Home Quote</p>
                        </div>

                        <div @click="floodProduct = true" :class="floodProduct ? 'bg-custom-blue text-white' : ''" class="w-full h-full grid cursor-pointer">
                            <p class="m-auto">Flood Quote</p>
                        </div>
                    </div>

                    <textInput @inputUpdate="inputChange" :inputValue="form.app_id" :id="'app_id'" :label="'Application ID'" :placeholderText="'RFAA000000 / RMGA00000'" />

                    <!-- Application Errors -->
                    <div class="w-full h-[450px] grid gap-8 overflow-y-scroll">
                        <div v-for="(error, index) in form.errors" :key="index" class="w-full h-fit grid pr-4">
                            <!-- Rater Select -->
                            <div v-if="!floodProduct" class="w-full h-fit grid">
                                <label class="text-custom-light-gray text-[16px] font-medium">Rater <span class="text-custom-red">*</span></label>
                                <VueMultiselect
                                    v-model="error.rater"
                                    :options="raters"
                                    :searchable="false"
                                    :close-on-select="true"
                                    :allow-empty="false"
                                    :show-labels="false"
                                    :showNoResults="false"
                                    label="name"
                                    track-by="code"
                                    placeholder="Select a the rater used for the carrier"
                                />
                            </div>

                            <!-- Carrier Select -->
                            <label :class="!floodProduct ? 'mt-4' : ''" class="text-custom-light-gray text-[16px] font-medium">Carrier <span class="text-custom-red">*</span></label>
                            <VueMultiselect
                                v-model="error.carrier"
                                :options="options"
                                :searchable="true"
                                :close-on-select="true"
                                :allow-empty="false"
                                :show-labels="false"
                                :showNoResults="false"
                                label="name"
                                track-by="code"
                                placeholder="Select a carrier for this error"
                            />

                            <!-- File Uploads -->
                            <label class="mt-4 text-custom-light-gray text-[16px] font-medium">Attachments</label>
                            <input @change="uploadFile($event, index)" type="file" multiple accept=".png, .jpg, .jpeg" :name="'error'+index+'uploads'" :id="'error'+index+'uploads'" class="hidden">
                            <div class="w-full grid grid-cols-5">
                                <div @click="clickUpload(index)" class="col-span-2 md:col-span-3 flex gap-4 w-full h-[48px] px-4 text-white font-medium bg-custom-blue rounded-[2px] cursor-pointer">
                                    <ArrowUpTrayIcon class="h-[18px] my-auto" />
                                    <p class="flex gap-2 text-[13px] my-auto">Upload <span class="hidden md:block">Attachments</span></p>
                                </div>

                                <!-- Attachment List -->
                                <div class="col-span-3 md:col-span-2 h-[48px] grid px-2 text-[12px] text-custom-gray overflow-x-clip overflow-y-scroll">
                                    <div v-for="(upload, uploadIndex) in error.uploads" :key="uploadIndex" class="w-fit h-fit flex gap-2">
                                        <XCircleIcon @click="removeUpload(index, uploadIndex)" class="h-[1.25em] my-auto text-custom-red cursor-pointer" />
                                        <p class="truncate mr-[2px] min-h-[1.25em] my-auto">{{ upload.name }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Error Description -->
                            <label class="mt-4 text-custom-light-gray text-[16px] font-medium">Description <span class="text-custom-red">*</span></label>
                            <textarea v-model="error.desc" @paste="pasteEvent($event, index)" class="h-[96px] text-custom-gray border-[1px] border-custom-light rounded-[2px] focus:ring-0 focus:border-custom-light"></textarea>
                            
                            <div class="w-full h-fit flow-root">
                                <!-- Delete Button -->
                                <button v-if="form.errors.length > 1" type="button" @click="deleteError(index)" class="float-right flex gap-2 w-fit mt-4 px-4 py-2 text-[12px] text-custom-red font-semibold border-[1px] border-custom-red rounded-[2px] justify-right hover:bg-custom-red hover:text-white"><TrashIcon class="h-[24px] my-auto" /><span class="my-auto">Delete Error</span></button>
                            </div>
                        </div>
                    </div>

                    <!-- Add Additional Errors -->
                    <div class="w-full h-fit flow-root">
                        <button @click="addError()" type="button" class="float-right p-2 w-fit flex gap-2 border-[1px] border-custom-gray rounded-[2px] hover:bg-custom-gray hover:text-white"><PlusIcon class="h-[20px] my-auto" />Add Additional Carriers</button>
                    </div>

                    <!-- Submit Button -->
                    <input type="submit" class="w-full py-2 text-[16px] text-white font-semibold bg-custom-bluerounded-[2px] bg-custom-blue border-l-[4px] border-b-[5px] border-[#7EA9E8] active:border-custom-blue rounded-[2px] shadow-newdrop active:shadow-none cursor-pointer">
                </div>
            </form>

            <!-- Submitted View -->
            <div v-else class="w-[80%] h-fit mx-auto mt-24 grid gap-4 bg-white p-6 rounded-[8px] border-[1px] border-custom-light shadow-newdrop">
                <h2 class="text-center text-[28px] text-custom-blue font-semibold">Error Report Submitted!!</h2>
                <p class="text-[16px] text-custom-gray">Thank you for helping us maintain Rocket Automation rating platforms. The below carrier errors have been submitted for application <span class="text-custom-blue">{{ form.app_id }}</span>:</p>
            
                <ul class="grid ml-8 text-[16px] text-custom-light-gray font-medium list-disc">
                    <li v-for="(error, index) in form.errors" :key="index">{{ this.carriers[error.carrier].name }}</li>
                </ul>

                <p class="text-[16px] text-custom-orange">This page will automatically reload in 10 seconds.</p>
            </div>

            <div class="grid w-full h-[50px] mt-10 md:mt-0 px-4 md:px-0 justify-items-center md:absolute md:bottom-0 border-t-2">
                <div class="w-fit h-fit flex my-auto text-[14px] text-custom-light-gray text-center font-medium uppercase">
                    <p><a href="https://rocketflood.com/legal/terms-of-use">All Rights Reserved</a> | <a href="https://rocketflood.com">Rocket Flood</a> | <a href="https://rocketflood.com/legal/privacy-policy">Privacy Policy</a></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import loading from '../components/loading.vue'
import textInput from '../components/textInput.vue'

import carriers from '../../assets/carriers.json'
import raters from '../../assets/raters.json'

import { PlusIcon, TrashIcon, ArrowUpTrayIcon, XCircleIcon } from '@heroicons/vue/24/outline'
import VueMultiselect from 'vue-multiselect'

export default {
    name: "Report Error",
    data(){
        return {
            ready: false,
            loading: false,
            submitted: false,
            floodProduct: true,
            form: {
                app_id: "",
                errors: [
                    {
                        carrier: "",
                        rater: "",
                        desc: "",
                        uploads: []
                    }
                ]
            },
            carriers: carriers,
            raters: raters,
            options: []
        }
    },
    async created(){
        this.loading = true
        const keys = Object.keys(this.carriers)

        keys.forEach(key => {
            this.options.push({
                "name": this.carriers[key].name,
                "code": key
            })
        })

        this.ready = true
        this.loading = false
    },
    methods: {
        clickUpload(index){
            document.getElementById(`error${index}uploads`).click();
        },
        removeUpload(index, uploadIndex){
            this.form.errors[index].uploads.splice(uploadIndex, 1)
        },
        async uploadFile(e, index){
            const keys = Object.keys(e.target.files)

            keys.forEach(key => {
                const type = e.target.files[key].name.split('.').pop()
                const newFile = new File([e.target.files[key]], this.form.app_id+(!this.floodProduct ? this.form.errors[index].rater.code : '')+"_"+this.form.errors[index].carrier.code+"_upload"+this.form.errors[index].uploads.length+"."+type)

                this.form.errors[index].uploads.push(newFile)
            })
        },
        inputChange(id, value, errors){
            this.form[id] = value
        },
        addError(){
            this.form.errors.push({
                carrier: "",
                rater: "",
                desc: "",
                uploads: []
            })
        },
        deleteError(index){
            this.form.errors.splice(index, 1)
        },
        async pasteEvent(e, index){
            if(e.clipboardData.items[0].getAsFile()){
                const type = e.clipboardData.items[0].getAsFile().name.split('.').pop()
                const newFile = new File([e.clipboardData.items[0].getAsFile()], this.form.app_id+this.form.errors[index].rater+this.form.errors[index].carrier.code+"upload"+this.form.errors[index].uploads.length+"."+type)

                this.form.errors[index].uploads.push(newFile)
            }
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
                }
                else if(!this.floodProduct && !error.rater){
                    this.$alert({
                        title: "Carrier Report Error",
                        text: "Please select rater for error "+(index + 1).toString(),
                        type: 'warn'
                    })

                    valid = false
                } 
                else if(!error.desc){
                    this.$alert({
                        title: "Carrier Report Error",
                        text: "Please enter a description for error "+(index + 1).toString(),
                        type: 'warn'
                    })

                    valid = false
                }

                // Update Error Carrier from Object
                if(error.carrier){
                    error.carrier = error.carrier.code
                }

                // Update Error Rater from Object
                if(error.rater){
                    error.rater = error.rater.code
                }
            })

            if(!valid){
                this.loading = false

                this.form.errors.forEach(error => {
                    if(error.carrier){
                        error.carrier = {
                            "name": carriers[error.carrier].name,
                            "code": error.carrier
                        }
                    }
                })
            } else {
                // Set Product type if not flood
                if(!this.floodProduct){
                    this.form.product = 'HOME'
                }

                this.form.errors.forEach(error => {
                    error.type = carriers[error.carrier].type
                })

                const header = {
                    headers: {'content-type': 'multipart/form-data'}
                }

                await axios.post('/api/task/add', this.form, header)
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
        VueMultiselect,
        PlusIcon,
        TrashIcon,
        ArrowUpTrayIcon,
        XCircleIcon
    }
}
</script>