<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Liste des evenements</h3>

              <div class="card-tools">

                <button 
                type="button" 
                class="btn btn-sm btn-warning"
                @click="reportingModal"    
                >
                  Reporting
                </button>

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
                    <th>Début</th>
                    <th>Début</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="evenement in search()" :key="evenement.id">
                    <td>{{ evenement.id }}</td>
                    <td>{{ evenement.date_debut }}</td>
                    <td>{{ evenement.date_fin }}</td>

                    <td>
                      <a href="#" @click="editDetail(evenement)">
                        <i class="fa fa-list orange"></i>
                      </a>
                      /
                      <a href="#" @click="editModal(evenement)">
                        <i class="fa fa-edit blue"></i>
                      </a>
                      /
                      <a href="#" @click="deleteSegment(evenement.id)">
                        <i class="fa fa-trash red"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <pagination
                :data="evenements"
                @pagination-change-page="getResults"
              ></pagination>
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
                Créer un événement
              </h5>
              <h5 class="modal-title" v-show="editmode">
                Modifier un événement
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
              @submit.prevent="editmode ? updateSegment() : createSegment()"
            >
              <div class="modal-body">
                <div class="form-group">
                  <label>Date début</label>
                  <input
                    v-model="form.date_debut"
                    type="datetime-local"
                    name="date_debut"
                    autocomplete="off"
                    maxlength="30"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('date_debut') }"
                  />
                  <has-error :form="form" field="date_debut"></has-error>
                </div>

                <div class="form-group">
                  <label>Date début</label>
                  <input
                    v-model="form.date_fin"
                    type="datetime-local"
                    name="date_fin"
                    autocomplete="off"
                    maxlength="30"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('date_fin') }"
                  />
                  <has-error :form="form" field="date_fin"></has-error>
                </div>

                <div class="form-group">
                  <label>Nombre agents impacté</label>
                  <input
                    v-model="form.nb_agent_impacte"
                    type="text"
                    name="nb_agent_impacte"
                    autocomplete="off"
                    maxlength="30"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('nb_agent_impacte'),
                    }"
                  />
                  <has-error :form="form" field="nb_agent_impacte"></has-error>
                </div>

                <div class="form-group">
                  <label>Détails</label>
                  <textarea
                    v-model="form.details"
                    type="text"
                    name="details"
                    autocomplete="off"
                    maxlength="80"
                    class="form-control form-control-sm"
                    :class="{ 'is-invalid': form.errors.has('details') }"
                    id=""
                    cols="30"
                    rows="2"
                  ></textarea>

                  <has-error :form="form" field="details"></has-error>
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

      <!-- Begin Modal Add/ Edit -->
      <div
        class="modal fade"
        id="editDetail"
        tabindex="-1"
        role="dialog"
        aria-labelledby="editDetail"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                Détails de l'événement {{ evenement.date_debut }}
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

            <form @submit.prevent="editmode ? updateDetails() : createDetails()">
              <div class="modal-body">
                <div class="form-group" v-show="false">
                  <label>Evenement</label>
                  <input
                    v-model="form_d.evenement_id"
                    type="text"
                    name="evenement_id"
                    autocomplete="off"
                    maxlength="30"
                    class="form-control form-control-sm"
                    :class="{ 'is-invalid': form.errors.has('evenement_id') }"
                    readonly
                  />
                  <has-error :form="form_d" field="evenement_id"></has-error>
                </div>





                <div class="form-group">
                  <label>Portée </label>
                  <select
                    class="form-control"
                    :class="{ 'is-invalid': form_d.errors.has('porte_id') }"
                    v-model="form_d.porte_id"
                    @change="loadSegmentAndDys"
                  >
                    <option
                      v-for="(dys, index) in portes"
                      :key="index"
                      :value="index"
                      :selected="index == form_d.porte_id"
                    >
                      {{ dys }}
                    </option>
                  </select>
                  <has-error :form="form_d" field="porte_id"></has-error>
                </div>
                <div class="form-group">
                  <label>Campagne </label>
                  <select
                    class="form-control"
                    :class="{ 'is-invalid': form_d.errors.has('campagne_id') }"
                    v-model="form_d.campagne_id"
                    @change="loadSegmentAndDys"
                  >
                    <option
                      v-for="(camp, index) in campagnes"
                      :key="index"
                      :value="index"
                      :selected="index == form_d.campagne_id"
                    >
                      {{ camp }}
                    </option>
                  </select>
                  <has-error :form="form_d" field="campagne_id"></has-error>
                </div>

                <div class="form-group">
                  <label>Segment </label>
                  <select
                    class="form-control"
                    :class="{ 'is-invalid': form_d.errors.has('segment_id') }"
                    v-model="form_d.segment_id"
                  >
                    <option
                      v-for="(camp, index) in segments"
                      :key="index"
                      :value="index"
                      :selected="index == form_d.segment_id"
                    >
                      {{ camp }}
                    </option>
                  </select>
                  <has-error :form="form_d" field="segment_id"></has-error>
                </div>

                <div class="form-group">
                  <label>Dysfonctionnement </label>
                  <select
                    class="form-control"
                    :class="{
                      'is-invalid': form_d.errors.has('dysfonctionnement_id'),
                    }"
                    v-model="form_d.dysfonctionnement_id"
                  >
                    <option
                      v-for="(dys, index) in dysfonctionnements"
                      :key="index"
                      :value="index"
                      :selected="index == form_d.dysfonctionnement_id"
                    >
                      {{ dys }}
                    </option>
                  </select>
                  <has-error
                    :form="form_d"
                    field="dysfonctionnement_id"
                  ></has-error>
                </div>

                <div class="form-group" v-show="editmode">
                                <label>Volume prévu</label>
                                <input
                                  v-model="form_d.volume_prevu"
                                  type="text"
                                  name="volume_prevu"
                                  autocomplete="off"
                                  maxlength="30"
                                  class="form-control form-control-sm"
                                  :class="{ 'is-invalid': form.errors.has('volume_prevu') }"
                                />
                                <has-error :form="form_d" field="volume_prevu"></has-error>
              </div>

              <div class="form-group" v-show="editmode">
                                <label>Volume Réalisé</label>
                                <input
                                  v-model="form_d.volume_realise"
                                  type="text"
                                  name="volume_realise"
                                  autocomplete="off"
                                  maxlength="30"
                                  class="form-control form-control-sm"
                                  :class="{ 'is-invalid': form.errors.has('volume_realise') }"
                                />
                                <has-error :form="form_d" field="volume_realise"></has-error>
                </div>

              </div>

              
            

              <div class="modal-footer">
                 

                <button
                  type="button"
                  class="btn btn-danger"
                  data-dismiss="modal"
                >
                  Fermer
                </button>

                <button
                  type="button"
                  class="btn btn-secondary"
                  @click="cancelDetails()"
                >
                  Annuler
                </button>
                <button v-show="editmode" type="submit" class="btn btn-success">
                  Enregistrer
                </button>
                <button
                  v-show="!editmode"
                  type="submit"
                  class="btn btn-primary"
                >
                  Ajouter
                </button>
              </div>
            </form>

            <div class="card-body table-responsive p-0">
              <table class="table table-hover" id="datatable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Dysfonctionnement</th>
                    <th>Segment</th>
                    <th>Volume prevu</th>
                    <th>Volume realise</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="detailsevent in detailsevenements" :key="detailsevent.id">
                    <td>{{ detailsevent.id }}</td>
                    <td>{{ detailsevent.dysfonctionnement_nom }}</td>
                    <td>{{ detailsevent.segment_nom }}</td>
                    <td>{{ detailsevent.volume_prevu }}</td>
                    <td>{{ detailsevent.volume_realise }}</td>
                    <td>
                      <a href="#" @click="deleteDetails(detailsevent.id)">
                        <i class="fa fa-trash red"></i>
                      </a>
                      /
                      <a href="#" @click="editDetailsevenement(detailsevent)">
                        <i class="fa fa-edit blue"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- End Modal Add/ Edit -->


      <!-- Begin Modal Reporting -->
      <div
        class="modal fade"
        id="mReporting"
        tabindex="-1"
        role="dialog"
        aria-labelledby="mReporting"
        aria-hidden="true"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
               Reporting
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
              @submit.prevent="createReporting()"
            >
              <div class="modal-body">
                <div class="form-group">
                  <label>Date début</label>
                  <input
                    v-model="form_r.date_debut"
                    type="datetime-local"
                    name="date_debut"
                    autocomplete="off"
                    maxlength="30"
                    class="form-control"
                    :class="{ 'is-invalid': form_r.errors.has('date_debut') }"
                  />
                  <has-error :form="form_r" field="date_debut"></has-error>
                </div>

                <div class="form-group">
                  <label>Date fin</label>
                  <input
                    v-model="form_r.date_fin"
                    type="datetime-local"
                    name="date_fin"
                    autocomplete="off"
                    maxlength="30"
                    class="form-control"
                    :class="{ 'is-invalid': form_r.errors.has('date_fin') }"
                  />
                  <has-error :form="form_r" field="date_fin"></has-error>
                </div>

               

              </div>

              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-dismiss="modal">
                  Fermer
                </button>
                <button type="submit" class="btn btn-success">
                  Extraire
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
<!-- End Modal Reporting -->
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
      info: null,
      search_key: ["id", "date_debut", "date_fin", "actif"],
      query: "",
      originalrows: [],
      evenements: {},
      evenement: {},
      campagnes: [],
      dysfonctionnements: [],
      segments: [],
      portes: [],
      detailsevenements: [],
      form: new Form({
        id: "",
        date_debut: "",
        date_fin: "",
        details: "",
        nb_agent_impacte: "",
      }),
      form_d: new Form({
        id: "",
        campagne_id: "",
        segment_id: "",
        porte_id: "",
        evenement_id: "",
        dysfonctionnement_id: "",
        volume_prevu: "0",
        volume_realise: "0",
      }),
      form_r:new Form({
        date_debut:"",
        date_fin: "",
      })
    };
  },
  methods: {
    search() {
      var results = [];
      var searchData = this.evenements.data;
      var sparam = this.query.toLowerCase();
      if (this.query == "") {
        // this.campagnes.data = searchData;
        results = searchData;
      } else {
        searchData.forEach((element) => {
          // pour chauque ligne

          //Les attributs à prendre en compte pour la recherche par défaut sont ceux de l'objet
          var keys = Object.keys(element);
          if (this.search_key.length > 0) {
            // On récupère les attributs s'ils sont définit dans le tableau search_key
            keys = this.search_key;
          }
          for (var i = 0; i < keys.length; i++) {
            // pour chaque attribut
            // un attribut de l'abjet répond au critère de recherche
            if (
              typeof element[keys[i]] == "string" &&
              element[keys[i]].toLowerCase().indexOf(sparam) >= 0
            ) {
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

      axios
        .get("/api/evenement?page=" + page)
        .then(({ data }) => (this.evenements = data.data));

      this.$Progress.finish();
    },
    loadCampagnes() {
      axios
        .get("/api/campagne/list/")
        .then(({ data }) => (this.campagnes = data.data));
    },
    loadPortes() {
      axios
        .get("/api/porte/list/")
        .then(({ data }) => (this.portes = data.data));
    },
    loardDetailsevenements() {
      axios
        .get("/api/detailsevenement/list/" + this.evenement.id)
        .then(({ data }) => {
          this.detailsevenements = data.data;
        });
    },

    loadSegmentAndDys() {
      axios
        .get("/api/segment/list/" + this.form_d.campagne_id)
        .then(({ data }) => {
          this.segments = data.data;
        });

      if (this.form_d.campagne_id != "" && this.form_d.porte_id) {
        axios
          .get(
            "/api/evenement/list/" +
              this.form_d.campagne_id +
              "/" +
              this.form_d.porte_id
          )
          .then(({ data }) => {
            this.dysfonctionnements = data.data;
          });
      }
    },

    loadSegments() {
      axios.get("/api/evenement").then(({ data }) => {
        this.evenements = data.data;
      });
    },
    editModal(site) {
      this.editmode = true;
      this.form.reset();
      $("#addNew").modal("show");
      this.form.fill(site);
    },
    editDetail(evenement) {
      this.evenement = evenement;
      this.form_d.evenement_id = this.evenement.id;
      this.loardDetailsevenements();
      $("#editDetail").modal("show");
    },
    editDetailsevenement(detailsevenement) { 
      this.editmode = true;
      this.form_d.id=detailsevenement.id;
      this.form_d.porte_id=detailsevenement.porte_id;
      this.form_d.campagne_id=detailsevenement.campagne_id;
      this.loadSegmentAndDys();
      this.form_d.segment_id=detailsevenement.segment_id;
      this.form_d.dysfonctionnement_id=detailsevenement.dysfonctionnement_id;
      this.form_d.volume_prevu=detailsevenement.volume_prevu;
      this.form_d.volume_realise=detailsevenement.volume_realise;
      
      
   
    },
    newModal() {
      this.editmode = false;
      this.form.reset();
      $("#addNew").modal("show");
    },
    reportingModal(){
      this.editmode = false;
      this.form.reset();
      $("#mReporting").modal("show");

    },
    createReporting(){
       axios({
                  url: "/api/evenement/reporting/" + this.form_r.date_debut+"/"+this.form_r.date_fin,
                  method: 'GET',
                  responseType: 'arraybuffer',
              })
        //.get("/api/evenement/reporting/" + this.form_r.date_debut+"/"+this.form_r.date_fin)
        .then((response) => {
          
                   let blob = new Blob([response.data], {
                            type: 'pplication/vnd.ms-excel'
                        })
                        let link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        let MyDate=new Date(this.form_r.date_debut).getDate().toString().padStart(2, '0')
                        let Month=new Date(this.form_r.date_debut).getMonth().toString().padStart(2, '0')
                        let Year=new Date(this.form_r.date_debut).getFullYear()
                        let Hours=new Date(this.form_r.date_debut).getHours().toString().padStart(2, '0')
                        let Minutes=new Date(this.form_r.date_debut).getMinutes().toString().padStart(2, '0')
                        let Seconds=new Date(this.form_r.date_debut).getSeconds().toString().padStart(2, '0')                
                        link.download = 'reporting_'+MyDate+'_'+Month+'_'+Year+'_'+Hours+'_'+Minutes+'_'+Seconds+'.xlsx';
                        link.click()
              });
    }, 
    createSegment() {
      this.$Progress.start();
 
      this.form
        .post("/api/evenement")
        .then((data) => {
          if (data.data.success) {
            $("#addNew").modal("hide");

            Toast.fire({
              icon: "success",
              title: data.data.message,
            });

            this.loadCampagnes();
            this.loadSegments();
            this.loadPortes();
            this.loardDetailsevenements();
            this.$Progress.finish();
          } else {
            Toast.fire({
              icon: "error",
              title: "Some error occured! Please try again",
            });

            this.$Progress.failed();
          }
        })
        .catch(() => {
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          });
        });
    },
    updateSegment() {
      this.$Progress.start();
      this.form
        .put("/api/evenement/" + this.form.id)
        .then((response) => {
          // success
          $("#addNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          // Rafraissiement après mise à jour
          this.loadCampagnes();
          this.loadSegments();
          this.loadPortes();
          this.$Progress.finish();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    deleteSegment(id) {
      Swal.fire({
        title: "Êtes vous sûrs?",
        text: "Vous ne pourrez pas revenir en arrière !",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Oui, supprimez-le !",
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.form
            .delete("/api/evenement/" + id)
            .then(() => {
              Swal.fire("suppression!", "Enregistrement supprimé.", "success");
              // Fire.$emit('AfterCreate');
              this.loadSegments();
            })
            .catch((data) => {
              Swal.fire("Failed!", data.message, "warning");
            });
        }
      });
    },

    deleteDetails(id) {
      Swal.fire({
        title: "Êtes vous sûrs?",
        text: "Vous ne pourrez pas revenir en arrière !",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Oui, supprimez-le !",
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.form
            .delete("/api/detailsevenement/" + id)
            .then(() => {
              Swal.fire("suppression!", "Enregistrement supprimé.", "success");
              // Fire.$emit('AfterCreate');
              this.loardDetailsevenements();
            })
            .catch((data) => {
              Swal.fire("Failed!", data.message, "warning");
            });
        }
      });
    },
    cancelDetails(){
      this.editmode=false;
      this.form_d.reset();
    },
    createDetails() {
      this.$Progress.start();
      this.form_d
        .post("/api/detailsevenement")
        .then((data) => {
          if (data.data.success) {
            Toast.fire({
              icon: "success",
              title: data.data.message,
            });

            this.loadCampagnes();
            this.loadSegments();
            this.loadPortes();
            this.loardDetailsevenements();

            // this.$Progress.finish();
          } else {
            Toast.fire({
              icon: "error",
              title: "Some error occured! Please try again",
            });

            //this.$Progress.failed();
          }
        })
        .catch(() => {
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          });
        });
    },
    updateDetails(){
              this.$Progress.start();
              this.form_d.put('/api/detailsevenement/'+this.form_d.id)
              .then((response) => {
                  
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  this.loardDetailsevenements();
                  this.$Progress.finish();

              })
              .catch(() => {
                  this.$Progress.fail();
              });
    }
  },
  mounted() {},
  updated() {},
  created() {
    this.$Progress.start();
    this.loadCampagnes();
    this.loadSegments();
    this.loadPortes();
    this.$Progress.finish();
  },
  filters: {
    truncate: function (text, length, suffix) {
      return text.substring(0, length) + suffix;
    },
  },
  computed: {
    filteredItems() {
      return this.autocompleteItems.filter((i) => {
        return i.text.toLowerCase().indexOf(this.tag.toLowerCase()) !== -1;
      });
    },
  },
  watch: {
    query() {
      this.search();
    },
  },
};
</script>
