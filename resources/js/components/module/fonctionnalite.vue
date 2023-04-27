<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste des fonctionnalite </h3>

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
                      <th>Controller</th>
                      <th>Est actif</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="fonctionnalite in search()" :key="fonctionnalite.id">
                      <td>{{fonctionnalite.id}}</td>
                      <td>{{fonctionnalite.nom}}</td>                    
                      <td>{{fonctionnalite.ctrl_nom}}</td>
                      <td v-show="fonctionnalite.actif"><span   class="badge badge-success"> {{fonctionnalite.actif}}</span></td>
                      <td v-show="!fonctionnalite.actif"><span   class="badge badge-danger"> {{fonctionnalite.actif}}</span></td>
                      <!-- <td><img v-bind:src="'/' + fonctionnalite.photo" width="100" alt="fonctionnalite"></td> -->
                      <td>
                        
                        <a href="#" @click="editModal(fonctionnalite)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteFonctionnalite(fonctionnalite.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="fonctionnalites" @pagination-change-page="getResults"></pagination>
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
                    <h5 class="modal-title" v-show="!editmode">Créer un fonctionnalite</h5>
                    <h5 class="modal-title" v-show="editmode">Modifier fonctionnalite</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updateFonctionnalite() : createFonctionnalite()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nom</label>
                            <input v-model="form.nom" type="text" name="nom" autocomplete="off" maxlength="30"      
                                class="form-control" :class="{ 'is-invalid': form.errors.has('nom') }">
                            <has-error :form="form" field="nom"></has-error>
                        </div>

                                            
                      <div class="form-group">
                            <label>Controller</label>
                            <select class="form-control" v-model="form.controller_id">
                              <option 
                                  v-for="(mod,index) in controllers" :key="index"
                                  :value="index"
                                  :selected="index == form.controller_id">{{ mod }}</option>
                            </select>
                            <has-error :form="form" field="controller_id"></has-error>
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
  

    export default {
      components: {
          VueTagsInput,
        },
        data () {
            return {
                editmode: false,
                info: null,
                fonctionnalites : {},
                search_key:['id','nom','ctrl_nom','url','actif'],
                query: "",
                originalrows :[],
                controllers :[],
                form: new Form({
                    id : '',
                    nom : '',
                    controller_id: '',
                    controller:'',
                    actif: true
                }),
            }
        },
        methods: {            
search() {
      var results = [];
      var searchData = this.fonctionnalites.data;
      var sparam = this.query.toLowerCase();
      if(this.query == ""){
       // this.fonctionnalites.data = searchData;
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
              
              axios.get('/api/fonctionnalite?page=' + page).then(({ data }) => (this.fonctionnalites = data.data));

              this.$Progress.finish();
          },
          loadFonctionnalites(){
              axios.get("/api/fonctionnalite").then(({ data }) => {
                this.fonctionnalites = data.data; 
                }
                );
          },  loadControllers(){
              axios.get("/api/controllererp/list/").then(({ data }) => (this.controllers= data.data));
          },
          editModal(fonctionnalite){
              this.editmode = true;
              this.form.reset();
              $('#addNew').modal('show');
              this.form.fill(fonctionnalite);
          },
          newModal(){
              this.editmode = false;
              this.form.reset();
              $('#addNew').modal('show');
          },
          createFonctionnalite(){
              this.$Progress.start();

              this.form.post('/api/fonctionnalite')
              .then((data)=>{
                if(data.data.success){
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: data.data.message
                    });
                  this.$Progress.finish();
                  this.loadFonctionnalites();

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
          updateFonctionnalite(){
              this.$Progress.start();
              this.form.put('/api/fonctionnalite/'+this.form.id)
              .then((response) => {
                  // success
                  $('#addNew').modal('hide');
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  this.$Progress.finish();
                      //  Fire.$emit('AfterCreate');

                  this.loadFonctionnalites();
              })
              .catch(() => {
                  this.$Progress.fail();
              });

          },
          deleteFonctionnalite(id){
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
                              this.form.delete('/api/fonctionnalite/'+id).then(()=>{
                                      Swal.fire(
                                      'suppression!',
                                      'Enregistrement supprimé.',
                                      'success'
                                      );
                                  // Fire.$emit('AfterCreate');
                                  this.loadFonctionnalites();
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
            }
            ,


        },
        mounted() {
          
        },
        updated() {
      
},
        created() {
            this.$Progress.start();
           this.loadControllers();  
            this.loadFonctionnalites();        
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
