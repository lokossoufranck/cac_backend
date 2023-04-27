<template>
  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header"> {{ this.module.nom }}</div>
        <div class="card-body">
         <!-- <h5 class="card-title">  {{ this.module.description }}</h5> -->
          <p class="card-text">
        
           {{ this.module.description }}
          </p>
         
        </div>
      </div>
    </div>
  </section>
</template>

<script>
    export default {
        data () {
            return {
                editmode: false,
                module : {},
                form: new Form({
                    id : '',
                    name: '',
                    description: '',
                })
            }
        },
        methods: {

            loadModule(){
               // if(this.$gate.isAdmin()){
                    axios.get("/api/module/"+ this.$route.params.id).then(({ data }) => (this.module = data.data));
               // }
            },
            


        },
        mounted() {
            console.log('Component mounted.')
        },
        created() {

            this.$Progress.start();
            this.loadModule();
            this.$Progress.finish();
        }
    }
</script>
