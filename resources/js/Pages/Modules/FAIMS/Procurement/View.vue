<template>
    <Head title="Profile" />
    <PageHeader title="Procurement Overview" pageTitle="User" />
    <div class="row">
        <div class="col-md-3">
            <div class="card h-90 mb-3">
                <div>
                <div class="card-header bg-light-subtle">
                    <h5 class="card-title   mb-0"><span class="text-muted">PR No.:</span> {{  procurement?.code }} </h5>
                     <h3 class="card-title   mb-0"><span class="text-muted">Status:</span>
                        <b-badge :class="procurement.status.bg">{{ procurement.status?.name }}</b-badge>
                      </h3>
                </div>
                </div>
                <div class="card-body" style="height: calc(100vh - 220px); overflow: auto;">
                    <b-list-group class="list-group-fill-success mt-4">
                        <BListGroupItem :active="activeTab === 1"  class="list-group-item-action" @click="show(1)">
                            <i class="ri-apps-2-fill align-middle me-2"></i>Procurement Details
                        </BListGroupItem>
                        <BListGroupItem :active="activeTab === 2"  class="list-group-item-action" @click="show(2)">
                            <i class="ri-profile-fill align-middle me-2"></i>Request of Quotations(RFQs)
                        </BListGroupItem>
                        <BListGroupItem :active="activeTab === 3"  class="list-group-item-action" @click="show(3)" v-if="procurement?.status.name == 'For Bids' || procurement?.status.name == 'For BAC Resolution'">
                            <i class="ri-profile-fill align-middle me-2"></i>Abstract of Bids(AOBs)
                        </BListGroupItem>
                        <BListGroupItem :active="activeTab === 4" class="list-group-item-action" @click="show(4)">
                            <i class="ri-profile-fill align-middle me-2"></i>BAC Resolutions
                        </BListGroupItem>

                        <BListGroupItem :active="activeTab === 5"  class="list-group-item-action" @click="show(5)">
                            <i class="ri-profile-fill align-middle me-2"></i>Notice of Award(NOAs)
                        </BListGroupItem>
                        <BListGroupItem :active="activeTab === 6"  class="list-group-item-action" @click="show(6)">
                            <i class="ri-profile-fill align-middle me-2"></i>Purchase Order(POs)
                        </BListGroupItem>
                    </b-list-group>
                </div>
            </div>
        </div>
        <div class="col-md-9" style="margin-top: 6px;">
            <Overview :procurement="procurement"  v-if="activeTab === 1"/>
            <Quotation :dropdowns="dropdowns" :procurement="procurement"  v-if="activeTab === 2"/>
            <AbstractOfBids :dropdowns="dropdowns" :procurement="procurement"  v-if="activeTab === 3 "/>
            <BACResolution :dropdowns="dropdowns" :procurement="procurement"  v-if="activeTab === 4  "/>
            <NoticeOfAward :dropdowns="dropdowns" :procurement="procurement"   v-if="activeTab === 5  "/>
            <PurchaseOrder :dropdowns="dropdowns" :procurement="procurement"   v-if="activeTab === 6  "/>
        </div>
    </div>
</template>
<script>

import { useForm } from "@inertiajs/vue3"
import Overview from "./Components/Detail.vue";
import Quotation from "./Quotations/Index.vue";
import BACResolution from "./BACResolution/Index.vue";
import AbstractOfBids from "./Bids/Index.vue";
import NoticeOfAward from "./Components/NoticeOfAward.vue";
import PurchaseOrder from "./Components/PurchaseOrder.vue";

import PageHeader from '@/Shared/Components/PageHeader.vue';
export default {
    components: { PageHeader, Overview, Quotation, BACResolution, AbstractOfBids, NoticeOfAward, PurchaseOrder  },
    props:[ 'dropdowns' , 'procurement' ],
    data() {
        return {
            currentUrl: window.location.origin,
            activeTab: 1, 
            form: useForm({
              
            }),
        };
    },
    methods: {
        show(tab){
            this.activeTab = tab;
        },

    }
}
</script>