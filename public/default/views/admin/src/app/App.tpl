<div class="wrapper">
    <div class="preloader"></div>
  <app-header></app-header>
    <!-- sidebar start -->
    <app-sidebar></app-sidebar>
    <!-- sidebar end -->

    <!-- content start -->   
    <div class="site-content">        
        <div class="content-area p-y-1">
            <div class="container-fluid">
                <!-- <app-bread></app-bread> -->
                <router-view></router-view>
            </div>
        </div>
        <app-dialog></app-dialog> 
        <app-footer></app-footer>        
    </div>  

    <!-- content end -->
</div>

