<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

/**
 * Functions (WPDreams)
 *
 * Generic Functions for all WPDreams producst
 *
 * @version  1.0
 * @package  WPDreams/Functions
 * @category Functions
 * @author   Ernest Marcinko
 */


if (!function_exists("wd_get_inner_substring")) {
    /**
     * Get the string from inbetween delimiters
     *
     * @param $string
     * @param $delim
     * @return string
     */
    function wd_get_inner_substring($string, $delim) {

        $string = explode($delim, $string, 3); // also, we only need 2 items at most

        return isset($string[1]) ? $string[1] : '';
    }
}

if (!function_exists("wd_closetags")) {
    /**
     * Close unclosed HTML tags
     *
     * @param $html
     * @return string
     */
    function wd_closetags ( $html ) {
        $unpaired = array('hr', 'br', 'img');

        // put all opened tags into an array
        preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
        $openedtags = $result[1];
        // remove unpaired tags
        if (is_array($openedtags) && count($openedtags)>0) {
            foreach ($openedtags as $k=>$tag) {
                if (in_array($tag, $unpaired))
                    unset($openedtags[$k]);
            }
        } else {
	        // Replace a possible un-closed tag from the end, 30 characters backwards check
	        $html = preg_replace('/(.*)(\<[a-zA-Z].{0,30})$/', '$1', $html);
            return $html;
        }
        // put all closed tags into an array
        preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
        $closedtags = $result[1];
        $len_opened = count ( $openedtags );
        // all tags are closed
        if( count ( $closedtags ) == $len_opened ) {
	        // Replace a possible un-closed tag from the end, 30 characters backwards check
	        $html = preg_replace('/(.*)(\<[a-zA-Z].{0,30})$/', '$1', $html);
            return $html;
        }
        $openedtags = array_reverse ( $openedtags );
        // close tags
        for( $i = 0; $i < $len_opened; $i++ ) {
            if ( !in_array ( $openedtags[$i], $closedtags ) ) {
                $html .= "</" . $openedtags[$i] . ">";
            } else {
                unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
            }
        }
	    // Replace a possible un-closed tag from the end, 30 characters backwards check
	    $html = preg_replace('/(.*)(\<[a-zA-Z].{0,30})$/', '$1', $html);
        return $html;
    }
}

if (!function_exists("wd_mysql_escape_mimic")) {
	/**
	 * Mimics the old mysql_escape function
	 *
	 * @internal
	 * @param $inp
	 * @return mixed
	 */
	function wd_mysql_escape_mimic($inp) {
		if(is_array($inp))
			return array_map(__METHOD__, $inp);

		if(!empty($inp) && is_string($inp)) {
			return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
		}

		return $inp;
	}
}

if (!function_exists("wd_substr_at_word")) {
    /**
     * Substring cut off at word endings
     *
     * @param $text
     * @param $length
     * @return string
     */
    function wd_substr_at_word($text, $length) {
        if (strlen($text) <= $length) return $text;
        $blogCharset = get_bloginfo('charset');
        $charset = $blogCharset !== '' ? $blogCharset : 'UTF-8';
        if (function_exists('mb_substr')) {
              $s = mb_substr($text, 0, $length, $charset);
              return mb_substr($s, 0, strrpos($s, ' '), $charset);
        } else {
              $s = substr($text, 0, $length);
              return substr($s, 0, strrpos($s, ' '));
        }
  }
}

if (!function_exists("wd_in_array_r")) {
    /**
     * Recursive in_array
     *
     * @param $needle
     * @param $haystack
     * @param bool $strict
     * @return bool
     */
    function wd_in_array_r($needle, $haystack, $strict = true) {
      foreach ($haystack as $item) {
          if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && wd_in_array_r($needle, $item, $strict))) {
              return true;
          }
      }
  
      return false;
  }
}

