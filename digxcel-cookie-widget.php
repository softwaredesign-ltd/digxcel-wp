<?php

if ( ! class_exists( 'DigxcelCookieWidget' ) ) {

  class DigxcelCookieWidget{

    private $blocked_cookies = array();
    private $cookie_settings = array();

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
        add_filter('final_output', array( $this, 'digxcel_toggle_scripts' ));
      }
    }

    private function digxcel_get_blocked_cookies() {
      if( array_key_exists('digxcel-consents', $_COOKIE) ) {
        $this->cookie_settings = json_decode(stripslashes($_COOKIE['digxcel-consents']), true);
        foreach($this->cookie_settings as $key=>$value) {
          if ($value['accepted'] === false) {
            array_push($this->blocked_cookies, $value);
          }
        }
      }
    }

    public function digxcel_insert_widget_config() {
      echo '<div id="digxcelConfig" url="' . get_option('digxcel_cookie_widget_key') . '"></div>';
    }

    public function digxcel_insert_widget_script() {
      wp_enqueue_script( 'digxcel-cookie-widget', plugins_url( 'digxcel/assets/js/digxcel-cookie-widget.js' , dirname(__FILE__) ), array(), date("h:i:s") );
      wp_enqueue_style( 'digxcel-cookie-style', plugins_url( 'digxcel/assets/css/digxcel-style.css' , dirname(__FILE__) ), array(), date("h:i:s") );
    }

    public function digxcel_toggle_http_cookies() {
      foreach($this->blocked_cookies as $key => $cookie) {
        unset( $_COOKIE[$cookie['name']] );
        setcookie($cookie['name'], null, strtotime('-1 day'), $cookie['path'], $cookie['domain'], false, true);
        setcookie($cookie['name'], null, strtotime('-1 day'), $cookie['path'], $cookie['domain'], false, false);
        setcookie($cookie['name'], null, strtotime('-1 day'), $cookie['path'], $cookie['domain'], true, true);
        setcookie($cookie['name'], null, strtotime('-1 day'), $cookie['path'], $cookie['domain'], true, false);
      }
    }

    private function digxcel_node_blocked($node, $node_identifier, &$blockedElements) {
      foreach($this->blocked_cookies as $key => $what) {
        $elementMetadata = array("cookie"=>$what['name'], "nodeId"=>$node_identifier, "node"=>$node, "domain"=>$what['domain'], "source"=>$what['source']);
        if( $what['thirdParty'] === true && strpos($node_identifier, $what['domain']) !== false ) {
          array_push($blockedElements, $elementMetadata);
          return true;
        }
        if( $what['source'] !== NULL && $what['source'] != '' && strpos($node_identifier, $what['source']) !== false ){
          array_push($blockedElements, $elementMetadata);
          return true;
        }
      }
      return false;
    }

    public function digxcel_toggle_scripts($dom) {

      if(strpos($dom, '<html') === false) {
        return $dom;
      } else if (strpos($dom, '<html') > 200 ) {
        return $dom;
      }

      libxml_use_internal_errors(true);

      $doc = new DOMDocument();
      $doc->encoding = 'utf-8';
      $doc->loadHTML(mb_convert_encoding($dom, 'HTML-ENTITIES', 'UTF-8'));

      $domElemsToRemove = array();
      $blockedElements = array();

      if( count($this->blocked_cookies) > 0 ) {
        // Block scripts
        foreach($doc->getElementsByTagName('script') as $script) {
          // Remove node if src matches
          if ($this->digxcel_node_blocked($script, $script->getAttribute('src'), $blockedElements) === true) {
            array_push($domElemsToRemove, $script);
            continue;
          }

          // Remove node if contents match
          if ($script->nodeValue && ($this->digxcel_node_blocked($script, $script->nodeValue, $blockedElements) === true)){
            array_push($domElemsToRemove, $script);
          }
        }

        // Block iFrames
        foreach($doc->getElementsByTagName('iframe') as $iframe) {
          if ($this->digxcel_node_blocked($iframe, $iframe->getAttribute('src'), $blockedElements) === true) {
            array_push($domElemsToRemove, $iframe);
          }
        }

        // Remove anything that we want to block
        foreach( $domElemsToRemove as $domElement ){
          $domElement->parentNode->removeChild($domElement);
        }
      }

      // Log cookie preferences
      $cookiePreferenceNode = $doc->createElement("digxcel-prefs");
      $cookiePreferenceNode->setAttribute("style", "display: none;");
      foreach($this->cookie_settings as $key => $cookie) {
        $cookiePreference = $doc->createElement("cookie");
        $cookiePreference->setAttribute("name", $cookie["name"]);
        $cookiePreference->setAttribute("path", $cookie["path"]);
        $cookiePreference->setAttribute("domain", $cookie["domain"]);
        $cookiePreference->setAttribute("thirdParty", $cookie["thirdParty"]);
        $cookiePreference->setAttribute("source", $cookie["source"]);
        $cookiePreference->setAttribute("accepted", $cookie["accepted"]);
        $cookiePreferenceNode->appendChild($cookiePreference);
      }
      $doc->appendChild($cookiePreferenceNode);

      // Log blocked elements
      $blockedElementNode = $doc->createElement("digxcel-blocked");
      $blockedElementNode->setAttribute("style", "display: none;");
      $blockedElementNode->setAttribute("blockedElementCount", sizeOf($domElemsToRemove));
      $blockedElementNode->setAttribute("blockedCookieCount", sizeOf($this->blocked_cookies));
      foreach( $blockedElements as $blockedElement ){
        $blockedElementChild = $doc->createElement("blocked");
        $blockedElementChild->setAttribute("cookie", $blockedElement["cookie"]);
        $blockedElementChild->setAttribute("domain", $blockedElement["domain"]);
        $blockedElementChild->setAttribute("source", $blockedElement["source"]);
        $blockedElementChild->setAttribute("nodeId", $blockedElement["nodeId"]);
        $blockedElementChild->setAttribute("node", $blockedElement["node"]->nodeValue);
        $blockedElementNode->appendChild($blockedElementChild);
      }
      $doc->appendChild($blockedElementNode);

      $output = $doc->saveHTML();
      libxml_use_internal_errors(false);
      return $output;
    }
  }
}
