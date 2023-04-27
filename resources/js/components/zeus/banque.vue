<template>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste des banques</h3>
  
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
                      <th>Numéro de compte</th>
                      <th>Site</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="banque in banques" :key="banque.id">
                      <td>{{ banque.id }}</td>
                      <td>{{ banque.nom }}</td>
                      <td>{{ banque.numero_de_compte }}</td>
                      <td>{{ banque.site_nom }}</td>
  
                      <td>
                        <a href="#" @click="editModal(banque)">
                          <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deactivateBanque(banque.id)">
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
                  :data="banques"
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
                  Créer une banque
                </h5>
                <h5 class="modal-title" v-show="editmode">
                  Modifier une banque
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
                @submit.prevent="editmode ? updateBanque() : createBanque()"
              >
                <div class="modal-body">

                  <div class="form-group">
                      <label>Nom</label>
                      <input v-model="form.nom" type="text" name="nom" autocomplete="off"
                          class="form-control" :class="{ 'is-invalid': form.errors.has('nom') }">
                      <has-error :form="form" field="nom"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Numero de compte</label>
                      <input v-model="form.numero_de_compte" type="text" name="numero_de_compte" autocomplete="off"
                          class="form-control" :class="{ 'is-invalid': form.errors.has('numero_de_compte') }">
                      <has-error :form="form" field="numero_de_compte"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Site</label>
                      <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('site_id') }" v-model="form.site_id">
                        <option 
                            v-for="(site, index) in sites" :key="index"
                            :value="site.id"
                            :selected="site.id == form.site_id">{{ site.nom }}</option>
                      </select>
                      <has-error :form="form" field="site_id"></has-error>
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
        banques: {},
        sites: {},

        form: new Form({
          id: "",
          nom: "",
          numero_de_compte: "",
          site_id: null,
        }),
      };
    },
    methods: {
      
        loadBanques() {
            axios.get("/api/banque").then(({ data }) => {
                this.banques = data.data;
            });
        },

        loadSites() {
            axios.get("/api/site").then(({ data }) => {
                this.sites = data.data.data;
            });
        },


        search(){

        },

      newModal() {
        this.editmode = false;
        this.form.reset();
        $("#addNew").modal("show");
      },

      createBanque() {
        this.$Progress.start();

        this.form.post('/api/banque')
        .then((data)=>{
          if(data.data.success){
            $('#addNew').modal('hide');

            Toast.fire({
                  icon: 'success',
                  title: data.data.message
              });
            this.$Progress.finish();
            this.loadBanques();

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

      editModal(banque){
          this.editmode = true;
          this.form.reset();
          $('#addNew').modal('show');
          this.form.fill(banque);
      },

      updateBanque() {
        this.$Progress.start();
        this.form.put('/api/banque/'+this.form.id)
        .then((response) => {
            // success
            $('#addNew').modal('hide');
            Toast.fire({
              icon: 'success',
              title: response.data.message
            });
            this.$Progress.finish();
                //  Fire.$emit('AfterCreate');

            this.loadBanques();
        })
        .catch(() => {
            this.$Progress.fail();
        });
      },

      deactivateBanque(id) {

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
                          this.form.delete('/api/banque/'+id).then(()=>{
                                  Swal.fire(
                                  'suppression!',
                                  'Enregistrement supprimé.',
                                  'success'
                                  );
                              // Fire.$emit('AfterCreate');
                              this.loadBanques();
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
      this.loadBanques();
      this.loadSites();
      this.$Progress.finish();
    },

  };
  </script>
  