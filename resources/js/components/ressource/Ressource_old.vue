<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Liste des ressources</h3>

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
                    <th>Code</th>
                    <th>Format</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="ressources in search()" :key="ressources.id">
                    <td>{{ ressources.id }}</td>
                    <td>{{ ressources.name }}</td>
                    <td>{{ ressources.code }}</td>
                    <td>{{ ressources.format_name }}</td>
                    <td>
                      <a href="#" @click="editModal(ressources)">
                        <i class="fa fa-edit blue"></i>
                      </a>
                      /
                      <a href="#" @click="deleteRessource(ressources.id)">
                        <i class="fa fa-trash red fa-1x"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <pagination
                :data="ressources"
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
                Créer une ressource
              </h5>
              <h5 class="modal-title" v-show="editmode">Modifier ressources</h5>
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
              @submit.prevent="editmode ? updateRessource() : createRessource()"
              enctype="multipart/form-data"
            >
              <div class="modal-body">
                <div class="form-group">
                  <label>Nom</label>
                  <input
                    v-model="form.name"
                    type="text"
                    name="name"
                    autocomplete="off"
                    maxlength="30"
                    class="form-control form-control-sm"
                    :class="{
                      'is-invalid': form.errors.has('name'),
                    }"
                  />
                  <has-error :form="form" field="name"></has-error>
                </div>

                <div class="form-group">
                  <label >Code  </label>
                  <input
                    v-model="form.code"
                    type="text"
                    name="code"
                    autocomplete="off"
                    maxlength="15"
                    class="form-control form-control-sm"
                    :class="{
                      'is-invalid': form.errors.has('code'),
                    }"
                  />
                   <!--  <has-error :form="form" field="code"></has-error> -->
                    <span v-if="errors.code" :class="['invalid-feedback d-block']"> {{ errors.code[0] }}</span>
                </div>

                <div class="form-group">
                  <label>Format</label>
                  <select
                    class="form-control form-control-sm"
                    :class="{ 'is-invalid': form.errors.has('format_id') }"
                    v-model="form.format_id"
                  >
                    <option
                      v-for="(format, index) in formats"
                      :key="index"
                      :value="format.id"
                      :selected="format.id == form.format_id"
                    >
                      {{ format.name }}
                    </option>
                  </select>
                  <!--  <has-error :form="form" field="code"></has-error> -->
                    <span v-if="errors.format_id" :class="['invalid-feedback d-block']"> {{ errors.format_id[0] }}</span>
                </div>

                <div class="form-group">
                  <label>Ressource</label>
                  <input
                    type="file"
                    @change="onData_ressourceChange"
                    name="data_ressource"
                    class="form-control form-control-sm"
                    :class="{
                      'is-invalid': form.errors.has('data_ressource'),
                    }"
                  />
                    <!--  <has-error :form="form" field="code"></has-error> -->
                    <span v-if="errors.data_ressource" :class="['invalid-feedback d-block']"> {{ errors.data_ressource[0] }}</span>
                </div>

                <div class="form-group">
                  <label>Description </label>

                  <textarea
                    class="form-control form-control-sm"
                    :class="{
                      'is-invalid': form.errors.has('description'),
                    }"
                    v-model="form.description"
                    autocomplete="off"
                    name="description"
                    id="description"
                    cols="30"
                    rows="2"
                  >
                  </textarea>
                    <!--  <has-error :form="form" field="code"></has-error> -->
                    <span v-if="errors.description" :class="['invalid-feedback d-block']"> {{ errors.description[0] }}</span>
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
import Form from "vform";
// import 'datatables.net/js/jquery.dataTables.min.js'
//  import 'datatables.net-autofill'
// import "datatables.net/js/dataTables.dataTables"
// import "datatables.net-dt/css/jquery.dataTables.min.css"

