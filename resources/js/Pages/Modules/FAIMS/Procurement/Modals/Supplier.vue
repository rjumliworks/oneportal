<template>
    <b-modal v-model="showModal" header-class="p-3"  :title="editable ? 'Update Supplier' : 'New Supplier'" size="lg" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop >
        <form class="customform">
           <BRow>
            <BCol lg="6" class="mt-2">
                <InputLabel value="Company/ Business Name" />
                <TextInput v-model="form.name" type="text" class="form-control" placeholder="Enter Supplier name"  />
            </BCol>
        </BRow>

        <!-- <BRow>
        

        <InputLabel value="Attachments" class="text-muted text-center mt-5"/>
             <div>
                <table class="table mt-3" v-for="attachment in attachments" :key="attachment.id">
                    <thead>
                        <th>Title</th>
                        <th>Path</th>
                    </thead>
                    <tbody>
                        <td>{{ attachment.name }}</td>
                        <td>{{  attachment.path }}</td>
                    </tbody>
                </table>
            </div>
        </BRow> -->

        </form>
   
          <template v-slot:footer>
            <b-button @click="hide()" variant="light" block>Close</b-button>
            <b-button @click="saveSupplier(form)" variant="success"  block>Save</b-button>
        </template>
        
    </b-modal>
</template>
<script>
import { useForm } from '@inertiajs/vue3';
import Multiselect from "@vueform/multiselect";
import InputError from '@/Shared/Components/Forms/InputError.vue';
import InputLabel from '@/Shared/Components/Forms/InputLabel.vue';
import TextInput from '@/Shared/Components/Forms/TextInput.vue';

export default {
    components: { InputError, InputLabel, TextInput, Multiselect  },
    props:[],
    data(){
        return {
            currentUrl: window.location.origin,
            form: useForm({
                id: null,
                name: null,
            }),   
            showModal: false,
            editable: false,
        }
    },


    methods: { 
        show(){
            this.editable = false;
            this.form.reset();
            this.showModal = true;
        },

        edit(data){
            this.editable= true;
            this.form.id = data.id;
            this.form.name = data.title;
            this.showModal = true;
        },
      
        hide(){
            this.form.reset();
            this.showModal = false;
        },

        saveSupplier(data){ 
            if(this.editable){
                this.form.put(`/faims/suppliers/`+data.id,{
                    preserveScroll: true,
                    onSuccess: (response) => {
                        this.$emit('update', true);
                        this.form.reset();
                        this.hide();
                    }
                });
                console.log('edit');
            }else{
                this.form.post('/faims/suppliers',{
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