<template>
  <Head title="Requests" />
  <PageHeader title="Procurement Requests" pageTitle="List" />
  <BRow>
    <div class="col-md-12">
      <div class="card bg-light-subtle shadow-none border">
        <div class="card-header bg-light-subtle">
          <div class="d-flex mb-n3">
            <div class="flex-shrink-0 me-3">
              <div style="height: 2.5rem; width: 2.5rem">
                <span class="avatar-title bg-primary-subtle rounded p-2 mt-n1">
                  <i class="ri-pin-distance-fill text-primary fs-24"></i>
                </span>
              </div>
            </div>
            <div class="flex-grow-1">
              <h5 class="mb-0 fs-14"><span class="text-body">Procurement Requests</span></h5>
              <p class="text-muted text-truncate-two-lines fs-12">
                A detailed list of submitted procurement requests including code, purpose,
                title, and status.
              </p>
            </div>
            <div class="flex-shrink-0" style="width: 45%"></div>
          </div>
        </div>
        <div class="car-body bg-white border-bottom shadow-none">
          <b-row class="mb-2 ms-1 me-1" style="margin-top: 12px">
            <b-col lg>
              <div class="input-group mb-1">
                <span class="input-group-text">
                  <i class="ri-search-line search-icon"></i
                ></span>
                <input
                  type="text"
                  v-model="filter.keyword"
                  placeholder="Search Procurement Request"
                  class="form-control"
                  style="width: 40%"
                />
                <Multiselect
                  class="white"
                  style="width: 15%"
                  :options="dropdowns.statuses"
                  v-model="filter.status"
                  label="name"
                  :searchable="true"
                  placeholder="Select Status"
                />
                <span
                  @click="refresh()"
                  class="input-group-text"
                  v-b-tooltip.hover
                  title="Refresh"
                  style="cursor: pointer"
                >
                  <i class="bx bx-refresh search-icon"></i>
                </span>
                <b-button type="button" variant="primary" @click="goCreatePage">
                  <i class="ri-add-circle-fill align-bottom me-1"></i> Create
                </b-button>
              </div>
            </b-col>
          </b-row>
        </div>
            <b-card no-body>
              <b-tabs card  >
                <b-tab title="All" active >
                  <div class="card-body bg-white rounded-bottom mt-3">
                    <div
                      class="table-responsive table-card"
                      style="margin-top: -39px; height: calc(100vh - 455px); overflow: auto"
                    >
                    <table class="table align-middle table-striped table-centered mb-0">
                    <thead class="table-light thead-fixed">
                      <tr class="fs-11">
                        <th style="width: 3%" class="text-center">#</th>
                        <th style="width: 10%">Code</th>
                        <th style="width: 14%" class="text-center">Purpose</th>
                        <th style="width: 14%" class="text-center">Division</th>
                        <th style="width: 10%" class="text-center">Requested By</th>
                        <th style="width: 10%" class="text-center">PAP Code</th>
                        <th style="width: 10%" class="text-center">Quotation Count</th>
                        <th style="width: 10%" class="text-center">Reawarded Count</th>
                        <th style="width: 10%" class="text-center">Rebid Count</th>
                        <th style="width: 15%" class="text-center">Date Created</th>
                        <th style="width: 10%" class="text-center">Status</th>
                        <th style="width: 10%" class="text-center">Sub-status</th>
                        <th style="width: 5%"></th>
                      </tr>
                    </thead>
                    <tbody class="table-white fs-12">
                      <tr v-for="(list, index) in lists" v-bind:key="index" @click="selectRow(list.id)" :class="{ 'bg-info-subtle': selectedRow === list.id }" >
                        <td class="text-center">
                          {{ index + 1 }}
                        </td>
                        <td>
                          <h5 class="fs-13 mb-0 fw-semibold text-primary">{{ list.code }}</h5>
                        </td>
                        <td class="text-center">{{ list.purpose }}</td>
                        <td class="text-center">
                          {{ list.division?.name }}
                        </td>
                        <td class="text-center">{{ list.requested_by }}</td>
                        <td class="text-center">
                          <div v-for="(list, index) in list.codes" v-bind:key="index">
                            <b-badge>
                              {{ list?.procurement_code?.mode_of_procurement?.name }}
                            </b-badge>
                          </div>
                        </td>
                        <td class="text-center">
                          <span v-if="list.quotation_count > 0">
                            {{ list.quotation_count }}
                          </span>
                          <span else></span>
                        </td>

                        <td class="text-center">
                          <span v-if="list.reawarded_count > 0">
                            {{ list.reawarded_count }}
                          </span>
                          <span else></span>
                        </td>
                        <td class="text-center">
                          <span v-if="list.rebidded_count > 0">
                            {{ list.rebidded_count }}
                          </span>
                        </td>
                        <td class="text-center">{{ list.date }}</td>
                        <td class="text-center">
                          <b-badge :class="list.status.bg">{{ list.status?.name }}</b-badge>
                        </td>
                        <td class="text-center">
                          <b-badge  :class="list.sub_status?.bg">{{ list.sub_status?.name }}</b-badge>
                        </td>
                        <td class="text-end">
                          <div class="d-flex gap-3 justify-content-center">
                            <div class="dropdown" @click.stop>
                              <button
                                class="btn btn-light btn-icon btn-sm dropdown material-shadow-none"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                              >
                                <i class="ri-more-fill align-bottom"></i>
                              </button>
                              <ul class="dropdown-menu dropdownmenu-primary dropdown-menu-end">
                                <li
                                    @click="goViewPage(list)"
                                    class="dropdown-item d-flex align-items-center"
                                    role="button"
                                  >
                                    <i class="ri-eye-fill align-bottom me-1"></i>View
                                </li>
                                
                                <li
                                    v-if="list.status.name == 'Pending'"
                                    @click="goEditPage(list)"
                                    class="dropdown-item d-flex align-items-center"
                                    role="button"
                                  >
                                    <i class="ri-edit-2-fill align-bottom me-1"></i>Edit
                                </li>
                                <li
                                    v-if="
                                      list.status.name == 'Pending' &&
                                      roles.includes('Procurement Officer')
                                    "
                                    @click="goReviewPage(list)"
                                    class="dropdown-item d-flex align-items-center"
                                    role="button"
                                  >
                                    <i class="ri-check-double-fill align-bottom me-1"></i>Review
                                </li>
                                <li
                                    v-if="
                                      list.status.name == 'Reviewed' &&
                                      roles.includes('Procurement Officer')
                                    "
                                    @click="goApprovePage(list)"
                                    class="dropdown-item d-flex align-items-center"
                                    role="button"
                                  >
                                    <i class="ri-check-fill align-bottom me-1"></i>Approve
                                </li>

                                <li
                                    v-if="
                                      list.status.name == 'Completed' || 
                                      (list.status.name == 'Rebid' && list.sub_status?.name == 'For Quotations') ||
                                      (list.status.name == 'Approved' &&
                                        roles.includes('Procurement Officer')) ||
                                      roles.includes('Procurement Staff')
                                    "
                                    @click="goQuotationPage(list)"
                                    class="dropdown-item d-flex align-items-center"
                                    role="button"
                                  >
                                    <i class="ri-check-fill align-bottom me-1"></i>Quotations
                                </li>

                                <li
                                    v-if="
                                      (list.status.name == 'Rebid' && list.sub_status?.name == 'For Bids') ||
                                      (list.status.name == 'Rebid' && list.sub_status?.name == 'For BAC Resolution') ||
                                      list.status.name == 'For Bids' ||  list.status.name == 'Completed' || 
                                      (list.status.name == 'For Approval of BAC Resolution' || 
                                      list.status.name == 'For BAC Resolution' && 
                                      roles.includes('Procurement Officer')) ||
                                      roles.includes('Procurement Staff')
                                    "
                                    @click="goBidsPage(list)"
                                    class="dropdown-item d-flex align-items-center"
                                    role="button"
                                  >
                                    <i class="ri-check-fill align-bottom me-1"></i>Abstract of
                                    Bids
                                </li>

                                <li
                                    v-if="
                                      list.status.name == 'Completed' || 
                                      list.status.name == 'For Approval of BAC Resolution' || list.status.name == 'For NOA' || list.status.name == 'Served to Supplier' ||
                                      list.status.name == 'NOA Conformed /For PO' || list.status.name == 'Partially Delivered/For Inspection' || 
                                      list.status.name == 'NOA Served to Supplier' &&
                                      roles.includes('Procurement Officer') ||
                                      roles.includes('Procurement Staff')
                                    "
                                    @click="goBACResolutionPage(list)"
                                    class="dropdown-item d-flex align-items-center"
                                    role="button"
                                  >
                                    <i class="ri-check-fill align-bottom me-1"></i>BAC Resolution
                                </li>

                                  <li
                                    v-if="
                                      list.status.name == 'Re-award'  ||  list.status.name == 'Rebid' &&
                                      roles.includes('Procurement Officer') ||
                                      roles.includes('Procurement Staff')
                                    "
                                    @click="goReawardPage(list)"
                                    class="dropdown-item d-flex align-items-center"
                                    role="button"
                                  >
                                    <i class="ri-file-fill align-bottom me-1"></i>
                                    
                                    <span v-if=" list.status.name =='Re-award'">Re-award</span> 
                                    <span v-else-if=" list.status.name =='Rebid' ">Rebid</span> 
                                </li>



                                <li><hr class="dropdown-divider" /></li>
                                <li>
                                  <a
                                    @click="openPrint(list)"
                                    class="dropdown-item d-flex align-items-center"
                                    role="button"
                                  >
                                    <i class="ri-printer-fill me-2"></i> Print
                                  </a>
                                
                                </li>
                              </ul>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                    <div class="card-footer">
                    <Pagination
                      class="ms-2 me-2 mt-n1"
                      v-if="meta"
                      @fetch="fetch"
                      :lists="lists.length"
                      :links="links"
                      :pagination="meta"
                    />
                 </div>
                   </div>
                </div>
                </b-tab>
                <b-tab title="Comments" v-if="selectedRow">
                  <b-card-text>Comments</b-card-text>
                </b-tab>
                <b-tab title="Logs" v-if="selectedRow">
                  <b-card-text>Status Logs</b-card-text>
                </b-tab>
              </b-tabs>
            </b-card>
          </div>
      
      
      </div>
 
  </BRow>
