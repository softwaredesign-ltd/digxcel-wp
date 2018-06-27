<?php

if ( ! class_exists( 'DigxcelProfilingRequest' ) ) {

  class DigxcelProfilingRequest{

    public function digxcel_profile_request(WP_REST_Request $request) {
      $dataStoreId = $request->get_param('dataStoreId');

      if(!$dataStoreId)
        return new WP_Error( 'invalid_parameters', 'Invalid parameters', array( 'status' => 400 ) );

      if( $dataStoreId == 'default' )
        return $this->digxcel_get_core_data();

      return $this->digxcel_get_third_party_data($dataStoreId);
    }

    private function digxcel_get_core_data() {
      $profiles = array();

      array_push($profiles, array(
        'category' => 'general',
        'description' => 'This is the standard category for users',
      ));

      return array('data' => $profiles);
    }

    private function digxcel_get_third_party_data($dataStoreId) {
      $profiles = array();

      $data = apply_filters( 'digxcel_profile_request', $profiles, $dataStoreId);
      if ($data && is_array($data)) {
        return array('data' => $data);
      }

      return array();
    }
  }
}
