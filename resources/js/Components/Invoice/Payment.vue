
<template>
    <div class="container-fluid">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                            <div class="card-body">
                                <h4>Payment</h4>
                                <input id="id" name="id" v-model="form.payment"  class="form-control" type="text"/>
                                <button type="button" @click="submit" class="btn w-100 btn-success">Payment</button>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import {useForm } from '@inertiajs/vue3';
const form = useForm({payment:''})
const id=new URLSearchParams(window.location.search).get('customer_id');

function submit(){
    form.post(`/payment?customer_id=${id}`);

}

</script>
