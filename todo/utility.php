<?php

  /**
   * Simple function that translates a timestamp from the past into a friendlier
   * number of somethings ago (ex: 15 minutes ago, 1 hour ago)
   * @param int timestamp unix timestamp to convert
   * @return string nicely formatted label	 	 
   */
  function time_ago($timestamp) {
    $spans = array ( "year"   => 31536000,
                     "month"  =>  2592000,
                     "week"   =>   604000,
                     "day"    =>    86400,
                     "hour"   =>     3600,
                     "minute" =>       60,
                     "second" =>        1 );
    
    $diff = mktime() - $timestamp - 24;
    $label = "second";
    $span = 1;
    foreach($spans as $key => $value) {
      if($diff > $value) {
        $label = $key;
        $span = $value;
        break;
      }
    }
    
    $amt = floor($diff / $span);	
    
    return $amt . " " . $label . ($amt != 1 ? "s" : "") . " ago";
  }
  
  /**
   * Helpful templating function to make the pages cleaner
   * Creates and displays a generic todo form
   * @param string $action Page to submit to	 
   * @param string $label Label for submit button and general action
   * @param string $value [optional] Value to populate the textbox with, defaults to empty
   * @param string $id [optional] Todo id to be embedded in the form as a hidden field
   * @param bool $edit [optional] should the text box be editable, defaults to true
   */
  function display_form($action, $label, $value = "", $id = null, $edit = true) {
    $result = "<h3>$label todo</h3>\n";
    
    $result .= "<form class=\"todo\" action=\"$action\" method=\"post\">\n";
      $result .= "\t<div class=\"item\"\n";
        if($id) {
          $result .= "\t\t<input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
        }
        if($edit) {
          $result .= "\t\t<input type=\"text\" name=\"title\" value=\"$value\" />\n";
        } else {
          $result .= "\t\t$value\n";
        }
      $result .= "\t</div>\n";
      $result .= "\t<div class=\"controls\">\n";
        $result .= "\t\t<input type=\"submit\" value=\"$label\" />\n";
        $result .= "\t\t<button onclick=\"window.location.href='index.php'; return false;\">cancel</button>\n";
      $result .= "\t</div>\n";
    $result .= "</form>\n";
    
    echo $result;
  }
  
  /**
   * Helper function to dump data out pretty printed and syntax highlighted
   * @param mixed $target The target to print
   * @param string $language [optional] Syntax brush to paint with, defaults to php
   */           
  function syntax_print($target, $language = "php") {
    echo "<pre class=\"brush: $language\">";
      print_r($target);
    echo "</pre>";
  }
  
?>