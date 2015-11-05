
<?php
//-----------------------+
//    Global Site Vars   |
//-----------------------+

$base_url = "";
$site_title = "MetaMia";
$rs_url = ""; // ResourceSpace URL
$rs_api_key = "";
$rs_api_secret = "";
$allowed_indexes = "" // Indexes within ElasticSearch (e.g. resourcespace, tms, website);
$allowed_types = "Audio,Video,Document,Photo,page";
$download_path = $base_url."ctrl/downloadfile.php";
$largetext=array("Extracted Text","text","Text");
//-----------------------+
//    Config Database    |
//-----------------------+
define("DB_HOST", "localhost");
define("DB_USER", "");
define("DB_PASS", "");
define("DB_NAME", "");
//-------------------+
//    Config LDAP    |
//-------------------+
// For authentication if desired
//$ldap['server']="";
//$ldap['port'] = "389";
//$ldap['field']="";
//$ldap['basedn'] = "ou=,dc=,dc=";
//--------------------+
//    Config Redis    |
//--------------------+
// For saving carts
$rds['server']="";
$rds['port']="";
$rds['db']="1";
//-----------------------------+
//    Config Elastic Search    |
//-----------------------------+
$elastic_url = "localhost:9200/";
$elastic_config = array(
    //ResourceSpace
    "resourcespace"=>array(
        "url"=>"",
        "result"=>array(
            "title"=>"title",
            "link"=>"",
            "image"=>"thumbnail",
            "download"=>"",
            "view"=>array(
                "Caption",
                "Source Dept",
            )
        ),
        "title"=>"ResourceSpace",
        "color"=>"rgb(140 , 210 , 205)",
    ),
    //TMS
    "objects"=>array(
        "url"=>"",
        "result"=>array(
            "title"=>"title",
            "alt_title"=>"title",
            "link"=>"",
            "image_valid"=>"",
            "image"=>"",
            "download"=>"",
            "view"=>array(
                "description",
                "medium",
                "dated"
            )
         ),
         "title"=>"TMS",
         "color"=>"rgb(239 , 53 , 53)",
    ),
    //Website
    "yoursite.org"=>array(
        "url"=>"",
        "result"=>array(
            "title"=>"title",
            "alt_title"=>"title",
            "link"=>"",
            "download"=>false,
            "image"=>"image",
            "view"=>array(
                "text"
            )
        ),
        "title"=>"Website",
        "color"=>"rgb(155 , 206 , 124)",
    ),
    //MediaBin
    "mediabin"=>array(
      "result"=>array(
        "title"=>"title",
        "alt_title"=>"filename",
        "link"=>"",
        "download"=>"",
        "image"=>array(
          "type"=>array(
            "object_images"=>"",
            "non_object_images"=>""
           )
        ),
        "alt_img"=>"filename",
        "view"=>array(
            "period",
            "classification",
            "medium",
            "department",
            //non-object
            "description",
        )
     ),
     "title"=>"MediaBin",
     "color"=>"rgb(128 , 86 , 150)",
    ),
);

