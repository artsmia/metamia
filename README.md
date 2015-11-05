[Configuring Indices](#Conf-Indc)<br/>
[The Main Query](#Main-Query)<br/>
[FIlters](#Filters)<br/>
[Query Assistant Filters](#QA)<br/>
[Defining Query Assistant Filters](#DQA)<br/>

<h1>Config.php </h1>
<h2>Indices</h2>
To add, update or remove an index that is to be queried from ElasticSearch lets first look at the core components in the config file. 

<h3>Defining the Index and Types</h3>
In the Global Site Vars section there are two variables to pay attention to: $allowed_indexes and $allowed_types.  This is what tells the system what and where to query from.  In order to add an index we must first add the top level index name that we want to query in $allowed_indexes. Then for any or all of the types within that index that we want access to we need to set the type in $allowed_types.  *note that setting the index in $allowed_indexes will return 0 results for the index, the types MUST be set. 
<a name="Conf-Indc"></a>
<h3>Configuring the Index</h3>
Once we have defined the Index we now need to configure how it will interact with the system.  The entire config section is defined as a variable $elastic_config that expects a multi-dimensional array as it’s value.  The top level keys are the name of the indices themselves.  These values contain a 3 part array: result, title and color.  Title and color take string values for the title that you want to Associate this index as (ie. If the index resourcespace_rev_1087635 existed in elastic search we could rename it here to display as ResourceSpace sitewide) and a CSS acceptable color format for the color field. The results portion of each array consists of title, link, download, image, and view and is outlined as follows:

```php
    "result" => array(
        "title"=>”string”,
        "link"=> array(),
        "download"=>bool(false), OR array(),
        "image"=>”string” OR array(),
        "image_valid"=>array("key","value"),
        "view"=>array(), OR array(["type"]=>array),
)
```
Looking at the key value pairs that exist within the result array you may notice that each key may take several different types of values.  If the key takes several values it is because there is different logic to handle the values. Let’s take a look one by one at what each key means, what it relates to and how the different values affect how the result will display.

<h4>Title:</h4>
This key only accepts a string as its value. This title relates to the result that is queried not the index. Its value is the field in elasticsearch from which search results should pull a title from.

<h4>Link:</h4>
This key accepts only an array (However an empty string will disable the “view in source” functionality for this index). The link that this refers to is the link to which the asset lives in outside of ElasticSearch in the source systems. The array itself consists of 3 parts: 1. the url path 2. The result field from which a value is needed to complete the url and 3. the remaining path to the link url or an empty string if no further string is needed.
```php
    "link" => array(
        “www.someurl.com?asset=”,
        “id”,
        “otherparamsvalue”
    )
```
Example Output
> www.someurl.com?asset=1

<h4>Download:</h4>
Download is very similar in structure as it only accepts an array or false. False will render this action disabled for this index within the system. The download array is very similar to the link array except it takes a 4th Boolean parameter of true. If the 4th parameter of download is set to true then this means that the url portions are to the actual image of the asset and MetaMia are responsible for the downloading action of this asset. If not set or set to false then the system treats the download as a query method to trigger and the link to an external download mechanism for this asset. Also the second parameter of the array can also be an array if multiple values are needed from the result. 
Example: 2nd parameter as array:
```php
    array(
        "external.com/download.php?authkey=123”,
        array(
            “key1”=>"a",
            “key2”=>"b"
        )," "
    )
```
a and b are fields returned from elastic search from which values you need to build the url. Meaning that in the case of the above example if key1 and key2 where field keys in the result with values value1 and value2 the output would be:

Ex Output:
>"external.com/download.php?authkey=123?key1=value1&key2=value2"

And an example if we wanted to handle the download internally:

Config.php
```php
    array(
        “api.artsmia.org/images/”, 
        “id” , 
        “800/large.jpg”,
        true
    )
```
Here id is the key that is needed from the result and lets say id = 1234:

Ex Output: 
>“http://api.artsmia.org/1234/800/large.jpg"

<h4>View:</h4>
The “view” key holds a sequential array of field keys for the image view of the results page. It tells the system what fields from the result to use to display a thumbnail for reach results.  
 
EX:
```php
    array(
        “title”,
        ”description”
    )  
```
This would pull the title and description fields from the result and display them in the thumbanail and thumbnail list views.

Image
The image key holds either an array or string value. Strings are analyzed as result field from which to return the value from. If the index in question were to have a thumbnail field, a simple string could be used to return its value. If the thumbnail for the result can be generated via another value in the result (such as an id) than the array option can be utilized to compile the thumbnail path.  The structure of this array is array(“url”,”field”,”appendage”)
Ex: 
```php 
    array(
        “somesite.com”,
        ”id”,
        ”/300/small.jpg”
    )
```
Here the second parameter identifies the id field as the field from which to return a value from the result. Lets say in this instance the id field for the result contained the value 123. The returned output would be:

>Output: somesite.com/123/300/small.jpg
   
<h4>Image valid</h4>
Image valid is the only optional key for each index in $elastic_config.  It directly relates to the image key. If this image valid key does not exist than the image key is considered valid as long as the key and value exist in the result. If further analysis such as permissions need to be analyzed, the presence of the image valid key tells the system a specific key value pair that the result must have in order to display the image. For example if the index required that the image have a field(key) named "access" with a value of "public" this would be defined as:
Ex 
```php
    "image_valid"=>array(
         “access”,
         ”public”
     )
```
Queries

<h3>Indices and Types</h3>
With an empty search string the queries default to a match all query. This is done so that filters can be applied without having to submit a search string at all. 

The system to search through is defined in the URL that sends the request to ElasticSearch and this defaults to the $allowed_indices variable that is set up in the config file. 

>Ex. http://elasticsearchserver/$allowed_indices/
 
When indices are removed or added from a search they are simply updated in the url that sends the request.  This is also the same for the index types which in the config is defined as $allowed_types. The main structure of a query is:

>Ex: http://elasticsearchserver/$allowed_indices/$allowed_types/ 

This was done so that results are not returned from types or indices that are not defined.  Meaning that test indices can live in the same system without having to worry about their data showing up on the production site. 
<a name="Main-Query"></a>
<h2>Main Query</h2>
When a query is submitted the following occurs:

The main query structures is a Boolean Query consisting of three key parts:
```php
    Bool{
        Must_match
        Should match
        Must NOT match
    }
```
Each part can hold as many queries that are needed to execute the search by the user.  For example a search looking for “this thing” and “and those” but not “that” and not “all of these” and also “not” “sure?” would look like:

>“this thing” !that “!all of these” and those not sure?

Which would be analyzed as:
*note the below is not proper syntax an is for demonstrating the concept. Please refer to ElasticSearch for proper syntax. Or look at the full query on the last page to get a better idea of what a true query will look like.
```php
    Bool=>{
        MUST{
            match_phrase => all => "this thing",
            match => all => “those”,
            match => all => "not”,
            Wildcard => all => “sure?”
        },
        SHOULD{
            match => all => “and”
        },
        MUST NOT{
            match => all => “that",
       match_phrase => all => “all of these”
        }
    }
```
The first part in each clause tells the type of analysis to be done. In this case the query is analyzed as two phrases,four terms, and a wildcard. The “all” tells the system to search “all fields” and the third part is the terms to perform the analysis on. 

<h3>Analysis</h3>
When a search string first hits queries.php it is first analyzed for phrases that are contained within double quotes. These phrases are added to the must match clause unless and exclamation point characacter is detected right after the first double quote. If the exclamation is detected, the term is added to the must not clause of the query.  If the remaining search string is not empty further analysis is done by breaking the string into an array of terms by white space.  Each term is then analyzed for wildcard and "must not" special characters and if detected these terms are added to either the must or must not clauses. The rest of the terms are further analyzed to detect if they are stop words and if so are placed in the should clause.  Any other terms are added to the must clause of the query and analyzed as basic terms. 

<h3>Stop Words</h3>
Why is “and” a term in the “should match” section you ask.  This is because there is also an array defined in queries.php called “stop words” which are small common words such as “and”, “the”, “or”, “a”, “etc”.   If a stop word is detected in the search string it is treated as a should match (or) analyzer meaning that we would like for it to exist in the result but it is not as important as other words in the string and we may not want to exclude a result based on a stop word.  In order to make the “and” just as important to the other terms the user could wrap the phrase in double quotes which would be analyzed(in the previous case) as: 

>Must match: 
>Phrase: “and those”. 

<h3>Wildcards</h3>
Wildcards are also analyzed if * or ? is detected in the search string and added as must match. In order to exclude ? or * as being analyzed as a wildcard the same procedure of wrapping the term(s) in double quotes would disable the default wildcard behavior and instead analyze the symbol(s) as a character in the term.

<a name="Filters"></a>
<h2>Filters</h2>
Filters are defined much in the same as the Boolean query; consisting of the same key three sections. However with filters there are new filter types introduced of “range”, “type”, ”or”, ”and”, ”exists” and ”indices”.

<em>*note: These are the only type of filters that seemed suited for the queries needed at the time but logic can be implemented to compile any filter that elastic search allows within one of the three Boolean clauses. </em>

Again the structure looks much the same as before:
```php
Bool{
  Must_match
  Should match
  Must NOT match
}
```
But now we have extra types to put in each of these sections. For example lets say we wanted to filter just images. 

In the config file images are defined as coming from two of the source systems (ResourceSpace and MediaBin). In order to know it’s an image from MediaBin the config currently defines that the index type of “object_images” within elasticsearch contains all images. In order to know its an image from Resourcespace the config defines ResourceSpace images as having a field titled “resource_type” with a value of “Photo”. In this case our filter would look as follows:

```php
Bool{
  Must match
  Indices: ResourceSpace , MediaBin
  Filter : or {
   Term: resource_type: photo
   Type: value: object_images. 
} 
```
Here we have introduce the new “indices”, “or” and “type” filters. This filter states that results must come from ResourceSpace or MediaBin. The entire filter is in the must match section of the Boolean clause meaning that it must match this filter to compile results but only the term or type have to match the or clause in order to return a result . 

The previous example was using a predefined filter that was setup in the config file. Using this same filter lets now see how stacking more filters work. 

Lets say we found the videos but we also want to find documents that have the same search query as well.  So we click the documents filter checkbox and run the search. Since Documents (at the moment) are only defined in ResourceSpace as {resource_type : Document}; all that is added is another Term to the “or” clause operator in the indices filter which looks like:
```php
Bool{
    Must match
    Indices ResourceSpace , MediaBin
    Filter : or {
        Term: resource_type: photo
        Type: value: object_images. 
        Term: resource_type: Document
} 
```
Now lets say we want to see which videos and documents that have the word “elastic” in their title. So we move over to the query assistant, select title and put the word “elastic” in the field that pops up. 
<a name="QA"></a>
<h3>Query Assistant</h3>
This is a predefined filter in the config that links to multiple different fields in all of the systems. To include this filter with our pre-existing filter an “and” and “or” operator is introduced:
```php
Bool
Must match
    And{
        Indices ResourceSpace , MediaBin
            Filter : or{
                Term: resource_type: photo,
            Type: value: object_images,
         Term: resource_type: Document,
            }
   },
    And{
        Filter: or{
            "term":"PhotoshopHeadline":"elastic",
          "term":"object_title":"elastic",
          "term":"Object_alternate_titles":"elastic", etc. etc. 
      }
}
```
 
This filter match our first defined filter and we have now added a second filter it MUST also match. However it only has to match one of the terms defined in the filter to return a result. This is why we have the and+or stacking going on. 

Now lets say we want continue filtering to see only results that have the file extension “.jpg” so we click on the word file extension in the full view which adds a file extension field to our filters sidebar with the value of “.jpg” and we run the search. Our filter structure now looks like
```php

Bool
    Must match
        And{
             Indices ResourceSpace , MediaBin
             Or{
                 Term: resource_type: photo
            Type: value: object_images.   
                Term: resource_type: Document
            }
   },
        And{
            Or{
                "term":"PhotoshopHeadline":"elastic",
      "term":"object_title":"elastic",
      "term":"Object_alternate_titles":"elastic", etc. etc. 
          }
        },
        And{
       “term”:”file extension” : “.jpg”
        }
```

But lets say we meant that we wanted to see which results do NOT have the file extension of jpg. So we change the operator in the sidebar next to the field from “=” to “!” and execute the query. Our new search looks like:
```php
Bool
    Must match
        And{
            Indices resourcespace , mediabin
            Filter : or
            Term: resource_type: photo
            Type: value: object_images. 
            Term: resource_type: Document
        }
        And{
            Or{    
                "term":"PhotoshopHeadline":"elastic",
                "term":"object_title":"elastic",
                "term":"Object_alternate_titles":"elastic", etc. etc. 
            }
        }
        Must Not Match
            “term”:”file extension” : “.jpg”
```
Since it’s a single term and field that is not supposed to match it is added to the “must not match” section of the Boolean clause. 

All of the other filter types work in the same form and fashion. If multiple filters are applied to the same Boolean section (Must, Should and Must Not) than an “and” operator is added to the array for each filter. 
```php
And filter{type}
and filter{type}
and filter{type}
```
If each filter is predefined to have several possible areas that should return a result (such as the Query Assistant) than an “or” filter encapsulates each filter type.  
```php
And filter{or type, or type, or type}
and filter{or type, or type, or type}
and filter{or type, or type, or type}
```
Depending on how the query is structured, each filter type can be added to any section of the Boolean clause.
```php
Must match
And filter{or type, or type, or type}
and filter{or type, or type, or type}
Should match
And filter{or type, or type, or type}
and filter{or type, or type, or type}
Must Not Match
And filter{or type, or type, or type}
and filter{or type, or type, or type}
```
The operators that encapsulate the filter types determine how many types must match and how they the terms must be analyzed.  If the filter is a phrase the string is broken up by the white spaces and a terms filter is applied instead of a term filter which looks like. 

Terms: field:[term1,term2,term3,etc], Operator[‘and’]

This means that for a given field all of the terms MUST match.

<a name="DQA"></a>
<h3>Defining Query Assistant</h3>
Located in the config file is a variable defined as $assistant_filters.  This is what defines the “Query assistant” mappings located in the sidebar. It is an associative array where the primary keys define the title of the filter. The value is an array that defines the system(s) that the query will target and the field from where to check. For example:
```php
$assistant_filters = array(
    “filter 1”=> array(
        “System1”=> array(“field”,”field”,field)
        “System2”=>array(“field”,”field”)
    ),
    “filter 2”=>array(  
        “System1”=>array(“field”,field)
        “System 3”=>array(“field”)
    )
)
```
<h3>Sessions</h3>
Sessions are stored in the MySQL database in the sessions table and handled by the Session class located in include/sessions.php and the LDAP control. Located in ctrl/ldap.php. A cron job that is set in the cron.d directory is scheduled to execute killsessions.php every hour to remove any sessions in the database that exceed 12hrs. The structure to the database is:

```
+----------------+--------------+------+-----+---------+-------+
| Field          | Type         | Null | Key | Default | Extra |
+----------------+--------------+------+-----+---------+-------+
| session_id     | varchar(32)  | NO   | PRI | NULL    |       |
| session_status | int(10)      | NO   |     | 0       |       |
| session_data   | varchar(600) | NO   |     | NULL    |       |
| session_access | int(10)      | NO   |     | NULL    |       |
+----------------+--------------+------+-----+---------+-------+
```
Session_status is a unix timestamp that updates as the user is active within the site. Session_data is where all data is stored for the user.  This data is available and can be set using the global $_SESSIONS variable. For example lets say we wanted to set the username for the session to "sombody". This can be achieve by $_SESSIONS['username']="somebody".

<h3>Results</h3>
Results returned through ElasticSearch are first handled through the results view located in views/results.php. This view iterates through each result handing them off to the Results class located in ctrl/results.php. This Class first configures each result based on the system index settings in config.php. The class returns each configuration to the view which then can be used to call the different methods of the class for view options. 

<h3>Saving Searches</h3>
The search system uses a Redis database. All configurations for the database are configured in config.php. phpRedis is used handle all database transactions which is included in the redis model located in /model/redis.php. Hashes are utilized to store data for each user in the system in four separate tables: User, User:carts_#, User:saved_carts and User:saved_search. The username in all instances is stored as an md5 hash where in the user table the raw username is stored.  In the user table the cart_count is kept in order to know the id of the next cart to add when creating a new cart. Each cart is saved as username:cart_(cart#) and stores a key value pair for each result in the cart. The key that is stored is the id of the item that was added to the cart and the value is the title.  Saved_carts keeps track of the cart id and the title for the cart.  cart_0 is the default temp cart and is what is used to store user data before it is perminantly saved. The temp cart is set with a ttl of 4 weeks.  

```

1) "1610838743cc90e3e4fdda748282d9b8"
#Example of user data
    1) "username"
    2) "admin"
    3) "cart_count"
    4) "3"
2) "1610838743cc90e3e4fdda748282d9b8:saved_search"
#Example of saved_search data
    1) "save query"
    2) "?view=list&sb=f&index=_all&search=&current_cart=0&cp=0&type%5B%5D=Image"
    
3) "1610838743cc90e3e4fdda748282d9b8:saved_carts"
#Example of saved_carts data
    1) "0"
    2) "Temp."
    3) "1"
    4) "savedcart title"
    5) "2"
    6) "2nd savedcart title"
    
4) "1610838743cc90e3e4fdda748282d9b8:cart_0"
5) "1610838743cc90e3e4fdda748282d9b8:cart_1"
6) "1610838743cc90e3e4fdda748282d9b8:cart_2"
#Example of cart data
    1) id1
    2) "Holidays"
    3) "id2"
    4) "Allegorical Still Life with Bernini"
    5) "id3"
    6) "The Camel"
```

The structure of the data is as follows:

<h3>Example Query:</h3>
Note: this query is what is compiled as php from queries.php running the search illustrated from the examples above.  ElasticSearch takes a JSON string so theis entire query wich is defined as $queryData is ran through php's json_encode before sending off to ElasticSearch
```php
Array
(
    [highlight] => Array
        (
            [tag_schema] => styled
            [fields] => Array
                (
                    [*] => Array
                        (
                            [pre_tags] => Array
                                (
                                    [0] => <em class='highlight'>
                                )

                            [post_tags] => Array
                                (
                                    [0] => </em>
                                )

                        )

                )

        )

    [from] => 0
    [size] => 25
    [query] => Array
        (
            [filtered] => Array
                (
                    [query] => Array
                        (
                            [bool] => Array
                                (
                                    [must] => Array
                                        (
                                            [0] => Array
                                                (
                                                    [match_phrase] => Array
                                                        (
                                                            [_all] => Array
                                                                (
                                                                    [query] => this thing
                                                                )

                                                        )

                                                )

                                            [1] => Array
                                                (
                                                    [match] => Array
                                                        (
                                                            [_all] => Array
                                                                (
                                                                    [query] => those
                                                                )

                                                        )

                                                )

                                            [2] => Array
                                                (
                                                    [match] => Array
                                                        (
                                                            [_all] => Array
                                                                (
                                                                    [query] => not
                                                                )

                                                        )

                                                )

                                            [3] => Array
                                                (
                                                    [wildcard] => Array
                                                        (
                                                            [_all] => sure?
                                                        )

                                                )

                                        )

                                    [must_not] => Array
                                        (
                                            [0] => Array
                                                (
                                                    [match_phrase] => Array
                                                        (
                                                            [_all] => Array
                                                                (
                                                                    [query] => all of these
                                                                )

                                                        )

                                                )

                                            [1] => Array
                                                (
                                                    [match] => Array
                                                        (
                                                            [_all] => Array
                                                                (
                                                                    [query] => that
                                                                )

                                                        )

                                                )

                                        )

                                    [should] => Array
                                        (
                                            [0] => Array
                                                (
                                                    [bool] => Array
                                                        (
                                                            [should] => Array
                                                                (
                                                                    [0] => Array
                                                                        (
                                                                            [match] => Array
                                                                                (
                                                                                    [_all] => Array
                                                                                        (
                                                                                            [query] => and
                                                                                        )

                                                                                )

                                                                        )

                                                                )

                                                        )

                                                )

                                        )

                                )

                        )

                    [filter] => Array
                        (
                            [bool] => Array
                                (
                                    [must] => Array
                                        (
                                            [0] => Array
                                                (
                                                    [indices] => Array
                                                        (
                                                            [indices] => Array
                                                                (
                                                                    [0] => resourcespace
                                                                    [1] => mediabin
                                                                )

                                                            [filter] => Array
                                                                (
                                                                    [or] => Array
                                                                        (
                                                                            [0] => Array
                                                                                (
                                                                                    [term] => Array
                                                                                        (
                                                                                            [resource_type] => photo
                                                                                        )

                                                                                )

                                                                            [1] => Array
                                                                                (
                                                                                    [type] => Array
                                                                                        (
                                                                                            [value] => object_images
                                                                                        )

                                                                                )

                                                                        )

                                                                )

                                                            [no_match_filter] => Array
                                                                (
                                                                    [exists] => Array
                                                                        (
                                                                            [field] => needstonotmatch
                                                                        )

                                                                )

                                                        )

                                                )

                                        )

                                )

                        )

                )

        )

    [sort] => Array
        (
            [_score] => Array
                (
                    [order] => desc
                )

        )

)
```
