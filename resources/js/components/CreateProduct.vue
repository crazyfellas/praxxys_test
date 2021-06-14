<template>
    <div class="row justify-content-md-center">
        <div class="col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header mb-3"><h3 class="card-title"><h3 class="text-center">Create Product</h3></h3></div>
                    <div class="row px-4">
                        <div class="col-md-12 col-sm-12">
                      
                       <form action="/vuejs/form" @submit.prevent="upload">
                           
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
                                <date-picker  :config="options" v-model="product.datetime" required placeholer="Select Date"></date-picker>
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
                                <button type="submit" class="btn btn-primary">Save Product</button>
                            </div>
                           </form> 
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
            category : '',
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
            upload() { //SAVE TO DATABASE
                
                const formData = new FormData();
                this.files.forEach(file => {
                    formData.append('images[]', file, file.name);
                });

                 formData.append("name", this.product.name);
                formData.append("description", this.product.description);
                formData.append("category_id", this.product.category_id);
                formData.append("datetime", this.product.datetime);

                
                axios.post('/api/products', formData)
                .then((res) => {
                    this.$router.push({ name: 'products' })
                })
                .catch(error => {
                    this.allerrors = error.response.data.errors;
                    console.log(error);
                })

            }
        
        },
        mounted(){
            axios.get('api/category')
            .then(response => {
                this.category = response.data.data;
            });
        }
}


</script>


<style lang="scss" scoped>
@import '/css/images.scss';
</style>