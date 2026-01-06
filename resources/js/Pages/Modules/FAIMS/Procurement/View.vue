<template>
  <Head title="Profile" />
  <PageHeader title="Procurement Overview" pageTitle="User" />
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
          <div v-if="!isCollapsed">
            <span class="card-title mb-1"
              ><i class="ri-file-list-3-line me-2"></i
              ><span class="fs-5 text-muted">PR#:</span>
              <span class="fw-bold">{{ procurement?.code }}</span>
            </span>
            <p class="card-title mb-0 fs-10">
              <span>Status:</span>
              <b-badge
                :class="procurement.status.bg + ' ms-1'"
                style="font-size: 0.75rem; text-align: center"
                >{{ procurement.status?.name }}</b-badge
              >
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
              style="transition: all 0.3s ease; width: 50px; height: 50px"
              v-b-tooltip.hover
              title="Abstract of Bids(AOBs)"
            >
              <i class="ri-auction-line fs-5"></i>
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
              style="transition: all 0.3s ease; width: 50px; height: 50px"
              v-b-tooltip.hover
              title="Notice of Award(NOAs)"
            >
              <i class="ri-trophy-line fs-5"></i>
            </button>
            <button
              :class="[
                'nav-link mb-2 rounded-pill border-0 transition-all p-2',
                activeTab === 6
                  ? 'bg-primary text-white shadow-sm'
                  : 'bg-white text-dark hover-bg-light',
              ]"
              @click="show(6)"
              style="transition: all 0.3s ease; width: 50px; height: 50px"
              v-b-tooltip.hover
              title="Purchase Order(POs)"
            >
              <i class="ri-shopping-cart-line fs-5"></i>
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
        v-if="activeTab === 5"
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
              <button
                :class="['nav-link', activeRightTab === 3 ? 'active' : '']"
                @click="showRightTab(3)"
              >
                <i class="ri-flow-chart me-1"></i>Status
              </button>
            </div>
            <div v-if="activeRightTab === 1" class="comments-section">
              <div class="text-center text-muted mt-5">
                <i class="ri-chat-1-line fs-1"></i>
                <p class="mt-2">No comments yet</p>
                <small>Add a comment to start the discussion</small>
              </div>
            </div>
            <div v-if="activeRightTab === 2" class="logs-section">
              <div class="text-center text-muted mt-5">
                <i class="ri-file-list-line fs-1"></i>
                <p class="mt-2">No logs available</p>
                <small>Activity logs will appear here</small>
              </div>
            </div>
            <div v-if="activeRightTab === 3" class="status-flow-section">
              <h6 class="text-muted mb-3 fw-bold text-center">Procurement Status Flow</h6>
              <div class="status-flow">
                <template v-for="(statusId, index) in statusOrder" :key="statusId">
                  <div
                    class="status-step"
                    :class="{
                      active: procurement.status?.id === statusId,
                      past: procurement.status?.id > statusId,
                    }"
                  >
                    <div class="status-circle">
                      <i
                        v-if="procurement.status?.id > statusId"
                        class="ri-check-line"
                      ></i>
                      <i v-else :class="statusIcons[statusId]"></i>
                    </div>
                    <span class="status-label">{{ statusLabels[statusId] }}</span>
                    <span v-if="procurement.status?.id === statusId" class="current-badge"
                      >Current</span
                    >
                  </div>
                  <div
                    v-if="index < statusOrder.length - 1"
                    class="status-connector"
                  ></div>
                </template>
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
            <button
              :class="[
                'nav-link mb-2 rounded-pill border-0 transition-all p-2',
                activeRightTab === 3
                  ? 'bg-primary text-white shadow-sm'
                  : 'bg-white text-dark hover-bg-light',
              ]"
              @click="showRightTab(3)"
              style="transition: all 0.3s ease; width: 50px; height: 50px"
              v-b-tooltip.hover
              title="Status Flow"
            >
              <i class="ri-flow-chart fs-5"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { useForm } from "@inertiajs/vue3";
import Overview from "./Components/Detail.vue";
import Quotation from "./Quotations/Index.vue";
import BACResolution from "./BACResolution/Index.vue";
import AbstractOfBids from "./Bids/Index.vue";
import NoticeOfAward from "./Components/NoticeOfAward.vue";
import PurchaseOrder from "./Components/PurchaseOrder.vue";
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
  },
  props: ["dropdowns", "procurement", "tab"],
  data() {
    return {
      currentUrl: window.location.origin,
      activeTab: parseInt(this.tab) || 1,
      isCollapsed: false,
      isRightCollapsed: true,
      activeRightTab: 1,
      statusOrder: [36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 49, 51, 52, 53], // Main statuses, partial statuses made optional/situational
      statusLabels: {
        36: "Pending",
        37: "Reviewed",
        38: "Approved",
        39: "Available of Award",
        40: "Available for Re-award",
        41: "Not Available for Award/Re-award",
        42: "For Bids",
        43: "Awarded",
        44: "For BAC Resolution",
        45: "For Approval of BAC Resolution",
        46: "For NOA",
        47: "Served to Supplier",
        48: "NOA Served to Supplier",
        49: "PO Issued",
        50: "Partially NOA Conformed",
        51: "PO Conformed",
        52: "Delivered/For Inspection",
        53: "Completed",
        54: "Conformed",
        55: "Not Conformed",
        56: "NOA Conformed",
        57: "Partially Awarded",
        58: "Partially NOA Conformed",
        59: "Re-award",
        60: "Rebid",
        61: "Not Conformed",
        62: "PO Pending",
        63: "Partially PO Pending",
        64: "PO Partially Issued",
        65: "PO Partially Conformed",
        66: "Partially Delivered/For Inspection",
        67: "Partially Completed/Awaiting for Inspection",
        68: "Not Conformed",
      },
      statusIcons: {
        36: "ri-time-line",
        37: "ri-eye-line",
        38: "ri-check-circle-line",
        39: "ri-trophy-line",
        40: "ri-trophy-line",
        41: "ri-close-circle-line",
        42: "ri-file-text-line",
        43: "ri-auction-line",
        44: "ri-file-line",
        45: "ri-file-line",
        46: "ri-trophy-line",
        47: "ri-send-plane-line",
        48: "ri-send-plane-line",
        49: "ri-shopping-cart-line",
        50: "ri-check-line",
        51: "ri-check-line",
        52: "ri-truck-line",
        53: "ri-check-line",
        54: "ri-check-line",
        55: "ri-close-line",
        56: "ri-check-line",
        57: "ri-auction-line",
        58: "ri-check-line",
        59: "ri-trophy-line",
        60: "ri-refresh-line",
        61: "ri-close-line",
        62: "ri-time-line",
        63: "ri-time-line",
        64: "ri-shopping-cart-line",
        65: "ri-check-line",
        66: "ri-truck-line",
        67: "ri-check-line",
        68: "ri-close-line",
      },
      form: useForm({}),
    };
  },

  watch: {
    tab() {
      this.activeTab = parseInt(this.tab) || 1;
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
</style>
