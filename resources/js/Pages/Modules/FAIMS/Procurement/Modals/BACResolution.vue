<template>
  <b-modal v-model="showModal" header-class="p-3" :title="editable ? 'Update BAC Resolution' : 'Create BAC Resolution'" size="xl" centered no-close-on-backdrop>
    <form class="customform">
      <BRow>
        <BCol class="mt-2" v-if="form.code">
          <InputLabel value="BAC Resolution Number" />
          <TextInput v-model="form.code" class="form-control" :light="true" readonly/>
        </BCol>
        <BCol  class="mt-2">
          <InputLabel value="Type" />
          <Multiselect
            :options="['Award', 'Re-award', 'Rebid']"
            v-model="form.type"
            :searchable="true"
            label="name"
            disabled
            placeholder="Select BAC Resolution Type"
          />
        </BCol>

        <BCol  class="mt-2" >
          <InputLabel value="Procurement Number" />
          <TextInput v-model="procurement.code" class="form-control" :light="true" disabled />
        </BCol>
 
        <BCol lg="12" class="mt-2" >
          <InputLabel value="Content" />
           <CustomEditor  v-model="form.body" />

        </BCol>
      </BRow> 
    </form>

    <template v-slot:footer>
      <b-button @click="hide()" variant="light" block>Cancel</b-button>
      <b-button @click="submit(form)" variant="success" v-if="editable" block>Update</b-button>
      <b-button @click="submit(form)" variant="success" v-else block>Save</b-button>
     
    </template>
  </b-modal>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import Multiselect from "@vueform/multiselect";
import InputError from '@/Shared/Components/Forms/InputError.vue';
import InputLabel from '@/Shared/Components/Forms/InputLabel.vue';
import TextInput from '@/Shared/Components/Forms/TextInput.vue';
import axios from 'axios';
import CustomEditor from "@/Shared/Components/Forms/CustomEditor.vue";

