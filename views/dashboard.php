<div id="results-dashboard">
        <div id="search-status">
        <?php include(__DIR__."/sentence.php");?>
        </div>

      <ul id="view-selector" class="clear">
        <li>Views:</li>
        <li><a id="thum-view">
          <span>Thumb</span>
          <span></span><span></span><span></span><span></span>
        </a></li>
        <li>
          <a id="mid-view">
            <span>Mid</span>
           <span><span></span></span>
           <span><span></span></span>
           <span><span></span></span>
        </a></li>
        <li>
          <a id="list-view">
            <span>List</span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
          </a>
        </li>
      </ul>
      <form id="result-order" method="POST">
          <label for="ordval">Order by:</label>
          <select name="ordval" id="ordval">
              <option <?php if(isset($_GET['ordval']) && $_GET['ordval']=="Relevance"){echo "selected";}?> value="Relevance">Relevance</option>
              <option <?php if(isset($_GET['ordval']) && $_GET['ordval']=="title.sort"){echo "selected";}?> value="title.sort">Title</option>
              <option <?php if(isset($_GET['ordval']) && $_GET['ordval']=="_index"){echo "selected";}?> value="_index">System</option>
              <option <?php if(isset($_GET['ordval']) && $_GET['ordval']=="_type"){echo "selected";}?> value="_type">Type</option>
          </select>
          <label for="ordtype">
          <select name="ordtype" id="ordtype">
              <option <?php if(isset($_GET['ordtype']) && $_GET['ordtype']=="desc"){echo "selected";}?>>desc</option>
              <option <?php if(isset($_GET['ordtype']) && $_GET['ordtype']=="asc"){echo "selected";}?>>asc</option>
          </select>
          </label>
      </form>
        <div class="pagination">
            Page: <?php echo $from/$perpage+1;?> of <?php echo ceil($response['hits']['total']/$perpage);?> - viewing <?php echo count($hits);?>.
            <?php echo makePagination($response,$from,$perpage,$current_page);?>
        </div>
    <div class="clear"></div>
    </div>
