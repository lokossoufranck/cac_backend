<template>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste des modes de calcul</h3>
  
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
                      <th>Libellé</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(mode, index) in modes" :key="mode.id">
                      <td>{{ mode.id }}</td>
                      <td>{{ mode.libelle }}</td>
  
                      <td>
                        <a href="#" @click="editModal(mode)">
                          <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a v-if="mode.actif" href="#" @click="deactivateMode(mode.id)">
                          <i class="fa fa-trash red" title="Désactiver"></i>
                        </a>
                        <a v-else href="#" @click="reactivateMode(index)">
                          <i class="fa fa-reply green" title="Réactiver"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <!-- <pagination
                  :data="mode"
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
                  Créer un mode de calcul
                </h5>
                <h5 class="modal-title" v-show="editmode">
                  Modifier un mode de calcul
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
                @submit.prevent="editmode ? updateMode() : createMode()"
              >
                <div class="modal-body">

                  <div class="form-group">
                      <label>Libellé</label>
                      <input v-model="form.libelle" type="text" name="libelle" autocomplete="off"
                          class="form-control" :class="{ 'is-invalid': form.errors.has('libelle') }">
                      <has-error :form="form" field="libelle"></has-error>
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
        modes: {},
        sites: {},

        form: new Form({
          id: "",
          libelle: "",
          actif: true,
        }),
      };
    },
    methods: {
      
        loadModes() {
            axios.get("/api/mode_calcul").then(({ data }) => {
                this.modes = data.data;
            });
        },


        search(){

        },

      newModal() {
        this.editmode = false;
        this.form.reset();
        $("#addNew").modal("show");
      },

      createMode() {
        this.$Progress.start();

        this.form.post('/api/mode_calcul')
        .then((data)=>{
          if(data.data.success){
            $('#addNew').modal('hide');

            Toast.fire({
                  icon: 'success',
                  title: data.data.message
              });
            this.$Progress.finish();
            this.loadModes();

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

      editModal(mode){
          this.editmode = true;
          this.form.reset();
          $('#addNew').modal('show');
          this.form.fill(mode);
      },

      updateMode() {
        this.$Progress.start();
        this.form.put('/api/mode_calcul/'+this.form.id)
        .then((response) => {
            // success
            $('#addNew').modal('hide');
            Toast.fire({
              icon: 'success',
              title: response.data.message
            });
            this.$Progress.finish();
                //  Fire.$emit('AfterCreate');

            this.loadModes();
        })
        .catch(() => {
            this.$Progress.fail();
        });
      },

      deactivateMode(id) {

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
                          this.form.delete('/api/mode_calcul/'+id).then(()=>{
                                  Swal.fire(
                                  'suppression!',
                                  'Enregistrement supprimé.',
                                  'success'
                                  );
                              // Fire.$emit('AfterCreate');
                              this.loadModes();
                          }).catch((data)=> {
                              Swal.fire("Failed!", data.message, "warning");
                          });
                        }
                  })

      },

      reactivateMode(index) {

        var mode = this.modes[index] ;
        Swal.fire({
                  title: 'Êtes vous sûrs?',
                  text: "Vous ne pourrez pas revenir en arrière !",
                  showCancelButton: true,
                  confirmButtonColor: '#38c172',
                  cancelButtonColor: '#3085d6',
                  confirmButtonText: 'Oui, réactivez-le !'
                  }).then((result) => {

                      // Send request to the server
                        if (result.value) {
                          this.$Progress.start();
                          this.form.libelle = mode.libelle
                          this.form.actif = true;
                          this.form.put('/api/mode_calcul/'+mode.id).then(()=>{
                                  Swal.fire(
                                  'Réactivation!',
                                  'Enregistrement réactivé.',
                                  'success'
                                  );
                              // Fire.$emit('AfterCreate');
                              this.loadModes();
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
      this.loadModes();
      this.$Progress.finish();
    },

  };
  </script>
  