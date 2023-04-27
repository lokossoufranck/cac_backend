<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Liste des whatslearns</h3>

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
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Icon</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="whatslearns in search()" :key="whatslearns.id">
                    <td>{{ whatslearns.id }}</td>
                    <td>{{ whatslearns.name }}</td>
                    <td>{{ whatslearns.icon }}</td>
                    <td>{{ whatslearns.status }}</td>
                    <td>
                      <a href="#" @click="editModal(whatslearns)">
                        <i class="fa fa-edit blue"></i>
                      </a>
                      /
                      <a href="#" v-if="whatslearns.status==false" @click="deleteWhatslearn(whatslearns.id,whatslearns.status)">
                        <i class="fa fa-trash red fa-1x"></i>
                      </a>
                       <a href="#" v-else @click="deleteWhatslearn(whatslearns.id,whatslearns.status)">
                        <i class='fa fa-window-restore fa-1x'></i>
                      </a>


                      
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <pagination
                :data="whatslearns_s"
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
              <h5 class="modal-title" v-show="!editmode">
                Créer un whatslearns
              </h5>
              <h5 class="modal-title" v-show="editmode">Modifier whatslearns</h5>
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
              @submit.prevent="editmode ? updateWhatslearn() : createWhatslearn()"
            >
              <div class="modal-body">
                <div class="form-group">
                  <label>Nom</label>
                  <input
                    v-model="form.name"
                    type="text"
                    name="name"
                    autocomplete="off"
                    maxlength="120"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('name'),
                    }"
                  />
                  <has-error :form="form" field="name"></has-error>
                </div>

                <div class="form-group">
                  <label>Code</label>
                  <input
                    v-model="form.icon"
                    type="text"
                    name="icon"
                    autocomplete="off"
                    maxlength="15"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('icon'),
                    }"
                  />
                  <has-error :form="form" field="icon"></has-error>
                </div>

                <div class="form-group">
                  <label>Ordre</label>
                  <input
                    v-model="form.order"
                    type="number"
                    name="order"
                    autocomplete="off"
                    maxlength="20"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('order'),
                    }"
                  />
                  <has-error :form="form" field="order"></has-error>
                </div>

               

                <div class="form-check">
                  <input
                    v-model="form.status"
                    type="checkbox"
                    id="status"
                    class="form-check-input"
                    :class="{
                      'is-invalid': form.errors.has('status'),
                    }"
                  />
                  <label for="status" class="form-check-label">
                    Est actif
                  </label>
                  <has-error :form="form" field="status"></has-error>
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

// import 'datatables.net/js/jquery.dataTables.min.js'
//  import 'datatables.net-autofill'
// import "datatables.net/js/dataTables.dataTables"
// import "datatables.net-dt/css/jquery.dataTables.min.css"

export default {
  components: {
    VueTagsInput,
  },
  data() {
    return {
      editmode: false,
      info: null,
      whatslearns_s: {},
      search_key: ["name", "icon"],
      query: "",
      originalrows: [],
      form: new Form({
        id: "",
        name: "",
        icon: "",
        order: "",
        url_photo_1: "",
        url_photo_2: "",
        description_photo_1:"",
        description_photo_2:"",
        status: true,
      }),
    };
  },
  methods: {
    search() {
      var results = [];
      var searchData = this.whatslearns_s.data;
      // var searchData = this.whatslearns_s;
      var sparam = this.query.toLowerCase();
      if (this.query == "") {
        this.whatslearns_s.data = searchData;
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
        .get("/api/whatslearn?page=" + page)
        .then(({ data }) => (this.whatslearns_s = data.data));

      this.$Progress.finish();
    },
    loadWhatslearns() {
      axios.get("/api/whatslearn").then(({ data }) => {
        this.whatslearns_s = data.data;
      });
    },
    editModal(whatslearns) {
      this.editmode = true;
      this.form.reset();
      $("#addNew").modal("show");
      this.form.fill(whatslearns);
    },
    newModal() {
      this.editmode = false;
      this.form.reset();
      $("#addNew").modal("show");
    },
    createWhatslearn() {
      this.$Progress.start();

      this.form
        .post("/api/whatslearn")
        .then((data) => {
          if (data.data.success) {
            $("#addNew").modal("hide");

            Toast.fire({
              icon: "success",
              title: data.data.message,
            });
            this.$Progress.finish();
            this.loadWhatslearns();
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
    updateWhatslearn() {
      this.$Progress.start();
      this.form
        .put("/api/whatslearn/" + this.form.id)
        .then((response) => {
          // success
          $("#addNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.$Progress.finish();
          //  Fire.$emit('AfterCreate');

          this.loadWhatslearns();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    deleteWhatslearn(id,status) {
      var action=( status== true ? "supprimez" : "restaurez");
      var labelAction=( status== true ? "suppression" : "restauration");

      Swal.fire({
        title: "Êtes vous sûrs?",
        text: "Vous ne pourrez pas revenir en arrière !",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Oui,  "+action+"-le !",
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.form
            .delete("/api/whatslearn/" + id)
            .then(() => {
              Swal.fire(labelAction+"!", "Enregistrement "+action+".", "success");
              // Fire.$emit('AfterCreate');
              this.loadWhatslearns();
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
    this.loadWhatslearns();
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
