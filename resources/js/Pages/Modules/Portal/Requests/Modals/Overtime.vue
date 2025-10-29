<template>
     <!-- style="--vz-modal-width: 750px;" -->
    <b-modal v-model="showModal" style="--vz-modal-width: 700px;" header-class="p-3 bg-light" title="Render Overtime Service" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop>
            
        <form class="customform">
            <BRow class="g-3 mdiv">
                <BCol lg="12" class="mt-3">
                    <InputLabel for="name" value="Date" :message="form.errors.date_type"/>
                    <Multiselect :options="['Single Day','Range']" v-model="dateType" @input="handleInput('date_type')" placeholder="Select Date type"/>
                    <!-- 'Multiple Dates (non-continuous)' -->
                </BCol>
                <template v-if="dateType == 'Single Day'">
                    <BCol lg="6" class="mt-1">
                        <InputLabel for="name" value="Inclusive Dates"  :message="form.errors.dates"/>
                        <flat-pickr v-model="date" :config="single" placeholder="Select date" class="form-control flatpickr-input" style="min-height: 38.4px !important; border-color: #e9ebec; background-color: #f5f6f7;" @input="handleInput('dates')"></flat-pickr>
                    </BCol>
                    <BCol lg="6" class="mt-1">
                        <InputLabel for="name" value="Time of Day"  :message="form.errors.date"/>
                        <Multiselect :options="['Whole Day','AM','PM']" label="name" v-model="form.timeOfDay" placeholder="Select Date type"/>
                    </BCol>
                </template>
                <template v-if="dateType == 'Range'">
                    <BCol lg="12" class="mt-1">
                        <div class="d-flex">
                            <div style="width: 100%;">
                                <InputLabel for="name" value="Inclusive Dates"  :message="form.errors.dates"/>
                                <flat-pickr v-model="date" :config="range" placeholder="Select date" class="form-control flatpickr-input" style="min-height: 38.4px !important; border-color: #e9ebec; background-color: #f5f6f7;" @input="handleInput('dates')"></flat-pickr>
                            </div>
                            <div class="flex-shrink-0">
                                <b-button @click="openDates()" style="margin-top: 20px;" variant="light" class="waves-effect waves-light ms-1"><i class="ri-calendar-fill"></i></b-button>
                            </div>
                        </div>
                    </BCol>
                </template>
                <!-- <template v-if="dateType == 'Multiple Dates (non-continuous)'">
                    <BCol lg="12" class="mt-1">
                        <div class="d-flex">
                            <div style="width: 100%;">
                                <InputLabel for="name" value="Inclusive Dates"  :message="form.errors.dates"/>
                                <flat-pickr v-model="date" :config="multiple" placeholder="Select dates" class="form-control flatpickr-input" style="min-height: 38.4px !important; border-color: #e9ebec; background-color: #f5f6f7;"></flat-pickr>
                            </div>
                            <div class="flex-shrink-0">
                                <b-button @click="openDates()" style="margin-top: 20px;" variant="light" class="waves-effect waves-light ms-1"><i class="ri-calendar-fill"></i></b-button>
                            </div>
                        </div>
                    </BCol>
                </template> -->
                <BCol lg="12" class="mt-0"><hr class="text-muted"/></BCol>
                <BCol lg="12" class="mt-1">
                    <Textarea id="name" v-model="form.purpose" type="text" rows="3" placeholder="Add purpose of the request" class="form-control" :class="{ 'is-invalid': form.errors.purpose }" @input="handleInput('purpose')" :light="true"/>
                </BCol>
            </BRow>
        </form>
        <template v-slot:footer>
            <b-button @click="hide()" variant="light" block>Cancel</b-button>
            <b-button @click="submit('ok')" variant="primary" :disabled="form.processing" block>Submit</b-button>
        </template>
    </b-modal>
