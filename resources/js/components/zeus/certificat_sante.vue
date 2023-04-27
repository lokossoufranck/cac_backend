<template>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste des activités</h3>
  
                <div class="card-tools">
  
                  <!-- <button 
                  type="button" 
                  class="btn btn-sm btn-warning"
                  @click="reportingModal"    
                  >
                    Reporting
                  </button> -->
  
                  <button
                    type="button"
                    class="btn btn-sm btn-primary"
                    @click="newModal"
                  >
                    <i class="fa fa-plus-square"></i>
                    Nouveau
                  </button>
                  <input type="text" v-model="query" placeholder="Rechercher" />
                  <button class="btn-dark" @click="search()">Rechercher</button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover" id="datatable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nom</th>
                      <!-- <th>Numéro de compte</th> -->
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="activite in activites" :key="activite.id">
                      <td>{{ activite.id }}</td>
                      <td>{{ activite.nom }}</td>
                      <!-- <td>{{ activite.numero_de_compte }}</td> -->
  
                      <td>
                        <a href="#" @click="editModal(activite)">
                          <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deactivateActivite(activite.id)">
                          <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <!-- <pagination
                  :data="activites"
                  @pagination-change-page="getResults"
                ></pagination> -->
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
  
        <!-- Begin Modal Add -->
        <div
          class="modal fade"
          id="addNew"
          tabindex="-1"
          role="dialog"
          aria-labelledby="addNew"
          aria-hidden="true"
        >
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" v-show="!editmode">
                  Créer une activité
                </h5>
                <h5 class="modal-title" v-show="editmode">
                  Modifier une activité
                </h5>
                <button
                  type="button"
                  class="close"
                  data-dismiss="modal"
                  aria-label="Close"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
  
              <form
                @submit.prevent="editmode ? updateActivite() : createActivite()"
              >
                <div class="modal-body">

                  <div class="form-group">
                      <label>Nom</label>
                      <input v-model="form.nom" type="text" name="nom" autocomplete="off"
                          class="form-control" :class="{ 'is-invalid': form.errors.has('nom') }">
                      <has-error :form="form" field="nom"></has-error>
                  </div>

                </div>
  
                <div class="modal-footer">
                  <button
                    type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal"
                  >
                    Fermer
                  </button>
                  <button v-show="editmode" type="submit" class="btn btn-success">
                    Enregistrer
                  </button>
                  <button
                    v-show="!editmode"
                    type="submit"
                    class="btn btn-primary"
                  >
                    Créer
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- End Modal Add -->
  
      </div>
    </section>
  </template>
  
  <script>
  import VueTagsInput from "@johmun/vue-tags-input";
  import  "jquery/dist/jquery.min.js";
  import "datatables.net-bs4/js/dataTables.bootstrap4.js";
  
  export default {
    components: {
      VueTagsInput,
    },
    data() {
      return {
        editmode: false,
        // info: null,
        search_key: ["id", "date_debut", "date_fin", "actif"],
        query: "",
        activites: {},

        form: new Form({
          id: "",
          matricule: "",
          nom: "",
          prenoms: "",
          nb_enfants_a_charge: "",
          numero_cnss: "",
          url_photo_identite: null
        }),
      };
    },
    methods: {
      
        loadActivites() {
            axios.get("/api/activite").then(({ data }) => {
                this.activites = data.data.data;
            });
        },

        search(){

        },

      newModal() {
        this.editmode = false;
        this.form.reset();
        $("#addNew").modal("show");
      },

      triggerFileInput() {
        $("#user_photo_file_input").trigger("click");
      },

      createActivite() {
        this.$Progress.start();

        this.form.post('/api/activite')
        .then((data)=>{
          if(data.data.success){
            $('#addNew').modal('hide');

            Toast.fire({
                  icon: 'success',
                  title: data.data.message
              });
            this.$Progress.finish();
            this.loadActivites();

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

      editModal(activite){
          this.editmode = true;
          this.form.reset();
          $('#addNew').modal('show');
          this.form.fill(activite);
      },

      uploadPhotoIdentite(e) {

        let file = e.target.files[0];
        let reader = new FileReader();  
        if(file['size'] < 2111775)
        {
            reader.onloadend = (file) => {
            //console.log('RESULT', reader.result)
              this.form.url_photo_identite = reader.result;
            }              
            reader.readAsDataURL(file);
        }else{
            alert('File size can not be bigger than 2 MB')
        }

      },

      getPhotoIdentite(){
        return this.form.url_drapeau;
      },

      updateActivite() {
        this.$Progress.start();
        this.form.put('/api/activite/'+this.form.id)
        .then((response) => {
            // success
            $('#addNew').modal('hide');
            Toast.fire({
              icon: 'success',
              title: response.data.message
            });
            this.$Progress.finish();
                //  Fire.$emit('AfterCreate');

            this.loadActivites();
        })
        .catch(() => {
            this.$Progress.fail();
        });
      },

      deactivateActivite(id) {

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
                          this.$Progress.start();
                          this.form.delete('/api/activite/'+id).then(()=>{
                                  Swal.fire(
                                  'suppression!',
                                  'Enregistrement supprimé.',
                                  'success'
                                  );
                              // Fire.$emit('AfterCreate');
                              this.loadActivites();
                          }).catch((data)=> {
                              Swal.fire("Failed!", data.message, "warning");
                          });
                        }
                  })

      },
      
    },

    mounted() {},
    updated() {},
    created() {
      this.$Progress.start();
      this.loadActivites();
      this.$Progress.finish();
    },

  };
  </script>
  