<?php
/**
 * @package Koolkit
 * Koolkit Fun-ctions! Kool and Fun! This file contains all the major func-
 * tionality of Koolkit. 
 */

global $post;
//wp_enqueue_script("jquery");
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

/**
 * kool_preview_boxes shows the next set of posts. It shows as many boxes as
 * you show posts on your blog page. This works best when there is an even
 * number of posts displayed (2,4,6,8,10) but it can work with an odd number
 * if alignment is set or if the width of boxes is set to 100% which would allow
 * for a column effct.
 * 
 * To use kool_preview simply add the function call in your theme's index after
 * the content is displayed. I could technically go before as well.      
 */  
function kool_preview_boxes ( ) {
  global $post;
  if ( is_paged() ) {
    $target_page = get_query_var('paged')+1;
  } else {
    $target_page = 2;
  }
  $args=array(
    'paged'=>$target_page,
    'orderby'=>'post_date',
    'post_type'=>'post',
    'post_status'=>'publish'
  );
  $k_posts = new WP_Query($args);
  if ( $k_posts and $k_posts->have_posts() ) {   
    while ($k_posts->have_posts()) {
      $k_posts->the_post();
      $thumb_query=array(
        'numberposts'=>1,
        'post_type'=>'attachment',
        'post_parent'=>$post->ID );
      echo ('<div class="preview_box" >');
      $attachment = get_posts($thumb_query);
      foreach ($attachment as $attach) {
        if ( $attach ) {
          $img = wp_get_attachment_image_src( $attach->ID, $size='thumbnail', $icon=true );
          echo ( "<img class='preview_thumb' src='$img[0]' >" );
        }  
      }
      echo ('<a rel="bookmark" href=' );
      the_permalink();
      echo ( '>' );
      echo (the_title() .'</a><br>');
      the_excerpt();
      echo "</div>";
    }
    
  }                                      
}

/**
 * kool_YQL
 * given an arbitrary YQL string and optional format returns
 * either a json object or a DOMObject (default)
 * 
 * There purpose of this function is to facilitate calls to YQL. In the
 * kool_weather example a function was created to represent the query and
 * another function was created to represent the output. Keeping all three
 * modular should help provide a facility for reuse where a different
 * configuration would help produce the same data in another format or
 * presentation. This is useful if you are producing code for both desktop and
 * mobile presentation.
 * 
 * You could also create one view for presentation on screen and another view
 * that produces a stream into a data file.          
 */
function kool_YQL ( $YQL_query, $format='X' ) {
  $YQL_base_query = 'http://query.yahooapis.com/v1/public/yql';
  // verify query
  if ( $YQL_query != '') {
    if ('J'==strtoupper($format)) {
      $YQL_query_url = $YQL_base_query . '?q=' . urlencode($YQL_query) . '&format=json';
      $session = curl_init($YQL_query_url);  
      curl_setopt($session, CURLOPT_RETURNTRANSFER,true);      
      $json = curl_exec($session);
      return $json;
    } else {
      $YQL_query_url = $YQL_base_query . '?q=' . $YQL_query;
      $doc = new DOMDocument();
      $doc->load($YQL_query_url);
      return $doc;  
    } 
  } else {
    return null;
  }
  return null;
}

/**
 * kool_weather_YQL ( Where )
 * given a location, function returns a YQL query
 * Use with kool_YQL and either a json parser or an XSLT transform.
 *    
 */
function kool_weather_YQL ( $where ) {
 return "USE 'http://www.datatables.org/weather/weather.woeid.xml'; select * from weather.woeid where w in (select woeid from geo.places where text = \"$where\") and u=\"f\"";    
}

/**
 * kool_weather_JSON2HTML ( jsonOBJ )
 * takes the result from JSON method of YQL using the kool_weather query
 * and returns a presentation block of html for regular web browsers.
 * call looks like this:
 * kool_weather_JSON2HTML (kool_YQL( kool_weather_YQL ('Nashville, TN'), 'J'));
 * See the shortcode version. 
 */
       
function kool_weather_JSON2HTML ( $jsonOBJ ) {
  $phpObj = json_decode($jsonOBJ);
  if (!is_null ($phpObj->query->results) ) {
    foreach($phpObj->query->results as $rss) {
      $htmlResult = '<div class="koolWeatherBlock">';
      $htmlResult .= "<a href=\"" .$rss->channel->item->link ."\">" .$rss->channel->item->title ."</a>";
      $htmlResult .= "<p class=\"koolAstronomy\">sunrise: " .$rss->channel->astronomy->sunrise ." sunset: " .$rss->channel->astronomy->sunset ."</p>";
      $htmlResult .= "<div class=\"koolTemperature\">Temp<br><span class=\"koolTempNum\">" .$rss->channel->item->condition->temp .$rss->channel->units->temperature ."</span></div>";
      $htmlResult .= "<p class=\"koolWeatherDesc\">" .$rss->channel->item->description ."</p>";
      $htmlResult .= "</div>&nbsp;";            
    }
    if (empty($htmlResult)) {
      $htmlResult = 'Sorry, weather for that location could not be found. Try debugging at the <a href=\"http://query.yahooapis.com/v1/public/yql?q=desc%20weather.bylocation&format=json&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=cbfunc\">YQL Console</a> with the given query and check your location spelling.';      
    }
  }
  return $htmlResult;
}

function kool_weather_shortcode ( $atts ) {
  extract(shortcode_atts( array(
    'location' => '',
    ), $atts ) );
    return kool_weather_JSON2HTML (kool_YQL( kool_weather_YQL ($location), 'J'));
}
add_shortcode('KoolWeather', 'kool_weather_shortcode');

?>
