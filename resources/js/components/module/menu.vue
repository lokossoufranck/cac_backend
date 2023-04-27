<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste des menu </h3>

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
                      <th>Id</th>
                      <th>Nom</th>
                      <th>Module</th>
                      <th>Est actif</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="menu in search()" :key="menu.id">
                      <td>{{menu.id}}</td>
                      <td>{{menu.nom}}</td>                    
                      <td>{{menu.mod_nom}}</td>
                      <td v-show="menu.actif"><span   class="badge badge-success"> {{menu.actif}}</span></td>
                      <td v-show="!menu.actif"><span   class="badge badge-danger"> {{menu.actif}}</span></td>
                      <!-- <td><img v-bind:src="'/' + menu.photo" width="100" alt="menu"></td> -->
                      <td>
                        
                        <a href="#" @click="editModal(menu)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteMenu(menu.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="menus" @pagination-change-page="getResults"></pagination>
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
                    <h5 class="modal-title" v-show="!editmode">Créer un menu</h5>
                    <h5 class="modal-title" v-show="editmode">Modifier menu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updateMenu() : createMenu()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nom</label>
                            <input v-model="form.nom" type="text" name="nom" autocomplete="off" maxlength="30"      
                                class="form-control" :class="{ 'is-invalid': form.errors.has('nom') }">
                            <has-error :form="form" field="nom"></has-error>
                        </div>
                        
                        <div class="form-group">
                            <label>Icon</label>
                            <input v-model="form.icon" type="text" name="icon" autocomplete="off" maxlength="40"      
                                class="form-control" :class="{ 'is-invalid': form.errors.has('icon') }">
                            <has-error :form="form" field="icon"></has-error>
                        </div>

                        <div class="form-group">
                            <label>URL</label>
                            <input v-model="form.url" type="text" name="url" autocomplete="off"
                                 maxlength="200"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('url') }">
                            <has-error :form="form" field="url"></has-error>
                        </div>
                    
                      <div class="form-group">
                            <label>Module</label>
                            <select class="form-control" v-model="form.module_id">
                              <option 
                                  v-for="(mod,index) in modules" :key="index"
                                  :value="index"
                                  :selected="index == form.module_id">{{ mod }}</option>
                            </select>
                            <has-error :form="form" field="module_id"></has-error>
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
                menus : {},
                search_key:['id','nom','mod_nom','url','actif'],
                query: "",
                originalrows :[],
                modules :[],
                form: new Form({
                    id : '',
                    nom : '',
                    icon:'',
                    url: '',
                    module_id: '',
                    module:'',
                    actif: true
                }),
            }
        },
        methods: {            
search() {
      var results = [];
      var searchData = this.menus.data;
      var sparam = this.query.toLowerCase();
      if(this.query == ""){
       // this.menus.data = searchData;
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
              
              axios.get('/api/menu?page=' + page).then(({ data }) => (this.menus = data.data));

              this.$Progress.finish();
          },
          loadMenus(){
              axios.get("/api/menu").then(({ data }) => {
                this.menus = data.data; 
                }
                );
          },  loadModules(){
              axios.get("/api/module/list/").then(({ data }) => (this.modules= data.data));
          },
          editModal(menu){
              this.editmode = true;
              this.form.reset();
              $('#addNew').modal('show');
              this.form.fill(menu);
          },
          newModal(){
              this.editmode = false;
              this.form.reset();
              $('#addNew').modal('show');
          },
          createMenu(){
              this.$Progress.start();

              this.form.post('/api/menu')
              .then((data)=>{
                if(data.data.success){
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: data.data.message
                    });
                  this.$Progress.finish();
                  this.loadMenus();

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
          updateMenu(){
              this.$Progress.start();
              this.form.put('/api/menu/'+this.form.id)
              .then((response) => {
                  // success
                  $('#addNew').modal('hide');
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  this.$Progress.finish();
                      //  Fire.$emit('AfterCreate');

                  this.loadMenus();
              })
              .catch(() => {
                  this.$Progress.fail();
              });

          },
          deleteMenu(id){
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
                              this.form.delete('/api/menu/'+id).then(()=>{
                                      Swal.fire(
                                      'suppression!',
                                      'Enregistrement supprimé.',
                                      'success'
                                      );
                                  // Fire.$emit('AfterCreate');
                                  this.loadMenus();
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
                    //console.log('RESULT', reader.result)
                     this.form.url_drapeau = reader.result;
                    }              
                     reader.readAsDataURL(file);
                }else{
                    alert('File size can not be bigger than 2 MB')
                }
            },
            getUrl_drapeau(){
              // return  "/images/module/flag/"+ this.form.url_drapeau;
               return  this.form.url_drapeau;
            }
            ,


        },
        mounted() {
          
        },
        updated() {
      
},
        created() {
            this.$Progress.start();
            this.loadMenus();  
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
