<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste des sites </h3>

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
                      <th>Sigle</th>
                      <th>Pays</th>
                      <th>Est actif</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="site in search()" :key="site.id">
                      <td>{{site.id}}</td>
                      <td>{{site.nom}}</td>  
                      <td>{{site.sigle}}</td>                   
                      <td>{{site.pay_nom}}</td>
                      <td v-show="site.actif"><span   class="badge badge-success"> {{site.actif}}</span></td>
                      <td v-show="!site.actif"><span   class="badge badge-danger"> {{site.actif}}</span></td>
                      <!-- <td><img v-bind:src="'/' + site.photo" width="100" alt="site"></td> -->
                      <td>
                        
                        <a href="#" @click="editModal(site)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteSite(site.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="sites" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade " id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Créer un site</h5>
                    <h5 class="modal-title" v-show="editmode">Modifier site</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form  @submit.prevent="editmode ? updateSite() : createPays()">
                    <div class="modal-body">
                    <div class="form-row">
                      

                      <div class="col-md-4 mb-3">
                            <label>Nom</label>
                            <input v-model="form.nom" type="text" name="nom" autocomplete="off" maxlength="30"      
                                class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('nom') }">
                            <has-error :form="form" field="nom"></has-error>
                      </div>

                      <div class="col-md-4 mb-3">
                            <label>Sigle</label>
                            <input v-model="form.sigle" type="text" name="sigle" autocomplete="off" maxlength="30"      
                                class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('sigle') }">
                            <has-error :form="form" field="sigle"></has-error>
                      </div>
                      <div class="col-md-4 mb-3">
                            <label>Adresse</label>
                            <input v-model="form.adresse" type="text" name="adresse" autocomplete="off" maxlength="30"      
                                class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('adresse') }">
                            <has-error :form="form" field="adresse"></has-error>
                      </div>

                      <div class="col-md-4 mb-3">
                            <label>Email</label>
                            <input v-model="form.email" type="text" name="email" autocomplete="off" maxlength="80"      
                                class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                      </div>

                        <div class="col-md-4 mb-3">
                            <label>Site web</label>
                            <input v-model="form.site_web" type="text" name="site_web" autocomplete="off" maxlength="80"      
                                class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('site_web') }">
                            <has-error :form="form" field="site_web"></has-error>
                      </div>

                      <div class="col-md-4 mb-3">
                            <label>Téléphone 1</label>
                            <input v-model="form.tel1" type="text" name="tel1" autocomplete="off" maxlength="80"      
                                class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('tel1') }">
                            <has-error :form="form" field="tel1"></has-error>
                      </div>

                      <div class="col-md-4 mb-3">
                            <label>Téléphone 2</label>
                            <input v-model="form.tel2" type="text" name="tel2" autocomplete="off" maxlength="80"      
                                class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('tel2') }">
                            <has-error :form="form" field="tel2"></has-error>
                      </div>

                       <div  class="col-md-4 mb-3">
                            <label>Logo</label>
                            <input type="file" @change='uploadUrl_logo' name="url_logo" 
                            class="form-control" 
                            :class="{ 'is-invalid': form.errors.has('url_logo') }">
                            <has-error :form="form" field="url_logo"></has-error>
                          <img class="profile-user-img img-fluid" :src=" (getUrl_logo()=='/images/module/logo/') ? getUrl_logo()+'default_logo.jpg': getUrl_logo() " 
                       alt="Image du drapeau">
                      </div>

                      <div  class="col-md-4 mb-3">
                            <label>Header_1</label>
                            <input type="file" @change='uploadUrl_header_1' name="url_header_1" 
                            class="form-control" 
                            :class="{ 'is-invalid': form.errors.has('url_header_1') }">
                            <has-error :form="form" field="url_header_1"></has-error>
                          <img class="profile-user-img img-fluid" :src=" (getUrl_header_1()=='/images/module/header/') ? getUrl_header_1()+'default_header.jpg': getUrl_header_1() " 
                       alt="Image du drapeau">
                      </div>

                      <div  class="col-md-4 mb-3">
                            <label>Header_2</label>
                            <input type="file" @change='uploadUrl_header_2' name="url_header_2" 
                            class="form-control" 
                            :class="{ 'is-invalid': form.errors.has('url_header_2') }">
                            <has-error :form="form" field="url_header_2"></has-error>
                          <img class="profile-user-img img-fluid" :src=" (getUrl_header_2()=='/images/module/header/') ? getUrl_header_2()+'default_header.jpg': getUrl_header_2() " 
                       alt="Image du drapeau">
                      </div>


                       <div  class="col-md-4 mb-3">
                            <label>Footer_1</label>
                            <input type="file" @change='uploadUrl_footer_1' name="url_footer_1" 
                            class="form-control" 
                            :class="{ 'is-invalid': form.errors.has('url_footer_1') }">
                            <has-error :form="form" field="url_footer_1"></has-error>
                          <img class="profile-user-img img-fluid" :src=" (getUrl_footer_1()=='/images/module/footer/') ? getUrl_footer_1()+'default_footer.jpg': getUrl_footer_1() " 
                       alt="Image du drapeau">
                      </div>

                      <div  class="col-md-4 mb-3">
                            <label>Footer_2</label>
                            <input type="file" @change='uploadUrl_footer_2' name="url_footer_2" 
                            class="form-control" 
                            :class="{ 'is-invalid': form.errors.has('url_footer_2') }">
                            <has-error :form="form" field="url_footer_2"></has-error>
                          <img class="profile-user-img img-fluid" :src=" (getUrl_footer_2()=='/images/module/footer/') ? getUrl_footer_2()+'default_footer.jpg': getUrl_footer_2() " 
                       alt="Image du drapeau">
                      </div>
                     

                     


                      <div  class="col-md-4 mb-3">
                        <input v-model="form.actif" type="checkbox"  id="actif" 
                        class="form-check-input" 
                        :class="{ 'is-invalid': form.errors.has('actif') }"> 
                        <label for="actif" class="form-check-label"> Est actif </label>
                       <has-error :form="form" field="actif"></has-error>
                      </div> 


                      <div  class="col-md-4 mb-3">
                        <input v-model="form.is_siege" type="checkbox"  id="is_siege" 
                        class="form-check-input" 
                        :class="{ 'is-invalid': form.errors.has('is_siege') }"> 
                        <label for="is_siege" class="form-check-label"> Est le siège </label>
                       <has-error :form="form" field="is_siege"></has-error>
                      </div> 

                       <div  class="col-md-4 mb-3">
                            <label>Pays</label>
                            <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('pays_id') }" v-model="form.pays_id">
                              <option 
                                  v-for="(pay,index) in pays_s" :key="index"
                                  :value="index"
                                  :selected="index == form.pays_id">{{ pay }}</option>
                            </select>
                            <has-error :form="form" field="pays_id"></has-error>
                        </div>

                      
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
                sites : {},
                search_key:['id','nom','pay_nom','url','actif'],
                query: "",
                originalrows :[],
                pays_s :[],
                form: new Form({
                    id : '',
                    nom : '',
                    tel1:'',
                    tel2:'',
                    url_logo:'',
                    url_header_1:'',
                    url_header_2:'',
                    url_footer_1:'',
                    url_footer_2:'',
                    email: '',
                    site_web:'',
                    pays_id: '',
                    menu:'',
                    is_siege:false,
                    actif: true,	
                }),
            }
        }, 
        methods: {            
search() {
      var results = [];
      var searchData = this.sites.data;
      var sparam = this.query.toLowerCase();
      if(this.query == ""){
       // this.sites.data = searchData;
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
              
              axios.get('/api/site?page=' + page).then(({ data }) => (this.sites = data.data));

              this.$Progress.finish();
          },
          loadSites(){
              axios.get("/api/site").then(({ data }) => {
                this.sites = data.data; 
                }
                );
          },  loadPayss(){
              axios.get("/api/pays/list/").then(({ data }) => (this.pays_s= data.data));
          },
          editModal(site){
              this.editmode = true;
              this.form.reset();
              $('#addNew').modal('show');
              this.form.fill(site);
          },
          newModal(){
              this.editmode = false;
              this.form.reset();
              $('#addNew').modal('show');
          },
          createPays(){
              this.$Progress.start();

              this.form.post('/api/site')
              .then((data)=>{
                if(data.data.success){
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: data.data.message
                    });

                  this.loadSites();
                  this.$Progress.finish();

                  

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
          updateSite(){
              this.$Progress.start();
              this.form.put('/api/site/'+this.form.id)
              .then((response) => {
                  // success
                  $('#addNew').modal('hide');
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  // Rafraissiement après mise à jour
                  this.loadSites();
                  this.$Progress.finish(); 
                
              })
              .catch(() => {
                  this.$Progress.fail();
              });

          },
          deleteSite(id){
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
                              this.form.delete('/api/site/'+id).then(()=>{
                                      Swal.fire(
                                      'suppression!',
                                      'Enregistrement supprimé.',
                                      'success'
                                      );
                                  // Fire.$emit('AfterCreate');
                                  this.loadPays();
                              }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                        }
                  })
          },
           uploadUrl_logo(e){
                let file = e.target.files[0];
                let reader = new FileReader();  
                if(file['size'] < 2111775)
                {
                    reader.onloadend = (file) => {
                    //console.log('RESULT', reader.result)
                     this.form.url_logo = reader.result;
                    }              
                     reader.readAsDataURL(file);
                }else{
                    alert('File size can not be bigger than 2 MB')
                }
            },
          uploadUrl_header_1(e){
                let file = e.target.files[0];
                let reader = new FileReader();  
                if(file['size'] < 2111775)
                {
                    reader.onloadend = (file) => {
                     this.form.url_header_1 = reader.result;
                    }              
                     reader.readAsDataURL(file);
                }else{
                    alert('File size can not be bigger than 2 MB')
                }
            },
          uploadUrl_header_2(e){
                let file = e.target.files[0];
                let reader = new FileReader();  
                if(file['size'] < 2111775)
                {
                    reader.onloadend = (file) => {
                    //console.log('RESULT', reader.result)
                     this.form.url_header_2 = reader.result;
                    }              
                     reader.readAsDataURL(file);
                }else{
                    alert('File size can not be bigger than 2 MB')
                }
            },
          uploadUrl_footer_1(e){
                let file = e.target.files[0];
                let reader = new FileReader();  
                if(file['size'] < 2111775)
                {
                    reader.onloadend = (file) => {
                    //console.log('RESULT', reader.result)
                     this.form.url_footer_1 = reader.result;
                    }              
                     reader.readAsDataURL(file);
                }else{
                    alert('File size can not be bigger than 2 MB')
                }
            },
          uploadUrl_footer_2(e){
                let file = e.target.files[0];
                let reader = new FileReader();  
                if(file['size'] < 2111775)
                {
                    reader.onloadend = (file) => {
                    //console.log('RESULT', reader.result)
                     this.form.url_footer_2 = reader.result;
                    }              
                     reader.readAsDataURL(file);
                }else{
                    alert('File size can not be bigger than 2 MB')
                }
          },
            getUrl_logo(){
            return  this.form.url_logo;
            } ,
          getUrl_header_1(){
            return  this.form.url_header_1;
            } ,
            getUrl_header_2(){
              return  this.form.url_header_2;
            } , 
            getUrl_footer_1(){
            return  this.form.url_footer_1;
            } ,
            getUrl_footer_2(){
              return  this.form.url_footer_2;
            } ,


        },
        mounted() {
          
        },
        updated() {
      
},
        created() {
            this.$Progress.start();
            this.loadPayss();  
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