</template>
<script>
import _ from "lodash";

import Multiselect from "@vueform/multiselect";
import PageHeader from "@/Shared/Components/PageHeader.vue";
import Pagination from "@/Shared/Components/Pagination.vue";
import { router } from "@inertiajs/vue3";
import { list } from "postcss";
export default {
  components: { PageHeader, Pagination, Multiselect },
  props: ["dropdowns", "roles" ],
  data() {
    return {
      currentUrl: window.location.origin,
      lists: [],
      meta: {},
      links: {},
      filter: {
        keyword: null,
        type: null,
        status: null,
        mode: null,
        expense: null,
        leave: null,
      },
      icons: [
        "ri-flight-takeoff-fill",
        "ri-car-fill",
        "ri-calendar-2-fill",
        "ri-alarm-fill",
      ],
      selectedRow: null,
      view_type: 'all',
      index: null,
      units: [],
    };
  },
  watch: {
    "filter.keyword"(newVal) {
      this.checkSearchStr(newVal);
    },
    "filter.status"(newVal) {
      this.fetch();
    },
    "filter.mode"(newVal) {
      this.fetch();
    },
    "filter.expense"(newVal) {
      this.fetch();
    },
  },
  created() {
    this.fetch();
  },
  methods: {
    checkSearchStr: _.debounce(function (string) {
      this.fetch();
    }, 300),
    fetch(page_url) {
      page_url = page_url || "/faims/procurements";
      axios
        .get(page_url, {
          params: {
            keyword: this.filter.keyword,
            type: this.filter.type,
            status: this.filter.status,
            mode: this.filter.mode,
            count: 10,
            option: "lists",
          },
        })
        .then((response) => {
          if (response) {
            this.lists = response.data.data;
            this.meta = response.data.meta;
            this.links = response.data.links;
          }
        })
        .catch((err) => console.log(err));
    },
    formatDateRange(start, end) {
      const startDate = new Date(start);
      const endDate = new Date(end);

      const options = { month: "long", day: "numeric" };
      const startStr = startDate.toLocaleDateString("en-US", options);
      const endStr = endDate.toLocaleDateString("en-US", { day: "numeric" });

      if (start === end) {
        return startDate.toLocaleDateString("en-US", {
          month: "long",
          day: "numeric",
          year: "numeric",
        });
      }

      const year = startDate.getFullYear(); // assume same year
      return `${startStr}-${endStr}, ${year}`;
    },
    

    view(view_type) {
      this.view_type = view_type;
    },

    goCreatePage() {
      router.get("/faims/procurements/create", { option: "create" });
    },

    goViewPage(data) {
      router.get("/faims/procurements/"+ data.id, { option: "view" });
    },

    goEditPage(data) {
      router.get("/faims/procurements/" + data.id, { option: "edit" });
    },

    goReviewPage(data) {
      router.get("/faims/procurements/" + data.id, { option: "review" });
    },

    goApprovePage(data) {
      router.get("/faims/procurements/" + data.id, { option: "approve" });
    },

    goQuotationPage(data) {
      router.get("/faims/procurements/" + data.id, { option: "quotations" });
    },

    goBidsPage(data) {
      router.get("/faims/procurements/" + data.id, { option: "bids" });
    },

    goBACResolutionPage(data) {
      router.get("/faims/procurements/" + data.id, { option: "bac_resolutions" });
    },

    goReawardPage(data) {
      router.get("/faims/procurements/" + data.id, { option: "bac_resolutions" });
    },
    
    openPrint(data) {
      window.open(`/faims/procurements/${data.id}?option=print&type=procurement`);
    },

    selectRow(index) {
         this.selectedRow = (this.selectedRow == index) ? null : index;
      },

    refresh() {
      this.filter.expense = null;
      this.filter.mode = null;
      this.filter.keyword = null;
      this.fetch();
    },
  },
};
</script>
