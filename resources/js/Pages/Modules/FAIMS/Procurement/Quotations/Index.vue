<template>
  <PageHeader title="Quotation Requests" pageTitle="List" />
  <b-row>
    <h5>
      <div>
        <span class="font-weight-bold">
          PR REQUEST NO:
          <u class="text-info">
            <span class="bg-light p-1">
              {{ procurement.code }}
            </span>
          </u>
        </span>
      </div>
    </h5>
  </b-row>
  <b-row class="g-2 mb-3 mt-n2">
    <b-col lg>
      <div class="input-group mb-1">
        <b-button type="button" variant="info" @click="goBackPage(procurement)">
          <i class="ri-arrow-left-line align-bottom me-1"></i> Back
        </b-button>
        <span class="input-group-text"> <i class="ri-search-line search-icon"></i></span>
        <input
          type="text"
          v-model="filter.keyword"
          placeholder="Search Quotation Request"
          class="form-control"
          style="width: 60%"
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
        <b-button type="button" variant="primary" @click="openCreate()">
          <i class="ri-add-circle-fill align-bottom me-1"></i> New
        </b-button>
      </div>
    </b-col>
  </b-row>
  <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
    <div
      class="file-manager-content w-100 p-4 pb-0"
      style="height: calc(90vh - 180px); overflow: auto"
      ref="box"
    >
      <table class="table mb-0">
        <thead class="table-light">
          <tr class="fs-11">
            <th>#</th>
            <th>RFQ No.</th>
            <th>Submission not later than</th>
            <th>Supplier</th>
            <th>Supply Officer</th>
            <th>Status</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr class="custom-hover-row" v-for="(list, index) in lists" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ list.code }}</td>
            <td>{{ list.submission_not_later_than }}</td>
            <td style="line-height: 0.1">
              <p>{{ list.supplier?.name }}</p>
              <p>
                {{ list.supplier?.address }}
              </p>
            </td>
            <td>{{ list.supply_officer }}</td>

            <td>
              <b-badge>
                {{ list.status.name }}
              </b-badge>
            </td>

            <td class="text-center">
              <b-button @click="print(list)" size="sm" class="me-2">
                <i class="ri-printer-fill align-bottom me-1"></i>
                <!-- Icon for Print -->
              </b-button>
              <b-button @click="editRFQ(list)" variant="success" size="sm" class="me-2">
                <i class="ri-edit-2-fill align-bottom me-1"></i>
              </b-button>
              <b-button @click="removeRFQ(list.id)" variant="danger" size="sm">
                <i class="ri-delete-bin-line"></i>
              </b-button>
            </td>
          </tr>
        </tbody>
      </table>
      <Pagination
        class="ms-2 me-2"
        v-if="meta"
        @fetch="fetch"
        :lists="lists.length"
        :links="links"
        :pagination="meta"
      />
    </div>
    <Quotation
      @add="fetch()"
      :procurement="procurement"
      :dropdowns="dropdowns"
      ref="create"
    />
  </div>
</template>
<script>
import _ from "lodash";

import Quotation from "../Modals/Quotation.vue";
import PageHeader from "@/Shared/Components/PageHeader.vue";
import Pagination from "@/Shared/Components/Pagination.vue";
import { router } from "@inertiajs/vue3";
export default {
  props: ["procurement", "dropdowns"],
  components: { PageHeader, Pagination, Quotation },
  data() {
    return {
      currentUrl: window.location.origin,
      lists: [],
      meta: {},
      links: {},
      filter: {
        keyword: null,
      },
      index: null,
    };
  },
  watch: {
    "filter.keyword"(newVal) {
      this.checkSearchStr(newVal);
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
      page_url = "/faims/quotations";
      axios
        .get(page_url, {
          params: {
            keyword: this.filter.keyword,
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

    openCreate() {
      this.$refs.create.show();
    },

    // // remove RFQ
    // removeRFQ(id) {
    //   router.delete(`/faims/quotations/${id}`);
    // },


     print(data) {
        window.open(`/faims/quotations/${data.id}?option=print&type=quotations`);
      },

    goBackPage() {
      this.$inertia.visit("/faims/procurements");
    },
  },
};
</script>

<style scoped>
.custom-hover-row:hover {
  background-color: hsl(0, 29%, 97%);
}
</style>
