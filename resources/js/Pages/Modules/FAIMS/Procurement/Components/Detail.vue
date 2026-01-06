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
      <div class="card-header bg-primary text-white p-3">
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
    return {};
  },
};
</script>
