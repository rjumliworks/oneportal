<template>
    <Head title="Profile" />
    <PageHeader title="Procurement Overview" pageTitle="User" />
    <div class="row">
        <div :class="['transition-all', isCollapsed ? 'col-md-1' : 'col-md-3']" style="transition: all 0.3s ease;">
            <div class="card h-90 mb-3 shadow-lg border-0" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px;">
                <div class="card-header bg-gradient-primary border-0 d-flex align-items-center justify-content-between" style="border-radius: 15px 15px 0 0 !important; padding: 1rem;">
                    <div v-if="!isCollapsed">
                        <span class="card-title mb-1"><i class="ri-file-list-3-line me-2"></i><span class="text-white">PR No.:</span> {{ procurement?.code }}</span>
                        <p class="card-title mb-0 fs-10"><span>Status:</span>
                            <b-badge :class="procurement.status.bg + ' ms-1'" style="font-size: 0.75rem;">{{ procurement.status?.name }}</b-badge>
                        </p>
                    </div>
                    <button @click="toggleSidebar" class="btn btn-sm btn-light rounded-circle p-2 ms-2" style="width: 40px; height: 40px;">
                        <i :class="isCollapsed ? 'ri-arrow-right-line' : 'ri-arrow-left-line'" class="text-primary fs-6"></i>
                    </button>
                </div>
                <div v-if="!isCollapsed" class="card-body p-0" style="height: calc(100vh - 220px); overflow: auto; border-radius: 0 0 15px 15px;">
                    <div class="p-3">
                        <h6 class="text-muted mb-3 fw-bold">Navigation</h6>
                        <div class="nav flex-column nav-pills">
                            <button :class="['nav-link text-start mb-2 rounded-pill border-0 transition-all', activeTab === 1 ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark hover-bg-light']" @click="show(1)" style="transition: all 0.3s ease;">
                                <i class="ri-information-line align-middle me-3 fs-5"></i>Procurement Details
                            </button>
                            <button :class="['nav-link text-start mb-2 rounded-pill border-0 transition-all', activeTab === 2 ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark hover-bg-light']" @click="show(2)" style="transition: all 0.3s ease;">
                                <i class="ri-file-text-line align-middle me-3 fs-5"></i>Request of Quotations(RFQs)
                            </button>
                            <button :class="['nav-link text-start mb-2 rounded-pill border-0 transition-all', activeTab === 3 ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark hover-bg-light']" @click="show(3)" style="transition: all 0.3s ease;">
                                <i class="ri-auction-line align-middle me-3 fs-5"></i>Abstract of Bids(AOBs)
                            </button>
                            <button :class="['nav-link text-start mb-2 rounded-pill border-0 transition-all', activeTab === 4 ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark hover-bg-light']" @click="show(4)" style="transition: all 0.3s ease;">
                                <i class="ri-file-line align-middle me-3 fs-5"></i>BAC Resolutions
                            </button>
                            <button :class="['nav-link text-start mb-2 rounded-pill border-0 transition-all', activeTab === 5 ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark hover-bg-light']" @click="show(5)" style="transition: all 0.3s ease;">
                                <i class="ri-trophy-line align-middle me-3 fs-5"></i>Notice of Award(NOAs)
                            </button>
                            <button :class="['nav-link text-start mb-2 rounded-pill border-0 transition-all', activeTab === 6 ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark hover-bg-light']" @click="show(6)" style="transition: all 0.3s ease;">
                                <i class="ri-shopping-cart-line align-middle me-3 fs-5"></i>Purchase Order(POs)
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="card-body p-0" style="height: calc(100vh - 220px); overflow: auto; border-radius: 0 0 15px 15px;">
                    <div class="p-2 d-flex flex-column align-items-center">
                        <button :class="['nav-link mb-2 rounded-pill border-0 transition-all p-2', activeTab === 1 ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark hover-bg-light']" @click="show(1)" style="transition: all 0.3s ease; width: 50px; height: 50px;" v-b-tooltip.hover title="Procurement Details">
                            <i class="ri-information-line fs-5"></i>
                        </button>
                        <button :class="['nav-link mb-2 rounded-pill border-0 transition-all p-2', activeTab === 2 ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark hover-bg-light']" @click="show(2)" style="transition: all 0.3s ease; width: 50px; height: 50px;" v-b-tooltip.hover title="Request of Quotations(RFQs)">
                            <i class="ri-file-text-line fs-5"></i>
                        </button>
                        <button :class="['nav-link mb-2 rounded-pill border-0 transition-all p-2', activeTab === 3 ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark hover-bg-light']" @click="show(3)"  style="transition: all 0.3s ease; width: 50px; height: 50px;" v-b-tooltip.hover title="Abstract of Bids(AOBs)">
                            <i class="ri-auction-line fs-5"></i>
                        </button>
                        <button :class="['nav-link mb-2 rounded-pill border-0 transition-all p-2', activeTab === 4 ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark hover-bg-light']" @click="show(4)" style="transition: all 0.3s ease; width: 50px; height: 50px;" v-b-tooltip.hover title="BAC Resolutions">
                            <i class="ri-file-line fs-5"></i>
                        </button>
                        <button :class="['nav-link mb-2 rounded-pill border-0 transition-all p-2', activeTab === 5 ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark hover-bg-light']" @click="show(5)" style="transition: all 0.3s ease; width: 50px; height: 50px;" v-b-tooltip.hover title="Notice of Award(NOAs)">
                            <i class="ri-trophy-line fs-5"></i>
                        </button>
                        <button :class="['nav-link mb-2 rounded-pill border-0 transition-all p-2', activeTab === 6 ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark hover-bg-light']" @click="show(6)" style="transition: all 0.3s ease; width: 50px; height: 50px;" v-b-tooltip.hover title="Purchase Order(POs)">
                            <i class="ri-shopping-cart-line fs-5"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div :class="['transition-all', isCollapsed ? 'col-md-11' : 'col-md-9']" style="margin-top: 6px; transition: all 0.3s ease;">
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
import { router } from "@inertiajs/vue3";

import PageHeader from '@/Shared/Components/PageHeader.vue';
export default {
    components: { PageHeader, Overview, Quotation, BACResolution, AbstractOfBids, NoticeOfAward, PurchaseOrder  },
    props:[ 'dropdowns' , 'procurement', 'tab' ],
    data() {
        return {
            currentUrl: window.location.origin,
            activeTab: 1 ,
            isCollapsed: false,
            form: useForm({

            }),
        };
    },
    methods: {

        show(tab) {
            this.activeTab = tab;
            router.visit("/faims/procurements/"+ this.procurement.id + "?option=view&tab=" + tab, { replace: true, preserveState: true });
        },

        toggleSidebar() {
            this.isCollapsed = !this.isCollapsed;
        },

    }
}
</script>