<template>
  <Head title="Profile" />
  <PageHeader title="Procurement Overview" pageTitle="User" />

  <!-- Status Flow Panel -->
  <BRow class="mb-4">
    <BCol xl="12">
      <BCard>
        <BCardHeader>
          <h4 class="card-title mb-0">Procurement Status Flow</h4>
        </BCardHeader>
        <BCardBody>
          <div class="status-flow-container">
            <div class="status-flow-wrapper">
              <div
                v-for="(status, index) in statusFlow"
                :key="status.name"
                class="status-step-modern"
                :class="{ 'current-status': status.isCurrent, 'past-status': status.isPast, 'future-status': !status.isCurrent && !status.isPast }"
                :style="{ animationDelay: `${index * 0.15}s` }"
              >
                <div class="status-card">
                  <div class="status-icon-wrapper">
                    <i v-if="status.isPast" class="ri-check-line status-icon completed"></i>
                    <i v-else-if="status.isCurrent" class="ri-star-fill status-icon current"></i>
                    <i v-else class="ri-circle-line status-icon pending"></i>
                  </div>
                  <div class="status-content">
                    <h6 class="status-title">{{ status.name }}</h6>
                  </div>
                </div>
                <div v-if="index < statusFlow.length - 1" class="status-connector-modern">
                  <div class="connector-line" :class="{ 'active': status.isPast || status.isCurrent }"></div>
                  <div class="connector-arrow" :class="{ 'active': status.isPast || status.isCurrent }">
                    <i class="ri-arrow-right-line"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </BCardBody>
      </BCard>
    </BCol>
  </BRow>

  <div class="row">
    <div
      :class="['transition-all', isCollapsed ? 'col-md-1' : 'col-md-3']"
      style="transition: all 0.3s ease"
    >
      <div
        class="card h-90 mb-3 shadow-lg border-0"
        style="
          background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
          border-radius: 15px;
        "
      >
        <div
          class="card-header bg-gradient-primary border-0 d-flex align-items-center justify-content-between"
          style="border-radius: 15px 15px 0 0 !important; padding: 1rem"
        >
          <div v-if="!isCollapsed" class="text-center">
            <span class="card-title mb-1"
              ><i class="ri-file-list-3-line me-2"></i
              ><span class="fs-5 text-muted">PR#:</span>
              <span class="fw-bold">{{ procurement?.code }}</span>
            </span>
            <p class="card-title mb-0 fs-10">
              <span>Status:</span>
              <div>
              <b-badge
                :class="procurement.status.bg + ' ms-1'"
                style="font-size: 0.75rem; text-align: center"
                >{{ procurement.status?.name }}</b-badge
              >
              </div>
            </p>
            <p class="card-title mb-0 fs-10">
              <span v-if="procurement.sub_status">Substatus:</span>
             <div>
              <b-badge
                :class="procurement.status.bg + ' ms-1'"
                style="font-size: 0.75rem; text-align: center"
                >{{ procurement.sub_status?.name }}</b-badge
              >
             </div>
            </p>
          </div>
          <button
            @click="toggleSidebar"
            class="btn btn-sm btn-light rounded-circle p-2 ms-2"
            style="width: 40px; height: 40px"
          >
            <i
              :class="isCollapsed ? 'ri-arrow-right-line' : 'ri-arrow-left-line'"
              class="text-primary fs-6"
            ></i>
          </button>
        </div>
        <div
          v-if="!isCollapsed"
          class="card-body p-0"
          style="
            height: calc(100vh - 220px);
            overflow: auto;
            border-radius: 0 0 15px 15px;
          "
        >
          <div class="p-3">
            <h6 class="text-muted mb-3 fw-bold">Navigation</h6>
            <div class="nav flex-column">
              <button
                :class="[
                  'nav-link text-start mb-2 rounded-pill border-0 transition-all',
                  activeTab === 1
                    ? 'bg-primary text-white shadow-sm'
                    : 'bg-white text-dark hover-bg-light',
                ]"
                @click="show(1)"
                style="transition: all 0.3s ease"
              >
                <i class="ri-information-line align-middle me-3 fs-5"></i>Procurement
                Details
              </button>
              <button
                :class="[
                  'nav-link text-start mb-2 rounded-pill border-0 transition-all',
                  activeTab === 2
                    ? 'bg-primary text-white shadow-sm'
                    : 'bg-white text-dark hover-bg-light',
                ]"
                @click="show(2)"
                style="transition: all 0.3s ease"

              >

                <i class="ri-file-text-line align-middle me-3 fs-5"></i>Request of
                Quotations(RFQs)
                <span v-if="quotationsCount > 0" :class="activeTab === 2 ? 'badge bg-light text-dark ms-auto' : 'badge bg-dark text-light ms-auto'">{{ quotationsCount }}</span>

              </button>
              <button
                :class="[
                  'nav-link text-start mb-2 rounded-pill border-0 transition-all',
                  activeTab === 3
                    ? 'bg-primary text-white shadow-sm'
                    : 'bg-white text-dark hover-bg-light',
                ]"
                @click="show(3)"
                style="transition: all 0.3s ease"
              >
                <i class="ri-auction-line align-middle me-3 fs-5"></i>Abstract of
                Bids(AOBs)
                <span v-if="bidsCount > 0" :class="activeTab === 3 ? 'badge bg-light text-dark ms-auto' : 'badge bg-dark text-light ms-auto'">{{ bidsCount }}</span>
              </button>
              <button
                :class="[
                  'nav-link text-start mb-2 rounded-pill border-0 transition-all',
                  activeTab === 4
                    ? 'bg-primary text-white shadow-sm'
                    : 'bg-white text-dark hover-bg-light',
                ]"
                @click="show(4)"
                style="transition: all 0.3s ease"
              >
                <i class="ri-file-line align-middle me-3 fs-5"></i>BAC Resolutions
                <span v-if="bacResolutionsCount > 0" :class="activeTab === 4 ? 'badge bg-light text-dark ms-auto' : 'badge bg-dark text-light ms-auto'">{{ bacResolutionsCount }}</span>
              </button>
              <button
                :class="[
                  'nav-link text-start mb-2 rounded-pill border-0 transition-all',
                  activeTab === 5
                    ? 'bg-primary text-white shadow-sm'
                    : 'bg-white text-dark hover-bg-light',
                ]"
                @click="show(5)"
                style="transition: all 0.3s ease"
              >
                <i class="ri-trophy-line align-middle me-3 fs-5"></i>Notice of Award(NOAs)
                <span v-if="noasCount > 0" :class="activeTab === 5 ? 'badge bg-light text-dark ms-auto' : 'badge bg-dark text-light ms-auto'">{{ noasCount }}</span>
              </button>
              <button
                :class="[
                  'nav-link text-start mb-2 rounded-pill border-0 transition-all',
                  activeTab === 6
                    ? 'bg-primary text-white shadow-sm'
                    : 'bg-white text-dark hover-bg-light',
                ]"
                @click="show(6)"
                style="transition: all 0.3s ease"
  
              >
                <i class="ri-shopping-cart-line align-middle me-3 fs-5"></i>Purchase
                Order(POs)
              </button>
            </div>
          </div>
        </div>
        <div
          v-else
          class="card-body p-0"
          style="
            height: calc(100vh - 220px);
            overflow: auto;
            border-radius: 0 0 15px 15px;
          "
        >
          <div class="p-2 d-flex flex-column align-items-center">
            <button
              :class="[
                'nav-link mb-2 rounded-pill border-0 transition-all p-2',
                activeTab === 1
                  ? 'bg-primary text-white shadow-sm'
                  : 'bg-white text-dark hover-bg-light',
              ]"
              @click="show(1)"
              style="transition: all 0.3s ease; width: 50px; height: 50px"
              v-b-tooltip.hover
              title="Procurement Details"
            >
              <i class="ri-information-line fs-5"></i>
            </button>
            <button
              :class="[
                'nav-link mb-2 rounded-pill border-0 transition-all p-2',
                activeTab === 2
                  ? 'bg-primary text-white shadow-sm'
                  : 'bg-white text-dark hover-bg-light',
              ]"
              @click="show(2)"
              style="transition: all 0.3s ease; width: 50px; height: 50px"
              v-b-tooltip.hover
              title="Request of Quotations(RFQs)"
            >
              <i class="ri-file-text-line fs-5"></i>
            </button>
            <button
              :class="[
                'nav-link mb-2 rounded-pill border-0 transition-all p-2',
                activeTab === 3
                  ? 'bg-primary text-white shadow-sm'
                  : 'bg-white text-dark hover-bg-light',
              ]"
              @click="show(3)"
              style="transition: all 0.3s ease; width: 50px; height: 50px; position: relative;"
              v-b-tooltip.hover
              title="Abstract of Bids(AOBs)"
            >
              <i class="ri-auction-line fs-5"></i>
              <span v-if="bidsCount > 0" :class="activeTab === 3 ? 'badge bg-light text-dark' : 'badge bg-dark text-light'" style="position: absolute; top: -5px; right: -5px; font-size: 0.6rem; padding: 0.1rem 0.2rem;">{{ bidsCount }}</span>
            </button>
            <button
              :class="[
                'nav-link mb-2 rounded-pill border-0 transition-all p-2',
                activeTab === 4
                  ? 'bg-primary text-white shadow-sm'
                  : 'bg-white text-dark hover-bg-light',
              ]"
              @click="show(4)"
              style="transition: all 0.3s ease; width: 50px; height: 50px"
              v-b-tooltip.hover
              title="BAC Resolutions"
            >
              <i class="ri-file-line fs-5"></i>
            </button>
            <button
              :class="[
                'nav-link mb-2 rounded-pill border-0 transition-all p-2',
                activeTab === 5
                  ? 'bg-primary text-white shadow-sm'
                  : 'bg-white text-dark hover-bg-light',
              ]"
              @click="show(5)"
              style="transition: all 0.3s ease; width: 50px; height: 50px; position: relative;"
              v-b-tooltip.hover
              title="Notice of Award(NOAs)"
            >
              <i class="ri-trophy-line fs-5"></i>
              <span v-if="noasCount > 0" :class="activeTab === 5 ? 'badge bg-light text-dark' : 'badge bg-dark text-light'" style="position: absolute; top: -5px; right: -5px; font-size: 0.6rem; padding: 0.1rem 0.2rem;">{{ noasCount }}</span>
            </button>
            <button
              :class="[
                'nav-link mb-2 rounded-pill border-0 transition-all p-2',
                activeTab === 6
                  ? 'bg-primary text-white shadow-sm'
                  : 'bg-white text-dark hover-bg-light',
              ]"
              @click="show(6)"
              style="transition: all 0.3s ease; width: 50px; height: 50px; position: relative;"
              v-b-tooltip.hover
              title="Purchase Order(POs)"
            >
              <i class="ri-shopping-cart-line fs-5"></i>
              <span v-if="posCount > 0" :class="activeTab === 6 ? 'badge bg-light text-dark' : 'badge bg-dark text-light'" style="position: absolute; top: -5px; right: -5px; font-size: 0.6rem; padding: 0.1rem 0.2rem;">{{ posCount }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div
      :class="[
        'transition-all',
        isCollapsed && isRightCollapsed
          ? 'col-md-10'
          : isCollapsed || isRightCollapsed
          ? 'col-md-8'
          : 'col-md-6',
      ]"
      style="margin-top: 6px; transition: all 0.3s ease"
    >
      <Overview :procurement="procurement" v-if="activeTab === 1" />
      <Quotation
        :dropdowns="dropdowns"
        :procurement="procurement"
        v-if="activeTab === 2"
      />
      <AbstractOfBids
        :dropdowns="dropdowns"
        :procurement="procurement"
        v-if="activeTab === 3"
      />
      <BACResolution
        :dropdowns="dropdowns"
        :procurement="procurement"
        v-if="activeTab === 4"
      />
      <NoticeOfAward
        :dropdowns="dropdowns"
        :procurement="procurement"
        v-if="activeTab === 5 && !showCreatePOFlag"
        @changeTab="show"
        @showCreatePO="handleShowCreatePO"
      />
      <CreatePO
        :dropdowns="dropdowns"
        :procurement="procurement"
        :noa="selectedNoa"
        v-if="showCreatePOFlag"
      />
      <PurchaseOrder
        :dropdowns="dropdowns"
        :procurement="procurement"
        v-if="activeTab === 6"
      />
    </div>
    <div
      :class="['transition-all', isRightCollapsed ? 'col-md-1' : 'col-md-3']"
      style="margin-top: 3px; transition: all 0.3s ease"
    >
      <div
        class="card h-90 mb-3 shadow-lg border-0"
        style="
          background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
          border-radius: 15px;
        "
      >
        <div
          class="card-header bg-gradient-primary text-white border-0 d-flex align-items-center justify-content-between"
          style="border-radius: 15px 15px 0 0 !important; padding: 1rem"
        >
          <div v-if="!isRightCollapsed">
            <h5 class="card-title mb-1">
              <i class="ri-chat-1-line me-2"></i
              ><span class="text-white">Comments & Logs</span>
            </h5>
          </div>
          <button
            @click="toggleRightSidebar"
            class="btn btn-sm btn-light rounded-circle p-2 ms-2"
            style="width: 40px; height: 40px"
          >
            <i
              :class="isRightCollapsed ? 'ri-arrow-left-line' : 'ri-arrow-right-line'"
              class="text-primary fs-6"
            ></i>
          </button>
        </div>
        <div
          v-if="!isRightCollapsed"
          class="card-body p-0"
          style="
            height: calc(100vh - 220px);
            overflow: auto;
            border-radius: 0 0 15px 15px;
          "
        >
          <div class="p-3">
            <div class="nav nav-tabs nav-justified mb-3">
              <button
                :class="['nav-link', activeRightTab === 1 ? 'active' : '']"
                @click="showRightTab(1)"
              >
                <i class="ri-chat-1-line me-1"></i>Comments
              </button>
              <button
                :class="['nav-link', activeRightTab === 2 ? 'active' : '']"
                @click="showRightTab(2)"
              >
                <i class="ri-file-list-line me-1"></i>Logs
              </button>
         
            </div>
            <div v-if="activeRightTab === 1" class="comments-section">
              <div
                v-if="procurement.comments && procurement.comments.length > 0"
                class="comments-list"
              >
                <div
                  v-for="comment in procurement.comments"
                  :key="comment.id"
                  class="comment-item p-3 mb-3"
                >
                  <div class="d-flex align-items-start">
                    <div class="comment-avatar me-3">
                      <img
                        :src="
                          comment.user?.profile?.avatar || '/images/avatars/avatar.jpg'
                        "
                        :alt="comment.user?.profile?.firstname"
                        class="rounded-circle"
                        style="width: 40px; height: 40px; object-fit: cover"
                      />
                    </div>
                    <div class="flex-grow-1">
                      <div
                        class="comment-header d-flex justify-content-between align-items-start mb-2"
                      >
                        <div>
                          <strong
                            >{{ comment.user?.profile?.firstname }}
                            {{ comment.user?.profile?.lastname }}</strong
                          >
                          <small class="text-muted ms-2">{{
                            formatDate(comment.created_at)
                          }}</small>
                        </div>
                      </div>
                      <div class="comment-content mb-2">
                        <p class="mb-0">{{ comment.comment }}</p>
                      </div>
                      <div
                        v-if="comment.replies && comment.replies.length > 0"
                        class="replies-section mt-3"
                      >
                        <div
                          v-for="reply in comment.replies"
                          :key="reply.id"
                          class="reply-item p-2 mb-2 ms-4 border-start"
                        >
                          <div class="d-flex align-items-start">
                            <div class="reply-avatar me-2">
                              <img
                                :src="
                                  reply.user?.profile?.avatar ||
                                  '/images/avatars/avatar.jpg'
                                "
                                :alt="reply.user?.profile?.firstname"
                                class="rounded-circle"
                                style="width: 30px; height: 30px; object-fit: cover"
                              />
                            </div>
                            <div class="flex-grow-1">
                              <div class="reply-header mb-1">
                                <strong class="small"
                                  >{{ reply.user?.profile?.firstname }}
                                  {{ reply.user?.profile?.lastname }}</strong
                                >
                                <small class="text-muted ms-2">{{
                                  formatDate(reply.created_at)
                                }}</small>
                              </div>
                              <div class="reply-content">
                                <p class="mb-0 small">{{ reply.comment }}</p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center text-muted mt-5">
                <i class="ri-chat-1-line fs-1"></i>
                <p class="mt-2">No comments yet</p>
                <small>Add a comment to start the discussion</small>
              </div>
            </div>
            <div v-if="activeRightTab === 2" class="logs-section">
              <div v-if="logs && logs.length > 0" class="logs-list">
                <div v-for="log in logs" :key="log.id" class="log-item p-3 mb-3">
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                      <div class="log-description mb-2">
                        <strong>{{ log.description }}</strong>
                      </div>
                      <div class="log-details small text-muted">
                        <span v-if="log.causer">
                          <i class="ri-user-line me-1"></i>{{ log.causer.name }}
                        </span>
                        <span class="ms-2">
                          <i class="ri-time-line me-1"></i
                          >{{ formatDate(log.created_at) }}
                        </span>
                      </div>
                      <div
                        v-if="log.changes && Object.keys(log.changes).length > 0"
                        class="log-changes mt-2"
                      >
                        <div class="small fw-bold text-muted mb-1">Changes:</div>
                        <div
                          v-for="(value, key) in log.changes"
                          :key="key"
                          class="change-item"
                        >
                          <span class="change-key">{{ key }}:</span>
                          <span class="change-value">{{ value }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="log-icon">
                      <i class="ri-file-list-line fs-4"></i>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center text-muted mt-5">
                <i class="ri-file-list-line fs-1"></i>
                <p class="mt-2">No logs available</p>
                <small>Activity logs will appear here</small>
              </div>
            </div>

          </div>
        </div>
        <div
          v-else
          class="card-body p-0"
          style="
            height: calc(100vh - 220px);
            overflow: auto;
            border-radius: 0 0 15px 15px;
          "
        >
          <div class="p-2 d-flex flex-column align-items-center">
            <button
              :class="[
                'nav-link mb-2 rounded-pill border-0 transition-all p-2',
                activeRightTab === 1
                  ? 'bg-primary text-white shadow-sm'
                  : 'bg-white text-dark hover-bg-light',
              ]"
              @click="showRightTab(1)"
              style="transition: all 0.3s ease; width: 50px; height: 50px"
              v-b-tooltip.hover
              title="Comments"
            >
              <i class="ri-chat-1-line fs-5"></i>
            </button>
            <button
              :class="[
                'nav-link mb-2 rounded-pill border-0 transition-all p-2',
                activeRightTab === 2
                  ? 'bg-primary text-white shadow-sm'
                  : 'bg-white text-dark hover-bg-light',
              ]"
              @click="showRightTab(2)"
              style="transition: all 0.3s ease; width: 50px; height: 50px"
              v-b-tooltip.hover
              title="Logs"
            >
              <i class="ri-file-list-line fs-5"></i>
            </button>
           
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { useForm } from "@inertiajs/vue3";
import Overview from "./Pages/Detail.vue";
import Quotation from "./Pages/Quotation.vue";
import BACResolution from "./Pages/BACResolution.vue";
import AbstractOfBids from "./Pages/Bids.vue";
import NoticeOfAward from "./Pages/NoticeOfAward.vue";
import PurchaseOrder from "./Pages/PurchaseOrder.vue";
import CreatePO from "./Pages/CreatePO.vue";
import { router } from "@inertiajs/vue3";

