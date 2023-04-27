<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    
    
    
    
    <!--
    <menu-item></menu-item>
-->
<li class="nav-item">
        <router-link to="/dashboard" class="nav-link">
          <i class="nav-icon fas fa-tachometer-alt blue"></i>
          <p>
            Dashboard 
          </p>
        </router-link>
 </li>


  @if (session('modules'))
    
        @foreach (session('modules') as $module)
        <li class="nav-item has-treeview">
            <a href="/app/{{$module->id}}" class="nav-link">
              <i class="nav-icon fa <?php echo $module->icon; ?>"></i>
              <p>{{ $module->nom}}</p>
            </a>
       </li>
      @endforeach

  @endif
    
   
     
         

      
      @can('isAdmin')
      <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fa fa-th-large green"></i>
          <p>
            Module
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">

        <li class="nav-item">
            <router-link to="/module/droit" class="nav-link">
              <i class="nav-icon fas fa-unlock orange"></i>
              <p>
               Droit
              </p>
            </router-link>
          </li>

        <li class="nav-item">
          <router-link to="/module/profile_user" class="nav-link">
              <i class="nav-icon fas fa-user orange"></i>
              <p>
               Profile d'utilisateur
              </p>
            </router-link>
        </li>

        <li class="nav-item">
          <router-link to="/module/profile" class="nav-link">
              <i class="nav-icon fas fa-users orange"></i>
              <p>
               Profile
              </p>
            </router-link>
        </li>
          

          <li class="nav-item">
            <router-link to="/module/sousmenu" class="nav-link">
              <i class="nav-icon fas fa-clone orange"></i>
              <p>
               Sous Menu
              </p>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/module/menu" class="nav-link">
              <i class="nav-icon fas fa-clone orange"></i>
              <p>
               Menu
              </p>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/module/controller" class="nav-link">
              <i class="nav-icon fas fa-border-none orange"></i>     
              <p>
               Controller
              </p>
            </router-link>
          </li>

          
          <li class="nav-item">
            <router-link to="/module/fonctionnalite" class="nav-link">
              <i class="nav-icon fas fa-circle orange"></i>     
              <p>
               Fonctionnalité
              </p>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/module" class="nav-link">
              <i class="nav-icon fas fa-cubes orange"></i>
              <p>
               Module (App)
              </p>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/module/segment" class="nav-link">
              <i class="nav-icon fa fa-braille orange"></i>
              <p>
               Segment
              </p>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/module/campagne" class="nav-link">
              <i class="nav-icon fas fa-bullhorn orange"></i>
              <p>
               Campagne
              </p>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/module/porte" class="nav-link">
              <i class="nav-icon fas fa-chart-bar orange"></i>
              <p>
               Portée
              </p>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/module/departement" class="nav-link">
             
              <i class="nav-icon fas fa-puzzle-piece orange" aria-hidden="true"></i>
              <p>
               Département
              </p>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/module/service" class="nav-link">
              <i class="nav-icon fas fa-object-group orange"></i>
              <p>
               Service
              </p>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/module/client" class="nav-link">
            <i class="nav-icon fas fa-solid fa-paperclip orange"></i>
            
              <p>
               Client d'affaires
              </p>
            </router-link>
          </li>




 
          <li class="nav-item">
            <router-link to="/module/site" class="nav-link">
              <i class="nav-icon fas fa-landmark orange"></i>
              <p>
               Site
              </p>
            </router-link>
          </li>



          <li class="nav-item">
            <router-link to="/module/pays" class="nav-link">
              <i class="nav-icon fas fa-map orange"></i>
              <p>
               Pays
              </p>
            </router-link>
          </li>

         
<!-- 
  https://fontawesome.com/v4.7/icons/
-->
  
        </ul>
      </li>
      @endcan

      @can('isAdmin')
      <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
          <i class="nav-icon fas fa-cog green"></i>
          <p>
            Settings 
            @if (session('connected_profile'))
                <div class="alert alert-success">
                    {{ session('connected_profile') }}
                </div>
            @endif
            
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">

         
            <li class="nav-item">
            <router-link to="/users" class="nav-link">
              <i class="fa fa-users nav-icon blue"></i>
              <p>Users</p>
            </router-link>
          </li>
            <li class="nav-item">
              <router-link to="/developer" class="nav-link">
                  <i class="nav-icon fas fa-cogs white"></i>
                  <p>
                      Developer
                  </p>
              </router-link>
            </li>
            
        </ul>
      </li>

      @endcan

      <li class="nav-item">
        <a href="#" class="nav-link" onclick="event.preventDefault();
          document.getElementById('logout-form').submit();">
          <i class="nav-icon fas fa-power-off red"></i>
          <p>
            {{ __('Logout') }}
          </p>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
        </form>
      </li>
    </ul>
  </nav>