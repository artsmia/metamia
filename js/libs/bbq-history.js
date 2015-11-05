$(function(){
  
  // Keep a mapping of url-to-container for caching purposes.
  var cache = {
    // If url is '' (no fragment), display this div's content.
    '': $('#results')
  };
  
  // Bind an event to window.onhashchange that, when the history state changes,
  // gets the url from the hash and displays either our cached content or fetches
  // new content to be displayed.
  $(window).bind( 'hashchange', function(e) {
    
    // Get the hash (fragment) as a string, with any leading # removed. Note that
    // in jQuery 1.4, you should use e.fragment instead of $.param.fragment().
    var url = $.param.fragment();
    
    // Remove .bbq-current class from any previously "current" link(s).
    $( 'a.fv-current' ).removeClass( 'fv-current' );
    
    // Hide any visible ajax content.
      $( '.more-data' ).fadeOut(2000);
      $('.result').show();
      var $container = $('#results');
      $container.masonry();
    // Add .bbq-current class to "current" nav link(s), only if url isn't empty.
    url && $( 'a[href="#' + url + '"]' ).addClass( 'fv-current' );
    
    if ( cache[ url ] ) {
      // Since the element is already in the cache, it doesn't need to be
      // created, so instead of creating it again, let's just show it!
      //jQuery("#"+cache[ url ]).show();
      jQuery("#"+url).fadeIn("fast");
    } else {
      // Show "loading" content while AJAX content loads.
      
      // Create container for this url's content and store a reference to it in
      // the cache.
      cache[ url ] = $( '<div class="more-data-sv"/>' )
        // Append the content container to the parent container.
        .appendTo( '#results' )
        
        // Load external content via AJAX. Note that in order to keep this
        // example streamlined, only the content in .infobox is shown. You'll
        // want to change this based on your needs.
        .load( url, function(){
          // Content loaded, hide "loading" content.
          $( '#loading-box' ).fadeOut();
          jQuery("#"+url).fadeIn("slow");
        });
    }
  })
  
  // Since the event is only triggered when the hash changes, we need to trigger
  // the event now, to handle the hash the page may have loaded with.
  $(window).trigger( 'hashchange' );
  
});
