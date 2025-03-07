
<template>
    <div class="container-fluid">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="float-end">
                            <Link href="/CategoryPage" class="btn btn-success mx-3 btn-sm">
                               Back
                            </Link>
                        </div>
                        <form @submit.prevent="submit">
                            <div class="card-body">
                                <h4>Save Category</h4>
                                <input id="id" name="id" v-model="form.id"  placeholder="Category ID" class="form-control" type="text"/>
                                <br/>
                                <input id="name" name="name" v-model="form.name"  placeholder="Category Name" class="form-control" type="text"/>
                                <br/>
                                <button type="submit"  class="btn w-100 btn-success">Save Change</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import {useForm,router,Link} from '@inertiajs/vue3'
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster();


const urlParams=new URLSearchParams(window.location.search)
let id=ref(parseInt(urlParams.get('id')))



const form = useForm({name:'',id:id})
import { usePage } from '@inertiajs/vue3';
import {ref} from "vue";
const page = usePage()

let URL="/create-category";
let list=page.props.list
if(id.value!==0 && list!==null){
    URL="/update-category";
    // fill the form with existing
    form.name=list['name']
    form.id=list['id']
    }
    


function submit(){
if(form.name.length===0){
    alert("Category name Required")
}
else  {
    form.post(URL,{
        onSuccess:()=>{
            if(page.props.flash.status===true){
                toaster.success(page.props.flash.message);
                setTimeout(()=>{
                    router.get("/CategoryPage")
                },500)
            }
            else {
                alert(page.props.flash.message)
            }
        }
    })
}

}

</script>
