<?php
/*
Plugin Name: KoolKit
Plugin URI: http://nectarineimpllc.com/KoolKit
Description: Fun functions to update your theme or site
Version: 0.1
Author: Peter Mancini
Author URI: http://nectarineimpllc.com/?page_id=8
License: Apache License, Version 2.0
*/

/**
 * Copyright 2011 Nectarine Imp

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/


/**
 * @package Koolkit
 * 
 * Koolkit gives you functions that can spice up your theme or site!
 * 
 * Using Koolkit is easy. Simply drop the functions into your theme where
 * appropriate!
 * 
 * Kool Preview Boxes
 * Shows a preview with thumbnail of your next page of blog entries. Helps
 * improve time on your site! To use add the following line to your loop.php
 * file!
 * 
 * <?php kool_preview_boxes ( ); ? >          
 */
$myStyleUrl = WP_PLUGIN_URL . '/koolkit/css/koolkit.css';
$myStyleFile = WP_PLUGIN_DIR . '/koolkit/css/koolkit.css';
if ( file_exists($myStyleFile) ) {
  wp_register_style('myStyleSheets', $myStyleUrl);
  wp_enqueue_style( 'myStyleSheets');
} else {
  /* error handling would go here */
}
include 'koolFunctions.php'; 

/* Plugin filter and action functions go here */


?>