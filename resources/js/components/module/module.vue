<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste des modules </h3>

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
                <table class="table table-hover" id="datatable">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nom</th>
                      <th>description</th>
                      <th>Est actif</th>
                      <th>Action</th>
                    </tr>
                  </thead>

                  <tbody>
                     <tr v-for="module in search()" :key="module.id">
                      <td>{{module.id}}</td>
                      <td>{{module.nom}}</td>
                      <td>{{module.description}}</td>
                      <td>{{module.actif}}</td>
                      <td>
                        
                        <a href="#" @click="editModal(module)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteModule(module.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                          /
                        <a href="#" @click="editSetting(module)">
                            <i class="fa fa-cog dark"></i>
                        </a>
                          /
                        <a href="#"  @click="downloadSetting(module.id)">
                            <i class="fa fa-download green"></i> 
                            
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="modules" @pagination-change-page="getResults"></pagination>
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
                    <h5 class="modal-title" v-show="!editmode">Créer un module</h5>
                    <h5 class="modal-title" v-show="editmode">Modifier module</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updateModule() : createModule()">
                    <div class="modal-body">

                        <div class="form-group">
                            <label>Nom</label>
                            <input v-model="form.nom" type="text" name="nom" autocomplete="off" maxlength="40"      
                                class="form-control" :class="{ 'is-invalid': form.errors.has('nom') }">
                            <has-error :form="form" field="nom"></has-error>
                        </div>
                        

                         <div class="form-group">
                            <label>Description</label>
                           <!--  <input v-model="form.description" type="text" name="description" autocomplete="off"
                                 maxlength="60"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('description') }"> -->

                            <textarea  class="form-control" :class="{ 'is-invalid': form.errors.has('description') }" v-model="form.description" autocomplete="off" name="description" id="description" cols="30" rows="4">
                            </textarea>    
                            <has-error :form="form" field="description"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Icon</label>
                            <input v-model="form.icon" type="text" name="icon" autocomplete="off" maxlength="40"      
                                class="form-control" :class="{ 'is-invalid': form.errors.has('icon') }">
                            <has-error :form="form" field="icon"></has-error>
                        </div>

                       <div class="form-group">
                            <label>Image Icon</label>
                            <input type="file" @change='uploadIcon' name="url_icon" 
                            class="form-control" 
                            :class="{ 'is-invalid': form.errors.has('url_icon') }">
                            <has-error :form="form" field="url_icon"></has-error>
                            
                        </div>
                       <img class="profile-user-img img-fluid" :src=" (getUrl_icon()=='/images/module/flag/') ? getUrl_icon()+'default_flag.jpg': getUrl_icon() " 
                       alt="Image du drapeau">


                        <div class="form-group">
                            <label>Url root</label>
                            <input v-model="form.url_root" type="text" name="url_root" autocomplete="off"
                               maxlength="20"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('url_root') }">
                            <has-error :form="form" field="url_root"></has-error>
                        </div>
  
                        <div class="form-group">
                            <label>API URL</label>
                            <input v-model="form.api_url" type="text" name="api_url" autocomplete="off"
                               maxlength="20"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('api_url') }">
                            <has-error :form="form" field="api_url"></has-error>
                        </div>
                        
                        <div class="form-group">
                            <label>API username</label>
                            <input v-model="form.api_username" type="text" name="api_username" autocomplete="off"
                               maxlength="20"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('api_username') }">
                            <has-error :form="form" field="api_username"></has-error>
                        </div>

                       <div class="form-group">
                            <label>API password</label>
                            <input v-model="form.api_password" type="text" name="api_password" autocomplete="off"
                               maxlength="20"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('api_password') }">
                            <has-error :form="form" field="api_password"></has-error>
                        </div>

                      <div class="form-group">
                            <label>Version</label>
                            <input v-model="form.version" type="text" name="version" autocomplete="off"
                               maxlength="20"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('version') }">
                            <has-error :form="form" field="version"></has-error>
                      </div>

                        <div class="form-check ">
                              <input v-model="form.actif" type="checkbox"  id="actif" 
                              class="form-check-input" 
                              :class="{ 'is-invalid': form.errors.has('actif') }"> 
                              <label for="actif" class="form-check-label"> Est actif </label>
                            <has-error :form="form" field="actif"></has-error>
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
        <!-- Modal -->
      <!-- Modal of configuration -->
        <div class="modal fade" id="editSetting" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Configuration Module {this.form.nom}</h5>
                    <h5 class="modal-title" v-show="editmode">Parametrage du module</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="updateModuleSetting()">
                    <div class="modal-body">
               
                        <div class="form-group">
                            <label>Fichier paramettre </label>
                            <input type="file" ref="file" id="file" @change="uploadFdescriptif()" name="file" 
                             class="form-control" 
                            :class="{ 'is-invalid': form.errors.has('file') }">
                            <has-error :form="form" field="file"></has-error>   
                        </div>
        
                      </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        <button v-show="editmode" type="submit" class="btn btn-success">Enregistrer</button>
                       <!-- <button v-show="!editmode" type="submit" class="btn btn-primary">Créer</button>-->
                    </div>
                  </form>
                </div>
            </div>
        </div>
        <!-- Modal of configuration -->


    </div>
  </section>
