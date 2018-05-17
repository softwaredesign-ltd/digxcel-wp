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
      $defaultDataStore = array("name"=>"Default", "id"=>"default");
      array_push($data, $defaultDataStore);

      return array( 'data' => $data );
    }

  }
}
