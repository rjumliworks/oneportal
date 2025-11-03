<template>
    <PageHeader v-if="option == 'create'" title="Create Purchase Request" pageTitle="PR" />
    <PageHeader v-if="option == 'edit'" title="Edit Purchase Request" pageTitle="PR" />
    <PageHeader v-if="option == 'review'" title="Review Purchase Request" pageTitle="PR" />
    <PageHeader v-if="option == 'approve'" title="Approve Purchase Request" pageTitle="PR" />
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
        <div class="file-manager-content w-100 p-4 pb-0" style="height: calc(100vh - 180px); overflow: auto;" ref="box">
            <!-- <Lists :dropdowns="dropdowns"/>         -->
            <form class="customform">
                <BRow>
                    <BCol lg="6" class="mt-2">
                    <div>
                        <b-card  class="bg-light">      
                            <BRow>
                                <BCol lg="6" class="mt-2">
                                    <InputLabel for="division" value="Division" :message="form.errors.division_id"/>
                                    <Multiselect 
                                    :options="dropdowns.divisions" 
                                    v-model="form.division_id"
                                    :searchable="true" label="name"
                                    placeholder="Select Division"/>
                                </BCol>

                                <BCol lg="6" class="mt-2">
                                    <InputLabel value="PR Date" :message="form.errors.purchase_request_date"/>
                                    <TextInput v-model="form.purchase_request_date" type="text" class="form-control"  :light="true" readonly/>
                                </BCol>
                                <BCol lg="6" class="mt-2">
                                    <InputLabel for="unit" value="Unit" :message="form.errors.unit_id"/>
                                    <Multiselect 
                                    :options="units" 
                                    v-model="form.unit_id"
                                    :searchable="true" label="name"
                                    placeholder="Select Unit"/>
                                </BCol>

                                <BCol lg="6" class="mt-2">
                                    <InputLabel for="fund_cluster" value="Fund Cluster" :message="form.errors.fund_cluster_id"/>
                                    <Multiselect 
                                    :options="dropdowns.fund_clusters" 
                                    v-model="form.fund_cluster_id"
                                    :searchable="true" label="name"
                                    placeholder="Select Fund Cluster"/>
                                </BCol>

                                <BCol lg="12" class="mt-2">
                                    <InputLabel value="PAP Code" :message="form.errors.procurement_code_ids"/>
                                    <Multiselect 
                                    :options="dropdowns.procurement_codes" 
                                    v-model="form.procurement_code_ids"
                                    :searchable="true" label="code"
                                    placeholder="Select PAP CODE"
                                    mode="tags"
                                    />
                                
                                </BCol>
                            </BRow>    
                        </b-card>
                    </div>
            </BCol>
            <BCol lg="6" class="mt-2">
                    <div>
                        <b-card  class="bg-light">             
                            <BRow>
                                <BCol lg="12" class="mt-2">
                                    <InputLabel for="purchase_request_purpose" value="Request Purpose" :message="form.errors.purchase_request_purpose"/>
                                    <b-form-textarea
                                        id="textarea"
                                        v-model="form.purchase_request_purpose"
                                        placeholder="Enter your request purpose"
                                        rows="4"
                                        max-rows="10"></b-form-textarea>
                                </BCol>

                                <BCol lg="12" class="mt-2" v-if="option == 'review'">
                                    <InputLabel for="purchase_request_title" value="Request Title" :message="form.errors.purchase_request_title"/>
                                    <b-form-textarea
                                        id="textarea"
                                        v-model="form.purchase_request_title"
                                        placeholder="Enter your request purpose"
                                        rows="2"
                                        max-rows="10"></b-form-textarea>
                                </BCol>

                                
                            </BRow>    
                        </b-card>
                    </div>
            </BCol>
     
            </BRow>
                <BRow>

                    <BCol lg="3" class="mt-2 mb-2"   >
                        <b-button :disabled="!form.division_id || !form.unit_id || !form.fund_cluster_id  || !form.purchase_request_purpose"  @click="openAddItem()" variant="light" block class="bg-success w-75 text-white">Add Item</b-button>
                    </BCol>
                    <!-- <div  class="bg-info font-weight text-white" v-if="option == 'review_purchase_request'">ITEM LIST</div> -->
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr class="fs-11">
                                    <th>Item No.</th>
                                    <th>Unit</th>
                                    <th>Item Description</th>
                                    <th>Quantity</th>
                                    <th>Unit Cost</th>
                                    <th>Total Cost</th>
                                    <th></th>
                                </tr>
                            </thead>     
                            <tbody>
                                <tr v-for="(item, index) in form.items" :key="index">
                                <td>{{ index + 1 }}</td>
                              <td>
                                {{ item.item_quantity > 1 ? item.item_unit_type?.name_long : item.item_unit_type?.name_short }}
                                </td>
                                    <td>
                                        <div v-html="item.item_description"></div>
                                    </td>
                                    <td>{{ item.item_quantity}}</td>
                                    <td>{{ formatCurrency(item.item_unit_cost) }}</td>
                                    <td>{{ formatCurrency(item.total_cost) }}</td>

                                    <td>
                                    <b-button @click="removeItem(index)" variant="success" size="sm" class="me-2">
                                         <i class="ri-edit-2-line"></i>
                                    </b-button>

                                    <b-button @click="removeItem(index)" variant="danger" size="sm" >
                                         <i class="ri-delete-bin-line"></i>
                                    </b-button>
                                    
                                    </td>
                                </tr>
                                <tr >
                                    <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                    <td>
                                        <strong>{{ formatCurrency(totalCostSum) }}</strong>
                                    </td>
                                    <td></td>
                                </tr>
                                </tbody>
                        </table>
                    </div>

                    <BCol lg="12" class="mt-5">
                    <div>
                        <b-card title="ASSIGNATOREES"  class="bg-light">             
                            <BRow>
                                <BCol lg="6" class="mt-2">
                                    <InputLabel for="requested_by" value="Requested By" :message="form.errors.requested_by_id"/>
                                    <Multiselect 
                                    :options="dropdowns.requesters" 
                                    v-model="form.requested_by_id"
                                    :searchable="true" label="name"
                                    placeholder="Select Requester"/>
                                </BCol>
                                <BCol lg="6" class="mt-2">
                                    <InputLabel for="approved_by" value="Approved By" :message="form.errors.approved_by_id"/>
                                    <Multiselect 
                                    :options="dropdowns.approvers" 
                                    v-model="form.approved_by_id"
                                    :searchable="true" label="name"
                                    placeholder="Select Approver"/>
                                </BCol>
                                
                            </BRow>    
                        </b-card>
                    </div>
            </BCol>

            <BCol lg="3" class="mt-2 mb-2" v-if="option == 'create'">
                <b-button :disabled="!form.division_id || !form.unit_id || !form.fund_cluster_id  || !form.purchase_request_purpose || !form.requested_by_id || !form.approved_by_id || !form.items.length > 0" 
                 @click="submit('ok')"  variant="light" block class="bg-success w-75 text-white">Save</b-button>
            </BCol>

            <BCol lg="3" class="mt-2 mb-2" v-if="option == 'edit'">
                <b-button @click="update(form)"  variant="light" block class="bg-success w-75 text-white">Update</b-button>
            </BCol>

            <BCol lg="3" class="mt-2 mb-2" v-if="option == 'review'">
                <b-button @click="review(form)"  variant="light" block class="bg-success w-75 text-white">Confirm</b-button>
            </BCol>

            <BCol lg="3" class="mt-2 mb-2" v-if="option == 'approve'">
                <b-button @click="approve(form)"  variant="light" block class="bg-success w-75 text-white">Approve</b-button>
            </BCol>
            <BCol lg="3" class="mt-2 mb-2">
                <b-button @click="goBackPage()" style="background-color: grey" block class=" w-75 text-white">Back</b-button>
            </BCol>
                </BRow>

            </form>
        </div>
    </div>

    <Create :dropdowns="dropdowns"   ref="create"/>
