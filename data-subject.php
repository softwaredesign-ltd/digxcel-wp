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

        $personalDataKeys = ['nickname', 'first_name', 'last_name', 'description', 'url', 'email', 'display_name'];
        $unrectifiablePersonalData = ['email'];
        $personalData = array();

        // Extract from user metadata
        $userMetadata = get_user_meta($user->id);
        foreach( $personalDataKeys as $dataKey ){
          if( array_key_exists($dataKey, $userMetadata) && $userMetadata[$dataKey][0] != "" ){
            array_push($personalData, array(
              'name' => $dataKey,
              'category' => 'personal', // OPTIONS: 'personal', 'sensitive'
              'value' => $userMetadata[$dataKey][0],
              'rectifiable' => !in_array($dataKey, $unrectifiablePersonalData)
            ));
          }
        }

        // Extract from user data
        $userData = get_userdata($user->id)->data;
        foreach( $personalDataKeys as $dataKey ){
          $prefixedMetadataKey = 'user_' . $dataKey;
          if( array_key_exists($prefixedMetadataKey, $userData) && $userData->$prefixedMetadataKey != "" ){
            array_push($personalData, array(
              'name' => $dataKey,
              'category' => 'personal', // OPTIONS: 'personal', 'sensitive'
              'value' => $userData->$prefixedMetadataKey,
              'rectifiable' => !in_array($dataKey, $unrectifiablePersonalData)
            ));
          }
        }

        array_push($dataSubjects, array(
          'email' => $user->user_email,
          'data' => $personalData
        ));
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
