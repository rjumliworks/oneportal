<template>
    <Head title="Installation"></Head>
    <div class="d-flex flex-column min-vh-100 justify-content-center align-items-center">
        <div class="text-center mb-2">
            <a to="/" class="d-block auth-logo">
                <img src="/images/logo-sm.png" alt="" height="50" class="auth-logo-dark mx-auto" />
            </a>
            <p class="font-size-11 mt-3">{{member.member.name}}<br> One-Stop Laboratory Services for Global Competitiveness</p>
          
        </div>
        
        <div class="mb-5 customform" style="width: 900px;">
            <div class="text-center mb-2">
                <p class="text-muted">Please select all laboratories offered by the agency from the dropdown menu.</p>
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label" for="address">Name  <span class="text-muted">(This will be used in printing reports)</span></label>
                    <TextInput type="text" v-model="form.name" class="form-control" :light="true"/>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="address">Acronym <span class="text-muted">(This will be used in printing reports)</span></label>
                    <TextInput type="text" v-model="form.acronym" class="form-control" :light="true"/>
                </div>
                <div class="col-md-12">
                    <hr class="text-muted mt-2 mb-2"/>
                </div>
                <div class="col-md-10">
                    Would you like to use the current year when generating sample code? <span class="text-muted fs-12">(CHE-2024-00001)</span>
                </div>
                <div class="col-md-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio" id="customRadio1" class="custom-control-input me-2" :value="true" v-model="form.samplecode_year">
                                <label class="custom-control-label fw-normal fs-12" for="customRadio1">Yes</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio" id="customRadio2" class="custom-control-input me-2" :value="false" v-model="form.samplecode_year">
                                <label class="custom-control-label fw-normal fs-12" for="customRadio2">No</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <hr class="text-muted mt-n2 mb-n3"/>
                </div>
                <div class="col-md-12">
                        <Multiselect label="name"
                        :options="laboratories" 
                        :searchable="true" 
                        object
                        :close-on-select="true"
                        v-model="laboratory"
                        mode="multiple"
                        placeholder="Select Laboratory"/>
                </div>
                <div class="col-md-12">
                    <div class="row g-2" v-for="(list,index) in form.laboratories" v-bind:key="index">
                        <div class="col-md-5">
                            <TextInput type="text" :value="list.name" class="form-control" readonly/>
                        </div>
                        <div class="col-md-2">
                            <TextInput type="text" v-model="list.tsr_count" placeholder="TSR count" class="form-control"/>
                        </div>
                        <div class="col-md-2">
                            <TextInput type="text" v-model="list.sample_count" placeholder="Sample count" class="form-control"/>
                        </div>
                        <div class="col-md-2">
                            <TextInput type="text" v-model="list.report_count" placeholder="Report count" class="form-control"/>
                        </div>
                        <div class="col-md-1">
                            <button @click="remove(list.value)" class="btn btn-md btn-soft-danger float-end" type="button"><i class="ri-close-circle-fill align-bottom"></i></button>
                        </div>
                    </div>
                </div>
            </div>
             <center>
                <button @click="submit()" type="button" class="mt-4 mb-4 btn w-lg btn-primary">Submit</button>
            </center>
        </div>
        
        <div class="text-center">
            <div>
                <p>© DOST-ONELAB <i class='bx bxs-heart text-danger'></i> 2024</p>
            </div>
        </div>
    </div>
</template>
<script>
import { useForm } from '@inertiajs/vue3';
import Multiselect from "@vueform/multiselect";
import TextInput from '@/Shared/Components/Forms/TextInput.vue';
export default {
    components: { Multiselect, TextInput },
    props:['member','laboratories'],
    layout: null,
    data(){
        return {
            currentUrl: window.location.origin,
            laboratory: [],
            form: useForm({
                name: this.member.member.name,
                acronym: this.member.name,
                laboratories: [],
                laboratory_id: this.member.id,
                samplecode_year: null,
            }),
        }
    },
    watch: {
        'laboratory'(){
            this.form.laboratories = this.laboratory.map(item => ({
                value: item.value,
                name: item.name,
                tsr_count: null,
                sample_count: null,
                report_count: null
            }));
        },
    },
    computed: {
        leftColumn() {
            const middleIndex = Math.ceil(this.laboratory.length / 2);
            return this.laboratory.slice(0, middleIndex);
        },
        rightColumn() {
            const middleIndex = Math.ceil(this.laboratory.length / 2);
            return this.laboratory.slice(middleIndex);
        }
    },
    methods: {
        remove(value){
            this.laboratory = this.laboratory.filter(item => item.value !== value);
        },
        submit(){
            this.form.post('/install',{
                preserveScroll: true,
                onSuccess: (response) => {
                    this.$inertia.get('/dashboard');
                },
            });
        },
    }
}
</script>
<style>
.row {
  display: flex;
}

.item {
  margin: 5px;
  padding: 10px;
  border: 1px solid #ccc;
}
</style>