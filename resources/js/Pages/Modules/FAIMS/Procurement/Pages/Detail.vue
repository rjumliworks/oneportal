<template>
  <div
    class="shadow-lg border-0"
    style="
      background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
      border-radius: 20px;
      overflow: hidden;
      margin-bottom: 50px;
    "
  >
    <div class="card">
      <div class="card-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          <i class="ri-information-line me-2 text-white"></i>
          <span class="text-white">Procurement Details</span>
        </h5>
 
      </div>
    </div>
    <div class="card-body p-2">
      <div class="row g-4">
        <!-- Left Column -->
        <div class="col-md-6">
          <div class="mb-4 p-3 bg-light rounded-3 shadow-sm">
            <h6 class="text-primary fw-bold mb-3">
              <i class="ri-file-list-3-line me-2"></i>Basic Information
            </h6>
            <div class="d-flex align-items-center mb-3">
              <i class="ri-hashtag text-primary me-3 fs-5"></i>
              <div>
                <small class="text-muted d-block">PR Number</small>
                <span class="fw-bold fs-6">{{ procurement.code }}</span>
              </div>
            </div>
            <div class="d-flex align-items-center mb-3">
              <i class="ri-calendar-line text-success me-3 fs-5"></i>
              <div>
                <small class="text-muted d-block">PR Date</small>
                <span class="fw-bold">{{ procurement.date }}</span>
              </div>
            </div>
            <div class="d-flex align-items-center mb-3">
              <i class="ri-building-line text-info me-3 fs-5"></i>
              <div>
                <small class="text-muted d-block">Division</small>
                <span class="fw-bold">{{ procurement.division?.name }}</span>
              </div>
            </div>
            <div class="d-flex align-items-center mb-3">
              <i class="ri-group-line text-warning me-3 fs-5"></i>
              <div>
                <small class="text-muted d-block">Unit</small>
                <span class="fw-bold">{{ procurement.unit?.name }}</span>
              </div>
            </div>
          </div>

          <div class="p-3 bg-light rounded-3 shadow-sm text-dark">
            <h6 class="text-primary fw-bold mb-3">
              <i class="ri-price-tag-3-line me-2"></i>PAP Codes
            </h6>
            <div
              v-for="code in procurement.codes"
              :key="code.id"
              class="d-flex align-items-center mb-2 text-dark"
            >
              <b-badge variant="primary" :class="procurement.status.bg + ' ms-1'">{{
                code.procurement_code?.title
              }}</b-badge>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-6">
          <div class="mb-4 p-3 bg-light rounded-3 shadow-sm">
            <h6 class="text-primary fw-bold mb-3">
              <i class="ri-bar-chart-line me-2"></i>Statistics
            </h6>
            <div class="d-flex align-items-center mb-3">
              <i class="ri-file-text-line text-primary me-3 fs-5"></i>
              <div>
                <small class="text-muted d-block">Quotation Count</small>
                <span class="fw-bold fs-5 text-primary">{{
                  procurement.quotation_count
                }}</span>
              </div>
            </div>
            <div class="d-flex align-items-center mb-3">
              <i class="ri-refresh-line text-warning me-3 fs-5"></i>
              <div>
                <small class="text-muted d-block">Reawarded Count</small>
                <span class="fw-bold">{{ procurement.reawarded_count }}</span>
              </div>
            </div>
            <div class="d-flex align-items-center mb-3">
              <i class="ri-loop-left-line text-danger me-3 fs-5"></i>
              <div>
                <small class="text-muted d-block">Rebid Count</small>
                <span class="fw-bold">{{ procurement.rebidded_count }}</span>
              </div>
            </div>
          </div>

          <div class="p-3 bg-light rounded-3 shadow-sm">
            <h6 class="text-primary fw-bold mb-3">
              <i class="ri-user-line me-2"></i>Personnel
            </h6>
            <div class="d-flex align-items-center mb-3">
              <i class="ri-user-add-line text-success me-3 fs-5"></i>
              <div>
                <small class="text-muted d-block">Created By</small>
                <span class="fw-bold">{{ procurement.created_by.profile.fullname }}</span>
              </div>
            </div>
            <div class="d-flex align-items-center mb-3">
              <i class="ri-user-shared-line text-info me-3 fs-5"></i>
              <div>
                <small class="text-muted d-block">Requested By</small>
                <span class="fw-bold">{{
                  procurement.requested_by.profile.fullname
                }}</span>
              </div>
            </div>
            <div class="d-flex align-items-center mb-3">
              <i class="ri-information-line text-warning me-3 fs-5"></i>
              <div>
                <small class="text-muted d-block">Status</small>
                <b-badge
                  :class="procurement.status.bg + ' px-3 py-2'"
                  style="font-size: 0.9rem"
                  >{{ procurement.status.name }}</b-badge
                >
              </div>
            </div>
            <div class="d-flex align-items-center">
              <i class="ri-information-line text-secondary me-3 fs-5"></i>
              <div>
                <small class="text-muted d-block">Substatus</small>
                <b-badge
                  :class="procurement?.sub_status?.bg + ' px-3 py-2'"
                  style="font-size: 0.9rem"
                  >{{ procurement?.sub_status?.name }}</b-badge
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: ["dropdowns", "procurement"],
  data() {
    return {
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
    };
  },
};
</script>
