<template>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Liste du personnel</h3>
  
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
                      <th>Nom</th>
                      <th>Prenoms</th>
                      <th>Matricule</th>
                      <th>Numéro CNSS</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(personnel, index) in personnels" :key="personnel.id">
                      <td>{{ personnel.id }}</td>
                      <td>{{ personnel.nom }}</td>
                      <td>{{ personnel.prenoms }}</td>
                      <td>{{ personnel.matricule }}</td>
                      <td>{{ personnel.numero_cnss }}</td>
  
                      <td>
                        <a href="#" @click="editModal(personnel)">
                          <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deactivatePersonnel(personnel.id)">
                          <i class="fa fa-trash red"></i>
                        </a>
                        /
                        <span class="dropdown">
                          <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v text-secondary"></i>
                          </a>
                          <!-- <a href="#">
                            <i class="fa fa-ellipsis-v red dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></i>
                          </a> -->
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" @click="showBankAccountList(personnel)">Comptes bancaires</a>
                            <a class="dropdown-item" href="#">Affectation Services/Départements</a>
                            <a class="dropdown-item" href="#">Affectation Programmes</a>
                          </div>
                        </span>

                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <!-- <pagination
                  :data="personnels"
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
                  Créer un employé
                </h5>
                <h5 class="modal-title" v-show="editmode">
                  Modifier un employé
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
                @submit.prevent="editmode ? updatePersonnel() : createPersonnel()"
              >
                <div class="modal-body">

                  <div class = "row mx-0 mb-3">
                    <img :src="(form.url_photo_identite == null) ? personnel_profile_photos+'user_icon.jpeg': ((form.url_photo_identite.indexOf('.png') == -1) ? '' : '/images/module/zeus/personnel_profile_photos/'+form.matricule+'/') + form.url_photo_identite" @click = "triggerFileInput()" width = "100em" class="rounded img-thumbnail ml-1 mr-3 col-2" alt="user_picture">

                    <div class="mb-3 d-none">
                      <label for="formFile" id = "user_photo_file_input" class="form-label">Default file input example</label>
                      <input class="form-control" type="file" id="formFile" name = "photo_identite" @change='uploadPhotoIdentite'>
                    </div>


                    <div class="form-group col">
                        <label>Matricule</label>
                        <input v-model="form.matricule" type="text" name="matricule" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('matricule') }" disabled>
                        <has-error :form="form" field="matricule"></has-error>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col">
                        <label>Nom</label>
                        <input v-model="form.nom" type="text" name="nom" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('nom') }">
                        <has-error :form="form" field="nom"></has-error>
                    </div>

                    <div class="form-group col">
                        <label>Prénoms</label>
                        <input v-model="form.prenoms" type="text" name="prenoms" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('prenoms') }">
                        <has-error :form="form" field="prenoms"></has-error>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col">
                        <label>Numéro CNSS</label>
                        <input v-model="form.numero_cnss" type="text" name="numero_cnss" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('numero_cnss') }">
                        <has-error :form="form" field="numero_cnss"></has-error>
                    </div>

                    <div class="form-group col">
                        <label>Enfants à charge</label>
                        <input v-model="form.nb_enfants_a_charge" type="number" name="nb_enfants_a_charge" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('nb_enfants_a_charge') }">
                        <has-error :form="form" field="nb_enfants_a_charge"></has-error>
                    </div>

                  </div>

                  <div class="row">
                    <div class="form-group col">
                        <label>Service</label>
                        <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('service_id') }" v-model="form.service_id">
                          <option 
                              v-for="(service, index) in services" :key="index"
                              :value="index"
                              :selected="index == form.service_id">{{ service.nom }}</option>
                        </select>
                        <has-error :form="form" field="service_id"></has-error>
                    </div>

                    <div class="form-group col">
                        <label>Adresse mail</label>
                        <input v-model="form.email" type="email" name="email" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                        <has-error :form="form" field="email"></has-error>
                    </div>
                  </div>

                  <div class="row" v-show="!editmode">

                    <!-- <div class="form-group col">
                        <label>Compte bancaire</label>
                        <select id = "input-compte-bancaire" class="form-control form-control-sm" @change="onCompteBancaireChange($event)" :class="{ 'is-invalid': form.errors.has('compte_bancaire_id') }" v-model="form.compte_bancaire_id">
                            <option 
                                value="Nouveau"
                                :selected="true">Nouveau</option>
                            <option 
                                v-for="(compteBancaire, index) in form.comptes_bancaires" :key="index"
                                :value="compteBancaire.id"
                                :selected="compteBancaire.id == form.compte_bancaire_id">{{ compteBancaire.banque_nom + " - " + compteBancaire.numero }}</option>
                          </select>
                          <has-error :form="form" field="compte_bancaire_id"></has-error>
                    </div> -->

                    <div class="form-group col">
                        <label>Banque</label>
                        <select id = "select-bank" class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('banque_id') }" v-model="form.banque_id">
                            <option 
                                v-for="(banque,index) in banques" :key="index"
                                :value="banque.id"
                                :selected="banque.id == form.banque_id">{{ banque.nom }}</option>
                          </select>
                          <has-error :form="form" field="banque_id"></has-error>
                    </div>

                    <div class="form-group col">
                        <label>Numero de compte</label>
                        <input id = "input-bank-account-number" v-model="form.numero_de_compte" type="text" name="numero_de_compte" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('numero_de_compte') }">
                        <has-error :form="form" field="numero_de_compte"></has-error>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col">
                        <label>Sexe</label>
                        <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('sexe') }" v-model="form.sexe">
                            <option 
                                value="Féminin"
                                :selected="'Masculin' == form.sexe">Féminin</option>

                            <option 
                                value="Masculin"
                                :selected="'Masculin' == form.sexe">Masculin</option>

                          </select>
                          <has-error :form="form" field="sexe"></has-error>
                    </div>

                    <div class="form-group col">
                        <label>Date de naissance</label>
                        <input v-model="form.date_naissance" type="date" name="date_naissance" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('date_naissance') }">
                        <has-error :form="form" field="date_naissance"></has-error>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col">
                        <label>Permis de conduire</label>
                        <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('permis_categorie_id') }" v-model="form.permis_categorie_id">
                          <option 
                              value="None"
                              :selected="true">Aucun</option>

                          <option 
                              v-for="(permis_categorie, index) in permisCategories" :key="index"
                              :value="permis_categorie.id"
                              :selected="permis_categorie.id == form.permis_categorie_id">{{ permis_categorie.libelle }}</option>
                        </select>
                          <has-error :form="form" field="permis_categorie_id"></has-error>
                    </div>

                    <div class="form-group col">
                        <label>Niveau Etude</label>
                        <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('etude_niveau_id') }" v-model="form.etude_niveau_id">
                          <option 
                              v-for="(etude_niveau, index) in etudeNiveaux" :key="index"
                              :value="etude_niveau.id"
                              :selected="etude_niveau.id == form.etude_niveau_id">{{ etude_niveau.libelle }}</option>
                        </select>
                          <has-error :form="form" field="etude_niveau_id"></has-error>
                        <has-error :form="form" field="etude_niveau_id"></has-error>
                    </div>
                    
                  </div>

                  <div class="row">
                    <!-- <div class="form-group col">
                        <label>Type d'activité</label>
                        <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('activite_id') }" v-model="form.activite_id">
                          <option 
                              v-for="(activite, index) in activites" :key="index"
                              :value="activite.id"
                              :selected="index == form.activite_id">{{ activite.nom }}</option>
                        </select>
                          <has-error :form="form" field="activite_id"></has-error>
                    </div> -->

                    <div class="form-group col">
                        <label>Domaine d'étude</label>
                        <input v-model="form.domaine_etude" type="text" name="domaine_etude" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('domaine_etude') }">
                        <has-error :form="form" field="domaine_etude"></has-error>
                    </div>

                    <div class="form-group col">
                        <label>Date d'entrée</label>
                        <input v-model="form.date_entree" type="date" name="date_entree" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('date_entree') }">
                        <has-error :form="form" field="date_entree"></has-error>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col">
                        <label>Adresse</label>
                        <input v-model="form.adresse" type="text" name="adresse" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('adresse') }">
                        <has-error :form="form" field="adresse"></has-error>
                    </div>

                    <div class="form-group col">
                        <label>Téléphone</label>
                        <input v-model="form.telephone" type="text" name="telephone" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('telephone') }">
                        <has-error :form="form" field="telephone"></has-error>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col">
                        <label>Nationalité</label>
                        <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('nationalite_id') }" v-model="form.nationalite_id">
                          <option 
                              v-for="(nationalite, index) in nationalites" :key="index"
                              :value="nationalite.id"
                              :selected="nationalite.id == form.nationalite_id">{{ nationalite.libelle }}</option>
                        </select>
                          <has-error :form="form" field="nationalite_id"></has-error>
                    </div>

                    <div class="form-group col">
                        <label>Situation matrimoniale</label>
                        <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('situation_matrimoniale') }" v-model="form.situation_matrimoniale">
                          <option 
                              v-for="(situationMatrimoniale, index) in situationsMatrimoniales" :key="index"
                              :value="situationMatrimoniale"
                              :selected="index == form.situation_matrimoniale">{{ situationMatrimoniale }}</option>
                        </select>
                        <has-error :form="form" field="situation_matrimoniale"></has-error>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col">
                        <label>Fonction</label>
                        <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('fonction_id') }" v-model="form.fonction_id">
                          <option 
                              v-for="(fonction, index) in personnelFonctions" :key="index"
                              :value="fonction.id"
                              :selected="index == form.fonction_id">{{ fonction.libelle }}</option>
                        </select>
                          <has-error :form="form" field="fonction_id"></has-error>
                    </div>

                    <div class="form-group col">
                        <label>Status</label>
                        <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('status_id') }" v-model="form.status_id">
                          <option 
                              v-for="(status, index) in personnelStatus" :key="index"
                              :value="status.id"
                              :selected="index == form.status_id">{{ status.libelle }}</option>
                        </select>
                        <has-error :form="form" field="status_id"></has-error>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col">
                        <label>Type Pièce Identité</label>
                        <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('piece_identite_categorie_id') }" v-model="form.piece_identite_categorie_id">
                          <option 
                              v-for="(categorie, index) in pieceIdentiteCategories" :key="index"
                              :value="categorie.id"
                              :selected="categorie.id == form.piece_identite_categorie_id">{{ categorie.libelle }}</option>
                        </select>
                          <has-error :form="form" field="piece_identite_categorie_id"></has-error>
                    </div>

                    <div class="form-group col">
                        <label>Numero Pièce Identité</label>
                        <input v-model="form.piece_identite_numero " type="text" name="piece_identite_numero" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('piece_identite_numero') }">
                        <has-error :form="form" field="piece_identite_numero"></has-error>
                    </div>

                    <div class="form-group col">
                        <label>Date d'expiration Pièce Identité</label>
                        <input v-model="form.piece_identite_date_expiration " type="date" name="piece_identite_date_expiration" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('piece_identite_date_expiration') }">
                        <has-error :form="form" field="piece_identite_date_expiration"></has-error>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col">
                        <label>Personne à contacter</label>
                        <input v-model="form.personne_a_contacter " type="text" name="personne_a_contacter" autocomplete="off"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('personne_a_contacter') }">
                          <has-error :form="form" field="personne_a_contacter"></has-error>
                    </div>

                    <div class="form-group col">
                        <label>Domicialition irrévocable</label>
                        <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('domiciliation_irrevocable') }" v-model="form.domiciliation_irrevocable">
                          <option 
                              :value="true"
                              :selected="'OUI' == form.domiciliation_irrevocable">OUI</option>
                            <option 
                              :value="false"
                              :selected="'NON' == form.domiciliation_irrevocable">NON</option>
                        </select>
                        <has-error :form="form" field="domiciliation_irrevocable"></has-error>
                    </div>
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
  
        <PersonnelComptesBancairesComponent :personnel="form" :comptes-bancaires="form.comptes_bancaires" :list-banques="banques" @process-done="onAccountProcessDone($event)" />

      </div>
    </section>
  </template>
  
  <script>
  import VueTagsInput from "@johmun/vue-tags-input";
  import  "jquery/dist/jquery.min.js";
  import "datatables.net-bs4/js/dataTables.bootstrap4.js";
  import PersonnelComptesBancairesComponent from "./personnel_comptes_bancaires.vue";
  
  export default {
    components: {
      VueTagsInput,
      PersonnelComptesBancairesComponent,
    },
    data() {
      return {
        personnel_profile_photos: "/images/module/zeus/personnel_profile_photos/",
        editmode: false,
        // info: null,
        search_key: ["id", "date_debut", "date_fin", "actif"],
        query: "",
        personnels: {},
        banques: [],
        services: {},
        etudeNiveaux: {},
        activites: {},
        permisCategories: {},
        nationalites: {},
        pieceIdentiteCategories: {},
        personnelFonctions: {},
        personnelStatus: {},
        situationsMatrimoniales: ["Célibataire", "Marié(e)", "Divorcé(e)", "Veuf/Veuve"],

        form: new Form({
          id: "",
          matricule: "",
          nom: "",
          prenoms: "",
          nb_enfants_a_charge: "",
          numero_cnss: "",
          url_photo_identite: null,
          banque_id: "",
          numero_de_compte: "",
          service_id: null,
          permis_categorie_id:"",
          sexe: "",
          date_naissance: null,
          date_entree: null,
          situation_matrimoniale: "",
          activite_id: "",
          domaine_etude: "",
          fonction_id: null,
          status_id: null,
          domiciliation_irrevocable: true,
          personne_a_contacter: "",
          nationalite_id: "",
          etude_niveau_id: "",
          email: "",
          adresse: "",
          telephone: "",
          piece_identite_numero: "",
          piece_identite_date_expiration: null,
          piece_identite_categorie_id: null,
          compte_bancaire_id: null,
          comptes_bancaires: [],
        }),
      };
    },
    methods: {
      
      loadServices() {
          axios.get("/api/service").then(({ data }) => {
              this.services = data.data.data;
              console.log("Services : ", this.services);
          });
      },

      loadPieceIdentiteCategories() {
          axios.get("/api/piece_identite_categorie").then(({ data }) => {
              this.pieceIdentiteCategories = data.data;
          });
      },

      loadPersonnelStatus() {
          axios.get("/api/personnel_status").then(({ data }) => {
              this.personnelStatus = data.data;
          });
      },

      loadPersonnelFonctions() {
          axios.get("/api/personnel_fonction").then(({ data }) => {
              this.personnelFonctions = data.data;
          });
      },

      loadComptesBancaires(id) {
          axios.get("/api/compte_bancaire?personnel_id="+id).then(({ data }) => {
              this.form.comptes_bancaires = data.data;
          });
      },

      loadBanques() {
          axios.get("/api/banque").then(({ data }) => {
              this.banques = data.data;
          });
      },

      loadPermisCategories() {
          axios.get("/api/permis_categorie").then(({ data }) => {
              this.permisCategories = data.data;
          });
      },

      loadEtudeNiveaux() {
          axios.get("/api/etude_niveau").then(({ data }) => {
              this.etudeNiveaux = data.data;
              console.log("Etude Niveaux : ", this.etudeNiveaux)
          });
      },

      onCompteBancaireChange(event){
        var selectBank = document.getElementById("select-bank");
        var inputBankAccountNumber = document.getElementById("input-bank-account-number");

        if (event.target.value != "Nouveau"){
          selectBank.setAttribute("disabled", "");
          inputBankAccountNumber.setAttribute("disabled", "");

          console.log("not Nouveau : ", event.target.value, this.personnelComptesBancaires)
          this.personnelComptesBancaires.forEach(element => {
            console.log("in each : ", element.id)
            if (element.id == event.target.value){
              this.form.numero_de_compte = element.numero;
              this.form.banque_id = element.id;
            }
          });

        }
        else{
          selectBank.removeAttribute("disabled");
          inputBankAccountNumber.removeAttribute("disabled");

          this.form.numero_de_compte = "";
          this.form.banque_id = "Nouveau";

        }
      },

      loadNationalites() {
          axios.get("/api/nationalite").then(({ data }) => {
              this.nationalites = data.data;
          });
      },

      loadPersonnels() {
            axios.get("/api/personnel").then(({ data }) => {
                this.personnels = data.data;
            });
        },

        search(){

        },

      newModal() {
        this.editmode = false;
        this.form.reset();

        // clearing some form controls
        // $("#input-compte-bancaire").children().each(function () {
        //   console.log("test in function", this);
        //   if (this.value != "Nouveau"){
        //     this.remove();
        //   }
        // });
        
        $("#addNew").modal("show");

      },

      triggerFileInput() {
        $("#user_photo_file_input").trigger("click");
      },

      createPersonnel() {
        this.$Progress.start();

        this.form.post('/api/personnel')
        .then((data)=>{
          if(data.data.success){
            $('#addNew').modal('hide');

            Toast.fire({
                  icon: 'success',
                  title: data.data.message
              });
            this.$Progress.finish();
            this.loadPersonnels();

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

      editModal(personnel){

          // this.loadComptesBancaires(personnel.id);
          this.editmode = true;
          this.form.reset();
          $('#addNew').modal('show');
          this.form.fill(personnel);
      },

      uploadPhotoIdentite(e) {

        let file = e.target.files[0];
        let reader = new FileReader();  
        if(file['size'] < 2111775)
        {
            reader.onloadend = (file) => {
            //console.log('RESULT', reader.result)
              this.form.url_photo_identite = reader.result;
            }              
            reader.readAsDataURL(file);
        }else{
            alert('File size can not be bigger than 2 MB')
        }

      },

      updatePersonnel() {
        this.$Progress.start();
        this.form.put('/api/personnel/'+this.form.id)
        .then((response) => {
            // success
            $('#addNew').modal('hide');
            Toast.fire({
              icon: 'success',
              title: response.data.message
            });
            this.$Progress.finish();
                //  Fire.$emit('AfterCreate');

            this.loadPersonnels();
        })
        .catch(() => {
            this.$Progress.fail();
        });
      },

      showBankAccountList(personnel){

        this.form.fill(personnel);

        this.loadComptesBancaires(personnel.id);

        $("#personnel_bank_account_list").modal("show");
      },

      deactivatePersonnel(id) {

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
                          this.form.delete('/api/personnel/'+id).then(()=>{
                                  Swal.fire(
                                  'suppression!',
                                  'Enregistrement supprimé.',
                                  'success'
                                  );
                              // Fire.$emit('AfterCreate');
                              this.loadPersonnels();
                          }).catch((data)=> {
                              Swal.fire("Failed!", data.message, "warning");
                          });
                        }
                  })

      },

      onAccountProcessDone(personnel_id){

        this.loadComptesBancaires(personnel_id);

      }
      
    },

    mounted() {},
    updated() {},
    created() {
      this.$Progress.start();
      this.loadPersonnels();
      this.loadBanques();
      this.loadServices();
      this.loadEtudeNiveaux();
      this.loadPermisCategories();
      this.loadNationalites();
      this.loadPersonnelFonctions();
      this.loadPersonnelStatus();
      this.loadPieceIdentiteCategories();
      this.$Progress.finish();
    },

  };
  </script>
  