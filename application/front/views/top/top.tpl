<nav class="navbar navbar-default white-top">
<div class="container clearfix">

 
<div id="et-info">
<a href="mailto:reboundmobileapp@gmail.com"><span id="">Contactus: reboundmobileapp@gmail.com</span></a>

</div> <!-- #et-info -->
<div  data-height="66" data-fixed-height="40" style="float:right;margin: 0 40px;">
<nav >
<ul id="top-menu" class="nav">
 <%if $include_script_template != 'index.tpl'%>
         <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-6 current_page_item menu-item-61 et-show-dropdown et-hover"><a href="<%$this->config->item('site_url')%>" aria-current="page">Home</a></li>
         <%else%>
        
<%/if%>

<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-6 current_page_item menu-item-61 et-show-dropdown et-hover"><a href="<%$this->config->item('site_url')%>privacy-policy.html" aria-current="page">Privacy Policy</a></li>
<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-6 current_page_item menu-item-61 et-show-dropdown et-hover"><a href="<%$this->config->item('site_url')%>terms-conditions.html">Terms and Conditions</a></li>
<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-6 current_page_item menu-item-61 et-show-dropdown et-hover"><a href="<%$this->config->item('site_url')%>end_user_license_agreement.html">EULA</a></li>
<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-6 current_page_item menu-item-61 et-show-dropdown et-hover"><a href="<%$this->config->item('site_url')%>admin" aria-current="page" target="_blank">Admin</a></li>

</ul>                       
</nav>
</div>
</nav>