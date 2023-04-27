<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste des pays </h3>

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
                      <th>Code ICAO</th>
                      <th>Devise</th>
                      <th>Est actif</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="pays in search()" :key="pays.id">
                      <td>{{pays.id}}</td>
                      <td>{{pays.nom}}</td>
                      <td>{{pays.code_icao}}</td>
                      <td>{{pays.devise}}</td>
                      <td>{{pays.actif}}</td>
                      <!-- <td><img v-bind:src="'/' + pays.photo" width="100" alt="pays"></td> -->
                      <td>
                        
                        <a href="#" @click="editModal(pays)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deletePays(pays.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="pays_s" @pagination-change-page="getResults"></pagination>
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
                    <h5 class="modal-title" v-show="!editmode">Créer un pays</h5>
                    <h5 class="modal-title" v-show="editmode">Modifier pays</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updatePays() : createPays()">
                    <div class="modal-body">

                      <div class="form-group">
                            <label>Nom</label>
                            <input v-model="form.nom" type="text" name="nom" autocomplete="off" maxlength="30"      
                                class="form-control" :class="{ 'is-invalid': form.errors.has('nom') }">
                            <has-error :form="form" field="nom"></has-error>
                      </div>




                        <div class="form-group">
                            <label>Code ICAO</label>
                            <input v-model="form.code_icao" type="text" name="code_icao" autocomplete="off"
                                 maxlength="3"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('code_icao') }">
                            <has-error :form="form" field="code_icao"></has-error>
                        </div>

                        <div class="form-group">
                            <label>Devise</label>
                            <input v-model="form.devise" type="text" name="devise" autocomplete="off"
                               maxlength="20"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('devise') }">
                            <has-error :form="form" field="devise"></has-error>
                        </div>
  
                        <div class="form-group">
                            <label>Drapeau</label>
                            <input type="file" @change='uploadDrapeau' name="url_drapeau" 
                            class="form-control" 
                            :class="{ 'is-invalid': form.errors.has('url_drapeau') }">
                            <has-error :form="form" field="url_drapeau"></has-error>
                            
                        </div>
                       <img class="profile-user-img img-fluid" :src=" (getUrl_drapeau()=='/images/module/flag/') ? getUrl_drapeau()+'default_flag.jpg': getUrl_drapeau() " 
                       alt="Image du drapeau">

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
                pays_s : {},
                search_key:['nom','code_icao'],
                query: "",
                originalrows :[],
                form: new Form({
                    id : '',
                    nom : '',
                    code_icao: '',
                    devise: '',
                    url_drapeau:'',
                    actif: true
                }),
            }
        },
        methods: {            
search() {
      var results = [];
      var searchData = this.pays_s.data;
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
              
              axios.get('/api/pays?page=' + page).then(({ data }) => (this.pays_s = data.data));

              this.$Progress.finish();
          },
          loadPayss(){
              axios.get("/api/pays").then(({ data }) => {
                this.pays_s = data.data; 
                }
                );
          },
          editModal(pays){
              this.editmode = true;
              this.form.reset();
              $('#addNew').modal('show');
              this.form.fill(pays);
          },
          newModal(){
              this.editmode = false;
              this.form.reset();
              $('#addNew').modal('show');
          },
          createPays(){
              this.$Progress.start();

              this.form.post('/api/pays')
              .then((data)=>{
                if(data.data.success){
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: data.data.message
                    });
                  this.$Progress.finish();
                  this.loadPayss();

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
          updatePays(){
              this.$Progress.start();
              this.form.put('/api/pays/'+this.form.id)
              .then((response) => {
                  // success
                  $('#addNew').modal('hide');
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  this.$Progress.finish();
                      //  Fire.$emit('AfterCreate');

                  this.loadPayss();
              })
              .catch(() => {
                  this.$Progress.fail();
              });

          },
          deletePays(id){
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
                              this.form.delete('/api/pays/'+id).then(()=>{
                                      Swal.fire(
                                      'suppression!',
                                      'Enregistrement supprimé.',
                                      'success'
                                      );
                                  // Fire.$emit('AfterCreate');
                                  this.loadPayss();
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
            this.loadPayss();          
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
