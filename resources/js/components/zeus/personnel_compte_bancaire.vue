<template>
  <!-- Begin Modal Add -->
  <div
    class="modal fade"
    id="new_personnel_bank_account"
    tabindex="-1"
    role="dialog"
    aria-labelledby="new_personnel_bank_account_list"
    aria-hidden="true"
    >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" v-show="!editMode">
            Créer un compte bancaire
          </h5>
          <h5 class="modal-title" v-show="editMode">
            Modifier un compte bancaire
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
          @submit.prevent="editMode ? updateCompteBancaire() : createCompteBancaire()"
        >

          <div class="modal-body">

            <div class="form-group">
                <label>Personnel</label>
                <input v-model="form.personnel_fullname" type="text" name="personnel_fullname" autocomplete="off"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('personnel_fullname') }" disabled="">
                <has-error :form="form" field="personnel_fullname"></has-error>
            </div>

            <div class="form-group">
                <label>Numero</label>
                <input v-model="form.numero" type="text" name="numero" autocomplete="off"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('numero') }">
                <has-error :form="form" field="numero"></has-error>
            </div>

            <div class="form-group">
                <label>Banque</label>
                <select class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('banque_id') }" v-model="form.banque_id" :disabled="editMode">
                  <option 
                      v-for="(banque, index) in banques" :key="index"
                      :value="banque.id"
                      :selected="banque.id == form.banque_id">{{ banque.nom }}</option>
                </select>
                <has-error :form="form" field="banque_id"></has-error>
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

          </div>

        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            data-dismiss="modal"
          >
            Fermer
          </button>

          <button v-show="editMode" type="submit" class="btn btn-success">
            Enregistrer
          </button>
          <button
            v-show="!editMode"
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
</template>


<script>
export default {

  props: {
    form: Object,
    editMode: Boolean,
    banques: Array,
  },

  data() {
    return {
      // count: 0
    }
  },

  methods:{
    updateCompteBancaire(){
      this.$emit('account-to-process', {"account": this.form, "action": "update"});
      console.log("Emitting update account event");
    },

    createCompteBancaire(){
      this.$emit('account-to-process', {"account": this.form, "action": "create"});
      console.log("Emitting create account event");
    },
    
  }
}
</script>