<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header mb-3">
                    <h3 class="card-title">List of All Products</h3>
                    <router-link to="/createProduct" class="btn btn-success float-right"><i class="fas fa-plus-circle"></i> Create</router-link>
                    </div>
            

            <div class="d-flex justify-content-between align-content-center mb-2">
            <div class="d-flex">
                <div>
                    <div class="d-flex align-items-center ml-4">
                        <label for="paginate" class="text-nowrap mr-2 mb-0"
                            >Per Page</label
                        >
                        <select v-model="paginate" class="form-control form-control-sm">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-center ml-4">
                        <label for="paginate" class="text-nowrap mr-2 mb-0"
                            >FilterBy Category</label
                        >
                        <select v-model="selectedCategory" class="form-control form-control-sm">
                            <option value="">All Category</option>
                            <option v-for="item in category" :key="item.id" :value="item.id">{{item.name}}</option>
                        </select>
                    </div>
                </div>

             

                <div>
                    <div class="dropdown ml-4">
                        <button v-if="checked.length > 0"
                            class="btn btn-secondary dropdown-toggle"
                            data-toggle="dropdown"
                        >
                            With Checked ({{ checked.length }})
                        </button>
                        <div class="dropdown-menu">
                            <a href="#" class="dropdown-item" type="button" @click.prevent="deleteRecords">
                                Delete
                            </a>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <input v-model="search"
                    type="search"
                    class="form-control"
                    placeholder="Search by product name"
                />
            </div>
        </div>

        <div class="col-md-10 mb-2" v-if="selectPage">
            <div v-if="selectAll">
                You are currently selecting all
                <strong>{{checked.length}}</strong> items.
            </div>
            <div v-else>
                You have selected <strong>{{checked.length}}</strong> items, Do you want to
                Select All <strong>{{products .meta.total}}</strong>?
                <a @click.prevent="selectAllRecords" href="#" class="ml-2">Select All</a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <th><input type="checkbox" v-model="selectPage"  /></th>
                        <th>
                            <a href="#" @click.prevent="change_sort('name')">Product Name</a>
                            <span v-if="sort_direction == 'desc' && sort_field == 'name'">&uarr;</span>
                            <span v-if="sort_direction == 'asc' && sort_field == 'name'">&darr;</span>
                        </th>
                        
                        <th>
                            Category
                        
                        </th>
                       
                       
                        <th>
                             <a href="#" @click.prevent="change_sort('datetime')">Date</a>
                            <span v-if="sort_direction == 'desc' && sort_field == 'datetime'">&uarr;</span>
                            <span v-if="sort_direction == 'asc' && sort_field == 'datetime'">&darr;</span>
                        </th>
                        
                        <th></th>
                        <th></th>
                    </tr>

                    <tr v-for="product in products.data" :key="product.id">
                        <td>
                            <input type="checkbox"  :value="product.id" v-model="checked"/>
                        </td>
                        <td>{{ product.name }}</td>
                        <td>{{ product.category }}</td>
                      
                        
                        <td>{{ product.datetime | formatDate }} </td>
                        
                        <td>
                            <button class="btn btn-danger btn-sm" @click="deleteSingleRecord(product.id)">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                        <td>
                            <router-link :to="{name: 'edit', params: { id: product.id }}" class="btn btn-success btn-sm"> <i class="fas fa-edit"></i></router-link>
                                
                            
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row mt-4">
            <div class="col-sm-6 offset-5">
                <pagination :data="products" @pagination-change-page="getProducts"></pagination>
            </div>
        </div>
        </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            products: {},
            paginate : 10,
            search : "",
            selectedCategory : "",
            checked : [],
            selectPage : false,
            SelectAll : false,
            sort_direction : 'desc',
            sort_field : 'datetime',
            category: ''
        }
    },
    watch:{
        paginate: function(value){
            this.getProducts();
        },
        search: function(value){
            this.getProducts();
        },
        selectedCategory: function(value){
            this.getProducts();
        },
        selectPage: function(value){
            this.checked = [];
            if(value){
                this.products.data.forEach(product => {
                    this.checked.push(product.id);
                });
            }else{
                this.checked = [];
                this.selectAll = false;
            }
        },
    },
    methods: {
        selectAllRecords(){
            axios.get('api/products/all')
            .then(response =>{
                this.checked = response.data;
                this.selectAll = true;
            })
            
        },
        change_sort(field){
            if(this.sort_field == field){
                this.sort_direction  = this.sort_direction == "asc" ? "desc" : "asc";
            }else{
                this.sort_field = field;
            }
            this.getProducts();

        },
        deleteRecords(){
            Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
            if (result.isConfirmed) {
                axios.delete('/api/products/massDestroy/' + this.checked)
                .then(response =>{
                    if(response.status == 204){
                        
                        this.checked=[];
                        this.getProducts();
                    }
                });

                Swal.fire(
                'Deleted!',
                'Your file has been deleted.',
                'success'
                )
            }
            })

            
        },
        deleteSingleRecord(product_id){
          
              Swal.fire({
                title: 'Are you sure want to delete the product?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete('/api/product/delete/' + product_id)
                    .then(response => {
                        this.checked = this.checked.filter(id => id != product_id)
                       
                        this.getProducts();
                    })

                    Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                    )
                }
                })
              
        },
      
        getProducts(page = 1){
            axios.get('/api/products?page='+ page 
            + '&paginate=' + this.paginate
            + '&q=' + this.search
            + '&selectedCategory=' + this.selectedCategory
            + '&sort_direction=' + this.sort_direction
            + '&sort_field=' + this.sort_field
            )
            .then(response => {
                this.products = response.data;
            });
        }
    },
    mounted(){
        axios.get('api/category')
        .then(response => {
            this.category = response.data.data;
        });
        this.getProducts();
    }
};
</script>