<?php
if ( ! class_exists( 'DigxcelCookieWidget' ) ) {

  class DigxcelCookieWidget{

    private $blocked_cookies = array();

    public function __construct() {
      $this->digxcel_get_blocked_cookies();

      if( get_option('digxcel_cookie_widget_enabled') && get_option('digxcel_cookie_widget_key') ){
        // Add cookie script - TODO pull this from cdn
        add_action('wp_enqueue_scripts', array( $this, 'digxcel_insert_widget_script' ));

        // Add widget key to DOM
        add_action('wp_footer', array( $this, 'digxcel_insert_widget_config' ));

        // Toggle httponly cookies
        add_action('wp_footer', array( $this, 'digxcel_toggle_http_cookies' ));

        // Toggle third party cookies
        add_filter('final_output', array( $this, 'digxcel_toggle_cookies' ));
      }
    }

    private function digxcel_get_blocked_cookies() {
      if( array_key_exists('digxcel-consents', $_COOKIE) ) {
        foreach(json_decode(stripslashes($_COOKIE['digxcel-consents']), true) as $key=>$value) {
          if ($value['accepted'] == false) {
            array_push($this->blocked_cookies, $value);
          }
        }
      }
    }

    public function digxcel_insert_widget_config() {
      echo '<div id="digxcelConfig" url="' . get_option('digxcel_cookie_widget_key') . '"></div>';
    }

    public function digxcel_insert_widget_script() {
      wp_enqueue_script( 'digxcel-cookie-widget', plugins_url( 'digxcel/assets/js/digxcel-cookie-widget.js' , dirname(__FILE__) ) );
    }

    public function digxcel_toggle_http_cookies() {
      foreach($this->blocked_cookies as $key => $cookie) {
        unset( $_COOKIE[$cookie['name']] );
        setcookie($cookie['name'], null, strtotime('-1 day'), $cookie['path'], $cookie['domain'], false, true);
      }
    }

    private function digxcel_node_blocked($node_identifier) {
      foreach($this->blocked_cookies as $key => $what) {
        if( strpos($node_identifier, $what['domain']) !== false ){
          return true;
        }
        if( strpos($node_identifier, $what['source']) !== false ){
          return true;
        }
      }
      return false;
    }

    public function digxcel_toggle_cookies($dom) {

      if(strpos($dom, '<html') === false) {
        return $dom;
      } else if (strpos($dom, '<html') > 200 ) {
        return $dom;
      } else if( count($this->blocked_cookies) == 0 ) {
        return $dom;
      }

      libxml_use_internal_errors(true);

      $doc = new DOMDocument();
      $doc->encoding = 'utf-8';
      $doc->loadHTML(mb_convert_encoding($dom, 'HTML-ENTITIES', 'UTF-8'));

      $domElemsToRemove = array();

      // Block scripts
      foreach($doc->getElementsByTagName('script') as $script) {
        // Remove node if src matches
        if ($this->node_blocked($script->getAttribute('src')) !== false) {
          array_push($domElemsToRemove, $script);
          continue;
        }

        // Remove node if contents match
        if ($script->nodeValue && ($this->node_blocked($script->nodeValue) !== false)){
          array_push($domElemsToRemove, $script);
        }
      }

      // Block iFrames
      foreach($doc->getElementsByTagName('iframe') as $iframe) {
        if ($this->node_blocked($iframe->getAttribute('src')) !== false) {
          array_push($domElemsToRemove, $iframe);
        }
      }

      // Remove anything that we want to block
      foreach( $domElemsToRemove as $domElement ){
        $domElement->parentNode->removeChild($domElement);
      }

      $output = $doc->saveHTML();
      libxml_use_internal_errors(false);
      return $output;
    }
  }
}
