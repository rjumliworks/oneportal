<template>
  <PageHeader class="mt-3 ms-3" title="Purchase Orders"  />
  <BRow>
    <div class="col-md-12">
      <div class="card bg-light-subtle shadow-none border">
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
                  placeholder="Search Purchase Orders"
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
                 <!-- <b-button type="button" variant="primary" @click="createPO()">
                <i class="ri-add-circle-fill align-bottom me-1"></i> New
                </b-button> -->
              </div>
            </b-col>
          </b-row>
        </div>

        <div class="card bg-white border-bottom shadow-none" no-body>
          <div class="d-flex">
            <div class="flex-grow-1">
              <ul class="nav nav-tabs nav-tabs-custom nav-primary fs-12" role="tablist">
                <li class="nav-item">
                  <BLink
                    class="nav-link py-3 active"
                    data-bs-toggle="tab"
                    role="tab"
                    aria-selected="true"
                    @click="viewStatus(null)"
                  >
                    <i class="ri-apps-2-line me-1 align-bottom"></i> All
                  </BLink>
                </li>
                 <li class="nav-item">
                  <BLink
                    v-if="!dropdowns.roles.includes('Procurement Officer')"
                    class="nav-link py-3"
                    data-bs-toggle="tab"
                    role="tab"
                    aria-selected="true"
                    @click="viewStatus(52)"
                  >
                    <i class="ri-file-search-line me-1 align-bottom"></i> Inspection and Acceptance
                  </BLink>
                </li>
            
              </ul>
            </div>
            <div class="flex-shrink-0">
              <div class="d-flex flex-wrap gap-2 mt-3"></div>
            </div>
          </div>
        </div>

     
        <div class="card-body bg-white rounded-bottom">
          <div
            class="table-responsive table-card"
            style="margin-top: -39px; height: calc(100vh - 300px); overflow: auto"
          >
            <table class="table align-middle table-striped table-centered mb-0">
              <thead class="table-light thead-fixed">
                <tr class="fs-11">
                  <th style="width: 3%" class="text-center">#</th>
                  <th style="width: 10%">Code</th>
                  <th style="width: 14%" class="text-center">PO Date</th>
                  <th style="width: 10%" class="text-center">Delivery Term</th>
                  <th style="width: 10%" class="text-center">Payment Term</th>
                  <th style="width: 10%" class="text-center">Date of delivery</th>
                  <th style="width: 14%" class="text-center">NOA Code</th>
                  <th style="width: 10%" class="text-center">Status</th>
                  <th style="width: 5%"></th>
                </tr>
              </thead>

              <tbody class="table-white fs-12">
                <tr v-for="(list, index) in lists" v-bind:key="index">
                  <td class="text-center">
                    {{ (meta.current_page - 1) * meta.per_page + index + 1 }}.
                  </td>
                  <td>
                    <h5 class="fs-13 mb-0 fw-semibold text-primary">{{ list.code }}</h5>
                  </td>
                  <td class="text-center">{{ list.po_date }}</td>
                  <td class="text-center">
                    {{ list.delivery_term }}
                  </td>
                  <td class="text-center">{{ list.payment_term }}</td>

                  <td class="text-center">
                    <span >
                      {{ list.date_of_delivery }}
                    </span>
                    <span else></span>
                  </td>
                  <td class="text-center">
                    {{  list.noa.code}}
                  </td>
                  <td class="text-center">
                    <b-badge :class="list.status.bg">{{ list.status?.name }}</b-badge>
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
                              @click="viewPO(list)"
                              class="dropdown-item d-flex align-items-center"
                              role="button"
                            >
                              <i class="ri-eye-fill align-bottom me-1"></i>View
                          </li>  

                            <li
                              @click="updateStatus(list)"
                              v-if="list.status.name == 'Delivered/For Inspection'"
                              class="dropdown-item d-flex align-items-center"
                              role="button"
                            >
                              <i class="ri-eye-fill align-bottom me-1"></i>Update Status
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

                            <li>
                            <a
                              @click="openPrint(list)"
                              class="dropdown-item d-flex align-items-center"
                              role="button"
                            >
                              <i class="ri-printer-fill me-2"></i> Print IAR
                            </a>
                          
                          </li>
                        </ul>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
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
  </BRow>
    <Inspection :procurement="procurement" @add="fetch()" ref="inspection" />

</template>
<script>
import _ from "lodash";

import Inspection from "../Modals/Inspection.vue";
import Multiselect from "@vueform/multiselect";
import PageHeader from "@/Shared/Components/PageHeader.vue";
import Pagination from "@/Shared/Components/Pagination.vue";
import { router } from "@inertiajs/vue3";

export default {
  components: { PageHeader, Pagination, Multiselect, Inspection },
  props: ['dropdowns' , 'procurement'],
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
      page_url = "/faims/purchase-orders";
      axios
        .get(page_url, {
          params: {
            keyword: this.filter.keyword,
            status: this.filter.status,
            option: "lists",
            procurement_id: this.procurement.id,
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


    updateStatus(data) {
      this.$refs.inspection.show(data, "PO");
    },

    viewStatus(status) {
      this.filter.status = status;
      console.log()
      this.fetch();
    },
    
    viewPO(data) {
        router.get(
            "/faims/procurements/" +
            this.procurement.id +
            "?noa_id=" +
            data.id +
            "&option=purchase_order"
        );
    },
 
    openPrint(data) {
      window.open(`/faims/purchase-orders/${data.id}?option=print&type=purchase_order`);
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