</template>
<script>

import PageHeader from '@/Shared/Components/PageHeader.vue';
import { useForm } from '@inertiajs/vue3';
import Multiselect from "@vueform/multiselect";
import InputError from '@/Shared/Components/Forms/InputError.vue';
import InputLabel from '@/Shared/Components/Forms/InputLabel.vue';
import TextInput from '@/Shared/Components/Forms/TextInput.vue';

export default {
    components: {PageHeader, InputError, InputLabel, TextInput, Multiselect },
    props: ['dropdowns' ],
    data(){
        return {
                currentUrl: window.location.origin,
                form: useForm({
                    id: null,
                    purchase_request_number:null,
                    purchase_request_purpose: null, 
                    purchase_request_title:null,  
                    purchase_request_date: this.getCurrentDate(),
                    division_id : null,
                    unit_id : null,
                    fund_cluster_id: null,
                    items: null,
                    requested_by_id: null,
                    approved_by_id: null,
                    procurement_code_ids: null,
                    status_id: 1,
                }),
                action: null,
                showModal: false,
                units: [],
                unit_type : null,
            }
    },

    watch: {
         "form.division_id"(newVal){
            if(newVal){
                this.getUnits(newVal);
            }
        }
    },

    mounted(){
   
    },

    methods: {
        
        getCurrentDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
            const day = String(today.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            }).format(value);
        },

        getUnits(division_id) {
            axios.get('/faims/procurements/create',{
                params : {
                    code : division_id,
                    option: 'units'
                }
            })
            .then(response => {
                if(response){
                    this.units = response.data;   
                }
            })
            .catch(err => console.log(err));
        },
    }
}
</script>