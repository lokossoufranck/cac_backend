<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste des profiles utilisateur </h3>

                <div class="card-tools">
                  
                  <button type="button" class="btn btn-sm btn-primary" @click="newModal">
                      <i class="fa fa-plus-square"></i>
                      Nouveau
                  </button>
                  <input type="text" v-model="query" placeholder="Rechercher"/>
                    <button class="btn-dark" @click="search()" >Rechercher</button>
                </div>
              </div>
              <!-- /.card-header -->

              <div class="card-body table-responsive p-0">

            <form @submit.prevent="">
                    <div class="modal-body">

                    <div class="form-group">
                            <label>Site</label>
                            <select class="form-control" @change="chargerProfile()" v-model="form.site_id">
                              <option 
                                  v-for="(site,index) in sites" :key="index"
                                  :value="index"
                                  :selected="index == form.site_id">{{ site }}</option>
                            </select>
                            <has-error :form="form" field="site_id"></has-error>
                    </div>

                    <div class="form-group">
                            <label>Profile</label>
                            <select class="form-control" v-model="form.profile_id" @change="chargerUserProfile()">
                              <option 
                                  v-for="(profile,index) in profiles" :key="index"
                                  :value="index"
                                  :selected="index == form.profile_id">{{ profile }}</option>
                            </select>
                            <has-error :form="form" field="profile_id"></has-error>
                    </div>

                                         
                  </div>

                   
                  </form>

                <table class="table table-hover" id="datatable">
                  <thead>
                    <tr>
                      <th>Id</th>
                      <th>Nom</th>
                      <th>Username</th>
                      <th>Profile</th>
                      <th>Est actif</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="profile_user in search()" :key="profile_user.id">
                      <td>{{profile_user.id}}</td>
                      <td>{{profile_user.user_nom}}</td>   
                      <td>{{profile_user.user_email}}</td>                   
                      <td>{{profile_user.profile_nom}}</td>
                      <td v-show="profile_user.actif"><span   class="badge badge-success"> {{profile_user.actif}}</span></td>
                      <td v-show="!profile_user.actif"><span   class="badge badge-danger"> {{profile_user.actif}}</span></td>
                      <td>
                        
                        <a href="#" @click="editModal(profile_user)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteUser_profile(profile_user.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="profile_users" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode"> Définition de profile</h5>
                    <h5 class="modal-title" v-show="editmode">  Modifier de profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updateUser_profile() : createUser_profile()">
                    <div class="modal-body">
                        
                    <div class="form-group">
                            <label>Utilisateur</label>
                            <select class="form-control"
                            :class="{ 'is-invalid': form_u.errors.has('user_id') }"
                             v-model="form_u.user_id" :disabled="editmode" >
                              <option selected>Veuillez sélectionner un utilisateur</option>
                              <option 
                                  v-for="(usr,index) in users" :key="index"
                                  :value="usr.id"
                                  :selected="usr.id == form_u.user_id">{{ usr.email }} - {{ usr.name }} </option>
                            </select>
                            <has-error :form="form_u" field="user_id"></has-error>
                     </div>  
                       
                    
                <div class="form-group">
                            <label>Site</label>
                            <select class="form-control" @change="chargerProfile_u()" v-model="form_u.site_id">
                              <option 
                                  v-for="(site,index) in sites" :key="index"
                                  :value="index"
                                  :selected="index == form_u.site_id">{{ site }}</option>
                            </select>
                            <has-error :form="form_u" field="site_id"></has-error>
                </div>

                  <div class="form-group">
                            <label>Profile</label>
                           <select class="form-control" v-model="form_u.profile_id">
                                    <option 
                                          v-for="(profile,index) in profiles" :key="index"
                                          :value="index"
                                          :selected="index == form_u.profile_id">{{ profile }}
                                    </option>
                            </select>
                          <has-error :form="form_u" field="profile_id"></has-error>
                    </div>

                   <div class="form-check ">
                        <input v-model="form_u.actif" type="checkbox"  id="actif" 
                        class="form-check-input" 
                        :class="{ 'is-invalid': form_u.errors.has('actif') }"> 
                        <label for="actif" class="form-check-label"> Est actif </label>
                       <has-error :form="form_u" field="actif"></has-error>
                      </div>  
                                          
                      
                             
                  </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                            <button v-show="editmode" type="submit" class="btn btn-success">Enregistrer</button>
                            <button v-show="!editmode" type="submit" class="btn btn-primary">Créer</button>
                        </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
  </section>
</template>

