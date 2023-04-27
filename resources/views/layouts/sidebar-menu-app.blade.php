<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    
    
    
    
    <!--
    <menu-item></menu-item>
-->
<li class="nav-item">
<a href="/home" class="nav-link">
          <i class="nav-icon fas fa-tachometer-alt blue"></i>
          <p>
          Dashboard
          </p>
        </a>
 </li>


 <!-- Affichage des menus du module sélectionné -->
 @if (session('sousmenus'))
        <?php $printed_menu=array();?>
       @foreach (session('sousmenus') as $sousmenu)
       
          @if ($sousmenu->module_id==request('id') && !in_array($sousmenu->module_id,$printed_menu))
          <?php array_push($printed_menu,$sousmenu->module_id) ;?>
              <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">  
                  <i class="nav-icon fas fa-check-circle orange"></i>
                    <p>
                      {{ $sousmenu->menu_nom}}
                    <i class="right fas fa-angle-left"></i>
                    </p>
                    </a>
                  
                  <ul class="nav nav-treeview"> 
                     <!--Affichage des sousmenus -->
                        @foreach (session('sousmenus') as $sm)
                            @if ($sm->menu_id==$sousmenu->menu_id)
                                <li class="nav-item">


                                <router-link to="/{{$sousmenu->menu_url}}/{{ $sm->url}}" class="nav-link">
                                  <i class="fa fa-chevron-right nav-icon blue"></i>
                                  <p> <?php echo $sm->nom; ?></p>
                               </router-link>


                                




                                </li>
                            @endif
                        @endforeach  
                     <!-- Affichage des sousmenus -->
                  </ul> 
                  
              </li>
          @endif
     @endforeach

 @endif
   
<!-- Affichage des menus du module sélectionné -->

 
   
     
         

    
       
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