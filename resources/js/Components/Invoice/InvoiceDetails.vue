<template>
    <div class="modal-backdrop">
        <div class="modal-content modal-lg modal-dialog-centered">
            <div class="modal-header">
                <h5 class="modal-title">Invoice Details</h5>
                <button type="button" class="btn-close" @click="closeModal"></button>
            </div>

            <div class="modal-body">
                <div class="container-fluid">

                    <div class="row" v-if="customer.length > 0">

                        <div class="col-8">
                            <strong>BILLED TO</strong>
                            <p class="mb-1">Name: <span>{{ customer[0].customer.name || 'N/A' }}</span></p>
                            <p class="mb-1">Email: <span>{{ customer[0].customer.email || 'N/A' }}</span></p>
                            <p class="mb-1">Customer ID: <span>{{ customer[0].customer.id || 'N/A' }}</span></p>
                        </div>

                        <div class="col-4 text-right">
                            <img class="w-40" src="../../Assets/img/logo.svg" alt="Company Logo" />
                            <p class="mb-1"><strong>Invoice</strong></p>
                            <p class="mb-1">Date: {{ new Date().toLocaleDateString() }}</p>
                        </div>
                    </div>

                    <hr class="my-2" />


                    <div v-for="(items,i) in customer" :key="i" class="row">
                        <div class="col-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Sale Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in items.invoice_product " :key="item.id">
                                        <td>{{ item.product.name  }}</td>
                                        <td>{{ item.qty || 0 }}</td>
                                        <td>{{ item.sale_price || 0 }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr class="my-2" />


                    <div class="row">
                        <div class="col-12">
                            <p>
                                <strong>Total:</strong>
                                {{ totalSalePrice }}
                            </p>
                            <p>
                                <strong>Total Payment:</strong>
                                {{ parseFloat(customer[0].customer.total_payment )}}
                            </p>
                            <p>
                                <strong>Last Payment:</strong>
                                {{ lastPayment }}
                            </p>
                            <p>
                                <strong>Due:</strong>
                                {{ totalAmount }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" @click="closeModal">Close</button>
                <button class="btn btn-primary" @click="printInvoice">Print</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";


const props = defineProps({
    customer: {
        type: Array,
        default: () => [],
    },
});



const emit = defineEmits(["close"]);


const closeModal = () => {
    emit("close");
};
const lastPayment = computed(() => {
    let payment= parseFloat(props.customer[props.customer.length-1].payment);
    if(payment==0){
        return parseFloat(props.customer[props.customer.length-2].payment)
    }else {
      return parseFloat(props.customer[props.customer.length-1].payment)
    }

});

const totalSalePrice = computed(() => {
    return props.customer.reduce((total, item) => {
        return total + item.invoice_product.reduce((sum, product) => sum + parseFloat(product.sale_price), 0);
    }, 0);
});


const printInvoice = () => {
    window.print();
};


const totalAmount = computed(() => {
    return props.customer.reduce((total, item) => total + (parseFloat(item.total) || 0), 0);
});
</script>

<style scoped>
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1500;
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    width: 80%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-body {
    max-height: 60vh;
    overflow-y: auto;
}

.w-40 {
    width: 40%;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
}
</style>
