<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste des clients d'affaires </h3>

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
                      <th>Site</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="client in clients" :key="client.id">
                      <td>{{client.id}}</td>
                      <td>{{client.nom}}</td>
                      <td>{{client.site_nom}}</td>
                      <!-- <td><img v-bind:src="'/' + pays.photo" width="100" alt="pays"></td> -->
                      <td>
                        
                        <a href="#" @click="editModal(client)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteClient(client.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="clients" @pagination-change-page="getResults"></pagination>
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
                    <h5 class="modal-title" v-show="!editmode">Créer un client d'affaires</h5>
                    <h5 class="modal-title" v-show="editmode">Modifier client d'affaires</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updateClient() : createClient()">
                  <div class="modal-body">
                    <div class="form-group">
                      <label>Nom</label>
                      <input v-model="form.nom" type="text" name="nom" autocomplete="off" maxlength="30"      
                          class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('nom') }">
                      <has-error :form="form" field="nom"></has-error>
                    </div>

                    <div class="form-group">
                      <label>Site</label>
                      <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('site_id') }" v-model="form.site_id">
                        <option 
                            v-for="(site, index) in sites" :key="index"
                            :value="index"
                            :selected="index == form.site_id">{{ site }}</option>
                      </select>
                      <has-error :form="form" field="site_id"></has-error>
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
                clients : {},
                search_key:['nom','code_icao'],
                query: "",
                originalrows :[],
                sites :[],
                form: new Form({
                    id : '',
                    nom : '',
                    site_id: '',
                }),
            }
        },
        methods: {            
search() {
      var results = [];
      var searchData = this.clients.data;
      var sparam = this.query.toLowerCase();
      if(this.query == ""){
       // this.pays_s.data = searchData;
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
              
              axios.get('/api/client?page=' + page).then(({ data }) => (this.clients = data.data));

              this.$Progress.finish();
          },
          loadClients(){
              axios.get("/api/client").then(({ data }) => {
                this.clients = data.data; 
                }
                );
          },

          loadSites(){
              axios.get("/api/site").then(({ data }) => {
                this.clients = data.data; 
                }
              );
          },

          editModal(client){
              this.editmode = true;
              this.form.reset();
              $('#addNew').modal('show');
              this.form.fill(client);
          },
          newModal(){
              this.editmode = false;
              this.form.reset();
              $('#addNew').modal('show');
          },

          loadSites(){
              console.log("Loading sites");
              axios.get("/api/site/list/").then(({ data }) => (this.sites = data.data));
          },

          createClient(){
              this.$Progress.start();

              this.form.post('/api/client')
              .then((data)=>{
                if(data.data.success){
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: data.data.message
                    });
                  this.$Progress.finish();
                  this.loadClients();

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
          updateClient(){
              this.$Progress.start();
              this.form.put('/api/client/'+this.form.id)
              .then((response) => {
                  // success
                  $('#addNew').modal('hide');
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  this.$Progress.finish();
                      //  Fire.$emit('AfterCreate');

                  this.loadClients();
              })
              .catch(() => {
                  this.$Progress.fail();
              });

          },
          deleteClient(id){
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
                              this.form.delete('/api/client/'+id).then(()=>{
                                      Swal.fire(
                                      'suppression!',
                                      'Enregistrement supprimé.',
                                      'success'
                                      );
                                  // Fire.$emit('AfterCreate');
                                  this.loadClients();
                              }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                        }
                  })
          },

        },
        mounted() {
          
        },
        updated() {
      
},
        created() {
            this.$Progress.start();
            this.loadClients();      
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
