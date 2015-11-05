<div id="search-filters">
  <ul id="type-chkbx">
    <div class="help-text">
      <a>i</a><p>Choose any of the following checkboxes to limit your search to the specific type that you would like to search on (i.e. "Images" will limit your search to only images.)</p>
    </div>
    <li><label class="faux-cbx" ><input class="type" name="type[]" type="checkbox" value="Image" <?php if(isset($_GET['type']) && in_array("Image",$_GET['type'])){echo "checked";}?>/>Images</label></li>
    <li><label class="faux-cbx" ><input class="type" name="type[]" type="checkbox" value="Video" <?php if(isset($_GET['type']) && in_array("Video",$_GET['type'])){echo "checked";}?>/>Video</label></li>
    <li><label class="faux-cbx" ><input class="type" name="type[]" type="checkbox" value="Audio" <?php if(isset($_GET['type']) && in_array("Audio",$_GET['type'])){echo "checked";}?>/>Audio</label></li>
    <li><label class="faux-cbx" ><input class="type" name="type[]" type="checkbox" value="Document" <?php if(isset($_GET['type']) && in_array("Document",$_GET['type'])){echo "checked";}?>/>Document</label></li>
    <li><label class="faux-cbx" ><input class="type" name="type[]" type="checkbox" value="Art Object" <?php if(isset($_GET['type']) && in_array("Art Object",$_GET['type'])){echo "checked";}?>/>Art Objects</label></li>
    <li><label class="faux-cbx" ><input class="type" name="type[]" type="checkbox" value="page" <?php if(isset($_GET['type']) && in_array("page",$_GET['type'])){echo "checked";}?>/>Web Page</label></li>
  </ul>
  <div class="help-text">
      <a>i</a><p>These filters are internaly mapped within the systems provide quick and easy data groupings. Once selected an input will appear where you can enter a term to further filter upon.</p>
  </div>
  <div id="qa">
    <h3>Query Assistant</h3>
    <select>
       <option disabled selected>Choose a Filter</>
       <?php foreach ($assistant_filters as $ak => $av){?>
           <option value="<?php echo $ak;?>"><?php echo $ak ?></option>
       <?php
       } ?>
    </select>
  </div>
    <div id="inputs">
        <?php
         $inp = $search->setFilterInputs();
         echo($inp);
        ?>
    </div>
</div>
