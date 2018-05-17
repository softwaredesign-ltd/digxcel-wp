<?php

if ( ! class_exists( 'DigxcelAccessRequest' ) ) {

  class DigxcelAccessRequest{

    public function digxcel_access_request(WP_REST_Request $request) {

      $dataStoreId = $request->get_param('dataStoreId');
      $dataSubjectId = $request->get_param('dataSubjectId');

      if(!$dataStoreId || !$dataSubjectId)
        return new WP_Error( 'invalid_parameters', 'Invalid parameters', array( 'status' => 400 ) );

      if( $dataStoreId == 'default' ) {
        $dataFilePaths = $this->digxcel_store_core_data($dataSubjectId);
      } else{
        $dataFilePaths = $this->digxcel_store_third_party_data($dataStoreId, $dataSubjectId);
      }

      $subjectData = array();
      foreach ($dataFilePaths as $dataFilePath) {
        if( file_exists($dataFilePath) ){
          array_push($subjectData, $this->digxcel_get_subject_data_from_file($dataFilePath));
          unlink($dataFilePath);
        }
      }

      if( empty($subjectData) )
        return new WP_Error( 'request_failed', 'Failed to retrieve data subject records for ' . $dataSubjectId . ' from datastore ' . $dataStoreId, array( 'status' => 404 ) );

      return array( 'data' => $subjectData );
    }

    private function digxcel_get_subject_data_from_file($dataFilePath) {
      $dataFilePathSplit = explode(".", $dataFilePath);
      $base64FileData = base64_encode(file_get_contents($dataFilePath));
      $subjectData = 'data:application/' . end($dataFilePathSplit) . ';base64,' . $base64FileData;
      return $subjectData;
    }

    private function digxcel_store_core_data($dataSubjectId) {
      $user = get_user_by('email', $dataSubjectId);
      if( !$user )
        return null;

      $csvFilePath = $dataSubjectId . '.csv';
      $data = array(
        array('email', 'display name', 'first name', 'last name'),
        array($user->user_email, $user->display_name, $user->first_name, $user->last_name)
      );
      $fp = fopen($csvFilePath, 'w');
      foreach ($data as $fields) {
        fputcsv($fp, $fields);
      }
      fclose($fp);

      return array($csvFilePath);
    }

    private function digxcel_store_third_party_data($dataStoreId, $dataSubjectId) {
      $dataFilePaths = array();
      $data = apply_filters( 'digxcel_access_request', $dataFilePaths, $dataStoreId, $dataSubjectId);
      if ($data && is_array($data)) {
        return $data;
      }
      return array();
    }

  }
}
