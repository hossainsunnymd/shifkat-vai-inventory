<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { createToaster } from "@meforma/vue-toaster";
import axios from 'axios';

const toaster = createToaster();
let page = usePage();

const selectedProduct = ref([]);
const selectedCustomer = ref(null);

const ProductHeader = [
    { text: "Image", value: "image" },
    { text: "Name", value: "name", sortable: true },
    { text: "Qty", value: "unit" },
    { text: "Action", value: "action" },
];

const CustomerHeader = [
    { text: "Customer", value: "name" },
    { text: "Pick", value: "number" },
];

const ProductItem = ref(page.props.product);
const CustomerItem = ref(page.props.customer);

const searchProductField = ref('name');
const searchProductValue = ref('');
const searchCustomerField = ref('name');
const searchCustomerValue = ref('');

const vatRate = ref(5);
const flatDiscount = ref(0);
const discountPercent = ref(0);
const total = ref(0);
const vatAmount = ref(0);
const discountAmount = ref(0);
const usePercentageDiscount = ref(false);




// Function to increment product quantity
// const incrementQty = (product) => {
//     if (product.unit < product.exitsQty) {
//         product.unit += 1;
//     }
//     calculateTotal();  // Recalculate the total price after increment
// };

// // Function to decrement product quantity
// const decrementQty = (product) => {
//     if (product.unit > 1) {
//         product.unit -= 1;
//     }
//     calculateTotal();  // Recalculate the total price after decrement
// };



// const addProductToSale = (id, image, name, price, productUnit) => {
//     const product = {
//         id: id,
//         image: image,
//         name: name,
//         price: price,
//         unit: 1,
//         exitsQty: productUnit - 1,
//         productPrice: price
//     };
//     selectedProduct.value.push(product);
//     calculateTotal();
// };




async function addProductToSale(id, image, name, price, productUnit){
    if (productUnit > 0) {  // Check if product is in stock
        const product = {
            id: id,
            image: image,
            name: name,
            price: price,
            unit: 1,
            exitsQty: productUnit - 1,
            productPrice: price
        };

        selectedProduct.value.push(product);
        calculateTotal();
    } else {
        toaster.warning(`${name} is out of stock!`);
    }
};





const addQty = (id) => {
    const product = selectedProduct.value.find((product) => product.id === id);
    if (product.exitsQty > 0) {
        product.unit++;
        product.exitsQty--;  // Decrease available stock
        product.price=parseFloat(product.productPrice)*parseFloat(product.unit);
        calculateTotal();
    } else {
        toaster.warning(`${product.name} is out of stock!`);
    }
};




// const addQty = (id)=>{
//     const product = selectedProduct.value.find((product) => product.id === id);
//     if(product.exitsQty>=product.unit){
//         product.unit++;
//         //product.price=parseFloat(product.productPrice)*parseFloat(product.unit);
//     }

//     calculateTotal();
// }


const removeQty = (id) => {
    const product = selectedProduct.value.find((product) => product.id === id);
    if (product.unit > 1) {
        product.unit--;
        product.exitsQty++;  // Increase available stock
        product.price-=product.productPrice;
        calculateTotal();
    }
};


// const removeQty = (id)=>{
//     const product = selectedProduct.value.find((product) => product.id === id);
//     if(product.unit>1){
//         product.unit--;
//         //product.price-=product.productPrice;
//     }
//     calculateTotal();

// }

const removeProductFromSale = (index) => {
    selectedProduct.value.splice(index,1);
    calculateTotal();
};

const addCustomerToSale = (customer) => {
    selectedCustomer.value = customer;
};

// const calculateTotal = () => {
//     return selectedProduct.value.reduce((sum, product) => sum + (product.price * product.unit), 0);
// };

const calculateTotal = () => {
    return selectedProduct.value.reduce((sum, product) => sum + (product.productPrice * product.unit), 0);
};


const applyVat = () => {
    vatAmount.value = (calculateTotal() * vatRate.value) / 100;
    calculatePayable();
};

const removeVat = () => {
    vatAmount.value = 0;
    calculatePayable();
};

const applyDiscount = () => {
    if (usePercentageDiscount.value) {
        discountAmount.value = (calculateTotal() * discountPercent.value) / 100;
    } else {
        discountAmount.value = flatDiscount.value;
    }
    calculatePayable();
};

