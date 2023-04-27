<template>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste des congés</h3>
  
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
                      <th>Date de début</th>
                      <th>Date de fin</th>
                      <th>Date de demande</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="conge in conges" :key="conge.id">
                      <td>{{ conge.id }}</td>
                      <td>{{ conge.date_debut }}</td>
                      <td>{{ conge.date_fin }}</td>
                      <td>{{ conge.date_demande }}</td>
  
                      <td>
                        <a href="#" @click="editModal(conge)">
                          <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteConge(conge.id)">
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
                  :data="conges"
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
                  Créer un congé
                </h5>
                <h5 class="modal-title" v-show="editmode">
                  Modifier un congé
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
                @submit.prevent="editmode ? updateConge() : createConge()"
              >
                <div class="modal-body">

                  <div class="form-group">
                      <label>Date de demande</label>
                      <input v-model="form.date_demande" type="date" name="date_demande" autocomplete="off"
                          class="form-control" :class="{ 'is-invalid': form.errors.has('date_demande') }">
                      <has-error :form="form" field="date_demande"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Date de début</label>
                      <input v-model="form.date_debut" type="date" name="date_debut" autocomplete="off"
                          class="form-control" :class="{ 'is-invalid': form.errors.has('date_debut') }">
                      <has-error :form="form" field="date_debut"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Date de fin</label>
                      <input v-model="form.date_fin" type="date" name="date_fin" autocomplete="off"
                          class="form-control" :class="{ 'is-invalid': form.errors.has('date_fin') }">
                      <has-error :form="form" field="date_fin"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Date de reprise</label>
                      <input v-model="form.date_reprise" type="date" name="date_reprise" autocomplete="off"
                          class="form-control" :class="{ 'is-invalid': form.errors.has('date_reprise') }">
                      <has-error :form="form" field="date_reprise"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Nombre de Jours</label>
                      <input v-model="form.nombre_jour" type="number" name="nombre_jour" autocomplete="off"
                          class="form-control" :class="{ 'is-invalid': form.errors.has('nombre_jour') }">
                      <has-error :form="form" field="nombre_jour"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Catégorie de congés</label>
                      <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('conge_categorie_id') }" v-model="form.conge_categorie_id">
                        <option 
                            v-for="(conge, index) in congeCategories" :key="index"
                            :value="conge.id"
                            :selected="conge.id == form.conge_categorie_id">{{ conge.libelle }}</option>
                      </select>
                        <has-error :form="form" field="conge_categorie_id"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Personnel</label>
                      <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('personnel_id') }" v-model="form.personnel_id">
                        <option 
                            v-for="(staffMember, index) in staffMembers" :key="index"
                            :value="staffMember.id"
                            :selected="staffMember.id == form.personnel_id">{{ staffMember.nom + " " + staffMember.prenoms }}</option>
                      </select>
                        <has-error :form="form" field="personnel_id"></has-error>
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
        conges: {},
        congeCategories: {},
        staffMembers: {},

        form: new Form({
          id: "",
          nombre_jour: 0,
          date_debut: null,
          date_fin: null,
          date_demande: null,
          date_reprise: null,
          nombre_jour: 0,
          conge_categorie_id: null,
        }),
      };
    },
    methods: {
      
      loadCongeCategories() {
            axios.get("/api/conge_categorie").then(({ data }) => {
                this.congeCategories = data.data;
            });
        },

        loadStaffMembers() {
            axios.get("/api/personnel").then(({ data }) => {
                this.staffMembers = data.data;
            });
        },

        loadConges() {
            axios.get("/api/conge").then(({ data }) => {
                this.conges = data.data;
            });
        },

        search(){

        },

      newModal() {
        this.editmode = false;
        this.form.reset();
        $("#addNew").modal("show");
      },

      createConge() {
        this.$Progress.start();

        this.form.post('/api/conge')
        .then((data)=>{
          if(data.data.success){
            $('#addNew').modal('hide');

            Toast.fire({
                  icon: 'success',
                  title: data.data.message
              });
            this.$Progress.finish();
            this.loadConges();

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

      editModal(conge){
          this.editmode = true;
          this.form.reset();
          $('#addNew').modal('show');
          this.form.fill(conge);
      },

      updateConge() {
        this.$Progress.start();
        this.form.put('/api/conge/'+this.form.id)
        .then((response) => {
            // success
            $('#addNew').modal('hide');
            Toast.fire({
              icon: 'success',
              title: response.data.message
            });
            this.$Progress.finish();
                //  Fire.$emit('AfterCreate');

            this.loadConges();
        })
        .catch(() => {
            this.$Progress.fail();
        });
      },

      deleteConge(id) {

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
                          this.form.delete('/api/conge/'+id).then(()=>{
                                  Swal.fire(
                                  'suppression!',
                                  'Enregistrement supprimé.',
                                  'success'
                                  );
                              // Fire.$emit('AfterCreate');
                              this.loadConges();
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
      this.loadConges();
      this.loadCongeCategories();
      this.loadStaffMembers();
      this.$Progress.finish();
    },

  };
  </script>
  