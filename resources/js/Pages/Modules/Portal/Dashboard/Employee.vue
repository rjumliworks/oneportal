<template>
    <Head title="Dashboard"/>
    <PageHeader title="Dashboard" pageTitle="List" />
    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-n4 p-1">
      <div class="file-manager-sidebar">
           <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td style="border-right: none; border-left: none; cursor: pointer;" @click="openInformation()"><span class="fw-semibold fs-12 ms-2">TSR Information</span></td>
                    </tr>
                </tbody>
           </table>
      </div>
      <div class="file-manager-content w-100 p-3 py-0">
        
      </div>
      <div class="file-manager-sidebar">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td style="border-right: none; border-left: none; cursor: pointer;" @click="openInformation()">
                            <span class="fw-semibold fs-12 ms-2">DTR Summary</span>
                            <!-- <span class="text-muted fs-12 float-end">{{  employee.dtr.data.date }}</span> -->
                        </td>
                    </tr>
                    <tr>
                        <td style="border-right: none; border-left: none;">
                            <div class="table-responsive bg-white">
                                <table class="table align-middle table-bordered table-centered mb-0">
                                    <thead class="table-light thead-fixed">
                                        <tr class="fs-11">
                                            <th style="width: 25%;" class="text-center">Am In</th>
                                            <th style="width: 25%;" class="text-center">Am Out</th>
                                            <th style="width: 25%;" class="text-center">Pm In</th>
                                            <th style="width: 25%;" class="text-center">Pm Out</th>
                                        </tr>
                                    </thead>
                                    <!-- <tbody class="table-white fs-12">
                                        <tr class="text-center text-muted">
                                            <td>
                                                {{ employee.dtr.data.am_in_at.time }}
                                            </td>
                                            <td>
                                                {{ employee.dtr.data.am_out_at.time }}
                                            </td>
                                            <td>
                                                {{ employee.dtr.data.pm_in_at.time }}
                                            </td>
                                            <td>
                                                {{ employee.dtr.data.pm_out_at.time }}
                                            </td>
                                        </tr>
                                    </tbody> -->
                                    
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-right: none; border-left: none; cursor: pointer;" @click="openInformation()"><span class="fw-semibold fs-12 ms-2">Credits Balance</span></td>
                    </tr>
                    <tr>
                        <td style="border-right: none; border-left: none; cursor: pointer;" @click="openInformation()"><span class="fw-semibold fs-12 ms-2">Leave Applications</span></td>
                    </tr>
                    <tr>
                        <td style="border-right: none; border-left: none; cursor: pointer;" @click="openInformation()"><span class="fw-semibold fs-12 ms-2">Travels</span></td>
                    </tr>
                </tbody>
           </table>
      </div>
    </div>
</template>
<script>
import PageHeader from '@/Shared/Components/PageHeader.vue';
export default {
    props: ['employee'],
    components: { PageHeader }
}
</script>