const removeDiscount = () => {
    discountAmount.value = 0;
    calculatePayable();
};

const calculatePayable = () => {
    const totalAmount = calculateTotal();
    payable.value = totalAmount + vatAmount.value - discountAmount.value;
};

const payable = ref(0);

const form = useForm({
    customer_id: '',
    product: '',
    vat: '1',
    discount: '1',
    payable: calculateTotal(),
    total: '',
});

const createInvoice = () => {
    form.product = selectedProduct.value;
    form.customer_id = selectedCustomer.value.id;
    form.total = total.value;
    form.payable = payable.value;

    const calculatedTotal = calculateTotal(); // Total amount before VAT and Discount

    form.total = calculatedTotal; // Set total price
    form.payable = payable.value;

    form.post('/invoice-create', {
        onSuccess: () => {
            if (page.props.flash.status === true) {
                toaster.success(page.props.flash.message);
                setTimeout(() => {
                    router.get('/invoice-list-page');
                }, 500);
            } else {
                toaster.warning(page.props.flash.message);
            }
        },
    });
};
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <!-- Billing Selection -->
            <div class="col-md-4 col-lg-4 p-2">
                <div class="card">
                    <div class="card-body">
                        <h4>Billed To</h4>
                        <div class="shadow-sm h-100 bg-white rounded-3 p-3 mt-4">
                            <div class="row">
                                <div class="col-8">
                                    <span class="text-bold text-dark">BILLED TO</span>
                                    <p class="text-xs mx-0 my-1">Name: <span>{{ selectedCustomer?.name || '' }}</span></p>
                                    <p class="text-xs mx-0 my-1">Email: <span>{{ selectedCustomer?.email || '' }}</span></p>
                                    <p class="text-xs mx-0 my-1">User ID: <span>{{ selectedCustomer?.id || '' }}</span></p>
                                </div>
                                <div class="col-4">
                                    <p class="text-bold mx-0 my-1 text-dark">Invoice</p>
                                    <p class="text-xs mx-0 my-1">Date: {{ new Date().toISOString().slice(0, 10) }}</p>
                                </div>
                            </div>
                            <hr class="mx-0 my-2 p-0 bg-secondary"/>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table w-100">
                                        <thead>
                                            <tr class="text-xs">
                                                <th>Name</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                                <th>Remove</th>
                                            </tr>
                                        </thead>
                                        <!-- <tbody>
                                            <tr v-for="(product,index) in selectedProduct" :key="product.id" class="text-xs">
                                                <td>{{ product.name }}</td>
                                                <td>
                                                    <button @click="removeQty(product.id)" class="btn btn-sm btn-danger">-</button>
                                                    {{ product.unit }}
                                                    <button @click="addQty(product.id)" class="btn btn-sm btn-success">+</button>
                                                </td>
                                                <td>{{ product.unit * product.price }}</td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm" @click="removeProductFromSale(index)">Remove</button>
                                                </td>
                                            </tr>
                                        </tbody> -->

                                        <tbody>
                                            <tr v-if="selectedProduct.length > 0" v-for="(product,index) in selectedProduct" :key="index" class="text-center">

                                                <td> {{ product['name'] }} </td>
                                                <td>{{ product['unit'] }}</td>
                                                <td> {{ product['price'] }}</td>
                                                <td>
                                                    <button @click="removeQty(product.id)" class="">-</button>
                                                    <button @click="addQty(product.id)" class="">+</button>
                                                    <button @click="removeProductFromSale(index)" class="btn btn-danger btn-sm">Remove</button>
                                                </td>
                                            </tr>
                                        </tbody>


                                    </table>
                                </div>
                            </div>
                            <hr class="mx-0 my-2 p-0 bg-secondary"/>
                            <div class="row">
                                <div class="col-12">
                                    <p class="text-bold text-xs my-1 text-dark">Total: <i class="bi bi-currency-dollar"></i> {{ calculateTotal() }}</p>
                                    <p class="text-bold text-xs my-1 text-dark">VAT ({{ vatRate }}%): <i class="bi bi-currency-dollar"></i> {{ vatAmount }}</p>
                                    <p><button class="btn btn-info btn-sm my-1 bg-gradient-primary w-40" @click="applyVat">Apply VAT</button></p>
                                    <p><button class="btn btn-secondary btn-sm my-1 bg-gradient-primary w-40" @click="removeVat">Remove VAT</button></p>
                                    <p><span class="text-xxs">Discount Mode:</span></p>
                                    <select v-model="usePercentageDiscount">
                                        <option :value="false">Flat Discount</option>
                                        <option :value="true">Percentage Discount</option>
                                    </select>
                                    <p class="text-bold text-xs my-1 text-dark">Discount: <i class="bi bi-currency-dollar"></i> {{ discountAmount }}</p>
                                    <div v-if="!usePercentageDiscount">
                                        <span class="text-xxs">Flat Discount:</span>
                                        <input type="number" v-model="flatDiscount" class="form-control w-40" min="0" />
                                        <p><button class="btn btn-warning btn-sm my-1 bg-gradient-primary w-40" @click="applyDiscount">Apply Flat Discount</button></p>
                                    </div>
                                    <div v-else>
                                        <span class="text-xxs">Discount (%):</span>
                                        <input type="number" v-model="discountPercent" class="form-control w-40" min="0" max="100" step="0.25" />
                                        <p><button class="btn btn-warning btn-sm my-1 bg-gradient-primary w-40" @click="applyDiscount">Apply Percentage Discount</button></p>
                                    </div>
                                    <p><button class="btn btn-secondary btn-sm my-1 bg-gradient-primary w-40" @click="removeDiscount">Remove Discount</button></p>
                                    <hr class="mx-0 my-2 p-0 bg-secondary"/>
                                    <p class="text-bold text-xs my-1 text-dark">Payable: <i class="bi bi-currency-dollar"></i> {{ payable }}</p>
                                    <p><button class="btn btn-success btn-sm my-3 bg-gradient-primary w-40" @click="createInvoice">Confirm</button></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Selection -->
            <div class="col-md-4 col-lg-4 p-2">
                <div class="card">
                    <div class="card-body">
                        <h4>Select Product</h4>
                        <input
                            placeholder="Search..."
                            class="form-control mb-2 w-auto form-control-sm"
                            v-model="searchProductValue"
                            type="text"
                        />
                        <EasyDataTable
                            buttons-paginations
                            alternating
                            :items="ProductItem"
                            :headers="ProductHeader"
                            :rows-per-page="10"
                            :search-value="searchProductValue"
                            :seach-field="searchProductField"
                        >
                            <template #item-image="{ image }">
                                <img :src="`/images/${image}`" alt="Product Image" height="40px" width="40px" />
                            </template>

                            <!-- <template #item-action="{ id,image,name,price,unit }">
                                <button
                                    class="btn btn-success btn-sm"
                                    @click="addProductToSale(id,image,name,price,unit)"
                                >
                                    Add
                                </button>

                            </template> -->

                            <template #item-action="{ id, image, name, price, unit }">
                                <button
                                    :class="['btn btn-sm', unit > 0 ? 'btn-success' : 'btn-warning']"
                                    :disabled="unit <= 0"
                                    @click="addProductToSale(id, image, name, price, unit)"
                                >
                                    {{ unit > 0 ? 'Add' : 'Stock Out' }}
                                </button>
                            </template>

                        </EasyDataTable>




                    </div>
                </div>
            </div>

            <!-- Customer Selection -->
            <div class="col-md-4 col-lg-4 p-2">
                <div class="card">
                    <div class="card-body">
                        <h4>Select Customer</h4>
                        <input
                            placeholder="Search..."
                            class="form-control mb-2 w-auto form-control-sm"
                            v-model="searchCustomerValue"
                            type="text"
                        />
                        <EasyDataTable
                            buttons-pagination
                            alternating
                            :headers="CustomerHeader"
                            :items="CustomerItem"
                            :rows-per-page="10"
                            :search-field="searchCustomerField"
                            :search-value="searchCustomerValue"
                        >
                            <template #item-number="{ id, name, email }">
                                <button class="btn btn-success btn-sm" @click="addCustomerToSale({ id, name, email })">Add</button>
                            </template>
                        </EasyDataTable>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
