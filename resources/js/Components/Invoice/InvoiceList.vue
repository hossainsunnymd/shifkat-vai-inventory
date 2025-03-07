<template>
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div>
                <h3>Invoice List</h3>
              </div>
              <hr />
              <div>
                <input
                  placeholder="Search..."
                  class="form-control mb-2 w-auto form-control-sm"
                  type="text"
                  v-model="searchValue"
                />
                <EasyDataTable
                  buttons-pagination
                  alternating
                  :headers="Header"
                  :items="Item"
                  :rows-per-page="10"
                  :search-field="searchField"
                  :search-value="searchValue"
                >

                  <template #item-number="{ customer_id }">

                    <button
                      @click="showDetails(customer_id)"
                      class="viewBtn btn btn-outline-dark text-sm px-3 py-1 btn-sm m-0"
                    >
                      <i class="fa text-sm fa-eye"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" @click="DeleteClick(customer_id)">Delete</button>
                    <Link class="btn btn-danger btn-success" :href="`/payment-page?customer_id=${customer_id}`">payment</Link>
                  </template>
                </EasyDataTable>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal component for invoice details -->
      <InvoiceDetails v-if="show" :customer="customer" @close="closeModal" />


    </div>
  </template>

  <script setup>
  import { useForm, router, Link } from '@inertiajs/vue3';
  import { computed } from 'vue';
  import { ref } from 'vue';
  import InvoiceDetails from './InvoiceDetails.vue'; // Make sure this path is correct
  import { usePage } from '@inertiajs/vue3';
  import { createToaster } from '@meforma/vue-toaster';

  const toaster = createToaster();
  const show = ref(false);
  const customer = ref([]);
  const page = usePage();

  const searchValue = ref();
  const searchField = ref(['customer.name']);

  const Header = [
    { text: 'No', value: 'id' },
    { text: 'Name', value: 'customer.name' },
    { text: 'Customer ID', value: 'customer_id' },
    { text: 'Phone', value: 'customer.mobile' },
    { text: 'Total', value: 'total' },
    { text: 'Discount', value: 'discount' },
    { text: 'Vat', value: 'vat' },
    { text: 'Payable', value: 'payable' },
    { text: 'Action', value: 'number' }
  ];

  const uniqueItems = computed(()=>{
    const customerSet=new Set();
    return page.props.list.filter((item) => {
      if (!customerSet.has(item.customer_id)) {
        customerSet.add(item.customer_id);
        return true;
      }
    });
  });

  const Item = ref(uniqueItems);



  if (page.props.flash.status === true) {
    toaster.success(page.props.flash.message);
  }

  if (page.props.flash.status === false) {
    toaster.warning(page.props.flash.message);
  }

  const showDetails = (id) => {
    show.value = !show.value;
    customer.value = page.props.list.filter((item) => item.customer_id === id);

  };

  const closeModal = () => {
    show.value = false;
  };

//   function payment(id) {
//     router.get(`/payment?customer_id=${id}`);
//   }

  const DeleteClick = (id) => {
    // alert(id);
    let text = 'Do you want to delete';
    if (confirm(text) === true) {
      router.get(`/invoice-delete/${id}`);
    } else {
      text = 'You canceled!';
    }
  };
  </script>