//------------------------+
//    Full View Layout    |
//------------------------+
$array_fields = array("Resource Category","Asset Flag","Shot Angle", "Shot Movement", "Shot Size");
$layout = array(
    "main"=>array(
        "resourcespace"=>array("File Name","resource_type",/*"title",*/"Caption","Description","Resource Category","Source Dept","title","Asset Flag","Extracted Text"
        ),
        "mediabin"=>array("filename","description","department", /*MidiaBin non-object*/ "filename", "Asset Type", "Department", "Classification"
        ),
        "yoursite.org"=>array("title","description","canonicalLink","text"
        ),
        "objects"=>array("accession_number",/*"title",*/"dated","life_date","culture","role","medium","dimension","country","continent","style","marks","provenance",
          "creditline","room","department","text","description","tags","id","nationality"
        ),
    ),
    "media"=>array(
        "resourcespace"=>array("City depicted","Concept depicted","Country depicted","Media Cataloguer","Media Constraints","Media Creator","Media Creator City",
        "Media Creator Country","Media Creator Email","Media Creator Phone","Media Creator State","Media Creator Street","Media Creator Title","Media Creator URL",
        "Media Creator Zipcode","Media Original Source","Media Source","Person Shown","State depicted","Shot Location"
        ),
        "mediabin"=>array(/*"id",*/"classification","width","height","medium","portfolio_series",
          /*MediaBin -non object*/
          "Exif Artist","Exif Image Description","Creator","Image Source","Image Type","Primary Image","Producer","Project"
        ),
        "yoursite.org"=>array(/*"text",*/"videos"),
    ),
    "object"=>array(
        "resourcespace"=>array("Art Object Creator","Art Object Creditline","Art Object Culture","Art Object Date","Art Object Dimensions","Art Object Medium","Art Object Name",
          "Art Object Number","ObjectID","Objects depicted",
        ),
        "mediabin"=>array("object_title","accession_number","constituent_1","object_alternate_titles","place_made","object_packages",
          "object_alternate_titles","place_made","object_packages","period",
          //MediaBin non-object
          "Exhibition Creditline","Exhibition Description","Exhibition Title","Exhibition Venue Dates","Exhibiton Curator",
        ),
//        "objects"=>array("accession_number","culture","dimension","life_date","marks","room","nationality","provenance","role","style"),
        ),

    "meta"=>array(
        "resourcespace"=>array(
        //ResourceSpace
        "Associated Event",
        "ConstituentID",
        "Creator Tool",
        "DAM Asset GUID",
        "Document ID",
        "EventID",
        "Exhib Credit Line",
        "Exhibition Dates",
        "Exhibition Title",
        "ExhibitionID",
        "File Created",
        "Keywords",
        "Location Created",
        "Location depicted",
        "Original Document ID",
        "Original File Name",
        "Project Job Reference",
        "Special Instructions",
        "Subject Creation date",
        "Supplier Media ID",
        "Tag Me",
        "file_extension",
        ),
        "mediabin"=>array(
        //MediaBin non-object
        "Media Type",
        "Keywords",
        "user Tags",
        "Cataloguer",
        "ICC Profile Identifier",
        "Image Relation",
        "Page Count",
        "Photoshop Author",
        "Photoshop Author's Position",
        "Photoshop Caption",
        "Photoshop Caption Writer",
        "Photoshop City",
        "Photoshop Copyright Notice",
        "Photoshop Copyright Status",
        "Photoshop Country",
        "Photoshop Credit",
        "Photoshop Headline",
        "Photoshop Instructions",
        "Photoshop Owner's URL",
        "Photoshop Source",
        "Photoshop State / Province",
        "Photoshop Title",
        ),
        "yoursite.org"=>array(
        //Website
        "tags",
        "url",
        "favicon",
        "lang",
        ),
        //TMS
//      "id"
    ),
    "rights"=>array(
        //Resourcespace
        "resourcespace"=>array(
        "Creative Commons License",
        "License End Date",
        "License Start date",
        "Licensor Email",
        "Licensor Name",
        "Licensor Notes",
        "Licensor URL",
        "Rights Creditline",
        "Rights Owner",
        "Rights Statement",
        "Rights Usage Terms",
        "Rights Web Statement",
        "archive"
        ),
        "mediabin"=>array(
        //MediaBin
        "creditline",
        "object_rights_type",
        "period",
        //MediaBin non-object
        "Exif Copyright",
        "Image Rights Restrictions",
        "Image Rights Statement",
        "Image Rights Type",
        ),
        "objects"=>array(
        //TMS
        "image_copyright",
        "image_rights_type"
        )
    )
);
$assistant_filters=array(
  "Title"=>array(
     "mediabin"=>array("Photoshop Headline","object_title","Object_alternate_titles","Portfolio_Series"),
     "resourcespace"=>array("Caption"),
     "object_data"=>"title",
     "yoursite.org"=>"title"
  ),
  "Description"=>array(
    "mediabin"=>array("Exif Image Description","Description"),
    "resourcespace"=>array("Description"),
    "object_data"=>array("Text Entry"),
    "yoursite.org"=>array("text"),
  ),
  "Object ID"=>array(
    "mediabin"=>"ObjectID",
    "resourcespace"=>"ObjectID",
    "object_data"=>"id",
  ),
  "Object Number"=>array(
    "mediabin"=>array("accession_number"),
    "resourcespace"=>array("Art Object Number"),
    "object_data"=>array("accession_number")
  ),
  "File Name"=>array(
    "mediabin"=>array("name"),
    "resourcespace"=>array("filename"),
    "yoursite.org"=>array("URL"),
  ),
  "Asset Creator"=>array(
    "mediabin"=>array("Creator"),
    "resourcespace"=>array("Media Creator"),
  ),
  "Artist"=>array(
    "mediabin"=>"constituent_1",
    "resourcespace"=>"Art Object Creator",
    "object_data"=>"artist"
  ),
  "Medium"=>array(
    "mediabin"=>array("medium"),
    "object_data"=>array("medium")
  ),
/*  "Format"=>array(
  ),*/
  "Creditline"=>array(
    "mediabin"=>array("EventID"),
    "resourcespace"=>("eventid")
  ),
  "Keywords"=>array(
   "mediabin"=>array("keywords"),
   "resourcespace"=>array("keywords","tag me"),
   "object_data"=>array("tags"),
  ),
  "Event Name"=>array(
   "mediabin"=>array("Event Name"),
   "resourcespace"=>array("eventname"),
  ),
  "Exhibition Name"=>array(
    "mediabin"=>array("Exhibition Title"),
    "resourcespace"=>array("exhibitiontitle"),
  ),
  "Person(s) Shown"=>array(
    "mediabin"=>array("Person Shown"),
    "resourcespace"=>array("Person Shown")
  )
);
?>