import PageHeader from "@/Shared/Components/PageHeader.vue";
export default {
  components: {
    PageHeader,
    Overview,
    Quotation,
    BACResolution,
    AbstractOfBids,
    NoticeOfAward,
    PurchaseOrder,
    CreatePO,
  },
  props: ["dropdowns", "procurement", "tab", "logs"],
  data() {
    return {
      currentUrl: window.location.origin,
      activeTab: parseInt(this.tab) || 1,
      isCollapsed: false,
      isRightCollapsed: true,
      activeRightTab: 1,
      selectedNoa: null,
      showCreatePOFlag: false,

      form: useForm({}),
    };
  },

  computed: {
    quotationsCount() {
      return this.procurement.quotations ? this.procurement.quotations.length : 0;
    },
    bidsCount() {
      return (this.procurement.bids ? this.procurement.bids.length : 0) + (this.procurement.quotations ? this.procurement.quotations.length : 0);
    },
    bacResolutionsCount() {
      return this.procurement.bac_resolutions ? this.procurement.bac_resolutions.length : 0;
    },
    noasCount() {
      return this.procurement.noas ? this.procurement.noas.length : 0;
    },
    posCount() {
      return this.procurement.pos ? this.procurement.pos.length : 0;
    },
    statusFlow() {
      // Define the procurement status flow with counts
      const currentStatus = this.procurement.status?.name;
      const statusFlow = [
        { name: 'Pending', bg: 'badge bg-secondary', count: this.procurement.status_distribution?.pending || 0, isCurrent: currentStatus === 'Pending' },
        { name: 'Reviewed', bg: 'badge bg-warning', count: this.procurement.status_distribution?.for_approval || 0, isCurrent: currentStatus === 'Reviewed' },
        { name: 'Approved', bg: 'badge bg-success', count: this.procurement.status_distribution?.approved || 0, isCurrent: currentStatus === 'Approved' },
        { name: 'For Bids', bg: 'badge bg-info', count: this.procurement.status_distribution?.rfq || 0, isCurrent: currentStatus === 'For Bids' },
        { name: 'For BAC Resolution', bg: 'badge bg-primary', count: this.procurement.status_distribution?.bidding || 0, isCurrent: currentStatus === 'For BAC Resolution' },
        { name: 'For Approval of BAC Resolution', bg: 'badge bg-primary', count: this.procurement.for_approval_of_bac_resolution || 0, isCurrent: currentStatus === 'For Approval of BAC Resolution' },
        { name: 'For NOA', bg: 'badge bg-success', count: this.procurement.status_distribution?.noa || 0, isCurrent: currentStatus === 'For NOA' },
        { name: 'NOA Served to Supplier', bg: 'badge bg-success', count: this.procurement.status_distribution?.noa || 0, isCurrent: currentStatus === 'NOA Served to Supplier' },
        { name: 'NOA Conformed', bg: 'badge bg-success', count: this.procurement.status_distribution?.noa || 0, isCurrent: currentStatus === 'NOA Conformed' },
        { name: 'PO Issued', bg: 'badge bg-danger', count: this.procurement.status_distribution?.po || 0, isCurrent: currentStatus === 'PO Issued' },
        { name: 'Delivered/For Inspection', bg: 'badge bg-danger', count: this.procurement.status_distribution?.po || 0, isCurrent: currentStatus === 'Delivered/For Inspection' },
        { name: 'Completed', bg: 'badge bg-success', count: this.procurement.completed?.po || 0, isCurrent: currentStatus === 'Completed' },
      ];
      const currentIndex = statusFlow.findIndex(s => s.isCurrent);
      statusFlow.forEach((status, index) => {
        status.isPast = index < currentIndex;
      });
      return statusFlow;
    },
  },
  watch: {
    tab() {
      this.activeTab = parseInt(this.tab) || 1;
      this.showCreatePOFlag = false; // Reset flag when tab changes
    },
  },
  mounted() {
    this.isRightCollapsed = localStorage.getItem("isRightCollapsed") === "true" || true;
    this.activeRightTab = parseInt(localStorage.getItem("activeRightTab")) || 1;
  },
  methods: {
    show(tab) {
      this.activeTab = tab;
      localStorage.setItem("activeTab", tab);
      router.visit(
        "/faims/procurements/" + this.procurement.id + "?option=view&tab=" + tab,
        { replace: true, preserveState: true }
      );
    },

    toggleSidebar() {
      this.isCollapsed = !this.isCollapsed;
    },

    toggleRightSidebar() {
      this.isRightCollapsed = !this.isRightCollapsed;
      localStorage.setItem("isRightCollapsed", this.isRightCollapsed);
    },

    showRightTab(tab) {
      this.activeRightTab = tab;
      localStorage.setItem("activeRightTab", tab);
    },

    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    },

    handleShowCreatePO(data) {
      this.selectedNoa = data;
      this.showCreatePOFlag = true;
    },

    getStatusBadgeClass(status) {
      if (status.isCurrent) {
        return status.bg;
      } else if (status.isPast) {
        return status.bg;
      } else {
        // Gray for future statuses
        return 'badge bg-secondary';
      }
    },
  },
};
</script>
<style scoped>
.status-flow {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.status-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  opacity: 0.5;
  transition: opacity 0.3s ease;
}

