<template>
    <div class="card bg-light-subtle shadow-none border">
        <div class="card-header bg-light-subtle">
            <div class="d-flex mb-n3">
                <div class="flex-shrink-0 me-3">
                    <div style="height:2.5rem;width:2.5rem;">
                        <span class="avatar-title bg-primary-subtle rounded p-2 mt-n1">
                            <i class="ri-apps-2-fill align-middle  text-primary fs-24"></i>
                        </span>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-0 fs-14"><span class="text-body">Procurement</span></h5>
                    <p class="text-muted text-truncate-two-lines fs-12">Details  </p>
                  
                </div>
                <div class="flex-shrink-0">
                  
                </div>
            </div>
        </div>
        <div class="card-body bg-white rounded-bottom" style="height: calc(100vh - 310px); overflow: auto;">
              <BRow>
                    <BCol class="col-md-6 ">
                        <div class=" ">
                            <span class="text-muted"> PR Number:</span> <b>{{ procurement.code }}</b>
                        </div>
                            <div>
                                <span class="text-muted text-end">PR Date:</span>  <b>{{ procurement.date }}</b>
                        </div>
                        <div>
                                <span class="text-muted text-end">Division:</span>  <b>{{ procurement.division?.name }}</b>
                        </div>
                        <div>
                                <span class="text-muted text-end">Unit:</span>  <b>{{ procurement.unit?.name }}</b>
                        </div>
                        <div>
                                <span class="text-muted text-end">PAP Codes:</span>  <b v-for="code in procurement.codes">
                                <div class="ms-3">{{ code.procurement_code?.title }}</div>
                                </b>
                        </div>
                    </BCol>

                         <BCol class="col-md-6">
                            <div>
                                 <span class="text-muted text-end">Quotation Count</span>  <b>{{  procurement.quotation_count }}</b>
                            </div>
                            <div>
                                 <span class="text-muted text-end">Reawarded Count:</span>  <b>{{ procurement.reawarded_count }}</b>
                            </div>
                             <div>
                                 <span class="text-muted text-end">Rebid Count:</span>  <b>{{ procurement.rebidded_count }}</b>
                            </div>

                            <div>
                                 <span class="text-muted text-end">Created By:</span>  <b>{{  procurement.created_by.profile.fullname }}</b>
                            </div>
                            <div>
                                 <span class="text-muted text-end">Requested By:</span>  <b>{{ procurement.requested_by.profile.fullname }}</b>
                            </div>

                            <div>
                             <span class="text-muted text-end">Status:</span>  <b-badge>{{ procurement.status.name }}</b-badge>
                            </div>
                        </BCol>
                    </BRow>
        </div>
    </div>
</template>
<script>
export default {
    props: [ 'dropdowns','procurement'],
    data(){
        return {
           
        }
    },
}
</script>