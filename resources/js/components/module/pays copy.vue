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
                      Add New
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nom</th>
                      <th>Code ICAO</th>
                      <th>Devise</th>
                      <th>Est Actif</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="pays in pays_s.data" :key="pays.id">
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
                    <h5 class="modal-title" v-show="editmode">Edit Pays</h5>
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
                       <img class="profile-user-img img-fluid" :src="editmode ? getUrl_drapeau() : getDrapeau() " 
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
                        <button v-show="editmode" type="submit" class="btn btn-success">Modifier</button>
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

    export default {
      components: {
          VueTagsInput,
        },
        data () {
            return {
                editmode: false,
                pays_s : {},
                form: new Form({
                    id : '',
                    nom : '',
                    code_icao: '',
                    devise: '',
                    drapeau:'',
                    url_drapeau:'',
                    actif: true
                }),
            }
        },
        methods: {

          getResults(page = 1) {

              this.$Progress.start();
              
              axios.get('/api/pays?page=' + page).then(({ data }) => (this.pays_s = data.data));

              this.$Progress.finish();
          },
          loadPayss(){
            // if(this.$gate.isAdmin()){
              axios.get("/api/pays").then(({ data }) => (this.pays_s = data.data));
            // }
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
              this.form.put('api/pays/'+this.form.id)
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
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#3085d6',
                  confirmButtonText: 'Yes, delete it!'
                  }).then((result) => {

                      // Send request to the server
                        if (result.value) {
                              this.form.delete('api/pays/'+id).then(()=>{
                                      Swal.fire(
                                      'Deleted!',
                                      'Your file has been deleted.',
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
          uploadDrapeau(e){
                let file = e.target.files[0];
                let reader = new FileReader();  
                if(file['size'] < 2111775)
                {
                    reader.onloadend = (file) => {
                    //console.log('RESULT', reader.result)
                     this.form.drapeau = reader.result;
                    }              
                     reader.readAsDataURL(file);
                }else{
                    alert('File size can not be bigger than 2 MB')
                }
            },
           //For getting Instant Uploaded Drapeau
            getDrapeau(){
              let drapeau="/images/module/flag/default_flag.jpg";
               if (this.form.drapeau!=''){
                   drapeau = this.form.drapeau;       
               }
               // drapeau="/images/module/flag/" + this.form.url_drapeau;             
              // let drapeau = (this.form.drapeau.length) ? this.form.drapeau : "/images/module/flag/flag_benin.png";
               return drapeau;
            },
            getUrl_drapeau(){
               return  "/images/module/flag/"+ this.form.url_drapeau;
            }
            ,


        },
        mounted() {
            
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
    }
</script>
