<?php
require_once(ABSPATH.'wp-admin/includes/user.php' );

if ( ! class_exists( 'DigxcelDeleteRequest' ) ) {

  class DigxcelDeleteRequest{

    public function digxcel_delete_request(WP_REST_Request $request) {

      $parameters = $request->get_params();
      $dataStoreId = $parameters['dataStoreId'];
      $dataSubjectId = $parameters['dataSubjectId'];

      if(!$dataStoreId || !$dataSubjectId)
        return new WP_Error( 'invalid_parameters', 'Invalid parameters', array( 'status' => 400 ) );

      if( $dataStoreId == 'default' ) {
        $result = $this->digxcel_delete_core_data($dataSubjectId);
      } else{
        $result = $this->digxcel_delete_third_party_data($dataStoreId, $dataSubjectId);
      }

      if( $result != 'success' )
        return new WP_Error( 'error', $result, array( 'status' => 500 ) );

      return array( 'success' => true );
    }

    private function digxcel_delete_core_data($dataSubjectId) {
      $user = get_user_by('email', $dataSubjectId);
      if( $user )
        wp_delete_user($user->ID);
      return 'success';
    }

    private function digxcel_delete_third_party_data($dataStoreId, $dataSubjectId) {
      // We expect a string response from filter:
      //  'success' = Record deleted
      //  'error message...' = Reason that record was not deleted
      $response = '';
      return apply_filters( 'digxcel_delete_request', $response, $dataStoreId, $dataSubjectId );
    }

  }
}
