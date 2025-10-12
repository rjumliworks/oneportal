
<template>
    <b-modal v-model="showModal" header-class="p-3 bg-light" title="Add Role" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop>
        <form class="customform">
            <BRow class="g-3 mt-n1">
                <BCol lg="12" class="mt-0 mb-2">
                    <InputLabel value="Role" :message="form.errors.role_id"/>
                    <Multiselect :options="filteredRoles" label="name" v-model="form.role_id" placeholder="Select Role" @input="handleInput('role_id')"/>
                </BCol>
            </BRow>
        </form>
       {{form}}
        <template v-slot:footer>
            <b-button @click="hide()" variant="light" block>Cancel</b-button>
            <b-button @click="submit('ok')" variant="primary" :disabled="form.processing" block>Submit</b-button>
        </template>
    </b-modal>
</template>
<script>
import { useForm } from '@inertiajs/vue3';
import Multiselect from "@vueform/multiselect";
import InputLabel from '@/Shared/Components/Forms/InputLabel.vue';
export default {
    components: { Multiselect, InputLabel },
    props: ['roles'],
    data(){
        return {
            currentUrl: window.location.origin,
            form: useForm({
                code: null,
                role_id: null,
                type: 'add',
                option: 'role'
            }),
            user: {},
            type: null,
            showModal: false,
        }
    },
    computed: {
        filteredRoles() {
            if (!this.roles || !this.user.roles) return this.roles || [];
            const activeUserRoles = this.user.roles
                .filter(r => r.is_active === 1)
                .map(r => r.name);
            return this.roles.filter(role => !activeUserRoles.includes(role.name));
        }
    },
    methods: { 
        show(data){
            this.user = data;
            this.form.code = this.user.code,
            this.showModal = true;
        },
        submit(){
            this.form.put('/users/update', {
                errorBag: 'updateProfileInformation',
                preserveScroll: true,
                onSuccess: () => {
                    this.$emit('update',this.$page.props.flash.data.data);
                    this.hide();
                },
            });
        },
        hide(){
            this.user = {};
            this.form.reset();
            this.form.clearErrors();
            this.showModal = false;
        }
    }
}
</script>