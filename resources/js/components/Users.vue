<template>
<div class="row">
        <div class="col-xs-12" style="margin-bottom:10px;">
            <div class="col-xs-2 input-group">
              <input type="text" class="form-control" @keyup.enter="searchIt()" v-model="search" placeholder="Search">
              <div class="input-group-btn">
                <button class="btn btn-default" @click="searchIt()">
                  <i class="fa fa-search"></i>
                </button>
              </div>
            </div>
        </div>
        <div class="col-xs-12" v-if="$gate.isAdminOrAuthor()">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Users</h3>

              <div class="box-tools">
                <div class="input-group input-group-sm hidden-xs" style="width: 150px;">

                </div>
                    <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#modal-add-new" @click="newModal()">Add User
                    <span class="fa fa-user-plus"></span></button>
              </div>
              
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Type</th>
                  <th>Date</th>
                  <th>Modify</th>
                </tr>
                <tr v-for="user in users.data" :key="user.id" :item="user">
                  <td>{{user.id}}</td>
                  <td>{{user.name}}</td>
                  <td>{{user.email}}</td>
                  <td>{{user.type | capitalize}}</td>
                  <td>{{user.created_at | customDate}}</td>
                  <td>
                      <a href="#" data-toggle="modal" data-target="#modal-add-new" @click="editModal(user)"><i class="fa fa-edit"></i></a> / 
                      <a href="#" @click="deleteUser(user.id)"><i class="fa fa-trash text-red"></i></a>
                  </td>
                </tr>
                </tbody
              ></table>
            </div>
            <div class="box-footer clearfix">
            <pagination :data="users"  class="pagination pagination-sm no-margin pull-right" @pagination-change-page="getResults"></pagination>

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <div class="col-xs-12" v-else>
          <not-found></not-found>
        </div>
        <div class="modal fade" id="modal-add-new" style="display: none;">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title" v-show="!editMode">Add User</h4>
                <h4 class="modal-title" v-show="editMode">Update User</h4>
              </div>
              <form @submit.prevent="editMode ? updateUser() : createUser()">
              <div class="modal-body">
                    <div class="form-group" :class="{ 'has-error': form.errors.has('name') }">
                        <input v-model="form.name" type="text" name="name" placeholder="Name"
                            class="form-control">
                        <has-error :form="form" field="name"></has-error>
                    </div>
                    <div class="form-group" :class="{ 'has-error': form.errors.has('email') }">
                        <input v-model="form.email" type="text" name="email" placeholder="Email Address"
                            class="form-control">
                        <has-error :form="form" field="email"></has-error>
                    </div>
                    <div class="form-group">
                        <textarea v-model="form.bio" type="text" name="bio" placeholder="Bio"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                        <has-error :form="form" field="bio"></has-error>
                    </div>
                    <div class="form-group">
                        <select  v-model="form.type" name="type"
                            class="form-control" :class="{ 'has-error': form.errors.has('type') }">
                            <option value="">Select User Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">Standard User</option>
                            <option value="author">Author</option>
                        </select>
                        <has-error :form="form" field="type"></has-error>
                    </div>
                    <div class="form-group" :class="{ 'has-error': form.errors.has('password') }"> 
                        <input v-model="form.password" type="password" name="password" placeholder="Password"
                            class="form-control">
                        <has-error :form="form" field="password"></has-error>
                    </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal" id="closeModal">Close</button>
                <button type="submit" class="btn btn-primary" v-show="editMode">Update</button>
                <button type="submit" class="btn btn-primary" v-show="!editMode">Create</button>
              </div>
              </form>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
      </div>
      
</template>

<script>
import moment from 'moment'
    export default {
        data () {
            return {
            editMode: false,  
            users: {},
            page: 0,  
            // Create a new form instance
            form: new Form({
                id: '',
                name: '',
                email: '',
                password: '',
                type: '',
                bio: '',
                photo: ''
            }),
            search: ''
            }
        },
        methods: {
          		// Our method to GET results from a Laravel endpoint
          getResults(page = 1) {
            this.page = page;
            axios.get('api/user?page=' + page)
              .then(response => {
                this.users = response.data;
              });
          },
          editModal(user) {
            this.editMode = true;
            this.form.clear();
            this.form.reset();
            // Use Vform fill method
            this.form.fill(user);
          },
          newModal() {
            this.page = 0;
            this.editMode = false;
            this.form.clear();
            this.form.reset();
          },
          deleteUser(id) {

            Swal.fire({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
              
            // Send request to the server

            if (result.value) {
                this.form.delete('api/user/' + id).then(() => {  
                  vueEvent.$emit('RefreshData');   
                    Swal.fire(
                      'Deleted!',
                      'Your file has been deleted.',
                      'success'
                    )
                }).catch(
                      Swal.fire(
                      'Failed!',
                      'There was something wrong.',
                      'warning'
                    )
                );
              }
            })
          },
          loadUsers(){
            if(this.$gate.isAdminOrAuthor()) {
              this.$Progress.start();
              axios.get('api/user').then(response => this.users = response.data)
              this.$Progress.finish();
            }
          },
          createUser() {
            this.form.post('api/user').then(function(){     
            $('#closeModal').click();
            $('#closeModal').click();
            vueEvent.$emit('RefreshData');         
            toast.fire({
              icon: 'success',
              title: 'User created successfully'
            })
            }).catch(function (error) {
              console.log(error);
            });;

          },
          updateUser() {
            // alert('test');
           
          this.$Progress.start();  
          this.form.put('api/user/' +this.form.id)
          .then(() => {
            $('#closeModal').click();
            $('#closeModal').click();
            this.$Progress.finish();
            vueEvent.$emit('RefreshData');   
              Swal.fire(
              'Updated!',
              'User\'s info has been updated',
              'success'
            );
          })
          .catch(() => {
          this.$Progress.fail(); 
          });
          },
          searchIt() {
            alert('test');
            axios.get('api/findUser/' + this.search)
              .then(response => {
                this.users = response.data;
              });
          }
        },
        filters: {
          capitalize: function (value) {
            if (!value) return ''
            value = value.toString()
            return value.charAt(0).toUpperCase() + value.slice(1)
          }, 
          customDate: function(value){
             if (!value) return ''
             return moment(value).format('MMMM Do YYYY');
          }
        },

        // The created hook can be used to run code after an instance is created

        created() {       
            this.loadUsers(); 
            //setInterval(() =>this.loadUsers(), 3000);  
            vueEvent.$on('RefreshData', ()=> {
              // alert(this.page);
              if(this.page > 0) {
                this.getResults(this.page);
              }
              else {
                this.loadUsers();
              }
              this.page = 0;
              // alert(this.page);
            });
        }
    }
</script>