.status-step.active {
  opacity: 1;
}

.status-circle {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #007bff, #0056b3);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.2rem;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.status-step.active .status-circle {
  background: linear-gradient(135deg, #28a745, #1e7e34);
  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.status-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: #495057;
  text-align: center;
  max-width: 80px;
}

.status-connector {
  width: 2px;
  height: 30px;
  background: #dee2e6;
  position: relative;
}

.status-connector::after {
  content: "";
  position: absolute;
  top: 0;
  left: -4px;
  width: 10px;
  height: 10px;
  background: #dee2e6;
  border-radius: 50%;
}

.status-step.active ~ .status-step .status-circle {
  background: #6c757d;
}

.status-step.active ~ .status-step .status-label {
  color: #6c757d;
}

.status-step.past {
  opacity: 1;
  animation: fadeInCheck 0.5s ease-in-out;
}

@keyframes fadeInCheck {
  0% {
    opacity: 0;
    transform: scale(0.8);
  }
  50% {
    opacity: 0.7;
    transform: scale(1.1);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

.status-step.past .status-circle {
  background: linear-gradient(135deg, #28a745, #1e7e34);
  animation: pulseGreen 1s ease-in-out;
}

@keyframes pulseGreen {
  0% {
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
  }
  50% {
    box-shadow: 0 4px 20px rgba(40, 167, 69, 0.6);
  }
  100% {
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
  }
}

.current-badge {
  background: linear-gradient(135deg, #28a745, #1e7e34);
  color: white;
  font-size: 0.7rem;
  font-weight: bold;
  padding: 0.2rem 0.5rem;
  border-radius: 10px;
  margin-top: 0.25rem;
  box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  animation: badgeGlow 1.5s ease-in-out infinite;
}

@keyframes badgeGlow {
  0%,
  100% {
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
  }
  50% {
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.6);
  }
}

.log-item {
  background: #f8f9fa;
  border-left: 4px solid #007bff;
  transition: all 0.3s ease;
}

.log-item:hover {
  background: #e9ecef;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.log-description {
  color: #495057;
  font-size: 0.9rem;
}

.log-details {
  color: #6c757d;
}

.log-changes {
  background: #ffffff;
  padding: 0.5rem;
  border-radius: 4px;
  border: 1px solid #dee2e6;
}

.change-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.25rem;
}

.change-key {
  font-weight: 600;
  color: #495057;
}

.change-value {
  color: #007bff;
  font-family: monospace;
  font-size: 0.85rem;
}

.log-icon {
  margin-left: 1rem;
  opacity: 0.7;
}

.status-item {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 0.6s ease-out forwards;
}

.current-status .status-badge {
  animation: pulseCurrent 2s ease-in-out infinite;
  box-shadow: 0 0 15px rgba(255, 193, 7, 0.5);
}

@keyframes fadeInUp {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulseCurrent {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 0 15px rgba(255, 193, 7, 0.5);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 0 25px rgba(255, 193, 7, 0.8);
  }
}

.status-flow-container {
  overflow-x: auto;
  white-space: nowrap;
  padding: 1rem 0;
}

.status-flow-wrapper {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0 1rem;
  min-width: max-content;
}

.status-step-modern {
  display: flex;
  align-items: center;
  gap: 1rem;
  opacity: 0;
  transform: translateY(20px);
  animation: slideInUp 0.6s ease-out forwards;
}

.status-step-modern.current-status {
  animation: slideInUp 0.6s ease-out forwards, pulseGlow 2s ease-in-out infinite;
}

.status-step-modern.past-status {
  opacity: 1;
  transform: translateY(0);
}

.status-step-modern.future-status {
  opacity: 0.6;
}

.status-card {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border: 2px solid #e9ecef;
  border-radius: 16px;
  padding: 1rem;
  min-width: 140px;
  text-align: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.status-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #007bff, #6610f2);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.status-step-modern.current-status .status-card::before {
  opacity: 1;
}

.status-step-modern.current-status .status-card {
  border-color: #007bff;
  box-shadow: 0 8px 25px rgba(0, 123, 255, 0.2);
  transform: translateY(-2px);
}

.status-step-modern.past-status .status-card {
  background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
  border-color: #28a745;
  box-shadow: 0 4px 15px rgba(40, 167, 69, 0.15);
}

.status-step-modern.future-status .status-card {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-color: #6c757d;
  box-shadow: 0 4px 15px rgba(108, 117, 125, 0.1);
}

.status-icon-wrapper {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 0.5rem;
  position: relative;
}

.status-icon {
  font-size: 1.5rem;
  transition: all 0.3s ease;
}

.status-icon.completed {
  color: #28a745;
  animation: checkPulse 1.5s ease-in-out infinite;
}

.status-icon.current {
  color: #ffc107;
  animation: starSpin 3s linear infinite;
}

.status-icon.pending {
  color: #6c757d;
}

.status-content {
  margin-top: 0.5rem;
}

.status-title {
  font-size: 0.85rem;
  font-weight: 600;
  color: #495057;
  margin-bottom: 0.25rem;
  line-height: 1.2;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.status-count {
  font-size: 0.75rem;
  color: #6c757d;
  font-weight: 500;
  background: rgba(108, 117, 125, 0.1);
  padding: 0.2rem 0.5rem;
  border-radius: 8px;
  display: inline-block;
}

.status-step-modern.current-status .status-count {
  background: rgba(0, 123, 255, 0.1);
  color: #007bff;
}

.status-step-modern.past-status .status-count {
  background: rgba(40, 167, 69, 0.1);
  color: #28a745;
}

.status-connector-modern {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.connector-line {
  width: 60px;
  height: 3px;
  background: #dee2e6;
  border-radius: 2px;
  transition: all 0.3s ease;
  position: relative;
}

.connector-line.active {
  background: linear-gradient(90deg, #28a745, #20c997);
  box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
}

.connector-arrow {
  color: #dee2e6;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.connector-arrow.active {
  color: #28a745;
  animation: arrowPulse 1.5s ease-in-out infinite;
}

@keyframes slideInUp {
  0% {
    opacity: 0;
    transform: translateY(30px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulseGlow {
  0%, 100% {
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.2);
  }
  50% {
    box-shadow: 0 8px 35px rgba(0, 123, 255, 0.4);
  }
}

@keyframes checkPulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

@keyframes starSpin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

@keyframes arrowPulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.2);
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .status-flow-wrapper {
    gap: 0.5rem;
    padding: 0 0.5rem;
  }

  .status-card {
    min-width: 120px;
    padding: 0.75rem;
  }

  .status-title {
    font-size: 0.8rem;
  }

  .connector-line {
    width: 40px;
  }
}
</style>