</template>

<script>
    import VueTagsInput from '@johmun/vue-tags-input';
    import 'jquery/dist/jquery.min.js';
    import 'datatables.net-bs4/js/dataTables.bootstrap4.js'
    
 // import 'datatables.net/js/jquery.dataTables.min.js'
 //  import 'datatables.net-autofill'
 // import "datatables.net/js/dataTables.dataTables"
 // import "datatables.net-dt/css/jquery.dataTables.min.css"

    export default {
      components: {
          VueTagsInput,
        },
        data () {
            return {
                editmode: false,
                info: null,
                modules : {},
                search_key:['nom','description','version'],
                query: "",
                originalrows :[],
                file: null, 
                form: new Form({
                    id : '',
                    nom : '',
                    description: '',
                    icon:'',
                    url_icon:'',
                    url_root: '',
                    api_url: '',
                    api_username: '',
                    api_password: '',
                    version: '',
                    actif: true
                }),

            }
        },
        methods: {            
search() {
      var results = [];
      var searchData = this.modules.data;
      var sparam = this.query.toLowerCase();
      if(this.query == ""){
       // this.modules.data = searchData;
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
              
              axios.get('/api/module?page=' + page).then(({ data }) => (this.modules = data.data));

              this.$Progress.finish();
          },
          loadModules(){
              axios.get("/api/module").then(({ data }) => {
                this.modules = data.data; 
                }
                );
          },
          editModal(module){
              this.editmode = true;
              this.form.reset();
              $('#addNew').modal('show');
              this.form.fill(module);
          },
          editSetting(module){
              this.editmode = true;
              this.form.reset();
              $('#editSetting').modal('show');
              this.form.fill(module);
          }
          ,
          newModal(){
              this.editmode = false;
              this.form.reset();
              $('#addNew').modal('show');
          },
          createModule(){
               this.$Progress.start();
               this.form.post('/api/module')
              .then((data)=>{
                if(data.data.success){
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: data.data.message
                    });
                  this.$Progress.finish();
                  this.loadModules();

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
          updateModule(){
              this.$Progress.start();
              this.form.put('/api/module/'+this.form.id)
              .then((response) => {
                  // success
                  $('#addNew').modal('hide');
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  this.$Progress.finish();
                      //  Fire.$emit('AfterCreate');

                  this.loadModules();
              })
              .catch(() => {
                  this.$Progress.fail();
              });

          },

           updateModuleSetting(){
              this.$Progress.start();
              let formData = new FormData();
              formData.append('file', this.file);
              formData.append('id', this.form.id);
              axios.post( '/api/module/uploadsetting',
              formData,
              {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
              }
            ).then((response) => {
               // success
               if(response.data.success){
                  $('#editSetting').modal('hide');
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  this.$Progress.finish();
                  this.loadModules();
               }
                 
            })
            .catch((error) => {
          // console.log(error.message);
           this.$Progress.fail();
           Toast.fire({
                    icon: 'error',
                  //  title:error.message
                   title:'Le fichier de configuration est neccessaire et doit être de type txt ou json.'
                  });
            });

          },
          deleteModule(id){
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
                              this.form.delete('/api/module/'+id).then(()=>{
                                      Swal.fire(
                                      'suppression!',
                                      'Enregistrement supprimé.',
                                      'success'
                                      );
                                  // Fire.$emit('AfterCreate');
                                  this.loadModules();
                              }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                        }
                  })
          },
        downloadSetting(id){
            
            axios({
                  url: '/api/module/downloadsetting/'+id,
                  method: 'GET',
                  responseType: 'arraybuffer',
              }).then((response) => {
                   let blob = new Blob([response.data], {
                            type: 'application/json'
                        })
                        let link = document.createElement('a')
                        link.href = window.URL.createObjectURL(blob)
                        link.download = 'module_'+id+'.json'
                        link.click()
              });

          },
           
            uploadFdescriptif(e){
          // Elle permet d'uploader le fichier 
          // Descriptif du module 
          this.file = this.$refs.file.files[0];

            },
            uploadIcon(e){
                let file = e.target.files[0];
                let reader = new FileReader();  
                if(file['size'] < 2111775)
                {
                    reader.onloadend = (file) => {
                    //console.log('RESULT', reader.result)
                     this.form.url_icon = reader.result;
                    }              
                     reader.readAsDataURL(file);
                }else{
                    alert('File size can not be bigger than 2 MB')
                }
            },
            getUrl_icon(){
               return  this.form.url_icon;
            }


        },
        mounted() {
          
        },
        updated() {
      
},
        created() {
            this.$Progress.start();
            this.loadModules();          
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
