<template>
    <div class="card bg-light-subtle shadow-none border">
       
        <template v-if="show">
            <div class="card-header bg-light-subtle">
                <div class="d-flex mb-n3">
                    <div class="flex-shrink-0 me-3">
                        <div style="height:2.5rem;width:2.5rem;">
                            <span class="avatar-title bg-primary-subtle rounded p-2 mt-n1">
                                <i class="ri-time-line text-primary fs-24"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-0 fs-14"><span class="text-body">Credit Logs</span></h5>
                        <p class="text-muted text-truncate-two-lines fs-12">Includes official comments, document references, and chronological status updates</p>
                    </div>
                </div>
            </div>
            <div class="card-body bg-white rounded-bottom">
                <div class="table-responsive table-card" style="height: calc(100vh - 380px); overflow: auto;">
                    <table class="table align-middle table-striped table-centered mb-0">
                        <thead class="table-light thead-fixed">
                            <tr class="fs-11">
                                <th class="text-center" style="width: 6%;">#</th>
                                <th>Leave</th>
                                <th class="text-center" style="width: 15%;">Credits</th>
                                <th class="text-center" style="width: 15%;">Old Balance</th>
                                <th class="text-center" style="width: 15%;">New Balance</th>
                                <th class="text-center" style="width: 15%;">Type</th>
                            </tr>
                        </thead>
                        <tbody class="table-white fs-12">
                            <tr v-for="(list,index) in credits" v-bind:key="index">
                                <td class="text-center">{{ index+1 }}</td>
                                <td>{{list.leave.name}}</td>
                                <td class="text-center text-muted">{{list.earned}}</td>
                                <td class="text-center text-muted">{{list.used}}</td>
                                <td class="text-center text-muted">{{list.carried_over}}</td>
                                <td class="text-center fw-semibold">{{list.balance}}</td>
                                <td class="text-center">
                                    <b-button variant="soft-info" v-b-tooltip.hover title="Edit" size="sm">
                                        <i class="ri-eye-fill align-bottom"></i>
                                    </b-button>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <Pagination class="ms-2 me-2 mt-n1" v-if="meta" @fetch="fetch" :lists="lists.length" :links="links" :pagination="meta" />
            </div>
        </template>
        <template v-else>
            <div class="card-body bg-white rounded-bottom d-flex justify-content-center align-items-center" style="height: calc(100vh - 345px); overflow: auto;">
                <p class="text-muted text-center fs-12 mb-0">Please choose on the list of credits to view the history</p>
            </div>
        </template>
    </div>
</template>
<script>
import Pagination from "@/Shared/Components/Pagination.vue";
export default {
    components: { Pagination },
    data(){
        return {
            lists: [],
            meta: {},
            links: {},
            show: true
        }
    },
     created(){
       this.fetch();
    },
    methods: {
        checkSearchStr: _.debounce(function(string) {
            this.fetch();
        }, 300),
        fetch(page_url){
            page_url = page_url || '/employees';
            axios.get(page_url,{
                params : {
                    keyword: this.filter.keyword,
                    count: 10,
                    option: 'list'
                }
            })
            .then(response => {
                if(response){
                    this.lists = response.data.data;
                    this.meta = response.data.meta;
                    this.links = response.data.links;          
                }
            })
            .catch(err => console.log(err));
        }
    }
}
</script>