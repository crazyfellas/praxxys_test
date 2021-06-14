<template>
<div class="row justify-content-md-center">
    <div class="col-md-6 col-sm-12">
        <div class="card">
            <div class="card-header mb-3"><h3 class="card-title"><h3 class="text-center">Edit Product</h3></h3></div>
                <div class="row px-4">
                    <div class="col-md-12 ">
                        <div class="row">
                            <div class="col-md-12">
                                <form @submit.prevent="upload">
                                
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" class="form-control" v-model="product.name" required>
                                        <span v-if="allerrors.name" :class="['label text-danger']">{{ allerrors.name[0] }}</span>
                                    
                                    
                                    </div>
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select v-model="product.category_id" class="form-control form-control-sm" required>
                                            <option v-for="item in category" :key="item.id" :value="item.id">{{item.name}}</option>
                                        </select>
                                        <span v-if="allerrors.category_id" :class="['label text-danger']">{{ allerrors.category_id[0] }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Detail</label>
                                        
                                        <vue-editor v-model="product.description" required/>
                                        <span v-if="allerrors.description" :class="['label text-danger']">{{ allerrors.description[0] }}</span>
                                    </div>

                                    <div class="form-group">
                                        <label>Date</label>
                                        <date-picker  :config="options" v-model="product.datetime" required></date-picker>
                                        <span v-if="allerrors.datetime" :class="['label text-danger']">{{ allerrors.datetime[0] }}</span>
                                    </div>

                                   
                                   <div class="form-group">
                                        <label>Images</label>
                                        <div class="uploader"
                                        @dragenter="OnDragEnter"
                                        @dragleave="OnDragLeave"
                                        @dragover.prevent
                                        @drop="onDrop"
                                        :class="{ dragging: isDragging }">
                                    
                                        <div class="upload-control" v-show="images.length">
                                            <label for="file" class="btn btn-sm btn-info" type="button">Select a file</label>
                                            <button @click="clearImages" class="btn btn-sm btn-warning" type="button">Clear All</button> 
                                        </div>


                                        <div v-show="!images.length">
                                            <i class="fa fa-cloud-upload"></i>
                                            <p class="p_drag">Drag your images here</p>
                                            <div>OR</div>
                                            <div class="file-input">
                                               <label for="file">Select a file</label>
                                                <input type="file" id="file" @change="onInputChange" ref="files" multiple name="image">
                                            

                                            </div>
                                        </div>

                                        <div class="images-preview" v-show="images.length">
                                            <div class="img-wrapper" v-for="(image, index) in images" :key="index">
                                                <img :src="image" :alt="`Image Uplaoder ${index}`">
                                                <div class="details">
                                                    <span class="name" v-text="files[index].name"></span>
                                                    <span class="size" v-text="getFileSize(files[index].size)"></span>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    
                                    <div class="form-group">
                                    <button class="btn btn-primary">Update Product</button>
                                    </div>
                                </form>

                                <div class="pt-3">
                                     <div class="card card-primary">
                                        <div class="card-header">
                                            <h4 class="card-title">Images</h4>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row">
                                                <div class="col-sm-4 border"  v-for="img in image" :key="img.id">
                                                    <img :src="img.path" class="img-fluid mb-2" alt="white sample" height="200" width="200">
                                                    <button class="btn btn-danger btn-sm" @click="removeImage(img.id)">Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

                            
</template>
 
<script>

import { VueEditor } from "vue2-editor";
 export default {

        data() {
            return {
                product: {},
                category: '',
                image: '',
                isDragging: false,
                dragCount: 0,
                files: [],
                images: [],
                options: {
                    format: 'YYYY-MM-DD',
                    useCurrent: false,
                },
                allerrors: [],
            }
        },
       
        methods: {
             OnDragEnter(e) { //START MULTIPLE IMAGE WITH PREVIEW
                e.preventDefault();

                this.dragCount++;
                this.isDragging = true;
                return false;
            },
            OnDragLeave(e) {
                e.preventDefault();
                this.dragCount--;
                if (this.dragCount <= 0)
                    this.isDragging = false;
            },
            onInputChange(e) {
                const files = e.target.files;
                Array.from(files).forEach(file => this.addImage(file));
            },
            onDrop(e) {
                e.preventDefault();
                e.stopPropagation();
                this.isDragging = false;
                const files = e.dataTransfer.files;
                Array.from(files).forEach(file => this.addImage(file));
            },
            addImage(file) {
                if (!file.type.match('image.*')) {
                    this.$toastr.e(`${file.name} is not an image. Please try again`);
                    return;
                }else if(file.size > 2500000){
                    this.$toastr.e(`${file.name} image is too large. Please try again`);
                    return;
                }

                this.files.push(file);
                const img = new Image(),
                    reader = new FileReader();
                reader.onload = (e) => this.images.push(e.target.result);
                reader.readAsDataURL(file);


            },
            clearImages() {
                 
                this.images = [];
                this.files = [];
            },
            getFileSize(size) {
                const fSExt = ['Bytes', 'KB', 'MB', 'GB'];
                let i = 0;

                while(size > 900) {
                    size /= 1024;
                    i++;
                }
                return `${(Math.round(size * 100) / 100)} ${fSExt[i]}`;
            }, //END MULTIPLE IMAGE WITH PREVIEW
            loadProducts(){ //LOAD PRODUCTS
                    axios.get(`/api/products/${this.$route.params.id}`)
                .then(response => {
                    this.product = response.data;
                });
            },
            loadImage(){ //LOAD IMAGES
                 axios.get(`/api/images/${this.$route.params.id}`)
                .then(response => {
                    this.image = response.data.data;
                });
            },
            removeImage(img){ //DELETE IMAGES
            
                Swal.fire({
                     title: 'Are you sure you want to delete the image?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                         

                    if (result.isConfirmed) {

                        axios.delete('/api/images/' + img);
                        Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                        )
                        Fire.$emit('AfterSave');
                    }
                });
            },
            upload() {   
               
                var id = this.$route.params.id;
                
                axios.put('/api/products/'+id, this.product)
                .then((res) => {
                        this.$toast.success('Products were Updated Successfully');
                        Fire.$emit('AfterSave');
                    
                    })
                .catch((error) => {
                    this.allerrors = error.response.data.errors;
                });

                const formData = new FormData();
                this.files.forEach(file => {
                    formData.append('images[]', file, file.name);
                });
                formData.append("productNumber", this.product.productNumber);
                axios.post('/api/updateImage/', formData);
                 
            },
            
        },
        mounted(){
            axios.get('/api/category/')
            .then(response => {
                this.category = response.data.data;
            });

            this.loadProducts();
            this.loadImage();


            Fire.$on('AfterSave',() => {
               this.loadProducts();
               this.loadImage();
              
           });
        }
    }
</script>


<style lang="scss" scoped>
@import '/css/images.scss';
</style>