<script>
    import VueTagsInput from '@johmun/vue-tags-input';
    import 'jquery/dist/jquery.min.js';
    import 'datatables.net-bs4/js/dataTables.bootstrap4.js'
  

    export default {
      components: {
          VueTagsInput,
        },
        data () {
            return {
                editmode: false,
                info: null,
                profile_users : {},
                profile_users_n : {}, // utiliser en mode nouveau
                search_key:['id','user_nom','user_email','profile_nom','actif'],
                query: "",
                originalrows :[],
                menus :[],
                users :[],
                profiles:[],
                sites:[],
                form: new Form({
                    id : '',
                    user_id: '',
                    site_id:'',
                    actif: true
                }),
                form_u: new Form({
                    id : '',
                    user_id: '',
                    site_id:'',
                    profile_id:'',
                    actif: true
                })
            }
        },
        methods: {            
search() {
      var results = [];
      var searchData = this.profile_users.data;
      var sparam = this.query.toLowerCase();
      if(this.query == ""){
       // this.profile_user.data = searchData;
       results=searchData;
      }else{
           searchData.forEach(element => {  // pour chauque ligne
          
        //Les attributs à prendre en compte pour la recherche par défaut sont ceux de l'objet
         var keys = Object.keys(element);
         if (this.search_key.length>0){  // On récupère les attributs s'ils sont définit dans le tableau search_key
          keys=this.search_key
         }
           for (var i=0 ; i < keys.length ; i++){ // pour chaque attribut
                // un attribut de l'abjet répond au critère de recherche
               if(typeof element[keys[i]] =="string" &&  element[keys[i]].toLowerCase().indexOf(sparam) >=0) {
                results.push(element);
                break;
                 }
           }    
           });
      }
      return results;
    },
          getResults(page = 1) {

              this.$Progress.start();
              
              axios.get('/api/profile_user?page=' + page).then(({ data }) => (this.profile_users = data.data));

              this.$Progress.finish();
          },

          loadUsers(page = 1){
            axios.get("/api/user?page="+page).then(({ data }) => {
              this.users = this.users.concat(data.data.data);
              if (page < data.data.last_page){
                this.loadUsers(page + 1);
              }
              else
                console.log(this.users);
            });
            
          },
          loadProfile(){
          axios.get("/api/profile/list/").then(({ data }) => {
            this.profiles= data.data;
            });
            
          },
          loadSites(){
          axios.get("/api/site/list/").then(({ data }) => {
            this.sites= data.data;
            });
            
          },
          chargerProfile(){
         
         axios.get("/api/profile/list/?site_id="+this.form.site_id).then(({ data }) => {
           this.profile_users={};
           this.profiles= data.data;
            });
          },
       chargerProfile_u(){
         
         axios.get("/api/profile/list/?site_id="+this.form_u.site_id).then(({ data }) => {
           this.profile_users={};
           this.profiles= data.data;
            });
          }
          ,
          chargerUserProfile(){

          axios.get("/api/profile_user/list/?profile_id="+ this.form.profile_id).then(({ data }) => {
                        this.profile_users = data.data; 
                        }
                        );
          },
          editModal(profile_user){
              this.form_u.site_id=profile_user.site_id;
              this. chargerProfile_u();
              this.editmode = true;
              this.profiles={};
              this.form_u.reset();
              $('#addNew').modal('show');
              this.form_u.fill(profile_user);
          },
          newModal(){
              this.editmode = false;
              this.profiles={};
              this.form_u.reset();
              $('#addNew').modal('show');
          },
          createUser_profile(){
              this.$Progress.start();

              this.form_u.post('/api/profile_user')
              .then((data)=>{
                if(data.data.success){
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: data.data.message
                    });
                  this.chargerUserProfile()
                  this.$Progress.finish();
                //*** */

                } else {
                  Toast.fire({
                      icon: 'error',
                      title: 'Some error occured! Please try again'
                  });

                  this.$Progress.failed();
                }
              })
              .catch(()=>{

                  Toast.fire({
                      icon: 'error',
                      title: 'Some error occured! Please try again'
                  });
              })
          },
          updateUser_profile(){
              this.$Progress.start();
              this.form_u.put('/api/profile_user/'+this.form_u.id)
              .then((response) => {
                  // success
                  $('#addNew').modal('hide');
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  this.chargerUserProfile()
                  this.$Progress.finish();
                      //  Fire.$emit('AfterCreate');
                 //*** */
              })
              .catch(() => {
                  this.$Progress.fail();
              });

          },
          deleteUser_profile(id){
              Swal.fire({
                  title: 'Êtes vous sûrs?',
                  text: "Vous ne pourrez pas revenir en arrière !",
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#3085d6',
                  confirmButtonText: 'Oui, supprimez-le !'
                  }).then((result) => {

                      // Send request to the server
                        if (result.value) {
                              this.form.delete('/api/profile_user/'+id).then(()=>{
                                      Swal.fire(
                                      'suppression!',
                                      'Enregistrement supprimé.',
                                      'success'
                                      );
                                       this.chargerUserProfile()
                                       this.$Progress.finish();
                                  // Fire.$emit('AfterCreate');
                                  //** */
                              }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                        }
                  })
          },
           //For getting Instant Uploaded Drapeau
            uploadDrapeau(e){
                let file = e.target.files[0];
                let reader = new FileReader();  
                if(file['size'] < 2111775)
                {
                    reader.onloadend = (file) => {
                    
                     this.form.url_drapeau = reader.result;
                    }              
                     reader.readAsDataURL(file);
                }else{
                    alert('File size can not be bigger than 2 MB')
                }
            }
            ,


        },
        mounted() {
          
        },
        updated() {
      
},
        created() {
            this.$Progress.start();
            this.loadUsers(); 
            this.loadSites();    
            this.$Progress.finish();
        },
        filters: {
            truncate: function (text, length, suffix) {
                return text.substring(0, length) + suffix;
            },
        },
        computed: {
          filteredItems() {
            return this.autocompleteItems.filter(i => {
              return i.text.toLowerCase().indexOf(this.tag.toLowerCase()) !== -1;
            });
          },
        },
        watch : {
            query () {
                this.search();
            }
        },
    }
</script>
