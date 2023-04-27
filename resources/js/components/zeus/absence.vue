<template>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste des absences</h3>
  
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
                      <th>Nombre d'heures</th>
                      <th>Motif</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(absence, index) in absences" :key="absence.id">
                      <td>{{ index }}</td>
                      <td>{{ absence.date_debut }}</td>
                      <td>{{ absence.date_fin }}</td>
                      <td>{{ absence.nombre_heures }}</td>
                      <td>{{ absence.motif_libelle }}</td>
  
                      <td>
                        <a href="#" @click="editModal(absence)">
                          <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteAbsence(absence.id)">
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
                  :data="absences"
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
                  Créer une absence
                </h5>
                <h5 class="modal-title" v-show="editmode">
                  Modifier une absence
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
                @submit.prevent="editmode ? updateAbsence() : createAbsence()"
              >
                <div class="modal-body">

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
                      <label>Nombre d'heures</label>
                      <input v-model="form.nombre_heures" type="number" name="nombre_heures" autocomplete="off"
                          class="form-control" :class="{ 'is-invalid': form.errors.has('nombre_heures') }">
                      <has-error :form="form" field="nombre_heures"></has-error>
                  </div>

                  <div class="form-group">
                      <label>Motif</label>
                      <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('motif_id') }" v-model="form.motif_id">
                        <option 
                            v-for="(motif, index) in motifs" :key="index"
                            :value="motif.id"
                            :selected="motif.id == form.motif_id">{{ motif.libelle }}</option>
                      </select>
                      <has-error :form="form" field="motif_id"></has-error>
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
        absences: {},
        sites: {},
        motifs: {},
        staffMembers: {},

        form: new Form({
          id: "",
          date_debut: "",
          date_fin: "",
          nombre_heures: 0,
          motif_id: null,
          personnel_id: null,
        }),
      };
    },
    methods: {
      
        loadAbsences() {
            axios.get("/api/absence").then(({ data }) => {
                this.absences = data.data;
            });
        },

        loadMotifs() {
            axios.get("/api/motif_absence").then(({ data }) => {
                this.motifs = data.data;
            });
        },

        loadStaffMembers() {
            axios.get("/api/personnel").then(({ data }) => {
                this.staffMembers = data.data;
            });
        },


        search(){

        },

      newModal() {
        this.editmode = false;
        this.form.reset();
        $("#addNew").modal("show");
      },

      createAbsence() {
        this.$Progress.start();

        this.form.post('/api/absence')
        .then((data)=>{
          if(data.data.success){
            $('#addNew').modal('hide');

            Toast.fire({
                  icon: 'success',
                  title: data.data.message
              });
            this.$Progress.finish();
            this.loadAbsences();

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

      editModal(absence){
          this.editmode = true;
          this.form.reset();
          $('#addNew').modal('show');
          this.form.fill(absence);
      },

      updateAbsence() {
        this.$Progress.start();
        this.form.put('/api/absence/'+this.form.id)
        .then((response) => {
            // success
            $('#addNew').modal('hide');
            Toast.fire({
              icon: 'success',
              title: response.data.message
            });
            this.$Progress.finish();
                //  Fire.$emit('AfterCreate');

            this.loadAbsences();
        })
        .catch(() => {
            this.$Progress.fail();
        });
      },

      deleteAbsence(id) {

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
                          this.form.delete('/api/absence/'+id).then(()=>{
                                  Swal.fire(
                                  'suppression!',
                                  'Enregistrement supprimé.',
                                  'success'
                                  );
                              // Fire.$emit('AfterCreate');
                              this.loadAbsences();
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
      this.loadAbsences();
      this.loadMotifs();
      this.loadStaffMembers();
      this.$Progress.finish();
    },

  };
  </script>
  