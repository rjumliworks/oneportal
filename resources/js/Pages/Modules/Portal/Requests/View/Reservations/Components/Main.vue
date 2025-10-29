<template>
    <div class="card bg-light-subtle shadow-none border">
        <div class="card-header bg-light-subtle">
            <div class="d-flex mb-n3">
                <div class="flex-shrink-0 me-3">
                    <div style="height:2.5rem;width:2.5rem;">
                        <span class="avatar-title bg-primary-subtle rounded p-2 mt-n1">
                            <i class="ri-chat-history-fill text-primary fs-24"></i>
                        </span>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-0 fs-14"><span class="text-body">Travel Order Activity</span></h5>
                    <p class="text-muted text-truncate-two-lines fs-12">Includes official comments, document references, and chronological status updates</p>
                </div>
            </div>
        </div>
        <div class="card-body bg-white border-bottom border-bottom">
            <div class="row g-3 p-0">
                <div class="col-md-6">
                    <div class="d-flex border border-dashed rounded p-3">
                        <div class="flex-shrink-0 avatar-xs align-self-center me-3">
                            <div v-if="information.status.name == 'Pending'" class="avatar-title bg-light rounded-circle fs-16 text-warning">
                                <i class="ri-record-circle-fill"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="mb-0 text-muted fs-12">Status :</p>
                            <h6 class="text-truncate fw-semibold fs-12 mb-0"> {{information.status.name}} </h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex border border-dashed rounded p-3">
                        <div class="flex-shrink-0 avatar-xs align-self-center me-3">
                            <div class="avatar-title bg-light rounded-circle fs-16 text-primary">
                                <i :class="(information.approved) ? 'text-success' : 'text-warning'" class="ri-emotion-fill"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="mb-0 fs-12">Approved By :</p>
                            <h6 v-if="information.approved" class="text-truncate fw-semibold fs-12 mb-0"> {{ information.approved }} </h6>
                            <span class="text-muted fs-11 mb-0"> not yet set </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body bg-white rounded-bottom" style="height: calc(100vh - 515px); overflow: auto;">
            <div class="d-flex mb-4">
                <div class="flex-shrink-0">
                    <img src="/assets/images/users/avatar-8.jpg" alt="" class="avatar-xs rounded-circle" />
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="fs-13">Joseph Parker <small class="text-muted ms-2">20
                            Dec 2021 - 05:47AM</small></h5>
                    <p class="text-muted">I am getting message from customers that when
                        they place order always get error message .</p>
                    <BLink href="javascript: void(0);" class="badge text-muted bg-light"><i class="mdi mdi-reply"></i>
                        Reply</BLink>
                    <div class="d-flex mt-4">
                        <div class="flex-shrink-0">
                            <img src="/assets/images/users/avatar-10.jpg" alt="" class="avatar-xs rounded-circle" />
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="fs-13">Alexis Clarke <small class="text-muted ms-2">22 Dec 2021 -
                                    02:32PM</small></h5>
                            <p class="text-muted">Please be sure to check your Spam
                                mailbox to see if your email filters have identified the
                                email from Dell as spam.</p>
                            <BLink href="javascript: void(0);" class="badge text-muted bg-light"><i class="mdi mdi-reply"></i> Reply
                            </BLink>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex mb-4">
                <div class="flex-shrink-0">
                    <img src="/assets/images/users/avatar-6.jpg" alt="" class="avatar-xs rounded-circle" />
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="fs-13">Donald Palmer <small class="text-muted ms-2">24
                            Dec 2021 - 05:20PM</small></h5>
                    <p class="text-muted">If you have further questions, please contact
                        Customer Support from the “Action Menu” on your <BLink href="javascript:void(0);"
                            class="text-decoration-underline">
                            Online Order Support
                        </BLink>.
                    </p>
                    <BLink href="javascript: void(0);" class="badge text-muted bg-light"><i class="mdi mdi-reply"></i>
                        Reply</BLink>
                </div>
            </div>
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <img src="/assets/images/users/avatar-10.jpg" alt="" class="avatar-xs rounded-circle" />
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="fs-13">Alexis Clarke <small class="text-muted ms-2">26
                            min ago</small></h5>
                    <p class="text-muted">Your <BLink href="javascript:void(0)" class="text-decoration-underline">Online Order
                            Support
                        </BLink>
                        provides you with the most current status of your order. To help
                        manage your order refer to the “Action Menu” to initiate return,
                        contact Customer Support and more.</p>
                    <BRow class="g-2 mb-3">
                        <BCol lg="1" sm="2" cols="6">
                            <img src="/assets/images/small/img-4.jpg" alt="" class="img-fluid rounded">
                        </BCol>
                        <BCol lg="1" sm="2" cols="6">
                            <img src="/assets/images/small/img-5.jpg" alt="" class="img-fluid rounded">
                        </BCol>
                    </BRow>
                    <BLink href="javascript: void(0);" class="badge text-muted bg-light"><i class="mdi mdi-reply"></i>
                        Reply</BLink>
                    <div class="d-flex mt-4">
                        <div class="flex-shrink-0">
                            <img src="/assets/images/users/avatar-6.jpg" alt="" class="avatar-xs rounded-circle" />
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="fs-13">Donald Palmer <small class="text-muted ms-2">8
                                    sec ago</small></h5>
                            <p class="text-muted">Other shipping methods are available
                                at checkout if you want your purchase delivered faster.
                            </p>
                            <BLink href="javascript: void(0);" class="badge text-muted bg-light"><i class="mdi mdi-reply"></i> Reply
                            </BLink>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       <div class="card-footer">
            <form>
                <BRow class="g-0 align-items-center">
                    <BCol cols="auto">
                        <div class="chat-input-links me-2">
                            <div class="links-list-item">
                                <BButton type="button" variant="link" class="text-decoration-none emoji-btn" id="emoji-btn">
                                    <i class="bx bx-smile align-middle"></i>
                                </BButton>
                            </div>
                        </div>
                    </BCol>
                    <BCol>
                        <input type="text" class="form-control chat-input bg-light border-light" placeholder="Add Comment.." >
                    </BCol>
                    <BCol cols="auto">
                        <div class="chat-input-links ms-2">
                            <div class="links-list-item">
                                <BButton variant="success" type="submit" class="chat-send">
                                    <i class="ri-send-plane-2-fill align-bottom"></i>
                                </BButton>
                            </div>
                        </div>
                    </BCol>
                </BRow>
            </form>
       </div>
        
    </div>
</template>
<script>
import simplebar from "simplebar-vue";
import Multiselect from "@vueform/multiselect";
export default {
    components: { simplebar, Multiselect },
    props: ['information'],
}
</script>