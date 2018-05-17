# digXcel Wordpress plugin

A Wordpress plugin to allow the digXcel agent to access data subject data stored on
a Wordpress site.


### API Overview

The plugin exposes 4 REST endpoints:


##### 1. getDataStores - returns list of data stores for the site

  Sample Request:
    GET `/wp-json/digxcel/v1/getDataStores?key=30f26f8e-5937-4795-ac57-01fd4d964c52`

  Sample Response:
  ```
  {
    data: [
      {
        name: "Default",
        id: "default"
      }
    ]
  }
  ```


##### 2. getDataSubjects - returns list of data subjects for given data store

  Sample Request:
    GET `/wp-json/digxcel/v1/getDataSubjects?dataStoreId=default&key=30f26f8e-5937-4795-ac57-01fd4d964c52`

  Sample Response:
    ```
    {
      data: [
        "john@digxcel.com",
        "joe@digxcel.com",
        "jane@digxcel.com",
        "jill@digxcel.com"
      ]
    }
    ```


##### 3. accessRequest - retrieves a list of base64 encoded strings representing the data subject's stored data

  Sample Request:
    GET `/wp-json/digxcel/v1/accessRequest?dataStoreId=default&dataSubjectId=joe@digxcel.com&key=30f26f8e-5937-4795-ac57-01fd4d964c52`

  Sample Response:
    ```
    {
      data: [
        "data:application/csv;base64,ZW1haWwsImRpc3BsYXkgbmFtZSIsImZpcnN0IG5hbWUiLCJsYXN0IG5hbWUiCnJvYkB0ZXN0dXlci5jb20scm9idGVzdHVzZXIo=",
        "data:application/csv;base64,ZW1haWwsImRpc3BsYXkgbmFtZSIsImZpcnN0IG5hbWUiLCJsYXN0IG5hbWUiCnJvYkB0ZXN0dXlci5jb20scm9idGVzdHVzZXIo=",
      ]
    }
    ```


##### 4. deleteRequest - delete's all data stored about a data subject

  Sample Request:
    DELETE `/wp-json/digxcel/v1/deleteRequest?dataStoreId=default&dataSubjectId=joe@digxcel.com&key=30f26f8e-5937-4795-ac57-01fd4d964c52`

  Sample Response:
    ```
    {
      success: true
    }
    ```  


### Default Implementation

* getDataStores - returns a single datastore called 'default'
* getDataSubjects - for data store 'default' returns list of data subjects stored in the Wordpress 'users' table
* accessRequest - retrieves all data stored about a data subject in the Wordpress 'users' table
* deleteRequest - deletes the entry from the Wordpress 'users' table



### 3rd Party Implementation

To configure the plugin to return custom data stores, and to return data subject information
from these custom data stores, a series of Wordpress filters need to be implemented in the `functions.php` file.


##### 1. getDataStores

To return custom data stores you will need to implement a filter which is subscribed to the
`digxcel_get_data_stores` event. Your filter will receive an array, you just need to push the names of
your data stores into that array and return it.

Sample Implementation:
```
  function get_my_data_stores($data) {
  	array_push($data, "dataStore1");
  	array_push($data, "dataStore2");
  	return $data;
  }
  add_filter( 'digxcel_get_data_stores', 'get_my_data_stores', 2, 2);
```


##### 2. getDataSubjects

To return data subjects for your custom data stores you will need to implement a filter which is subscribed to the
`digxcel_get_data_subjects` event.
Your filter will receive an array and a data store id, you just need to push the names of
your data subjects into that array if the data store id parameter matches your data store.

Sample Implementation:
```
  function get_my_data_subjects($data, $dataStoreId) {
    if( $dataStoreId == "this_data_store") {
      array_push($data, "joe@digxcel.com");
      array_push($data, "jill@digxcel.com");      
    }
    return $data;
  }
  add_filter( 'digxcel_get_data_subjects', 'get_my_data_subjects', 2, 2);
```


##### 3. accessRequest

To retrieve the data stored about a data subject in your custom data store you will need to implement a filter which is subscribed to the
`digxcel_access_request` event.
Your filter will receive an array, a data store id and a data subject id.
You will need to get all data stored for that data subject from your data store and write it
to a local file, or to multiple files if necessary, and then append all those file paths to the array.
If there was an error retrieving the data or the data subject wasn't found then there is no need
to add anything to the dataFilePaths array.

Sample Implementation:
```
  function retrieve_data_subject_data($dataFilePaths, $dataStoreId, $dataSubjectId) {
    if( $dataStoreId == "this_data_store") {

      // Create CSV file
      $csvFilePath = $dataStoreId . $dataSubjectId . '.csv';

      // Write to CSV
      $data = array(
        array('email', 'display name', 'first name', 'last name'),
        array('joe@digxcel.com', 'Joe', 'Joe', 'Smith')
      );
      $fp = fopen($csvFilePath, 'w');
      foreach ($data as $fields) {
        fputcsv($fp, $fields);
      }
      fclose($fp);

      // Return path to CSV
      array_push($dataFilePaths, $csvFilePath);
    }
    return $dataFilePaths;
  }
  add_filter( 'digxcel_access_request', 'retrieve_data_subject_data', 2, 3);
```


##### 4. deleteRequest

To delete the data stored about a data subject from your custom data store you will need to implement a filter which is subscribed to the
`digxcel_delete_request` event.
Your filter will receive an empty response string, a data store id and a data subject id.
If the data store id matches your custom data store and the data subject is present then you
will need to delete all the data stored for the data subject and return string 'success'.
If the data subject was not found then just return string 'success'.
If there was an error when trying to delete the data then set the response string to that error message.


Sample Implementation:
```
  function delete_data_subject_data($response, $dataStoreId, $dataSubjectId) {
    delete_subject_data($dataSubjectId);
    $response = 'success';
    return $response;
  }
  add_filter( 'digxcel_delete_request', 'delete_data_subject_data', 2, 3);
```