if (!function_exists("wpdreams_ismobile")) {
    /**
     * Checks if user agent is mobile device
     *
     * @return int|string
     */
    function wpdreams_ismobile() {
        $is_mobile = '0';
        if(preg_match('/(android|iphone|ipad|up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
            $is_mobile=1;
        if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']))))
            $is_mobile=1;
        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
        $mobile_agents = array('w3c ','acs-','alav','alca','amoi','andr','audi','avan','benq','bird','blac','blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno','ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-','maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-','newt','noki','oper','palm','pana','pant','phil','play','port','prox','qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar','sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-','tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp','wapr','webc','winw','winw','xda','xda-');

        if(in_array($mobile_ua,$mobile_agents))
            $is_mobile=1;

        if (isset($_SERVER['ALL_HTTP'])) {
            if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0)
                $is_mobile=1;
        }
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0)
            $is_mobile=0;
        return $is_mobile;
    }
}
if (!function_exists("wd_current_page_url")) {
    /**
     * Returns the current page url
     *
     * @return string
     */
    function wd_current_page_url() {
        $pageURL = 'http';
        if( isset($_SERVER["HTTPS"]) ) {
            if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
} 
if (!function_exists("wpdreams_hex2rgb")) {
    /**
     * HEX to RGB string conversion
     *
     * Works both 3-6 lengths, with or without hash tags
     *
     * @param $color
     * @return bool|string
     */
    function wpdreams_hex2rgb($color)
  {
      if (strlen($color)>7) return $color;
      if (strlen($color)<3) return "0, 0, 0";
      if ($color[0] == '#')
          $color = substr($color, 1);
      if (strlen($color) == 6)
          list($r, $g, $b) = array($color[0].$color[1],
                                   $color[2].$color[3],
                                   $color[4].$color[5]);
      elseif (strlen($color) == 3)
          list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
      else
          return false;
      $r = hexdec($r); $g = hexdec($g); $b = hexdec($b); 
      return $r.", ".$g.", ".$b;
  }  
}

if (!function_exists("wpdreams_rgb2hex")) {
    /**
     * RGB to HEX string converter
     *
     * @param $color
     * @return string
     */
    function wpdreams_rgb2hex($color)
    {
        if (strlen($color)>7) {
          preg_match("/.*?\((\d+), (\d+), (\d+).*?/", $color, $c);
          if (is_array($c) && count($c)>3) {
             $color = "#".sprintf("%02X", $c[1]);
             $color .= sprintf("%02X", $c[2]);
             $color .= sprintf("%02X", $c[3]);
          }
        }
        return $color;
    }
} 

if (!function_exists("get_content_w")) {
    /**
     * Gets the post content, manually filtered
     *
     * @deprecated
     * @param $id
     * @return mixed
     */
    function get_content_w($id) {
      $my_postid = $id;
      $content_post = get_post($my_postid);
      $content = $content_post->post_content;
      $content = apply_filters('the_content', $content);
      $content = str_replace(']]>', ']]&gt;', $content);
      return $content;
    }
}

if (!function_exists("wpdreams_utf8safeencode")) {
    /**
     * UTF-8 safe encoding
     *
     * @param $s
     * @param $delimiter
     * @return string
     */
    function wpdreams_utf8safeencode($s, $delimiter)
  {
    $convmap= array(0x0100, 0xFFFF, 0, 0xFFFF);
    return $delimiter."_".base64_encode(mb_encode_numericentity($s, $convmap, 'UTF-8'));
  }  
}

if (!function_exists("wpdreams_utf8safedecode")) {
    /**
     * UTF-8 safe decoding
     *
     * @param $s
     * @param $delimiter
     * @return string
     */
    function wpdreams_utf8safedecode($s, $delimiter)
  {
    if (strpos($s, $delimiter)!=0) return $s;
    $convmap= array(0x0100, 0xFFFF, 0, 0xFFFF);
    $_s = explode($delimiter."_", $s);
    return base64_decode(mb_decode_numericentity($s[1], $convmap, 'UTF-8'));
  }  
}

if (!function_exists("postval_or_getoption")) {
    /**
     * Returns post value if set, option value otherwise
     *
     * @param $option
     * @return mixed
     */
    function postval_or_getoption($option)
  {
    if (isset($_POST) && isset($_POST[$option]))
      return $_POST[$option];
    return get_option($option);
  }  
}

if (!function_exists("wpdreams_get_image_from_content")) {
    /**
     * Gets an image from the HTML content
     *
     * @param $content
     * @param int $number
     * @return bool|string
     */
    function wpdreams_get_image_from_content($content, $number = 0) {
      if ($content=="") return false;
      $dom = new domDocument;
      @$dom->loadHTML($content);
      $dom->preserveWhiteSpace = false;
      @$images = $dom->getElementsByTagName('img');
      if ($images->length>0) {
         if ($images->length > $number) {
           $im = $images->item($number)->getAttribute('src');
         } else {
           $number = 0;
           $im = $images->item(0)->getAttribute('src');
         }
         return $im;
      } else {
         return false;
      }
    }
}

if (!function_exists("wpdreams_on_backend_page")) {
    /**
     * Checks if the current page is back-end page
     *
     * @param $pages
     * @return bool
     */
    function wpdreams_on_backend_page($pages) {
        if (isset($_GET) && isset($_GET['page'])) {
            return in_array($_GET['page'] ,$pages);
        }
        return false;
    }
}

/* Extra Functions */
if (!function_exists("wd_isEmpty")) {
    /**
     * @param $v
     * @return bool
     */
    function wd_isEmpty($v) {
  	if (trim($v) != "")
  		return false;
  	else
  		return true;
  }
}

if (!function_exists("wpdreams_on_backend_post_editor")) {
    /**
     * Checks if current page is the post editor
     *
     * @return bool
     */
    function wpdreams_on_backend_post_editor() {
        $current_url = wd_current_page_url();
        return (strpos($current_url, 'post-new.php')!==false ||
            strpos($current_url, 'post.php')!==false);
    }
}

if (!function_exists("wpdreams_get_blog_list")) {
    /**
     * Gets all the blogs from the multisite network
     *
     * @param int $start
     * @param int $num
     * @param bool $ids_only
     * @return array
     */
    function wpdreams_get_blog_list( $start = 0, $num = 10, $ids_only = false ) {
  
  	global $wpdb;
    if (!isset($wpdb->blogs)) return array();
  	$blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM $wpdb->blogs WHERE site_id = %d ORDER BY registered DESC", $wpdb->siteid), ARRAY_A );

	if ($ids_only) {
		foreach ( (array) $blogs as $details ) {
			$blog_list[ $details['blog_id'] ] = $details['blog_id'];
			//$blog_list[ $details['blog_id'] ]['postcount'] = $wpdb->get_var( "SELECT COUNT(ID) FROM " . $wpdb->get_blog_prefix( $details['blog_id'] ). "posts WHERE post_status='publish' AND post_type='post'" );
		}
	} else {
		foreach ( (array) $blogs as $details ) {
			$blog_list[ $details['blog_id'] ] = $details;
			//$blog_list[ $details['blog_id'] ]['postcount'] = $wpdb->get_var( "SELECT COUNT(ID) FROM " . $wpdb->get_blog_prefix( $details['blog_id'] ). "posts WHERE post_status='publish' AND post_type='post'" );
		}
	}

  	unset( $blogs );
  	$blogs = $blog_list;
  
  	if ( false == is_array( $blogs ) )
  		return array();
  
  	if ( $num == 'all' )
  		return array_slice( $blogs, $start, count( $blogs ) );
  	else
  		return array_slice( $blogs, $start, $num );
  }
}

if (!function_exists("wpd_mem_convert")) {
    /**
     * Converts number to memory value with units
     *
     * @param $size
     * @return string
     */
    function wpd_mem_convert($size) {
        $unit=array('B','KB','MB','GB','TB','PB');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
}

//---------------------- FILE SYSTEM SPECIFIC WRAPPERS -------------------------

if (!function_exists("wpd_get_fs_credentials")) {
    /**
     * Gets the credenials to instantiate the WP_Filesystem class
     *         
     * @return mixed
     */
    function wpd_get_fs_credentials() {
    
        // We might need to include the file.php file
        if (!function_exists('request_filesystem_credentials')) {
          // Check if exists, some installs may have different folders
          if ( file_exists(ABSPATH . '/wp-admin/includes/file.php') ) 
            include (ABSPATH . '/wp-admin/includes/file.php');
          else 
            return false;
        }
                                              
        ob_start();
        $creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, null);
        $_c = ob_get_clean();

        if( WP_Filesystem($creds) )
          return $creds; 
        return false;
    }
}

if (!function_exists("wpd_put_file")) {
    /**
     * Writes to a file with the use of WordPress file API with a fallback to file_put_contents()
     *
     * @param $filename
     * @param $contents
     * @return bool
     */
    function wpd_put_file($filename, $contents) {
    
        // Replace double
        $filename = str_replace(array('\\\\', '//'), array('\\', '/'), $filename);
    
        // Fall back if there are no credentials, or something is wrong
        if ( false === ($creds = wpd_get_fs_credentials()) ) {
          return @file_put_contents($filename, $contents) === false ? false : true;  
        }
        
        // Safe to proceed, we most likely have access
        /* initialize the API */
      	if ( ! WP_Filesystem() ) {
      		/* any problems and we exit */
      		return @file_put_contents($filename, $contents) === false ? false : true; 
      	}	
        
        // One step closer
      	global $wp_filesystem;
        
        if ( ! $wp_filesystem->put_contents( $filename, $contents, FS_CHMOD_FILE ) ) {
            return @file_put_contents($filename, $contents) === false ? false : true;
        }
        // All went well, return
        return true;
    }
}

//---------------------- TAXONOMY AND TERM SPECIFIC -------------------------

if (!function_exists("wd_sort_terms_hierarchicaly")) {
	/**
	 * Recursively sort an array of taxonomy terms hierarchically. Child categories will be
	 * placed under a 'children' member of their parent term. Handles missing parent categories as well.
	 *
	 * @param Array   $cats     taxonomy term objects to sort, use get_terms(..)
	 * @param Array   $into     result array to put them in
	 * @param integer $parentId the current parent ID to put them in
	 * @param integer $depth the current recursion depth
	 */
	function wd_sort_terms_hierarchicaly(Array &$cats, Array &$into, $parentId = 0, $depth = 0) {
		foreach ($cats as $i => $cat) {
			if ($cat->parent == $parentId) {
				$into[$cat->term_id] = $cat;
				unset($cats[$i]);
			}
		}

		foreach ($into as $topCat) {
			$topCat->children = array();
			wd_sort_terms_hierarchicaly($cats, $topCat->children, $topCat->term_id, $depth + 1);
		}

		// Try the remaining - the first parent might be excluded
		if (is_array($cats) && $depth == 0)
			foreach ($cats as $topCat) {
				$topCat->children = array();
				wd_sort_terms_hierarchicaly($cats, $topCat->children, $topCat->term_id, $depth + 1);
			}

		// Still any remaining for some satanic reason? Put the rest to the end...
		if (is_array($cats) && $depth == 0)
			foreach ( $cats as $i => $cat ) {
				$into[$cat->term_id] = $cat;
				unset( $cats[$i] );
			}

	}
}

if (!function_exists("wd_flatten_hierarchical_terms")) {
	/**
	 * Flattens a hierarchical array of terms into a flat array, marking levels.
	 * Keeps ordering, sets a $cat->level attribute
	 *
	 * @param Array   $cats     Taxonomy term objects to sort, use get_terms(..)
	 * @param Array   $into     Target array
	 * @param int     $level    The current recursion depth
	 */
	function wd_flatten_hierarchical_terms(Array &$cats, Array &$into, $level = 0) {
		foreach ($cats as $i => $cat) {
			$cat->level = $level;
			$into[] = $cat;
			if ( isset($cat->children) && count($cat->children) > 0 ) {
				wd_flatten_hierarchical_terms( $cat->children, $into, $level + 1 );
			}
		}

		// We don't need the children structure
		foreach ($into as $cat) {
			unset($cat->children);
		}
	}
}