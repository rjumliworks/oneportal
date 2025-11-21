<template>
  <PageHeader title="PAP Codes" pageTitle="Libraries" />
  <b-row class="g-2 mb-3 mt-n2">
    <b-col lg>
      <div class="input-group mb-1">
        <span class="input-group-text"> <i class="ri-search-line search-icon"></i></span>
        <input
          type="text"
          v-model="filter.keyword"
          placeholder="Search PAP Code"
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
        <b-button type="button" variant="primary" @click="openPAPCodModal()">
          <i class="ri-add-circle-fill align-bottom me-1"></i> New
        </b-button>
      </div>
    </b-col>
  </b-row>
  <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
    <div
      class="file-manager-content w-100 p-4 pb-0"
      style="height: calc(100vh - 180px); overflow: auto"
      ref="box"
    >
      <table class="table mb-0">
        <thead class="table-light">
          <tr class="fs-11">
            <th>#</th>
            <th>PAP Codes</th>
            <th>Project Description/Title</th>
            <th>Allocated Budget</th>
            <th>Mode of Procurement / APP Type</th>
            <th>End Users</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr class="custom-hover-row" v-for="(list, index) in lists" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ list.code }}</td>
            <td>{{ list.title }}</td>
            <td>{{ formatCurrency(list.allocated_budget) }}</td>
            <td>
              <span>{{ list.mode_of_procurement.name }}</span> <br />
              <span class="text-muted">{{ list.app_type.name }}</span>
            </td>
            <td>
              <div v-for="(end_user, index) in list.end_users">
                <b-badge>
                  {{ end_user.end_user?.name }}
                </b-badge>
              </div>
            </td>

            <td>
              <b-button @click="editPAP(list)" size="sm">
                <i class="ri-file-fill align-bottom me-1"></i>
                <!-- Icon for Print -->
                Edit
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
  </div>

  <ProcurementCodeModal
    @add="fetch()"
    @update="fetch()"
    :mode_of_procurements="dropdowns.mode_of_procurements"
    :app_types="dropdowns.app_types"
    :end_users="dropdowns.end_users"
    ref="create"
  />
</template>
<script>
import _ from "lodash";
import PageHeader from "@/Shared/Components/PageHeader.vue";
import Pagination from "@/Shared/Components/Pagination.vue";
import ProcurementCodeModal from "@/Pages/Modules/FAIMS/Procurement/Modals/ProcurementCode.vue";

export default {
  props: ["dropdowns"],
  components: { ProcurementCodeModal, Pagination, PageHeader },
  data() {
    return {
      currentUrl: window.location.origin,
      lists: [],
      meta: {},
      links: {},
      filter: {
        keyword: null,
      },
      mode_of_procurements: {},
      index: null,
    };
  },
  watch: {
    "filter.keyword"(newVal) {
      this.checkSearchStr(newVal);
    },
  },

  mounted() {
    // this.getModeOfProcurements();
  },

  created() {
    this.fetch();
  },
  methods: {
    checkSearchStr: _.debounce(function (string) {
      this.fetch();
    }, 300),
    fetch(page_url) {
      page_url = "/faims/procurement-codes";
      axios
        .get(page_url, {
          params: {
            keyword: this.filter.keyword,
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

    openPAPCodModal() {
      this.$refs.create.show();
    },

    editPAP(data) {
      this.$refs.create.edit(data);
    },

    formatCurrency(value) {
      return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
      }).format(value);
    },

    // getModeOfProcurements() {
    //     axios.get('/faims/procurement-codes',{
    //         params : {
    //             option: 'mode_of_procurements'
    //         }
    //     })
    //     .then(response => {
    //         if(response){
    //             this.mode_of_procurements = response.data;
    //         }
    //     })
    //     .catch(err => console.log(err));
    // },
  },
};
</script>
