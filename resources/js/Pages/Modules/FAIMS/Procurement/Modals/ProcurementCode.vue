<template>
    <b-modal v-model="showModal" header-class="p-3"  :title="editable ? 'Update PAP' : 'New PAP'" size="lg" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop >
        <form class="customform">
           <BRow>
            <BCol lg="4" class="mt-2">
                <InputLabel value="Code" />
                <TextInput v-model="form.code" type="text" class="form-control" placeholder="Enter code"  />
            </BCol>
            <BCol lg="4" class="mt-2">
                <InputLabel value="Allocated Budget" />
                <Amount @amount="amount" />
            </BCol>

            <BCol lg="4" class="mt-2">
                <InputLabel value="Year" />
                <Multiselect 
                :options="yearOptions" 
                v-model="form.year"
                :searchable="true" label="text"
                placeholder="Year"/>
            </BCol>

            <BCol lg="12" class="mt-2">
                    <InputLabel for="app_types" value="App Type"/>
                    <Multiselect 
                    :options="app_types" 
                    v-model="form.app_type_id"
                    :searchable="true" label="name"
                    placeholder="Select End Users"/>
            </BCol>

            <BCol lg="12" class="mt-2">
                    <InputLabel for="end_users" value="End Users"/>
                    <Multiselect 
                    :options="end_users" 
                    v-model="form.end_user_ids"
                    :searchable="true" label="name"
                    mode="tags"
                    placeholder="Select End Users"/>
            </BCol>

            <BCol lg="12" class="mt-2">
                <InputLabel for="mode_of_procurement" value="Mode of Procurement"/>
                <Multiselect 
                :options="mode_of_procurements" 
                v-model="form.mode_of_procurement_id"
                :searchable="true" label="name"
                placeholder="Mode of Procurement"/>
            </BCol>
            <BCol lg="12" class="mt-2">
                <InputLabel value="Project Description/Title" />
                <textarea
                    id="description"
                    v-model="form.title"
                    class="form-control"
                    rows="5"
                    placeholder="Enter project description/title"
                    ></textarea>
            </BCol>
        </BRow>

        </form>
   
          <template v-slot:footer>
            <b-button @click="hide()" variant="light" block>Close</b-button>
            <b-button @click="savePAP(form)" variant="success"  block>Save</b-button>
        </template>
        
    </b-modal>
</template>
<script>
import { useForm } from '@inertiajs/vue3';
import Multiselect from "@vueform/multiselect";
import InputError from '@/Shared/Components/Forms/InputError.vue';
import InputLabel from '@/Shared/Components/Forms/InputLabel.vue';
import TextInput from '@/Shared/Components/Forms/TextInput.vue';
import Amount from '@/Shared/Components/Forms/Amount.vue';

export default {
    components: { InputError, InputLabel, TextInput, Multiselect ,Amount },
    props:['mode_of_procurements' , 'app_types' , 'end_users'],
    data(){
        return {
            currentUrl: window.location.origin,
            form: useForm({
                id: null,
                title: null,
                code: null,
                allocated_budget: null,
                year: null,
                end_user_ids : [],
                app_type_id: null,
                mode_of_procurement_id: null,
            }),   
            showModal: false,
            editable: false,
        }
    },

    mounted() {
        this.generateYearOptions();
    },

    methods: { 

        amount(val){
            this.form.allocated_budget = this.cleanCurrency(val);
        },

        cleanCurrency(value) {
            if (!value) return 0;

            // Remove ₱, commas, and spaces
            const cleaned = value.toString().replace(/[^0-9.]/g, '');

            return parseFloat(cleaned);
        },


        generateYearOptions() {
            const currentYear = new Date().getFullYear();
            const startYear = currentYear - 8;
            const endYear = currentYear + 2;

            this.yearOptions = [];

            for (let year = endYear; year >= startYear; year--) {
                this.yearOptions.push({ value: year, text: year.toString() });
            }

            // Set default selected year to current year
            this.form.year = currentYear;
        },

        show(){
            this.editable = false;
            this.form.reset();
            this.showModal = true;
        },

        edit(data){
            this.editable= true;
            this.form.id = data.id;
            this.form.title = data.title;
            this.form.code = data.code;
            this.form.allocated_budget = data.allocated_budget;
            this.form.app_type_id = data.app_type_id;
            this.form.mode_of_procurement_id = data.mode_of_procurement.id;
            this.showModal = true;
        },
      
        hide(){
            this.form.reset();
            this.showModal = false;
        },

        savePAP(data){ 
            if(this.editable){
                // this.form.put(`/faims/procurement-codes/`+data.id,{
                //     preserveScroll: true,
                //     onSuccess: (response) => {
                //         this.$emit('update', true);
                //         this.form.reset();
                //         this.hide();
                //     }
                // });
                console.log('edit');
            }else{
                this.form.post('/faims/procurement-codes',{
                preserveScroll: true,
                onSuccess: (response) => {
                    this.$emit('add',true);
                    this.hide();
                },
            });
            }
        },


       
    }
}
</script>