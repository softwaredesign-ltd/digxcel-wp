<?php
/*
Plugin Name: GDPR - WP Plugin
Plugin URI: http://digxcel.com/
Description: Promoting digital excellence
Author: digXcel
Author URI: https://digxcel.com/
Version: 1
*/
require_once (dirname(__FILE__).'/data-subject.php');
require_once (dirname(__FILE__).'/data-store.php');
require_once (dirname(__FILE__).'/access-request.php');
require_once (dirname(__FILE__).'/delete-request.php');
require_once (dirname(__FILE__).'/digxcel-menu.php');
require_once (dirname(__FILE__).'/digxcel-cookie-widget.php');
require_once (dirname(__FILE__).'/profiling-request.php');
require_once (dirname(__FILE__).'/automated-decisions-request.php');

if ( !class_exists('DigXcel') ) {

  class DigXcel{

    public function __construct() {
      $this->version = 1;
      $this->dataSubject = new DigxcelDataSubject();
      $this->dataStore = new DigxcelDataStore();
      $this->accessRequest = new DigxcelAccessRequest();
      $this->deleteRequest = new DigxcelDeleteRequest();
      $this->digxcelMenu = new DigxcelMenu();
      $this->digxcelMenu->digxcel_create_menu();
      $this->digxcelCookieWidget = new DigxcelCookieWidget();
      $this->profileRequest = new DigxcelProfilingRequest();
      $this->automatedDecisionsRequest = new DigxcelAutomatedDecisionsRequest();
    }

    public function digxcel_register_base_routes(){
      // GET /wp-json/digxcel/version
      add_action( 'rest_api_init', function () {
        register_rest_route( 'digxcel', 'version', array(
          'methods' => 'GET',
          'callback' => array($this, 'get_version')
        ) );
      } );
    }

    public function digxcel_register_routes_v1(){

      // Eg: GET /wp-json/digxcel/v1/getDataStores?key=1234
      add_action( 'rest_api_init', function () {
        register_rest_route( 'digxcel/v1', 'getDataStores', array(
          'methods' => 'GET',
          'callback' => array($this->dataStore, 'digxcel_get_data_stores'),
          'permission_callback' => array($this, 'digxcel_verify_api_key')
        ) );
      } );

      // Eg: GET /wp-json/digxcel/v1/getDataSubjects?dataStoreId=default&key=1234
      add_action( 'rest_api_init', function () {
        register_rest_route( 'digxcel/v1', 'getDataSubjects', array(
          'methods' => 'GET',
          'callback' => array($this->dataSubject, 'digxcel_get_data_subjects'),
          'permission_callback' => array($this, 'digxcel_verify_api_key')
        ) );
      } );

      // Eg: GET /wp-json/digxcel/v1/accessRequest?dataStoreId=default&dataSubjectId=info@softwaredesign.ie&key=1234
      add_action( 'rest_api_init', function () {
        register_rest_route( 'digxcel/v1', 'accessRequest', array(
          'methods' => 'GET',
          'callback' => array($this->accessRequest, 'digxcel_access_request'),
          'permission_callback' => array($this, 'digxcel_verify_api_key')
        ) );
      } );

      // Eg: POST /wp-json/digxcel/v1/deleteRequest?dataStoreId=default&dataSubjectId=info@softwaredesign.ie&key=1234
      add_action( 'rest_api_init', function () {
        register_rest_route( 'digxcel/v1', 'deleteRequest', array(
          'methods' => 'POST',
          'callback' => array($this->deleteRequest, 'digxcel_delete_request'),
          'permission_callback' => array($this, 'digxcel_verify_api_key')
        ) );
      } );

      // Eg: GET /wp-json/digxcel/v1/profileRequest?dataStoreId=default&key=1234
      add_action( 'rest_api_init', function () {
        register_rest_route( 'digxcel/v1', 'profileRequest', array(
          'methods' => 'GET',
          'callback' => array($this->profileRequest, 'digxcel_profile_request'),
          'permission_callback' => array($this, 'digxcel_verify_api_key')
        ) );
      } );

      // Eg: GET /wp-json/digxcel/v1/automatedDecisionsRequest?dataStoreId=default&key=1234
      add_action( 'rest_api_init', function () {
        register_rest_route( 'digxcel/v1', 'automatedDecisionsRequest', array(
          'methods' => 'GET',
          'callback' => array($this->automatedDecisionsRequest, 'digxcel_automated_decisions_request'),
          'permission_callback' => array($this, 'digxcel_verify_api_key')
        ) );
      } );
    }

    public function get_version($request){
      return $this->version;
    }

    public function digxcel_verify_api_key($request){
      $parameters = $request->get_params();

      if( !array_key_exists('key', $parameters) )
        return false;

      $keyParameter = hash('sha256', $parameters['key']);

      $pluginKey = get_option('digxcel_key');

      if($pluginKey && ($pluginKey == $keyParameter) ) {
        return true;
      } else {
        return false;
      }
    }
  }
}

if ( class_exists( 'DigXcel' ) ) {
  $digXcel = new DigXcel();
  $digXcel->digxcel_register_base_routes();
  $digXcel->digxcel_register_routes_v1();
}

// Register filter to manipulate final DOM output
ob_start('final_output');
function final_output($content){
    return apply_filters('final_output', $content);
}
