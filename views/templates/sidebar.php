<?php
if($items == true){
//    $items = json_decode($items, true);
  ?>
  <a id="sh-sidebar" href="#">Hide &#10097;</a>
  <aside  class="sidebar">
    <nav class="sb-nav">
     <h2><a id="sb-nav-filter">Filters</a></h2>
     <h2><a id="sb-nav-search">Searches</a></h2>
    </nav>
    <form method="POST" id="form-search-filters" class="sb-form-filter">
    <?php
    include_once(__DIR__."/search_filters.php");
    ?>
    <input type="submit" value="Apply Filters" id="refine-search"  <?php if(isset($index) && $index != "_all" && !is_array($index)){echo ("style='border: 2px solid".$elastic_config[$index]['color']."'");}?>/>
    <button id="flt-clear">Reset Filters</button>
    </form>
    <form id="saved-search-form" class="sb-form-search">
      <ul id="ss-subnav"><li><a class="active ss-current">Current Cart</a></li> | <li><a class="ss-saved">Saved</a></li></ul>
      <div id="cart">
        <ul id="saved-search-results"><em>No saved searches.</em></ul>
        <ul id="cart-actions">
          <li><a id="save-cart">Save</a></li>
          <li><a id="genurl-saved-search">Share</a></li>
          <li><a id="view-cart">View</a></li>
          <li><a id="clear-saved-search">Delete</a></li>
        </ul>
      </div>
      <div id="saved-carts">
        <ul class="saved-carts-nav">
          <li class="active"><a>All</a></li>
          <li><a>Carts</a></li>
          <li><a>Queries</a></li>
        </ul>
        <ol id="saved-items"></ol>
      </div>
    </form>
    <div id="advs-link">
      <a href="<?php echo $base_url;?>views/advancedsearch.php">Advanced Search</a>
      <div class="help-text">
        <a>i</a><p>Tired of all those countless hours spent digging through search results? Want to take charge of you search? Well the wait is over... just click the button.</p>
      </div>
    </div>
  </aside>
<?php
}else{
  echo("Cannot Connect to Elastic Search");
}
?>