export default {
  components: {
    InputError,
    InputLabel,
    TextInput,
    Multiselect,
    CustomEditor   
    // ckeditor: CKEditor.component
  },
  props: ['procurement'],
  data() {
    return {
    isEditorReady :false,
      form: useForm({
        id: null,
        code: null,
        procurement_id: this.procurement.id,
        body:  '',
        type: null,
      }),


      showModal: false,
      awardedQuotations: {},
      submission_not_later_than: null,
      submission_not_later_than_with_format: '',
      editable : false,
      award_body: '',
      rebid_body: '',
      reaward_body: '',
    };
  },

  watch: {
    'submission_not_later_than': function(value) {
        if (value) {
            this.submission_not_later_than_with_format = this.dateFormat(value);
        }
    },

    'form.type': function(value) {
      if(!this.editable){
        if (value === "Award") {
          this.form.body = this.award_body;
        }   
        else if (value === "Rebid") {
          this.form.body = this.rebid_body;
        }
        else if (value === "Re-award") {
          this.form.body = this.reaward_body;
        }
      }
       
    },

  },

  mounted() {
    // this.getDateSubmisionNotLaterThan();

  },

  methods: {
    show(type) {
      this.showModal = true;
      this.form.type = type;
      this.editable = false;
      this.Content();
    
    },

    edit(data) {
      this.form.id = data.id;
      this.form.code = data.code;
      this.form.procurement_id = data.procurement_id;
      this.form.body = data.body;
      this.form.type = data.type;
      this.showModal = true;
      this.editable = true;
    },

    hide() {
      this.showModal = false;
    },

    numberToWords(num) {
      if (!num || num === 0) return "ZERO";
      // ensure integer
      num = Math.floor(num);

      const ones = ["", "ONE", "TWO", "THREE", "FOUR", "FIVE", "SIX", "SEVEN", "EIGHT", "NINE"];
      const teens = ["ELEVEN", "TWELVE", "THIRTEEN", "FOURTEEN", "FIFTEEN", "SIXTEEN", "SEVENTEEN", "EIGHTEEN", "NINETEEN"];
      const tens = ["", "TEN", "TWENTY", "THIRTY", "FORTY", "FIFTY", "SIXTY", "SEVENTY", "EIGHTY", "NINETY"];
      const thousands = ["", "THOUSAND", "MILLION", "BILLION", "TRILLION"];

      function convertChunk(n) {
        let str = "";
        if (n >= 100) {
          str += ones[Math.floor(n / 100)] + " HUNDRED ";
          n %= 100;
        }
        if (n >= 11 && n <= 19) {
          str += teens[n - 11] + " ";
          return str;
        } else if (n >= 10) {
          str += tens[Math.floor(n / 10)] + " ";
          n %= 10;
        }
        if (n > 0) {
          str += ones[n] + " ";
        }
        return str;
      }

      let word = "";
      let chunkIndex = 0;

      while (num > 0) {
        let chunk = num % 1000;
        if (chunk > 0) {
          word = convertChunk(chunk) + thousands[chunkIndex] + " " + word;
        }
        num = Math.floor(num / 1000);
        chunkIndex++;
      }

      return word.trim();
    },


    dateFormat(dateString) {
      const date = new Date(dateString);
      const day = date.getDate();
      const month = date.toLocaleString('default', { month: 'long' });
      const year = date.getFullYear();
      const getOrdinalSuffix = (num) => {
        if (num >= 11 && num <= 13) return "th";
        const lastDigit = num % 10;
        if (lastDigit === 1) return "st";
        if (lastDigit === 2) return "nd";
        if (lastDigit === 3) return "rd";
        return "th";
      };
      const ordinalSuffix = getOrdinalSuffix(day);
      return `${day}<sup>${ordinalSuffix}</sup> day of ${month} ${year}`;
    },

    // get current date with format
    getFormattedDate() {
      const date = new Date();
      const day = date.getDate();
      const month = date.toLocaleString('default', { month: 'long' });
      const year = date.getFullYear();
      const getOrdinalSuffix = (num) => {
        if (num >= 11 && num <= 13) return "th";
        const lastDigit = num % 10;
        if (lastDigit === 1) return "st";
        if (lastDigit === 2) return "nd";
        if (lastDigit === 3) return "rd";
        return "th";
      };
      const ordinalSuffix = getOrdinalSuffix(day);
      return `${day}<sup>${ordinalSuffix}</sup> day of ${month} ${year}`;
    },

     // roman converter
    toRoman(num) {
        const romanMap = [
          { value: 1000, numeral: "M" },
          { value: 900, numeral: "CM" },
          { value: 500, numeral: "D" },
          { value: 400, numeral: "CD" },
          { value: 100, numeral: "C" },
          { value: 90, numeral: "XC" },
          { value: 50, numeral: "L" },
          { value: 40, numeral: "XL" },
          { value: 10, numeral: "X" },
          { value: 9, numeral: "IX" },
          { value: 5, numeral: "V" },
          { value: 4, numeral: "IV" },
          { value: 1, numeral: "I" }
        ];
        let result = "";
        for (const { value, numeral } of romanMap) {
          while (num >= value) {
            result += numeral;
            num -= value;
          }
        }
        return result;
    },

    // ordinal number converter
    toOrdinal(n) {
      const v = n % 100;
      let suffix = 'th';

      if (v < 11 || v > 13) {
        switch (n % 10) {
          case 1: suffix = 'st'; break;
          case 2: suffix = 'nd'; break;
          case 3: suffix = 'rd'; break;
        }
      }

      return `${n}<sup>${suffix}</sup>`;
    },

    Content() {
      const current_date = this.getFormattedDate();

      const mode_of_procurement_names = (this.procurement.codes.map(code => code.procurement_code?.mode_of_procurement?.name).filter(Boolean).join(", ")).toUpperCase();
      const mode_of_procurement_ra_nos = (this.procurement.codes.map(code => code.procurement_code?.mode_of_procurement?.others).filter(Boolean).join(", ")).toUpperCase();
      const app_types = this.procurement.codes.map(code => code.procurement_code?.app_type?.name).filter(Boolean).join(", ");
      const allocated_budget =  this.procurement.codes.map(code => code.procurement_code?.allocated_budget).filter(Boolean).join(", ");
      const budget_in_words = this.numberToWords(allocated_budget);
      
      const all_pr_supplier_names = (this.procurement.quotations).map(quotation => quotation.supplier?.name).filter(Boolean).join(", ");

      // Filter bidders
      const bidders = (this.procurement.quotations ).filter(quotation =>
        (quotation.items).some(item => item.bid_price != null && item.is_rebid == 0)
      );

      // purchase request date formatted
      const pr_date = new Date(this.procurement.date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
      });

      const submission_not_later_than_with_format = this.dateFormat(this.procurement.quotations[0].submission_not_later_than);


      this.awardContent(current_date, mode_of_procurement_names , mode_of_procurement_ra_nos , app_types, 
                        allocated_budget, budget_in_words ,all_pr_supplier_names, pr_date , submission_not_later_than_with_format, bidders);

      this.reawardContent(current_date,  mode_of_procurement_names , mode_of_procurement_ra_nos , app_types, 
                    allocated_budget, budget_in_words ,all_pr_supplier_names, pr_date , submission_not_later_than_with_format, bidders);

      this.rebidContent(current_date,  mode_of_procurement_names , mode_of_procurement_ra_nos , app_types, 
                    allocated_budget, budget_in_words ,all_pr_supplier_names, pr_date , submission_not_later_than_with_format , bidders);

    
   
      
    },

    awardContent(current_date, mode_of_procurement_names , mode_of_procurement_ra_nos , app_types, 
                allocated_budget, budget_in_words ,all_pr_supplier_names, pr_date, submission_not_later_than_with_format, bidders){

      // Filter only awarded quotation
      const awarded_quotations = (this.procurement.quotations ).filter(quotation =>
        (quotation.items).some(item => item.status_id == 43 && item.bid_price != null && item.is_rebid == 0)
      );

      const awarded_supplier_names = (awarded_quotations.map(quotation => quotation.supplier?.name).filter(Boolean).join(", ")).toUpperCase();
      const bidder_count = new Set(bidders).size;

      const bid_type_label_cap = (bidder_count === 1) ? "SINGLE" : "LOWEST";
      const bid_type_label_small_cap = (bidder_count === 1) ? "single" : "lowest";

     
      let counter = 2;
      const awarded_quotations_list = awarded_quotations.map(quotation => {
        const filtered_items = (quotation.items).filter(item => item.status_id == 43  && item.is_rebid == 0);
        if (filtered_items.length === 0) return "";
        const item_numbers = filtered_items.map(item => item.item.item_no).join(", ");
        const total_price = filtered_items.reduce((sum, item) => {
          const bp = parseFloat(item.bid_price) || 0;
          const bq = parseFloat(item.item.item_quantity) || 0;
          return sum + bp * bq;
        }, 0);
        const roman = this.toRoman(counter++);
        return `
          <li style="text-align: justify;  margin-bottom: 1em; ">
            <b>${roman}.</b> To recommend to the Head of Department of Science and Technology
            Regional Office No. IX for her consideration and approval of the award
            of contract for <b>Item No. ${item_numbers}</b> of the project entitled  <b>"${this.procurement.title.toUpperCase()}"</b>
            to the ${bid_type_label_small_cap} calculated and responsive bidder, <b>${quotation.supplier?.name}</b>,
            with the total contract amount of <b>Php${Number(total_price).toLocaleString()}</b> only.
          </li>
        `;
      }).join("");

      // === CORRECT total accumulation across awarded bids ===
      const award_bid_total_price = awarded_quotations.reduce((total, bid) => {
        const filtered_items = (bid.bid_items || []).filter(item => item.status_id == 43 && item.is_rebid == 0);
        const total_price = filtered_items.reduce((sum, quotation) => {
          const bp = parseFloat(quotation.bid_price) || 0;
          const bq = parseFloat(quotation.item_quantity) || 0;
          return sum + bp * bq;
        }, 0);
        return total + total_price; // accumulate correctly
      }, 0);

      const total_amount_contract_in_words = this.numberToWords(award_bid_total_price);

      const awarded_table_rows = awarded_quotations
        .map(quotation => {
          const filtered_items = (quotation.items).filter(item => item.status_id == 43 && item.is_rebid == 1);
          if (filtered_items.length === 0) return null;
          const item_ids = filtered_items.map(bid_item => bid_item.item_no).join(", ");
          return `
            <tr>
              <td>${quotation.supplier?.name}</td>
              <td>${item_ids}</td>
            </tr>
          `;
        })
        .filter(row => row !== null);

      const awarded_table_html = bidder_count.length > 2
      ? `
        <b>WHEREAS</b>, after thorough evaluation on the technical specifications of the bidders, the BAC 
        determined the following:
        <table style="width: 100%; border-collapse: collapse;" border="1">
          <tr>
            <th>Supplier</th>
            <th>Item No.</th>
          </tr>
          ${awarded_table_rows.join("")}
        </table>
      `
      : '';

      // ====== AWARD BODY ======
      this.award_body = `
        <div style=" font-size: 16px;">
          <p style="text-align: justify ;   text-align-last: center;  margin-bottom: 2em; ">
            <b>
              RECOMMENDING AWARD OF CONTRACT TO ${awarded_supplier_names}, AS THE ${bid_type_label_cap} CALCULATED AND RESPONSIVE BIDDER FOR THE PROCUREMENT
              "${this.procurement.title.toUpperCase()}" UNDERTAKEN THROUGH SECTION 53.9 (${mode_of_procurement_names})
              OF THE REVISED IMPLEMENTING RULES AND REGULATIONS OF (${mode_of_procurement_ra_nos})
            </b>
          </p>
          <p style="text-align: justify;  margin-bottom: 1em; ">
            <b>WHEREAS</b>, the Regional Director, Ms. ${this.procurement.approved_by.profile.fullname}, approved the DOST-IX ${app_types } for CY${new Date(this.procurement.date).getFullYear()} upon favorable recommendation of the Bids and Awards Committee;
          </p>

          <p style="text-align: justify;  margin-bottom: 1em; ">
            <b>WHEREAS</b>, the  ${ app_types } (Annex "A") contains the procurement
            <i style="font-size:12px">"${this.procurement.title.toUpperCase()}"</i>
            with allocated budget of ${budget_in_words} (PHP ${Number(allocated_budget || 0).toLocaleString()}),
            to be procured through Section 53.9 of the revised Implementing Rules and Regulations (IRR) of ${ mode_of_procurement_ra_nos} ;
          </p>

          <p style="text-align: justify; margin-bottom: 1em; ">
            <b>WHEREAS</b>, the BAC has duly received an approved purchase request for the procurement titled
            "${this.procurement.title.toUpperCase()}" to be bid per item. Detailed technical
            specifications pertaining to this procurement are meticulously outlined in
            PR no. ${this.procurement.code} dated ${pr_date} (refer to Annex "B");
          </p>
           
          <p style="text-align: justify;  margin-bottom: 1em; ">
            <b>WHEREAS</b>, the BAC initiated the procurement through its secretariat through dissemination
            of the request for quotation to at least four suppliers of known qualifications in
            Zamboanga City to wit: ${all_pr_supplier_names};
          </p>
           
          <p style="text-align: justify;  margin-bottom: 1em; ">
            <b>WHEREAS</b>, among the above-mentioned bidders, ${awarded_supplier_names} responded by submitting
            its price quotation to the BAC before opening of bids on ${ submission_not_later_than_with_format }.
          </p>
           
          ${awarded_table_html}
          <p style="text-align: justify;  margin-bottom: 1em; ">
            <b>NOW, THEREFORE,</b> We the members of the Bids and Awards Committee, by virtue of the powers vested on Us by the Law, hereby RESOLVE as it hereby RESOLVED;
          </p>
  
          <p style="text-align: justify;  margin-bottom: 1em; margin-top: 20px; margin-left: 0 ">
            <ul style="list-style-type: none;">
              <li style="text-align: justify; margin-bottom: 1em; "><b>I. </b> To declare ${awarded_supplier_names} as the ${bid_type_label_small_cap} Calculated and Responsive Bidder of the procurement for <b>"${this.procurement.title.toUpperCase()}"</b>
              </li>
              ${awarded_quotations_list}
            </ul>
          </p>

          <p style="text-align: justify;  margin-bottom: 1em; ">
            <b>RESOLVED</b>, at the Department of Science and Technology Regional Office IX, Pettit Barracks, Zamboanga City this ${current_date}.
          </p>

        </div>
      `;

    },

    reawardContent(current_date, mode_of_procurement_names , mode_of_procurement_ra_nos , app_types, 
                        allocated_budget, budget_in_words ,all_pr_supplier_names, pr_date, submission_not_later_than_with_format, bidders){
      
        // Filter rfq suppliers who bid same bid items
      const reawarded_quotations = (this.procurement.quotations).filter(quotation =>
        (quotation.items).some(item => item.status.id == 43 && item.bid_price != null)
      );

      console.log(this.procurement.reawarded_count , 11);

      const bidder_count = new Set(bidders).size;

      const bid_type_cap = (bidder_count === 1) ? "SINGLE" : "LOWEST";
      const bid_type_small_cap = (bidder_count === 1) ? "single" : "lowest";

      let counter = 2;
      const reawarded_quotations_list = reawarded_quotations.map(quotation => {
        const filtered_items = (quotation.items).filter(item => item.status_id == 43);
        if (filtered_items.length === 0) return "";
        const item_numbers = filtered_items.map(item => item.item.item_no).join(", ");
        const total_price = filtered_items.reduce((sum, item) => {
          const bp = parseFloat(item.bid_price);
          const bq = parseFloat(item.item.item_quantity);
          return sum + bp * bq;
        }, 0);
        const roman = this.toRoman(counter++);
        return `
          <li style="text-align: justify;  margin-bottom: 1em; ">
            <b>${roman}.</b> To recommend to the Head of Department of Science and Technology
            Regional Office No. IX for her consideration and approval of the award
            of contract for <b>Item No. ${item_numbers}</b> of the project entitled  <b>"${this.procurement.title.toUpperCase()}"</b>
            to the ${bid_type_small_cap} calculated and responsive bidder, <b>${quotation.supplier?.name}</b>,
            with the total contract amount of <b>Php${Number(total_price).toLocaleString()}</b> only.
          </li>
        `;
      }).join("");

      const reawarded_supplier_names = (reawarded_quotations.map(quotation => quotation.supplier?.name).filter(Boolean).join(", ")).toUpperCase();
  

      // === CORRECT total accumulation across reawarded bids ===
      const reawarded_bid_total_price = reawarded_quotations.reduce((total, bid) => {
        const filtered_bid_details = (bid.bid_items || []).filter(bid_item => bid_item.status_id == 43);
        const total_price_for_bid = filtered_bid_details.reduce((sum, detail) => {
          const bp = parseFloat(detail.item_bid_price) || 0;
          const bq = parseFloat(detail.item_quantity) || 0;
          return sum + bp * bq;
        }, 0);
        return total + total_price_for_bid; // accumulate correctly
      }, 0);

      const reaward_total_amount_contract_in_words = this.numberToWords(reawarded_bid_total_price);

      const reawarded_table_rows = reawarded_quotations
        .map(bid => {
          const filtered_bid_items = (bid.bid_items || []).filter(bid_item =>  bid_item.status_id == 43);
          if (filtered_bid_items.length === 0) return null;
          const bid_item_ids = filtered_bid_items.map(bid_item => bid_item.item_no).join(", ");
          return `
            <tr>
              <td>${bid.supplier?.name}</td>
              <td>${bid_item_ids}</td>
            </tr>
          `;
        })
        .filter(row => row !== null);

      const reawarded_table_html = bidder_count.length > 2
      ? `
        <b>WHEREAS</b>, after thorough evaluation on the technical specifications of the bidders, the BAC 
        determined the following:
        <table style="width: 100%; border-collapse: collapse;" border="1">
          <tr>
            <th>Supplier</th>
            <th>Item No.</th>
          </tr>
          ${reawarded_table_rows.join("")}
        </table>
      `
      : '';

      // ====== REAWARD BODY ======
      this.reaward_body = `
        <div style=" font-size: 16px;">
          <p style="text-align: justify ;   text-align-last: center;  margin-bottom: 2em; ">
            <b>
              RECOMMENDING FOR RE-AWARD OF CONTRACT TO ${reawarded_supplier_names}, AS THE ${this.toOrdinal(this.procurement.reawarded_count + 1)} ${bid_type_cap} CALCULATED AND RESPONSIVE BIDDER FOR THE PROCUREMENT
              "${this.procurement.title.toUpperCase()}" UNDERTAKEN THROUGH SECTION 53.9 (${mode_of_procurement_names})
              OF THE REVISED IMPLEMENTING RULES AND REGULATIONS OF (${mode_of_procurement_ra_nos})
            </b>
          </p>
          <p style="text-align: justify;  margin-bottom: 1em; ">
            <b>WHEREAS</b>, the Regional Director, Ms. ${this.procurement.approved_by.profile.fullname}, approved the DOST-IX ${app_types } for CY${new Date(this.procurement.date).getFullYear()} upon favorable recommendation of the Bids and Awards Committee;
          </p>

          <p style="text-align: justify;  margin-bottom: 1em; ">
            <b>WHEREAS</b>, the  ${ app_types } (Annex "A") contains the procurement
            <i style="font-size:12px">"${this.procurement.title.toUpperCase()}"</i>
            with allocated budget of ${budget_in_words} (PHP ${Number(allocated_budget).toLocaleString()}),
            to be procured through Section 53.9 of the revised Implementing Rules and Regulations (IRR) of ${ mode_of_procurement_ra_nos} ;
          </p>

          <p style="text-align: justify; margin-bottom: 1em; ">
            <b>WHEREAS</b>, the BAC has duly received an approved purchase request for the procurement titled
            "${this.procurement.title.toUpperCase()}" to be bid per item. Detailed technical
            specifications pertaining to this procurement are meticulously outlined in
            PR no. ${this.procurement.code} dated ${pr_date} (refer to Annex "B");
          </p>
           
          <p style="text-align: justify;  margin-bottom: 1em; ">
            <b>WHEREAS</b>, the BAC initiated the procurement through its secretariat through dissemination
            of the request for quotation to at least four suppliers of known qualifications in
            Zamboanga City to wit: ${all_pr_supplier_names};
          </p>
           
          <p style="text-align: justify;  margin-bottom: 1em; ">
            <b>WHEREAS</b>, among the above-mentioned bidders, ${reawarded_supplier_names} responded by submitting
            its price quotation to the BAC before opening of bids on ${ submission_not_later_than_with_format }.
          </p>
           
          ${reawarded_table_html}
          <p style="text-align: justify;  margin-bottom: 1em; ">
            <b>NOW, THEREFORE,</b> We the members of the Bids and Awards Committee, by virtue of the powers vested on Us by the Law, hereby RESOLVE as it hereby RESOLVED;
          </p>
  
          <p style="text-align: justify;  margin-bottom: 1em; margin-top: 20px; margin-left: 0 ">
            <ul style="list-style-type: none;">
              <li style="text-align: justify; margin-bottom: 1em; "><b>I. </b> To declare ${reawarded_supplier_names} as the ${bid_type_small_cap} Calculated and Responsive Bidder of the procurement for 
                <b>"${this.procurement.title.toUpperCase()}"</b>
              </li>
              ${reawarded_quotations_list}
            </ul>
          </p>

          <p style="text-align: justify;  margin-bottom: 1em; ">
            <b>RESOLVED</b>, at the Department of Science and Technology Regional Office IX, Pettit Barracks, Zamboanga City this ${current_date}.
          </p>

        </div>
      `;

    },

    rebidContent(current_date, mode_of_procurement_names , mode_of_procurement_ra_nos , app_types, 
                        allocated_budget, budget_in_words ,all_pr_supplier_names, pr_date, submission_not_later_than_with_format, bidders){
    
        // Filter only awarded quotation
      const awarded_quotations = (this.procurement.quotations).filter(quotation =>
        (quotation.items).some(item => (item.status_id == 43 && item.bid_price != null) || item.is_rebid == 0)
      );

      const awarded_supplier_names = (awarded_quotations.map(quotation => quotation.supplier?.name).filter(Boolean).join(", ")).toUpperCase();
      const bidder_count = new Set(bidders).size;
      const bid_type_label_cap = (bidder_count === 1) ? "SINGLE" : "LOWEST";
      const bid_type_label_small_cap = (bidder_count === 1) ? "single" : "lowest";

     
      let counter = 2;
      const awarded_quotations_list = awarded_quotations.map(quotation => {
        const filtered_items = (quotation.items).filter(item => item.status_id == 43 || item.is_rebid == 0);
        if (filtered_items.length === 0) return "";
        const item_numbers = filtered_items.map(item => item.item.item_no).join(", ");
        const total_price = filtered_items.reduce((sum, item) => {
          const bp = parseFloat(item.bid_price) || 0;
          const bq = parseFloat(item.item.item_quantity) || 0;
          return sum + bp * bq;
        }, 0);
        const roman = this.toRoman(counter++);
        return `
          <li style="text-align: justify;  margin-bottom: 1em; ">
            <b>${roman}.</b> To recommend to the Head of Department of Science and Technology
            Regional Office No. IX for her consideration and approval of the award
            of contract for <b>Item No. ${item_numbers}</b> of the project entitled  <b>"${this.procurement.title.toUpperCase()}"</b>
            to the ${bid_type_label_small_cap} calculated and responsive bidder, <b>${quotation.supplier?.name}</b>,
            with the total contract amount of <b>Php${Number(total_price).toLocaleString()}</b> only.
          </li>
        `;
      }).join("");


      // === CORRECT total accumulation across awarded bids ===
      const award_bid_total_price = awarded_quotations.reduce((total, quotation) => {
        const filtered_items = (quotation.items).filter(item => item.status_id == 43 || item.status?.id == 43 || item.is_rebid == 0);
        const total_price = filtered_items.reduce((sum, item) => {
          const bp = parseFloat(item.bid_price) || 0;
          const bq = parseFloat(item.item.item_quantity) || 0;
          return sum + bp * bq;
        }, 0);
        return total + total_price; // accumulate correctly
      }, 0);

      const total_amount_contract_in_words = this.numberToWords(award_bid_total_price);


      // ====== REBID BODY ======
      this.rebid_body = `
        <div style=" font-size: 16px;">
          <p style="text-align: center;"><b>DECLARATION OF FAILURE OF BIDDING FOR THE PROCUREMENT "${this.procurement.title.toUpperCase()}"</b></p>
          <p style="text-align: justify"><b>WHEREAS</b>, the Regional Director, Ms. ${this.procurement.approved_by.profile.fullname}, approved the DOST-IX 2nd Supplemental Annual Procurement Plan for CY${new Date().getFullYear()} upon favorable recommendation of the Bids and Awards Committee;</p>
          <p style="text-align: justify">
            <b>WHEREAS</b>, the ${ app_types } (Annex "A") contains the procurement
            <i style="font-size:12px">"${this.procurement.title.toUpperCase()}"</i>
            with allocated budget of ${budget_in_words} (PHP ${Number(allocated_budget).toLocaleString()}),
            to be procured through Section 53.9 of the revised IRR of ${mode_of_procurement_ra_nos};
          </p>
          <p style="text-align: justify">
            <b>WHEREAS</b>, the BAC received an approved Purchase Request (PR No. ${this.procurement.code}, dated ${pr_date})
            from the end-user for the <b>"${this.procurement.title.toUpperCase()}"</b>, with a total Approved Budget for the Contract (ABC) of
            <b>${total_amount_contract_in_words}</b> (PHP ${Number(award_bid_total_price).toLocaleString()}), to be procured on a per-item basis and following detailed technical specifications (Annex “B”);
          </p>
          <p style="text-align: justify">
            <b>WHEREAS</b>, the BAC, in full compliance with  ${mode_of_procurement_ra_nos}; and its IRR, initiated the procurement process by issuing Requests for Quotation (RFQs) 
            to at least three (3) suppliers with established qualifications, namely: ${all_pr_supplier_names};
          </p>
          <p style="text-align: justify">
            <b>WHEREAS</b>, upon opening and evaluation of the bid documents, the BAC found that ${awarded_supplier_names} met the legal requirements but failed to comply with the technical specifications; thus, no responsive and eligible bid was identified;
          </p>
          <p style="text-align: justify">
            <b>NOW, THEREFORE,</b> We the members of the Bids and Awards Committee, by virtue of the powers vested on Us by the Law, hereby RESOLVE as it hereby RESOLVED;
          </p>
          <p style="text-align: justify; margin-top:-20px">
           <ul style="list-style-type:none; padding-left:0; text-align:justify;">
            <div style="height:1em;"></div>

            <li style="padding-left:2em; text-align:justify; text-indent:-1.5em;">
              <b>I.</b> To declare a failure of bidding for the project 
              <b>"${this.procurement.title.toUpperCase()}"</b> due to the absence of a technically compliant bid;
            </li>

            <div style="height:1em;"></div>

          <li style="padding-left:2em; text-align:justify; text-indent:-1.5em;">
            <b>II.</b> To recommend to the Head of the Procuring Entity (HOPE), the Regional Director of DOST-IX, 
            the immediate review and evaluation of the causes of the bidding failure and to undertake appropriate 
            measures or revisions in accordance with the IRR of 
            ${this.procurement.pap_codes?.[0]?.pap_code?.mode_of_procurement?.republic_act_number};
          </li>

          <div style="height:1em;"></div>

          <li style="padding-left:2em; text-align:justify; text-indent:-1.5em;">
            <b>III.</b> To recommend the conduct of a re-bidding for the same project, subject to the necessary 
            adjustments and compliance with applicable procurement rules and procedures.
          </li>

        </ul>
          </p>
          <p style="text-align: justify"><b>RESOLVED</b>, at the Department of Science and Technology Regional Office IX, Pettit Barracks, Zamboanga City this ${current_date}.</p>
        </div>
      `;
    },


    submit() {
      if(this.editable){
        this.form.put('/faims/bac-resolutions/'+this.form.id);
        
      }
      else{
        this.form.post('/faims/bac-resolutions');
      }
      
      this.$emit('add', true);
      this.hide();
   
     
    },
  }
};
</script>
