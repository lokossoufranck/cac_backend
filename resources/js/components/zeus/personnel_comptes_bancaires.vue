<template>
  <!-- Begin Modal Add -->
  <div>
    <div
      class="modal fade"
      id="personnel_bank_account_list"
      tabindex="-1"
      role="dialog"
      aria-labelledby="personnel_bank_account_list"
      aria-hidden="true"
      >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Liste des comptes bancaires
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

            <div class="modal-body">

              <div class="card-tools mb-3">

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
                  @click="showNewAccountModal"
                >
                  <i class="fa fa-plus-square"></i>
                  Nouveau
                </button>
                <input type="text" placeholder="Rechercher" />
                <button class="btn-dark" @click="search()">Rechercher</button>
              </div>

              <div class="table-responsive">

                <table class="table table-hover" id="datatable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Banque</th>
                      <th>Numero</th>
                      <th>Actif</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(account, index) in comptesBancaires" :key="account.id">
                      <td>{{ account.id }}</td>
                      <td>{{ account.banque_nom }}</td>
                      <td>{{ account.numero }}</td>
                      <td v-show="account.actif">
                        <span class="badge badge-success">
                          {{ account.actif }}</span
                        >
                      </td>
                      <td v-show="!account.actif">
                        <span class="badge badge-danger">
                          {{ account.actif }}</span
                        >
                      </td>


                      <td>
                        <a href="#" @click="showAccountInfosModal(account)">
                          <i class="fa fa-edit blue"></i>
                        </a>
                        <!-- /
                        <a href="#" @click="deleteAccount(account.id)">
                          <i class="fa fa-trash red"></i>
                        </a> -->

                      </td>
                    </tr>
                  </tbody>
                </table>

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
              
            </div>
        </div>
      </div>
    </div>
    <!-- End Modal Add -->

  <CompteBancaireModalComponent :form="form" :edit-mode="editMode" :banques="listBanques" @account-to-process="processAccount($event)"/>

  </div>

</template>


<script>

import  CompteBancaireModalComponent from "./personnel_compte_bancaire.vue";

export default {

  props: {
    comptesBancaires: Array,
    listBanques: Array,
    personnel: Object,
  },

  components:{
    CompteBancaireModalComponent,
  },

  data() {
    return {
      personnel_id: "",

      form: new Form({
        id: "",
        banque_id: "",
        numero: "",
        personnel_id: "",
        actif: true,
        personnel_fullname: "",
      }),
      editMode: false,
    }
  },

  methods:{
    showNewAccountModal(){
      this.editMode = false;

      this.personnel_id = this.form.personnel_id;
      this.form.reset();
      this.form.personnel_fullname = this.personnel.nom + " "+ this.personnel.prenoms
      this.form.personnel_id = this.personnel_id;
      $("#new_personnel_bank_account").modal("show");
    },

    showAccountInfosModal(account){
      this.editMode = true;
      this.form.fill(account);
      this.form.personnel_fullname = this.personnel.nom + " "+ this.personnel.prenoms
      $("#new_personnel_bank_account").modal("show");
    },

    processAccount(event){

      console.log("Wired to event");
      console.log(event);

      if (event.action == "create"){
        this.createAccount(event.account);
      }
      else{
        this.updateAccount(event.account);
      }
      

    },

    updateAccount(form){

      // this.$Progress.start();
        form.put('/api/compte_bancaire/'+this.form.id)
        .then((response) => {
            // success
            $('#new_personnel_bank_account').modal('hide');
            Toast.fire({
              icon: 'success',
              title: response.data.message
            });
            // this.$Progress.finish();
            this.$emit('process-done', this.form.personnel_id);
            console.log("emitting process done");
            // this.loadPersonnels();
        })
        .catch(() => {
            // this.$Progress.fail();
        });

    },

    createAccount(form){

      // this.$Progress.start();

        form.post('/api/compte_bancaire')
        .then((data)=>{
          if(data.data.success){
            $('#new_personnel_bank_account').modal('hide');

            Toast.fire({
                  icon: 'success',
                  title: data.data.message
              });
            // this.$Progress.finish();
            this.$emit('process-done', this.form.personnel_id);
            console.log("emitting process done");
            // this.loadPersonnels();

          } else {
            Toast.fire({
                icon: 'error',
                title: 'Some error occured! Please try again'
            });

            // this.$Progress.failed();
          }
        })
        .catch(()=>{

            Toast.fire({
                icon: 'error',
                title: 'Some error occured! Please try again'
            });
        })

    },

    deleteAccount(id){

      // this.$Progress.start();
      this.form.delete('/api/compte_bancaire/'+id).then(()=>{
              Swal.fire(
              'suppression!',
              'Enregistrement supprimé.',
              'success'
              );
              this.$emit('process-done');
              console.log("emitting process done", this.form.personnel_id);
          // this.loadPersonnels();
      }).catch((data)=> {
          Swal.fire("Failed!", data.message, "warning");
      });

    }

  }
}
</script>










<!-- Begin Modal Add -->
<!-- <div
class="modal fade"
id="new_bank_account"
tabindex="-1"
role="dialog"
aria-labelledby="new_bank_account"
aria-hidden="true"
>
<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">
        Ajout compte bancaire
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

      <div class="modal-body">

         

      </div>

      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-dismiss="modal"
        >
          Fermer
        </button>
        
      </div>
  </div>
</div>
</div>
End Modal Add -->