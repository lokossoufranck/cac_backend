<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Liste des profiles utilisateur</h3>

              <div class="card-tools">
                <!-- <button
                  type="button"
                  class="btn btn-sm btn-primary"
                  @click="newModal"
                >
                  <i class="fa fa-plus-square"></i>
                  Nouveau
                </button> -->
                <input type="text" v-model="query" placeholder="Rechercher" />
                <button class="btn-dark" @click="loadDroits()">
                  Rechercher
                </button>
              </div>
            </div>
            <!-- /.card-header -->

            <div class="card-body table-responsive p-0">
              <form @submit.prevent="">
                <div class="modal-body">
                  <div class="form-group">
                    <label>Site</label>
                    <select
                      class="form-control"
                      @change="chargerProfile()"
                      v-model="form.site_id"
                    >
                      <option
                        v-for="(site, index) in sites"
                        :key="index"
                        :value="index"
                        :selected="index == form.site_id"
                      >
                        {{ site }}
                      </option>
                    </select>
                    <has-error :form="form" field="site_id"></has-error>
                  </div>

                  <div class="form-group">
                    <label>Profile</label>
                    <select
                      class="form-control"
                      v-model="form.profile_id"
                      @change="chargerProfiles_fonctionnalites()"
                    >
                      <option
                        v-for="(profile, index) in profiles"
                        :key="index"
                        :value="index"
                        :selected="index == form.profile_id"
                      >
                        {{ profile }}
                      </option>
                    </select>
                    <has-error :form="form" field="profile_id"></has-error>
                  </div>
                </div>
              </form>

              <table class="table table-hover" id="datatable">
                <thead>
                  <tr>
                    <th>Profile</th>
                    <th>Profile_id</th>
                    <th>Module</th>
                    <th>Controller</th>
                    <th>Fonction</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="prof_fon in profiles_fonctionnalites.data">
                    <td>{{ prof_fon.profile_id }}</td>
                    <td>{{ prof_fon.profile_nom }}</td>
                    <td>{{ prof_fon.module_nom }}</td>
                    <td>{{ prof_fon.controller_nom }}</td>
                    <td>{{ prof_fon.fonctionnalite_nom }}</td>
                    <td>
                      <div class="form-check form-switch">
                        <input
                          v-if="prof_fon.droit > 0"
                          class="form-check-input"
                          type="checkbox"
                          @click="update_droit(prof_fon, $event)"
                          checked
                        />
                        <input
                          v-else
                          class="form-check-input"
                          type="checkbox"
                          @click="update_droit(prof_fon, $event)"
                        />
                        <label
                          class="form-check-label"
                          for="flexSwitchCheckDefault"
                        >
                          Activer / Désactiver
                        </label>
                      </div>
                      <!--  <a href="#" @click="editModal(prof_fon)">
                            <i class="fa fa-edit blue"></i>
                        </a> -->
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
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
                Définition de profile
              </h5>
              <h5 class="modal-title" v-show="editmode">Modifier de droit</h5>
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
              @submit.prevent="
                editmode ? updateUser_profile() : createUser_profile()
              "
            >
              <div class="modal-body">
                <div class="form-group">
                  <label>Fonctionnalite</label>
                  <select
                    class="form-control"
                    v-model="form_u.fonctionnalite_id"
                  >
                    <option
                      v-for="(fonc, index) in fonctionnalites"
                      :key="index"
                      :value="index"
                      :selected="index == form_u.fonctionnalite_id"
                    >
                      {{ fonc }}
                    </option>
                  </select>
                  <has-error
                    :form="form_u"
                    field="fonctionnalite_id"
                  ></has-error>
                </div>

                <div class="form-group">
                  <label>Profile</label>
                  <select class="form-control" v-model="form_u.profile_id">
                    <option
                      v-for="(profile, index) in profiles"
                      :key="index"
                      :value="index"
                      :selected="index == form_u.profile_id"
                    >
                      {{ profile }}
                    </option>
                  </select>
                  <has-error :form="form_u" field="profile_id"></has-error>
                </div>

                <div class="form-check">
                  <input
                    v-model="form_u.actif"
                    type="checkbox"
                    id="actif"
                    class="form-check-input"
                    :class="{ 'is-invalid': form_u.errors.has('actif') }"
                  />
                  <label for="actif" class="form-check-label">
                    Est actif
                  </label>
                  <has-error :form="form_u" field="actif"></has-error>
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
      profiles_fonctionnalites: {},
      fonctionnalites: {},
      droits: [],
      search_key: ["id", "user_nom", "user_email", "profile_nom", "actif"],
      query: "",
      originalrows: [],
      profiles: [],
      sites: [],
      form: new Form({
        id: "",
        site_id: "",
        profile_id: "",
        actif: true,
      }),
      form_u: new Form({
        id: "",
        fonctionnalite_id: "",
        profile_id: "",
        actif: true,
      }),
    };
  },
  methods: {
    search() {
      var results = [];
      var searchData = this.profiles_fonctionnalites.data;
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
    loadProfile() {
      axios.get("/api/profile/list/").then(({ data }) => {
        this.profiles = data.data;
      });
    },

    loadSites() {
      axios.get("/api/site/list/").then(({ data }) => {
        this.sites = data.data;
      });
    },
    loadFonctionnalite() {
      axios.get("/api/fonctionnalite/list/").then(({ data }) => {
        this.fonctionnalites = data.data;
      });
    },
    chargerProfile() {
      axios
        .get("/api/profile/list/?site_id=" + this.form.site_id)
        .then(({ data }) => {
          this.profiles_fonctionnalites = {};
          this.profiles = data.data;
        });
    },
    chargerProfile_u() {
      axios
        .get("/api/profile/list/?site_id=" + this.form_u.site_id)
        .then(({ data }) => {
          this.profiles_fonctionnalites = {};
          this.profiles = data.data;
        });
    },
    chargerProfiles_fonctionnalites() {
      axios
        .get("/api/droit/list/?profile_id=" + this.form.profile_id)
        .then(({ data }) => {
          this.profiles_fonctionnalites = [];
          this.profiles_fonctionnalites = data;
        });
      console.log(this.profiles_fonctionnalites);
      return this.profiles_fonctionnalites;
    },
    editModal(droit) {
      this.editmode = true;
      this.form_u.reset();
      $("#addNew").modal("show");
      this.form_u.fill(droit);
    },
    
    newModal() {
      this.editmode = false;
      this.loadProfile();
      this.form_u.reset();
      $("#addNew").modal("show");
    },
    createUser_profile() {
      this.$Progress.start();

      this.form_u
        .post("/api/user_profile")
        .then((data) => {
          if (data.data.success) {
            $("#addNew").modal("hide");

            Toast.fire({
              icon: "success",
              title: data.data.message,
            });
            this.chargerProfiles_fonctionnalites();
            this.$Progress.finish();
            //*** */
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
    updateUser_profile() {
      this.$Progress.start();
      this.form_u
        .put("/api/user_profile/" + this.form_u.id)
        .then((response) => {
          // success
          $("#addNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.chargerProfiles_fonctionnalites();
          this.$Progress.finish();
          //  Fire.$emit('AfterCreate');
          //*** */
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    deleteUser_profile(id) {
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
            .delete("/api/sousmenu/" + id)
            .then(() => {
              Swal.fire("suppression!", "Enregistrement supprimé.", "success");
              // Fire.$emit('AfterCreate');
              //** */
            })
            .catch((data) => {
              Swal.fire("Failed!", data.message, "warning");
            });
        }
      });
    },
    //For getting Instant Uploaded Drapeau
    uploadDrapeau(e) {
      let file = e.target.files[0];
      let reader = new FileReader();
      if (file["size"] < 2111775) {
        reader.onloadend = (file) => {
          this.form.url_drapeau = reader.result;
        };
        reader.readAsDataURL(file);
      } else {
        alert("File size can not be bigger than 2 MB");
      }
    },
    update_droit(droit, e) {
      // Attribution et retrait de droit
      this.$Progress.start();
      this.form_u.profile_id = droit.profile_id;
      this.form_u.fonctionnalite_id = droit.fonctionnalite_id;
      this.form_u
        .post("/api/droit/change")
        .then((response) => {
          // success

          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.chargerProfiles_fonctionnalites();
          this.$Progress.finish();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    loadDroits() {
      axios
        .get("/api/droit/list_off_droit/?profile_id=" + this.form.profile_id)
        .then(({ data }) => {
          this.droits = [];
          this.droits = data.data;
        });
    },
  },
  mounted() {},
  updated() {},
  created() {
    this.$Progress.start();
    this.loadSites();
    this.loadFonctionnalite();
    this.loadDroits();
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
