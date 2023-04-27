<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Liste des segments</h3>

              <div class="card-tools">
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
                    <th>Campagne</th>
                    <th>Est actif</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="segment in search()" :key="segment.id">
                    <td>{{ segment.id }}</td>
                    <td>{{ segment.nom }}</td>
                    <td>{{ segment.camp_nom }}</td>
                    <td v-show="segment.actif">
                      <span class="badge badge-success"> {{ segment.actif }}</span>
                    </td>
                    <td v-show="!segment.actif">
                      <span class="badge badge-danger"> {{ segment.actif }}</span>
                    </td>
                    <!-- <td><img v-bind:src="'/' + site.photo" width="100" alt="site"></td> -->
                    <td>
                      <a href="#" @click="editModal(segment)">
                        <i class="fa fa-edit blue"></i>
                      </a>
                      /
                      <a href="#" @click="deleteSegment(segment.id)">
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
                :data="segments"
                @pagination-change-page="getResults"
              ></pagination>
            </div>
          </div>
          <!-- /.card -->
        </div>
      </div>

      <!-- Modal -->
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
              <h5 class="modal-title" v-show="!editmode">Créer une segment</h5>
              <h5 class="modal-title" v-show="editmode">
                Modifier une segment
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
                  <label>Nom</label>
                  <input
                    v-model="form.nom"
                    type="text"
                    name="nom"
                    autocomplete="off"
                    maxlength="30"
                    class="form-control form-control-sm"
                    :class="{ 'is-invalid': form.errors.has('nom') }"
                  />
                  <has-error :form="form" field="nom"></has-error>
                </div>

                <div class="form-group">
                  <label>Description</label>
                  <textarea v-model="form.description"
                    type="text"
                    name="description"
                    autocomplete="off"
                    maxlength="80"
                    class="form-control form-control-sm"
                    :class="{ 'is-invalid': form.errors.has('description') }" id="" cols="30" rows="2"></textarea>
                  
                  <has-error :form="form" field="description"></has-error>
                </div>

                <div class="col-md-4 mb-3">
                  <input
                    v-model="form.actif"
                    type="checkbox"
                    id="actif"
                    class="form-check-input"
                    :class="{ 'is-invalid': form.errors.has('actif') }"
                  />
                  <label for="actif" class="form-check-label">
                    Est actif
                  </label>
                  <has-error :form="form" field="actif"></has-error>
                </div>

                <div class="form-group">
                  <label>campagne </label>
                  <select
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('campagne_id') }"
                    v-model="form.campagne_id"
                  >
                    <option
                      v-for="(camp, index) in campagnes"
                      :key="index"
                      :value="index"
                      :selected="index == form.campagne_id"
                    >
                      {{ camp }}
                    </option>
                  </select>
                  <has-error :form="form" field="campagne_id"></has-error>
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
    </div>
  </section>
</template>

<script>
import VueTagsInput from "@johmun/vue-tags-input";
import "jquery/dist/jquery.min.js";
import "datatables.net-bs4/js/dataTables.bootstrap4.js";

export default {
  components: {
    VueTagsInput,
  },
  data() {
    return {
      editmode: false,
      info: null,
      search_key: ["id", "nom", "camp_nom", "actif"],
      query: "",
      originalrows: [],
      segments : {},
      campagnes: [],
      form: new Form({
        id: "",
        nom: "",
        description: "",
        actif: true,
        campagne_id: "",
      }),
    };
  },
  methods: {
    search() {
      var results = [];
      var searchData = this.segments.data;
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
        .get("/api/segment?page=" + page)
        .then(({ data }) => (this.segments = data.data));

      this.$Progress.finish();
    },
    loadCampagnes() {
      axios.get("/api/campagne/list/").then(({ data }) => (this.campagnes = data.data));
    },
    loadSegments(){
              axios.get("/api/segment").then(({ data }) => {
                this.segments = data.data; 
                }
                );
    },
    editModal(site) {
      this.editmode = true;
      this.form.reset();
      $("#addNew").modal("show");
      this.form.fill(site);
    },
    newModal() {
      this.editmode = false;
      this.form.reset();
      $("#addNew").modal("show");
    },
    createSegment() {
      this.$Progress.start();

      this.form
        .post("/api/segment")
        .then((data) => {
          if (data.data.success) {
            $("#addNew").modal("hide");

            Toast.fire({
              icon: "success",
              title: data.data.message,
            });

            this.loadCampagnes();
            this.loadSegments();
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
        .put("/api/segment/" + this.form.id)
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
            .delete("/api/segment/" + id)
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
  },
  mounted() {},
  updated() {},
  created() {
    this.$Progress.start();
    this.loadCampagnes();
    this.loadSegments();
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
