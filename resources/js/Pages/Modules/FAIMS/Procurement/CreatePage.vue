<template>
  <PageHeader
    v-if="option == 'create'"
    title="Create Procurement Request"
    pageTitle="PR"
  />
  <PageHeader v-if="option == 'edit'" title="Edit Procurement Request" pageTitle="PR" />
  <PageHeader
    v-if="option == 'review'"
    title="Review Procurement Request"
    pageTitle="PR"
  />
  <PageHeader
    v-if="option == 'approve'"
    title="Approve Procurement Request"
    pageTitle="PR"
  />
  <PageHeader v-if="option == 'view'" title="View Procurement Request" pageTitle="PR" />
  <div class="row d-flex" >
  <div  :class="[
        'transition-all',
        option == 'create' ? 'col-md-12' : (isRightCollapsed ? 'col-md-11' : 'col-md-9'),

      ]"
      style="transition: all 0.3s ease; height: 100%; overflow: hidden" >
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
      <div
        class="file-manager-content w-100 p-4 pb-0"
        style="height: 100vh; overflow: auto"
        ref="box"
      >
        <!-- <Lists :dropdowns="dropdowns"/>         -->
        <form class="customform">
          <BRow>
            <BCol lg="6" class="mt-2">
              <div>
                <b-card class="bg-light">
                  <BRow>
                    <BCol lg="6" class="mt-2">
                      <InputLabel
                        for="division"
                        value="Division"
                        :message="form.errors.division_id"
                      />
                      <Multiselect
                        :options="dropdowns.divisions"
                        v-model="form.division_id"
                        :searchable="true"
                        label="name"
                        placeholder="Select Division"
                      />
                    </BCol>

                    <BCol lg="6" class="mt-2">
                      <InputLabel value="PR Date" :message="form.errors.date" />
                      <TextInput
                        v-model="form.date"
                        type="text"
                        class="form-control"
                        :light="true"
                        readonly
                      />
                    </BCol>
                    <BCol lg="6" class="mt-2">
                      <InputLabel for="unit" value="Unit" :message="form.errors.unit_id" />
                      <Multiselect
                        :options="units"
                        v-model="form.unit_id"
                        :searchable="true"
                        label="name"
                        placeholder="Select Unit"
                      />
                    </BCol>

                    <BCol lg="6" class="mt-2">
                      <InputLabel
                        for="fund_cluster"
                        value="Fund Cluster"
                        :message="form.errors.fund_cluster_id"
                      />
                      <Multiselect
                        :options="dropdowns.fund_clusters"
                        v-model="form.fund_cluster_id"
                        :searchable="true"
                        label="name"
                        placeholder="Select Fund Cluster"
                      />
                    </BCol>

                    <BCol lg="12" class="mt-2">
                      <InputLabel
                        value="PAP Code"
                        :message="form.errors.procurement_code_ids"
                      />
                      <Multiselect
                        :options="dropdowns.procurement_codes"
                        v-model="form.procurement_code_ids"
                        :searchable="true"
                        label="code"
                        placeholder="Select PAP CODE"
                        mode="tags"
                      />
                    </BCol>
                  </BRow>
                </b-card>
              </div>
            </BCol>
            <BCol lg="6" class="mt-2">
              <div>
                <b-card class="bg-light">
                  <BRow>
                    <BCol lg="12" class="mt-2">
                      <InputLabel
                        for="purpose"
                        value="Request Purpose"
                        :message="form.errors.purpose"
                      />
                      <b-form-textarea
                        id="textarea"
                        v-model="form.purpose"
                        placeholder="Enter your request purpose"
                        rows="4"
                        max-rows="10"
                      ></b-form-textarea>
                    </BCol>

                    <BCol
                      lg="12"
                      class="mt-2"
                      v-if="option == 'review' || option == 'approve'"
                    >
                      <InputLabel
                        for="title"
                        value="Request Title"
                        :message="form.errors.title"
                      />
                      <b-form-textarea
                        id="textarea"
                        v-model="form.title"
                        placeholder="Enter your request purpose"
                        rows="2"
                        max-rows="10"
                      ></b-form-textarea>
                    </BCol>
                  </BRow>
                </b-card>
              </div>
            </BCol>
          </BRow>
          <BRow>
            <BCol lg="3" class="mt-2 mb-2">
              <b-button
                v-if="option == 'create' || option == 'edit'"
                :disabled="
                  !form.division_id ||
                  !form.unit_id ||
                  !form.fund_cluster_id ||
                  !form.purpose
                "
                @click="openAddItem()"
                variant="light"
                block
                class="bg-success w-75 text-white"
                >Add Item</b-button
              >
            </BCol>

            <div>
              <table class="table  mb-0">
                <thead class="table-light">
                  <tr class="fs-11">
                    <th>Item No.</th>
                    <th>Unit</th>
                    <th>Item Description</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Total Cost</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in form.items" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>
                      <span>
                        {{
                          item.item_quantity > 1
                            ? item.item_unit_type?.[0]?.name_long ||
                              item.item_unit_type?.name_long ||
                              ""
                            : item.item_unit_type?.[0]?.name_short ||
                              item.item_unit_type?.name_short ||
                              ""
                        }}
                      </span>
                    </td>
                    <td>
                      <div v-html="item.item_description "></div>
                    </td>
                    <td>{{ item.item_quantity }}</td>
                    <td>{{ formatCurrency(item.item_unit_cost) }}</td>
                    <td>{{ formatCurrency(item.total_cost) }}</td>

                    <td>
                      <b-button
                        @click="editItem(index)"
                        variant="success"
                        size="sm"
                        class="me-2 mb-2"
                      >
                        <i class="ri-edit-2-line"></i>
                      </b-button>

                      <b-button  class="me-2 mb-2"  @click="removeItem(index)" variant="danger" size="sm">
                        <i class="ri-delete-bin-line"></i>
                      </b-button>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="5" class="text-end"><strong>Total:</strong></td>
                    <td>
                      <strong>{{ formatCurrency(totalCostSum) }}</strong>
                    </td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <BCol lg="12" class="mt-5">
              <div>
                <b-card title="ASSIGNATOREES" class="bg-light">
                  <BRow>
                    <BCol lg="6" class="mt-2">
                      <InputLabel
                        for="requested_by"
                        value="Requested By"
                        :message="form.errors.requested_by_id"
                      />
                      <Multiselect
                        :options="dropdowns.requesters"
                        v-model="form.requested_by_id"
                        :searchable="true"
                        label="name"
                        placeholder="Select Requester"
                      />
                    </BCol>
                    <BCol lg="6" class="mt-2">
                      <InputLabel
                        for="approved_by"
                        value="Approved By"
                        :message="form.errors.approved_by_id"
                      />
                      <Multiselect
                        :options="dropdowns.approvers"
                        v-model="form.approved_by_id"
                        :searchable="true"
                        label="name"
                        placeholder="Select Approver"
                      />
                    </BCol>
                  </BRow>
                </b-card>
              </div>
            </BCol>

            <BCol lg="3" class="mt-2 mb-2" v-if="option == 'create'">
              <b-button
                :disabled="
                  !form.division_id ||
                  !form.unit_id ||
                  !form.fund_cluster_id ||
                  !form.purpose ||
                  !form.requested_by_id ||
                  !form.approved_by_id ||
                  !form.items.length > 0
                "
                @click="submit('ok')"
                variant="light"
                block
                class="bg-success w-75 text-white"
                >Save</b-button
              >
            </BCol>

            <BCol lg="3" class="mt-2 mb-2" v-if="option == 'edit'">
              <b-button
                @click="update(form)"
                variant="light"
                block
                class="bg-success w-75 text-white"
                >Update</b-button
              >
            </BCol>

            <BCol lg="3" class="mt-2 mb-2" v-if="option == 'review'">
              <b-button
                @click="review(form)"
                variant="light"
                block
                class="bg-success w-75 text-white"
                >Confirm</b-button
              >
            </BCol>

            <BCol lg="3" class="mt-2 mb-2" v-if="option == 'approve'">
              <b-button
                @click="approve(form)"
                variant="light"
                block
                class="bg-success w-75 text-white"
                >Approve</b-button
              >
            </BCol>
            <BCol lg="3" class="mt-2 mb-2">
              <b-button
                @click="goBackPage()"
                style="background-color: grey"
                block
                class="w-75 text-white"
                >Back</b-button
              >
            </BCol>
          </BRow>
        </form>
      </div>

    
    </div>
  </div>
  <RightSidebar v-if="option != 'create'" :procurement="procurement" :logs="logs" :isRightCollapsed="isRightCollapsed" @toggleRightSidebar="toggleRightSidebar" />
  </div>

  <Item :dropdowns="dropdowns" @refresh="getDataFromLocalStorage()" ref="item" />
