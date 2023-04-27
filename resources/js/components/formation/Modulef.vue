<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Liste des modules de formation</h3>

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
                    <th>Trire</th>
                    <th>Code</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="modulef in search()" :key="modulef.id">
                    <td>{{ modulef.id }}</td>
                    <td>{{ modulef.title }}</td>
                    <td>{{ modulef.code }}</td>
                    <td>{{ modulef.status }}</td>
                    <!-- <td><img v-bind:src="'/' + formations.photo" width="100" alt="formations"></td> -->
                    <td>
                      <a href="#" @click="editModal(modulef)">
                        <i class="fa fa-edit blue"></i>
                      </a>
                      /
                      <a
                        href="#"
                        v-if="modulef.status == true"
                        @click="deleteModulef(modulef.id, modulef.status)"
                      >
                        <i class="fa fa-trash red fa-1x"></i>
                      </a>
                      <a
                        href="#"
                        v-else
                        @click="deleteModulef(modulef.id, modulef.status)"
                      >
                        <i class="fa fa-window-restore fa-1x"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <pagination
                :data="modulefs_s"
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
                Créer un module formation
              </h5>
              <h5 class="modal-title" v-show="editmode">
                Modifier un module de formation
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
              @submit.prevent="editmode ? updateModulef() : createModulef()"
            >
              <div class="modal-body">
                <div class="form-group">
                  <label>Code</label>
                  <input
                    v-model="form.code"
                    type="text"
                    name="code"
                    autocomplete="off"
                    maxlength="30"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('code'),
                    }"
                  />
                  <has-error :form="form" field="code"></has-error>
                </div>

                <div class="form-group">
                  <label>Titre</label>
                  <input
                    v-model="form.title"
                    type="text"
                    name="title"
                    autocomplete="off"
                    maxlength="30"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('title'),
                    }"
                  />
                  <has-error :form="form" field="title"></has-error>
                </div>

                <div class="form-group">
                  <label> Formations </label>
                  <vue-tags-input
                    v-model="form.formation"
                    :tags="form.formations"
                    placeholder="Ajouter une formation"
                    :autocomplete-items="filteredFormationItems"
                    @tags-changed="(newTags) => (form.formations = newTags)"
                  />
                  <has-error :form="form" field="formations"></has-error>
                </div>

                <div class="form-group">
                  <label> Objectifs (Whatslearns) </label>
                  <vue-tags-input
                    placeholder="Ajouter un objectif"
                    v-model="form.whatslearn"
                    :tags="form.whatslearns"
                    :autocomplete-items="filteredWhatslearnItems"
                    @tags-changed="(newTags) => (form.whatslearns = newTags)"
                  />
                  <has-error :form="form" field="whatslearns"></has-error>
                </div>

                <div class="form-group">
                  <label>Prix</label>
                  <input
                    v-model="form.prix"
                    type="number"
                    name="prix"
                    autocomplete="off"
                    maxlength="15"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('prix'),
                    }"
                  />
                  <has-error :form="form" field="code"></has-error>
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

                <div class="form-group">
                  <label>Photo</label>
                  <input
                    type="file"
                    @change="onPhoto_1"
                    name="url_photo_1"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('url_photo_1'),
                    }"
                  />
                  <has-error :form="form" field="url_photo_1"></has-error>
                </div>
                <img
                  v-if="getUrl_photo_1() != '/modulefs/'"
                  class="profile-user-img img-fluid"
                  :src="
                    getUrl_photo_1() != ''
                      ? '/modulefs/' + getUrl_photo_1()
                      : '/modulefs/default.png'
                  "
                  alt="Image du drapeau"
                  width="50"
                  height="50"
                />

                <span
                  v-if="errors.data_photo_1"
                  :class="['invalid-feedback d-block']"
                >
                  {{ errors.data_photo_1[0] }}</span
                >

                <div class="form-group">
                  <label>Description photo1 </label>

                  <textarea
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('description_photo_1'),
                    }"
                    v-model="form.description_photo_1"
                    autocomplete="off"
                    name="description_photo_1"
                    id="description_photo_1"
                    cols="30"
                    rows="2"
                  >
                  </textarea>
                  <has-error
                    :form="form"
                    field="description_photo_1"
                  ></has-error>
                </div>

                <div class="form-group">
                  <label>Photo 2</label>
                  <input
                    type="file"
                    @change="onPhoto_2"
                    name="url_photo_2"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('url_photo_2'),
                    }"
                  />
                  <has-error :form="form" field="url_photo_2"></has-error>
                </div>
                <img
                  v-if="getUrl_photo_2() != '/modulefs/'"
                  class="profile-user-img img-fluid"
                  :src="
                    getUrl_photo_2() != ''
                      ? '/modulefs/' + getUrl_photo_2()
                      : '/modulefs/default.png'
                  "
                  alt="Image du drapeau"
                  width="50"
                  height="50"
                />

                <span
                  v-if="errors.data_photo_2"
                  :class="['invalid-feedback d-block']"
                >
                  {{ errors.data_photo_2[0] }}</span
                >
                <div class="form-group">
                  <label>Description photo2</label>

                  <textarea
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('description_photo_2'),
                    }"
                    v-model="form.description_photo_2"
                    autocomplete="off"
                    name="description_photo_2"
                    id="description_photo_2"
                    cols="30"
                    rows="2"
                  >
                  </textarea>
                  <has-error
                    :form="form"
                    field="description_photo_2"
                  ></has-error>
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
      modulefs_s: {},
      search_key: ["name", "code"],
      query: "",
      errors: [],
      originalrows: [],
      form: new Form({
        id: "",
        title: "",
        code: "",
        prix: "",
        duration: "",
        level: "",
        lang: "",
        order: "",
        formations: [],
        whatslearns: [],
        url_photo_1: "",
        url_photo_2: "",
        description_photo_1: "",
        description_photo_2: "",
        status: true,
      }),
      formation: "",
      whatslearn: "",
      autocompleteFormationItems: [],
      autocompleteWhatslearnItems: [],
    };
  },
  methods: {
    search() {
      var results = [];
      var searchData = this.modulefs_s.data;
      // var searchData = this.modulefs_s;
      var sparam = this.query.toLowerCase();
      if (this.query == "") {
        this.modulefs_s.data = searchData;
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
        .get("/api/formation?page=" + page)
        .then(({ data }) => (this.modulefs_s = data.data));

      this.$Progress.finish();
    },
    loadModulefs() {
      axios.get("/api/modulef").then(({ data }) => {
        this.modulefs_s = data.data;
      });
    },
    loadFormations() {
      axios
        .get("/api/formation/list")
        .then((response) => {
          this.autocompleteFormationItems = response.data.data.map((a) => {
            return { text: a.name, id: a.id };
          });
        })
        .catch(() => console.warn("Oh. Something went wrong"));
    },
    loadWhatslearns() {
      axios
        .get("/api/whatslearn/list")
        .then((response) => {
          this.autocompleteWhatslearnItems = response.data.data.map((a) => {
            return { text: a.name, id: a.id };
          });
        })
        .catch(() => console.warn("Oh. Something went wrong"));
    },
    editModal(formations) {
      this.editmode = true;
      this.form.reset();
      $("#addNew").modal("show");
     // formations.url_photo_1="";
    //  formations.url_photo_2="";
      this.form.fill(formations);
      //this.form.url_photo_1='';
    },
    newModal() {
      this.editmode = false;
      this.form.reset();
      $("#addNew").modal("show");
    },
    createModulef() {
      this.$Progress.start();

      this.form
        .post("/api/modulef")
        .then((data) => {
          if (data.data.success) {
            $("#addNew").modal("hide");

            Toast.fire({
              icon: "success",
              title: data.data.message,
            });
            this.$Progress.finish();
            this.loadModulefs();
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
    updateModulef() {
      this.$Progress.start();
      this.form
        .put("/api/modulef/" + this.form.id)
        .then((response) => {
          // success
          $("#addNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.$Progress.finish();
          //  Fire.$emit('AfterCreate');

          this.loadModulefs();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    deleteModulef(id, status) {
      var action = status == true ? "restaurez" : "supprimez";
      var labelAction = status == true ? "restauration" : "suppression";

      Swal.fire({
        title: "Êtes vous sûrs?",
        text: "Vous ne pourrez pas revenir en arrière !",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Oui,  " + action + "-le !",
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.form
            .delete("/api/modulef/" + id)
            .then(() => {
              Swal.fire(
                labelAction + "!",
                "Enregistrement " + action + ".",
                "success"
              );
              // Fire.$emit('AfterCreate');
              this.loadModulefs();
            })
            .catch((data) => {
              Swal.fire("Failed!", data.message, "warning");
            });
        }
      });
    },
    //For getting Instant Uploaded Photo1
    onPhoto_1(e) {
      let file = e.target.files[0];
      let reader = new FileReader();
      if (file["size"] < 2111775) {
        reader.onloadend = (file) => {
          this.form.url_photo_1 = reader.result;
        };
        reader.readAsDataURL(file);
      } else {
        alert("File size can not be bigger than 2 MB");
      }
    },
    onPhoto_2(e) {
      let file = e.target.files[0];
      let reader = new FileReader();
      if (file["size"] < 2111775) {
        reader.onloadend = (file) => {
          this.form.url_photo_2 = reader.result;
        };
        reader.readAsDataURL(file);
      } else {
        alert("File size can not be bigger than 2 MB");
      }
    },
    getUrl_photo_1() {
      return this.form.url_photo_1;
    },
    getUrl_photo_2() {
      return this.form.url_photo_2;
    },


  },
  mounted() {},
  updated() {},
  created() {
    this.$Progress.start();
    this.loadModulefs();
    this.loadFormations();
    this.loadWhatslearns();
    this.$Progress.finish();
  },
  filters: {
    truncate: function (text, length, suffix) {
      return text.substring(0, length) + suffix;
    },
  },
  computed: {
    filteredFormationItems() {
      return this.autocompleteFormationItems.filter((i) => {
        return (
          i.text.toLowerCase().indexOf(this.formation.toLowerCase()) !== -1
        );
      });
    },

    filteredWhatslearnItems() {
      return this.autocompleteWhatslearnItems.filter((i) => {
        return (
          i.text.toLowerCase().indexOf(this.whatslearn.toLowerCase()) !== -1
        );
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
