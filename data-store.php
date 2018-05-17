<?php

if ( ! class_exists( 'DigxcelDataStore' ) ) {

  class DigxcelDataStore{

    public function digxcel_get_data_stores(WP_REST_Request $request) {

      // Third party dataStores
      $data = apply_filters( 'digxcel_get_data_stores', array());
      if ( !$data || !is_array($data)) {
        $data = array();
      }

      // Core dataStore
      array_push($data, "default");

      $dataStores = array();
      foreach ($data as &$dataStore) {
        array_push($dataStores, array("name"=>$dataStore, "id"=>$dataStore));
      }
      return array( 'data' => $dataStores );
    }

  }
}