export default {
  components: {
    VueTagsInput,
  },
  data: () => ({
    editmode: false,
    info: null,
    ressources: {},
    search_key: ["name","code"],
    query: "",
    originalrows: [],
    errors: [],
    formats: {},

    form: new Form({
      id: "",
      name: "",
      code: "",
      format_id: "",
      url: "",
      description: "",
    }),

    form_2: new Form({
      name: "",
      file: null,
    }),
  }),

  methods: {
    search() {
      var results = [];
      var searchData = this.ressources.data;
      var sparam = this.query.toLowerCase();
      if (this.query == "") {
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
        .get("/api/ressource?page=" + page)
        .then(({ data }) => (this.ressources = data.data));

      this.$Progress.finish();
    },
    loadRessources() {
      axios.get("/api/ressource").then(({ data }) => {
        this.ressources = data.data;
      });
    },
    loadFormats() {
      axios.get("/api/format/list").then(({ data }) => {
        this.formats = data.data;
      });
    },
    editModal(ressources) {
      this.editmode = true;
      this.form.reset();
      $("#addNew").modal("show");
      this.form.fill(ressources);
    },
    newModal() {
      this.editmode = false;
      this.form.reset();
      $("#addNew").modal("show");
    },
    createRessource() {
      this.$Progress.start();

      const config = {
        headers: {
          "content-type": "multipart/form-data",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
            .content,
        },
      };
      // form data
      let formData = new FormData();
      formData.append("data_ressource", this.form.data_ressource);
      formData.append("code", this.form.code);
      formData.append("name", this.form.name);
      formData.append("format_id", this.form.format_id);
      formData.append("description", this.form.description);

      // send upload request
      axios
        .post("/api/ressource", formData, config)
        .then(function (response) {
           if (response.data.success) {
            $("#addNew").modal("hide");
            this.loadRessources();
            Toast.fire({
              icon: "success",
              title: data.data.message,
            });
            this.$Progress.finish();


          } else {
            Toast.fire({
              icon: "error",
              title: "Some error occured! Please try again",
            });

            this.$Progress.failed();
          }
         
        })
        .catch((error) =>{
          //this.errors=error;
          this.errors=error.response.data.errors;
          //currentObj.output = error;
         // console.log(error);
        });

      /* this.form
        .post("/api/ressource")
        .then((data) => {
          if (data.data.success) {
            $("#addNew").modal("hide");

            Toast.fire({
              icon: "success",
              title: data.data.message,
            });
            this.$Progress.finish();
            this.loadRessources();
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
        });*/
    },
    updateRessource() {
      this.$Progress.start();
      this.form
        .put("/api/ressource/" + this.form.id)
        .then((response) => {
          // success
          $("#addNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.$Progress.finish();
          //  Fire.$emit('AfterCreate');

          this.loadRessources();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    deleteRessource(id, status) {
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
            .delete("/api/ressource/" + id)
            .then(() => {
              Swal.fire(
                labelAction + "!",
                "Enregistrement " + action + ".",
                "success"
              );
              // Fire.$emit('AfterCreate');
              this.loadRessources();
            })
            .catch((data) => {
              Swal.fire("Failed!", data.message, "warning");
            });
        }
      });
    },
    //For getting Instant Uploaded Photo1
    uploadRessource(e) {
      let file = e.target.files[0];
      let reader = new FileReader();
      if (file["size"] < 2111775) {
        reader.onloadend = (file) => {
          //console.log('RESULT', reader.result)
          this.form.data_ressource = reader.result;
        };
        reader.readAsDataURL(file);
      } else {
        alert("File size can not be bigger than 2 MB");
      }
    },
    getUrl() {
      return this.form.url;
    },

    //For getting Instant Uploaded Photo1
    onData_ressourceChange(e) {
      this.form.data_ressource=e.target.files[0];
      /*const file = e.target.files[0];
      let reader = new FileReader();
      if (file["size"] < 2111775) {
        reader.onloadend = (file) => {
          //console.log('RESULT', reader.result)
          this.form_2.file = reader.result;
        };
        reader.readAsDataURL(file);
      } else {
        alert("File size can not be bigger than 2 MB");
      }

      this.form_2.name = "hello";
      console.log(this.form_2.file);*/
    },
  },
  mounted() {},
  updated() {},
  created() {
    this.$Progress.start();
    this.loadRessources();
    this.loadFormats();
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
   watch: {
    query() {
      this.search();
    },
  },
};
</script>
