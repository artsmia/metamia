</section><!--close the main content section-->
<footer>
    MIA : <em>Elastic Search - Interface</em> | 2015
</footer>
<script src="<?php echo $base_url?>js/masonry.pkgd.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>js/build/production.js"></script>
<?php
if(!isset($_GET['view']) || $_GET['view']=="" || $_GET['view'] == "list"){?>
    <script type="text/javascript">
        jQuery(document).ready(function(){  
            viewList();
        });
    </script>
<?php
}else if($_GET['view']=="thum"){?>
    <script>
        jQuery(document).ready(function(){  
            viewThumb();
        });
    </script>
<?php
}else if($_GET['view']=="mid"){
?>
    <script>
        jQuery(document).ready(function(){
            viewListThumb();
        });
    </script>
<?php
}
?>
</body>
</html>
