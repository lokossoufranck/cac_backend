<template>



 

   
<li class="nav-item has-treeview">
        <a href="app/" class="nav-link">
          <i class="nav-icon fas fa-list orange"></i>
          <p>
          Mes applications
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>

        <ul class="nav nav-treeview">
          <li v-for="(module,index) of uniqueModules()" :key="index" class="nav-item">     
            <router-link :to="{ path: module[1].url_root }" class="nav-link">
               <i class="fa fa-th-large nav-icon blue"></i>
     <!--  <i class="right fas fa-angle-left"> 
        <img  :src="module[1].url_icon" alt="User Image" class="img-circle elevation-3"></div>
       </i>   -->
              <p>
                {{module[1].nom}} 
              </p>
            </router-link>
          </li>     
        </ul>
</li>

</template>

<script>
    import VueTagsInput from '@johmun/vue-tags-input';
    import 'jquery/dist/jquery.min.js';
    import 'datatables.net-bs4/js/dataTables.bootstrap4.js'

    export default {
      components: {
          VueTagsInput,
        },
        data () {
            return {

                modules : [],
                search_key:['nom','description','version'],
                query: "",
                originalrows :[],
            }
        },
        methods: {            
search() {
      var results = [];
      var searchData = this.modules.data;
      var sparam = this.query.toLowerCase();
      if(this.query == ""){
       // this.modules.data = searchData;
       results=searchData;
      }else{
           searchData.forEach(element => {  // pour chauque ligne
        //Les attributs à prendre en compte pour la recherche par défaut sont ceux de l'objet
         var keys = Object.keys(element);
         if (this.search_key.length>0){  // On récupère les attributs s'ils sont définit dans le tableau search_key
          keys=this.search_key
         }
           for (var i=0 ; i < keys.length ; i++){ // pour chaque attribut
                // un attribut de l'abjet répond au critère de recherche
               if(typeof element[keys[i]] =="string" &&  element[keys[i]].toLowerCase().indexOf(sparam) >=0) {
                results.push(element);
                break;
                 }
           }    
           });
      }
      return results;
    },    uniqueModules(){
         var myMap = new Map();
          this.modules.forEach(object => {
              for (const key in object) {
                if(key=='nom'){
                    myMap.set(object[key], object)
                }
              
              }
          })
              return myMap;
        },
    getResults(page = 1) {
              this.$Progress.start();    
              axios.get('/api/module?page=' + page).then(({ data }) => (this.modules = data.data));
              this.$Progress.finish();
          },
    loadModules(){
              axios.get("/api/module/list_by_user").then(({ data }) => {
                this.modules = data.data; 
                }
                );
          }
    },
        mounted() {
          
        },
        updated() {
      
},
        created() {
            this.$Progress.start();
            this.loadModules();          
            this.$Progress.finish();
        },
        filters: {
            truncate: function (text, length, suffix) {
                return text.substring(0, length) + suffix;
            },
   


        },
        
        computed: {
          filteredItems() {
            return this.autocompleteItems.filter(i => {
              return i.text.toLowerCase().indexOf(this.tag.toLowerCase()) !== -1;
            });
          },
        },
        watch : {
            query () {
                this.search();
            }
        },
    }
</script>