</template>
<script>
import Item from "./Modals/Item.vue";
import PageHeader from "@/Shared/Components/PageHeader.vue";
import { useForm } from "@inertiajs/vue3";
import Multiselect from "@vueform/multiselect";
import InputError from "@/Shared/Components/Forms/InputError.vue";
import InputLabel from "@/Shared/Components/Forms/InputLabel.vue";
import TextInput from "@/Shared/Components/Forms/TextInput.vue";
import { router } from "@inertiajs/vue3";
import RightSidebar from "./Pages/Components/RightSidebar.vue";

export default {
  components: { PageHeader, InputError, InputLabel, TextInput, Multiselect, Item, RightSidebar },
  props: ["procurement", "dropdowns", "option", "regional_director"],
  data() {
    return {
      currentUrl: window.location.origin,
      form: useForm({
        id: null,
        code: null,
        purpose: null,
        title: null,
        date: this.getCurrentDate(),
        division_id: null,
        unit_id: null,
        fund_cluster_id: null,
        items: null,
        requested_by_id: null,
        approved_by_id: this.dropdowns.regional_director,
        procurement_code_ids: [],
        option: null,
      }),
      action: null,
      showModal: false,
      units: [],
      isRightCollapsed: false,
      isCollapsed: false,
    };
  },

  watch: {
    "form.division_id"(newVal) {
      if (newVal) {
        this.getUnits(newVal);
      }
    },

    "form.procurement_code_ids": function (value) {
      if (this.action == "create") {
        if (Array.isArray(value) && value.length > 0) {
          // Reset the title before adding new ones
          this.form.title = "";

          value.forEach((id) => {
            this.getProcurementTitle(id);
          });
        }
      }
    },

    action: function (value) {
      console.log(value);
      if (value == "edit" || value == "review" || value == "approve" || value == "view") {
        this.form.id = this.procurement.id;
        this.form.code = this.procurement.code;
        this.form.purpose = this.procurement.purpose;
        this.form.title = this.procurement.title;
        this.form.date = this.procurement.date;
        this.form.division_id = this.procurement.division_id;
        this.form.unit_id = this.procurement.unit_id;
        this.form.fund_cluster_id = this.procurement.fund_cluster_id;
        this.form.procurement_code_ids = this.procurement.codes.map(
          (code) => code.procurement_code_id
        );
        this.form.requested_by_id = this.procurement.requested_by_id;
        this.form.approved_by_id = this.procurement.approved_by_id;
        this.form.items = this.procurement.items;
        this.getDataFromLocalStorage(); // update items
      }
    },
  },

  computed: {
    totalCostSum() {
      if (!Array.isArray(this.form.items)) return 0;

      return this.form.items.reduce((sum, item) => {
        return sum + (parseFloat(item.total_cost) || 0);
      }, 0);
    },
  },

  mounted() {
    // Load from localStorage on component mount
    this.getDataFromLocalStorage();
    this.action = this.option;
    try {
      this.isRightCollapsed = JSON.parse(localStorage.getItem("isRightCollapsed")) ?? true;
    } catch (e) {
      this.isRightCollapsed = true;
      localStorage.setItem("isRightCollapsed", JSON.stringify(true));
    }
  },
  

  methods: {

    toggleRightSidebar() {
      this.isRightCollapsed = !this.isRightCollapsed;
      localStorage.setItem("isRightCollapsed", this.isRightCollapsed);
    },

    openAddItem() {
      this.$refs.item.show();
    },

    editItem(index) {
      this.$refs.item.edit(this.form.items[index], index);
    },

    removeItem(index) {
      // Get the current items
      let items = JSON.parse(localStorage.getItem("itemsAdded")) || [];

      // Remove 1 item at that index
      if (index >= 0 && index < items.length) {
        items.splice(index, 1);
      }

      // Save the updated array back to localStorage
      localStorage.setItem("itemsAdded", JSON.stringify(items));

      // Update your form items immediately
      this.form.items = items;
    },

    submit() {
      this.form.post("/faims/procurements", {
        onSuccess: () => {
          localStorage.removeItem("itemsAdded");
        },
        onError: (errors) => {
          console.error("Submission failed:", errors);
        },
      });
    },

    update(data) {
      this.form.option = this.action;
      this.form.put(`/faims/procurements/${data.id}`, {
        onSuccess: () => {
          localStorage.removeItem("itemsAdded");
          this.form.reset();
        },
        onError: (errors) => {
          console.error("Update failed:", errors);
        },
      });
    },

    review(data) {
      this.form.option = this.action;
      this.form.put("/faims/procurements/" + data.id);
      this.form.reset();
    },

    approve(data) {
      this.form.option = this.action;
      this.form.put("/faims/procurements/" + data.id);
      this.form.reset();
    },

    goBackPage() {
      router.get("/faims/procurements");
    },

    getDataFromLocalStorage() {
      // Get existing items from localStorage
      let storedItems = [];
      try {
        storedItems = JSON.parse(localStorage.getItem("itemsAdded")) || [];
      } catch (e) {
        storedItems = [];
        localStorage.setItem("itemsAdded", JSON.stringify([]));
      }

      // If form.items is not set yet, initialize it
      if (!Array.isArray(this.form.items)) {
        this.form.items = [];
      }

      // Merge locally stored ones with DB (form.items), giving priority to stored items
      const combined = [...storedItems, ...this.form.items];

      // Remove duplicates based on item id
      const uniqueItems = combined.filter(
        (item, index, self) => index === self.findIndex((t) => t.id === item.id)
      );

      // Update both localStorage and the form
      this.form.items = uniqueItems;
      localStorage.setItem("itemsAdded", JSON.stringify(uniqueItems));
    },

    getCurrentDate() {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, "0"); // Months are zero-based
      const day = String(today.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },

    formatCurrency(value) {
      return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
      }).format(value);
    },

    getUnits(division_id) {
      axios
        .get("/faims/procurements/create", {
          params: {
            code: division_id,
            option: "units",
          },
        })
        .then((response) => {
          if (response) {
            this.units = response.data;
          }
        })
        .catch((err) => console.log(err));
    },

    getProcurementTitle(id) {
      axios
        .get("/faims/procurements/create", {
          params: {
            id: id,
            option: "title",
          },
        })
        .then((response) => {
          if (response) {
            if (this.form.title) {
              this.form.title += ", " + response.data;
            } else {
              this.form.title = response.data;
            }
          }
        })
        .catch((err) => console.log(err));
    },
  },
};
</script>
