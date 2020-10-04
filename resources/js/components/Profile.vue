<template>
    <div class="row">
        <div class="col-md-12 mt-3">
            <div class="box box-widget widget-user">
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header bg-black" style="background-image:url('./img/user_cover.png')">
            <h3 class="widget-user-username">{{this.form.name}}</h3>
            <h5 class="widget-user-desc">{{this.form.type}}</h5>
        </div>
        <div class="widget-user-image">
            <img class="img-circle" :src="getProfilePhoto()" alt="User Avatar">
        </div>
        <div class="box-footer">
            <div class="row">
            <div class="col-sm-4 border-right">
                <div class="description-block">
                <h5 class="description-header">3,200</h5>
                <span class="description-text">SALES</span>
                </div>
                <!-- /.description-block -->
            </div>
            <!-- /.col -->
            <div class="col-sm-4 border-right">
                <div class="description-block">
                <h5 class="description-header">13,000</h5>
                <span class="description-text">FOLLOWERS</span>
                </div>
                <!-- /.description-block -->
            </div>
            <!-- /.col -->
            <div class="col-sm-4">
                <div class="description-block">
                <h5 class="description-header">35</h5>
                <span class="description-text">PRODUCTS</span>
                </div>
                <!-- /.description-block -->
            </div>
            <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        </div>
        </div>
        <div class="col-md-12 mt-3">
            <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                        <li class="active"><a href="#activity" data-toggle="tab" aria-expanded="true">Activity</a></li>
                        <li class=""><a href="#settings" data-toggle="tab" aria-expanded="false">Settings</a></li>
                        </ul>
                        <div class="tab-content">
                        <div class="tab-pane active" id="activity">
                            <!-- Post -->
                            <div class="post">
                            <!-- /.user-block -->
                            <p>
                                Lorem ipsum represents a long-held tradition for designers,
                                typographers and the like. Some people hate it and argue for
                                its demise, but others ignore the hate as they create awesome
                                tools to help create filler text for everyone from bacon lovers
                                to Charlie Sheen fans.
                            </p>
                            </div>
                            <!-- /.post -->


                        </div>

                        <!-- /.tab-pane -->

                        <div class="tab-pane" id="settings">
                            <form role="form">
                            <div class="form-group" :class="{ 'has-error': form.errors.has('name') }">
                                <label for="inputName" class="control-label">Name</label>
                                <input  v-model="form.name" type="text" class="form-control" id="inputName" placeholder="Name">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group" :class="{ 'has-error': form.errors.has('email') }">
                                <label for="inputEmail" class="control-label">Email</label>
                                <input v-model="form.email"  type="email" class="form-control" id="inputEmail" placeholder="Email">
                                <has-error :form="form" field="email"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="inputExperience" class="control-label">Experience</label>
                                <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="photo" class="control-label">Photo</label>
                                <input type="file" @change="updatePhoto()" id="photo" name="photo" placeholder="Photo">
                            </div>
                            <div class="form-group" :class="{ 'has-error': form.errors.has('password') }">
                                <label for="password" class="control-label">Password(leave empty if not changing)</label>
                                <input type="text" v-model="form.password" class="form-control" id="password" placeholder="Password">
                                <has-error :form="form" field="password"></has-error>
                            </div>
                            <div class="form-group">
                                <button type="submit" @click.prevent="updateInfo()" class="btn btn-danger">Update</button>
                            </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
        </div>
    </div>
        
    
</template>

<script>
    export default {
        data () {
            return {
            form: new Form({
                id: '',
                name: '',
                email: '',
                password: '',
                type: '',
                bio: '',
                photo: ''
            })
            }
        },
        methods: {
            updatePhoto() {             
                var file = $('#photo').prop('files')[0];
                // console.log(file);
                if(file['size'] < 2111775) {
                    var reader = new FileReader();
                    reader.onloadend = (file) => {
                        this.form.photo = reader.result;
                    }
                    reader.readAsDataURL(file);
                }
                else {
                    Swal.fire(                    
                        {
                            'icon' : 'error',
                            'title' : 'Ooops ...',
                            'text' : 'You are uploading a large file'
                        }
                    );
                }
                
            },
            updateInfo() {
                this.$Progress.start();  
                this.form.put('api/profile')
                .then(() => {
                    this.$Progress.finish(); 
                    vueEvent.$emit('RefreshData');   
                    Swal.fire(
                    'Updated!',
                    'User\'s profile has been updated',
                    'success'
                    );
                })
                .catch(() => {
                this.$Progress.fail(); 
                });
                },
            getProfilePhoto() {
                let photo = (this.form.photo.length > 200 ) ? this.form.photo : "img/profile/" + this.form.photo;
                return photo;
            },
            loadProfile() {
                axios.get('api/profile')
                .then(({ data }) => {this.form.fill(data) })
                .catch(function (error) {
                // handle error
                // console.log(error);
                })
            }
            
        },
        created() {
            this.loadProfile();
              vueEvent.$on('RefreshData', ()=> {
              this.loadProfile();
            });
        }

}
</script>
