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
                    <th>#</th>
                    <th>Nom</th>
                    <th>Code</th>
                    <th>Format</th>
                    <th>Fichier</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="ressource in search()" :key="ressource.id">
                    <td>{{ ressource.id }}</td>
                    <td>{{ ressource.code }}</td>
                    <td>{{ ressource.name }}</td>
                    <td>{{ ressource.format_name }}</td>
                    <td>
                      <a href="#" @click="download_ressource(ressource.url)"
                        >Télecharger</a
                      >
                    </td>
                    <td>
                      <a href="#" @click="editModal(ressource)">
                        <i class="fa fa-edit blue"></i>
                      </a>
                      /
                      <a href="#" @click="deleteRessource(ressource.id)">
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
              <h5 class="modal-title" v-show="editmode">
                Modifier une ressource
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
                  <label>Code </label>
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
                  <span
                    v-if="errors.code"
                    :class="['invalid-feedback d-block']"
                  >
                    {{ errors.code[0] }}</span
                  >
                </div>

                <div class="form-group">
                  <label>Format</label>
                  <select
                    class="form-control form-control-sm"
                    @change="handlerFormat"
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
                  <span
                    v-if="errors.format_id"
                    :class="['invalid-feedback d-block']"
                  >
                    {{ errors.format_id[0] }}</span
                  >
                </div>

                <div v-show="isWeb" class="form-group">
                  <label>Adresse</label>
                  <input
                    v-model="form.adresse"
                    type="text"
                    name="adresse"
                    autocomplete="off"
                    maxlength="30"
                    class="form-control form-control-sm"
                    :class="{
                      'is-invalid': form.errors.has('adresse'),
                    }"
                  />
                  <span
                    v-if="errors.adresse"
                    :class="['invalid-feedback d-block']"
                  >
                    {{ errors.adresse[0] }}</span
                  >
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

                  <img
                    v-if="getUrl() != '/resources/'"
                    class="profile-user-img img-fluid"
                    :src="
                      getUrl() != ''
                        ? '/ressources/' + getUrl()
                        : '/ressources/default.png'
                    "
                    alt="Image du drapeau"
                    width="50"
                    height="50"
                  />
                  <!--  <has-error :form="form" field="code"></has-error> -->
                  <span
                    v-if="errors.data_ressource"
                    :class="['invalid-feedback d-block']"
                  >
                    {{ errors.data_ressource[0] }}</span
                  >
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
                  <span
                    v-if="errors.description"
                    :class="['invalid-feedback d-block']"
                  >
                    {{ errors.description[0] }}</span
                  >
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
      isWeb: false,
      info: null,
      search_key: ["id", "name"],
      query: "",
      errors: [],
      originalrows: [],
      ressources: {},
      formats: {},
      form: new Form({
        id: "",
        name: "",
        code: "",
        format_id: "",
        url: "",
        description: "",
        adresse: "",
      }),
    };
  },
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
    handlerFormat(event) {
      var optionText =
        event.target.options[event.target.options.selectedIndex].text;
      //console.log(optionText);
      if (optionText == "web") {
        this.isWeb = true;
      } else {
        this.isWeb = false;
      }
    },
    getUrl() {
      return this.form.url;
    },
    getResults(page = 1) {
      this.$Progress.start();

      axios
        .get("/api/ressource?page=" + page)
        .then(({ data }) => (this.ressources = data.data));

      this.$Progress.finish();
    },
    //For getting Instant Uploaded Photo1
    onData_ressourceChange(e) {
      this.form.data_ressource = e.target.files[0];
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
    download_ressource(url) {
      axios({
        url: "/api/ressource/download/" + url,
        method: "GET",
        responseType: "arraybuffer",
      }).then((response) => {
        let blob = new Blob([response.data], {
          //type: 'application/json'
        });
        let link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = "ressource_" + url;
        link.click();
      });
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
    editModal(ressource) {
      this.editmode = true;
      this.form.reset();
      if (ressource.adresse == null || ressource.adresse == undefined) {
        this.isWeb = false;
      } else {
        this.isWeb = true;
        console.log("not web");
      }

      $("#addNew").modal("show");
      this.form.fill(ressource);
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
      formData.append("adresse", this.form.adresse);
      formData.append("description", this.form.description);

      axios
        .post("/api/ressource", formData, config)
        .then((data) => {
          if (data.data.success) {
            $("#addNew").modal("hide");
            Toast.fire({
              icon: "success",
              title: data.data.message,
            });
            this.loadRessources();
            this.$Progress.finish();
          } else {
            Toast.fire({
              icon: "error",
              title: "Some error occured! Please try again",
            });

            this.$Progress.failed();
          }
        })
        .catch((error) => {
          // this.errors=error.response.data.errors;
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          }).then((result) => {
            this.errors = error.response.data.errors;
          });
        });
    },
    updateRessource() {
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
      formData.append("adresse", this.form.adresse);
      formData.append("description", this.form.description);
      formData.append("_method", "PUT");

      axios
        .post("/api/ressource/" + this.form.id, formData, config)
        .then((data) => {
          if (data.data.success) {
            $("#addNew").modal("hide");
            Toast.fire({
              icon: "success",
              title: data.data.message,
            });
            this.loadRessources();
            this.$Progress.finish();
          } else {
            Toast.fire({
              icon: "error",
              title: "Some error occured! Please try again",
            });

            this.$Progress.failed();
          }
        })
        .catch((error) => {
          // this.errors=error.response.data.errors;
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          }).then((result) => {
            this.errors = error.response.data.errors;
          });
        });
    },
    deleteRessource(id) {
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
            .delete("/api/ressource/" + id)
            .then(() => {
              Swal.fire("suppression!", "Enregistrement supprimé.", "success");
              // Fire.$emit('AfterCreate');
              this.loadRessources();
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
};
</script>