</template>
<script>
import { useForm } from '@inertiajs/vue3';
import flatPickr from "vue-flatpickr-component";
import Multiselect from "@vueform/multiselect";
import InputLabel from '@/Shared/Components/Forms/InputLabel.vue';
import Textarea from '@/Shared/Components/Forms/Textarea.vue';
export default {
    components: { Multiselect, InputLabel, Textarea, flatPickr },
    props: ['dropdowns'],
    data(){
        return {
            currentUrl: window.location.origin,
            form: useForm({
                timeOfDay: 'Whole Day',
                dates: [],
                purpose: null,
                document: null,
                date_type: null,
                option: 'cto'
            }),
            date: null,
            single:{
                mode: "single",
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'F j, Y',
                minDate: new Date().setDate(new Date().getDate() + 1),
                disable: [
                    function(date) {
                        return (date.getDay() === 0 || date.getDay() === 6);
                    }
                ]
            },
            range: {
                mode: "range",
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'F j, Y',
                minDate:'',
            },
            multiple: {
                mode: "multiple",
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'F j, Y',
                minDate: new Date().setDate(new Date().getDate() + 1),
                disable: [
                    function(date) {
                        return (date.getDay() === 0 || date.getDay() === 6);
                    }
                ]
            },
            dateType: null,
            showModal: false
        }
    },
    computed: {
        formattedDate() {
            if (!this.date) return '';
            if (Array.isArray(this.date)) {
                return this.date
                    .map(d => new Date(d).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }))
                    .join(' to '); 
            }
            return new Date(this.date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        totalDays() {
            return this.form.dates.reduce((sum, d) => {
            if (d.timeOfDay === 'AM' || d.timeOfDay === 'PM') {
                return sum + 0.5
            }
            if (d.timeOfDay === 'Whole Day') {
                return sum + 1
            }
            return sum
            }, 0)
        }
    },
    watch: {
        date(newVal) {
            if (!newVal){
                this.form.dates = [];
                return;
            }
            this.form.borrowers = [];
            
            if (this.dateType === 'Single Day') {
                this.form.dates = [{
                    date: this.formatDate(newVal),
                    timeOfDay: this.form.timeOfDay || 'Whole Day'
                }];
            }else if (this.dateType === 'Range') {
                const parts = newVal.split(' to ');
                if (parts.length === 2) {
                    const start = new Date(parts[0]);
                    const end = new Date(parts[1]);
                    const datesInRange = [];
                    for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                        if (d.getDay() !== 0 && d.getDay() !== 6) {
                            datesInRange.push({
                                date: this.formatDate(new Date(d)),
                                timeOfDay: 'Whole Day'
                            });
                        }
                    }
                    this.form.dates = datesInRange;
                }
            }else if (this.dateType === 'Multiple Dates (non-continuous)') {
                const dateStrings = newVal.split(',').map(str => str.trim());
                this.form.dates = dateStrings.map(d => ({
                    date: this.formatDate(new Date(d)),
                    timeOfDay: 'Whole Day'
                }));
            }
        },
        'form.timeOfDay'(val) {
            if (this.dateType === 'Single Day' && this.form.dates?.length === 1) {
                this.form.dates[0].timeOfDay = val;
            }
        }
    },
    methods: { 
        show(data){
            this.selected = data;
            this.showModal = true;
        },
        submit(){
            this.form.date_type = this.dateType;
            this.form.post('/requests',{
                preserveScroll: true,
                onSuccess: (response) => {
                    this.$emit('update',true);
                    this.form.clearErrors();
                    this.form.reset();
                    this.hide();
                },
            });
        },
        formatDate(date) {
            const d = new Date(date);
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        formatDateWithWeekday(dateString) {
            const date = new Date(dateString);
            const day = date.getDate();
            const month = date.toLocaleString('en-US', { month: 'short' });
            const year = date.getFullYear();
            const weekday = date.toLocaleString('en-US', { weekday: 'long' });
            return `${month} ${day}, ${year} (${weekday})`;
        },
        openDates(){
            this.$refs.dates.show(this.form.dates);
        },
        handleInput(field) {
            this.form.errors[field] = false;
        },
        hide(){
            this.showModal = false;
        }
    }
}
</script>
<style scoped>
.multiselect__option--disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>