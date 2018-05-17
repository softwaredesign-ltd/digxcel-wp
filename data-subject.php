<?php

if ( ! class_exists( 'DigxcelDataSubject' ) ) {

  class DigxcelDataSubject{

    public function digxcel_get_data_subjects(WP_REST_Request $request) {

      $dataStoreId = $request->get_param('dataStoreId');

      if(!$dataStoreId)
        return new WP_Error( 'invalid_parameters', 'Invalid parameters', array( 'status' => 400 ) );

      if( $dataStoreId == 'default' )
        return $this->digxcel_get_core_data();

      return $this->digxcel_get_third_party_data($dataStoreId);
    }

    private function digxcel_get_core_data() {
      $dataSubjects = array();
      foreach ( get_users() as $user ) {
        array_push($dataSubjects, $user->user_email);
      }
      return array( 'data' => $dataSubjects );
    }

    private function digxcel_get_third_party_data($dataStoreId) {
      $data = apply_filters( 'digxcel_get_data_subjects', array(), $dataStoreId);
      if ($data && is_array($data)) {
        return array( 'data' => $data );
      }
      return array( 'data' => array() );
    }
  }
}
