      <form id="main-search-form" method="GET" action="<?php echo $base_url;?>views/home.php?search=<?php if(isset($_GET['search'])){ echo $_GET['search'];}?>&cp=0&match_type=<?php echo $match_type;?>">
        <?php include __DIR__."/help-section.php" ?>
        <input type="hidden" id="view-type" name="view" value=""/>
        <input type="hidden" id="sb-view" name="sb" value="<?php echo $sb;?>"/>
        <div id="main-search">
            <div id="faux-select">
                <div id="faux-select-selector"><span>All</span><div id="faux-select-selector-arw">&#x25BC</div></div>
                <ul id="faux-select-list">
                    <li style="background:#444" id="fs-_all">All</li>
                    <?php foreach ($elastic_config as $k => $v){
                        $list[]= "<li style='background:".$elastic_config[$k]['color']."' id='fs-".$k."'>".$elastic_config[$k]['title']."</li>";
                    }
                    echo implode(" ",$list);
                    ?>
                </ul>
                <input name="index" id="faux-select-input" type="hidden"/>
            </div>
            <div id="faux-input">
                <ul id="search-bubbles"></ul>
                <input type="text" id="main-search-selector"/>
                <input name="search" id="main-search-input" type="hidden" value="<?php if(isset($_GET['search'])){echo $_GET['search'];}?>" placeholder="Enter Search Term Here..."/>
                <button id="clear-search" title="Clear Search.">X</button>
                <input id="main-submit" type="submit" value="Go" />
            </div>
            <label id="ex-m" for="string-search"> Exact Match
                <input type="checkbox" id="string-search" name="match_type" value="match_phrase" <?php if(isset($_GET['match_type']) && $_GET['match_type']=="match_phrase"){echo "checked";}?>/>
                <span></span>
            </label>
            <input id="current-cart" name="current_cart" type="hidden" value="<?php echo $current_cart; ?>"/>
            <input id="current-page" name="cp" type="hidden" value="<?php if(isset($_GET['cp'])){echo $_GET['cp'];}else{echo 0;}?>">
        </div>
        <a id="reset-form">Reset</a>
    </form>
