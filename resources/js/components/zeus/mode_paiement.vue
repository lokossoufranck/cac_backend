<template>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste des Modes de paiement</h3>
  
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
                      <th>Mode de calcul</th>
                      <!-- <th>Site</th> -->
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="mode in modes" :key="mode.id">
                      <td>{{ mode.id }}</td>
                      <td>{{ mode.libelle }}</td>
                      <td>{{ mode.mode_calcul_libelle }}</td>
                      <!-- <td>{{ mode.site_nom }}</td> -->
  
                      <td>
                        <a href="#" @click="editModal(mode)">
                          <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deactivateMode(mode.id)">
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
                  :data="modes"
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
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" v-show="!editmode">
                  Créer un mode de paiement
                </h5>
                <h5 class="modal-title" v-show="editmode">
                  Modifier un mode de paiement
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

                  <div class="form-group">
                      <label>Heure de Présence</label>
                      <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('heure_presence') }" v-model="form.heure_presence">
                        <option 
                            :value="false"
                            :selected="false == form.heure_presence">NON</option>
                        <option 
                            :value="true"
                            :selected="true == form.heure_presence">OUI</option>
                      </select>
                      <has-error :form="form" field="heure_presence"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Calcul du SMIC</label>
                      <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('calcul_smic') }" v-model="form.calcul_smic">
                        <option 
                            :value="false"
                            :selected="false == form.calcul_smic">NON</option>
                        <option 
                            :value="true"
                            :selected="true == form.calcul_smic">OUI</option>
                      </select>
                      <has-error :form="form" field="calcul_smic"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Variable au prorata</label>
                      <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('variable_prorata') }" v-model="form.variable_prorata">
                        <option 
                            :value="false"
                            :selected="false == form.calcul_smic">NON</option>
                        <option 
                            :value="true"
                            :selected="true == form.calcul_smic">OUI</option>
                      </select>
                      <has-error :form="form" field="variable_prorata"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Jours Fériés</label>
                      <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('jour_ferie') }" v-model="form.jour_ferie">
                        <option 
                            :value="false"
                            :selected="false == form.jour_ferie">NON</option>
                        <option 
                            :value="true"
                            :selected="true == form.jour_ferie">OUI</option>
                      </select>
                      <has-error :form="form" field="jour_ferie"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Prime d'ancienneté</label>
                      <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('prime_anciennete') }" v-model="form.prime_anciennete">
                        <option 
                            :value="false"
                            :selected="false == form.prime_anciennete">NON</option>
                        <option 
                            :value="true"
                            :selected="true == form.prime_anciennete">OUI</option>
                      </select>
                      <has-error :form="form" field="prime_anciennete"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Mode de calcul</label>
                      <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('mode_calcul') }" v-model="form.mode_calcul_id">
                        <option 
                            v-for="(modeCalcul, index) in modeCalculs" :key="index"
                            :value="modeCalcul.id"
                            :selected="index == form.mode_calcul_id">{{ modeCalcul.libelle }}</option>
                      </select>
                      <has-error :form="form" field="mode_calcul"></has-error>
                  </div>

                  <!-- <div class="form-group">
                      <label>Activité</label>
                      <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('activite_id') }" v-model="form.activite_id">
                        <option 
                            v-for="(activite, index) in activites" :key="index"
                            :value="activite.id"
                            :selected="index == form.activite_id">{{ activite.nom }}</option>
                      </select>
                      <has-error :form="form" field="activite"></has-error>
                  </div> -->

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
        modeCalculs: {},
        activites: {},

        form: new Form({
          id: "",
          heure_presence: null,
          jour_ferie: null,
          variable_prorata: null,
          calcul_smic: null,
          libelle: "",
          prime_anciennete: null,
          mode_calcul_id: null,
        }),
      };
    },
    methods: {
      
        loadModePaiements() {
            axios.get("/api/mode_paiement").then(({ data }) => {
                this.modes = data.data;
            });
        },

        loadModeCalculs() {
            axios.get("/api/mode_calcul").then(({ data }) => {
                this.modeCalculs = data.data;
            });
        },

        loadSites() {
            axios.get("/api/site").then(({ data }) => {
                this.sites = data.data.data;
            });
        },

        loadActivites() {
            axios.get("/api/activite").then(({ data }) => {
                this.activites = data.data;
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

        this.form.post('/api/mode_paiement')
        .then((data)=>{
          if(data.data.success){
            $('#addNew').modal('hide');

            Toast.fire({
                  icon: 'success',
                  title: data.data.message
              });
            this.$Progress.finish();
            this.loadModePaiements();

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
        this.form.put('/api/mode_paiement/'+this.form.id)
        .then((response) => {
            // success
            $('#addNew').modal('hide');
            Toast.fire({
              icon: 'success',
              title: response.data.message
            });
            this.$Progress.finish();
                //  Fire.$emit('AfterCreate');

            this.loadModePaiements();
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
                          this.form.delete('/api/mode_paiement/'+id).then(()=>{
                                  Swal.fire(
                                  'suppression!',
                                  'Enregistrement supprimé.',
                                  'success'
                                  );
                              // Fire.$emit('AfterCreate');
                              this.loadModePaiements();
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
      this.loadModePaiements();
      this.loadSites();
      this.loadModeCalculs();
      // this.loadActivites();
      this.$Progress.finish();
    },

  };
  </script>
  