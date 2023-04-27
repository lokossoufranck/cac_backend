<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Service de gestion MTN ZIK</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
              <form @submit.prevent="">
                <div class="modal-body">
                  <div class="form-row">
                    <div class="col-md-6 mb-3">
                      <label for="phone">Numéro</label>
                      <input
                        type="text"
                        class="form-control"
                        id="phone"
                        placeholder="229 99 99 99 99"
                        required
                      />
                    </div>
                    <div class="col-md-3">
                     <button style="margin-top:30px" type="submit" class="btn btn-secondary">Valider</button>
                    </div>
                  </div>


                </div>

                <div class="modal-footer">
                  <button type="submit" class="btn btn-success ml-0">
                    Activer
                  </button>
                  <button type="submit" class="btn btn-danger mr-auto">
                    Désactiver
                  </button>
                </div>
              </form>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">MTN RBT</div>
          </div>
          <!-- /.card -->
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
    return {};
  },

  mounted() {},
  updated() {},
};
</script>
