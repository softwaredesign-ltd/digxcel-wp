<?php

if ( ! class_exists( 'DigxcelAutomatedDecisionsRequest' ) ) {

  class DigxcelAutomatedDecisionsRequest{

    public function digxcel_automated_decisions_request(WP_REST_Request $request) {

      $dataStoreId = $request->get_param('dataStoreId');

      if(!$dataStoreId)
        return new WP_Error( 'invalid_parameters', 'Invalid parameters', array( 'status' => 400 ) );

      if( $dataStoreId == 'default' )
        return $this->digxcel_get_core_data();

      return $this->digxcel_get_third_party_data($dataStoreId);
    }

    private function digxcel_get_core_data() {
      $decisions = array();
      return array('data' => $decisions);
    }

    private function digxcel_get_third_party_data($dataStoreId) {
      $decisions = array();

      $data = apply_filters( 'digxcel_automated_decisions_request', $decisions, $dataStoreId);
      if ($data && is_array($data)) {
        return array('data' => $data);
      }

      return array();
    }
  }
}
