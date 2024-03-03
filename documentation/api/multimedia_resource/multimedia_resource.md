# API Multimedia Resource

The following APIs pertain to operations on multimedia resources within the system.

## Add a New Multimedia Resource

- **URL:** `/api/multimedia-resource/add`
- **Method:** POST
- **Auth Required:** Yes
- **Permissions Required:** `MultimediaResourceVoter::CREATE`
- **Data Constraints:** The request must include a file in the 'file' field.

- **Success Responses:**
    - **If new resource is added:**
        - **Code:** 200 OK
        - **Content:**
          ```json
          {
            "message": "Resource added successfully.",
            "id": "[resource_id]",
            "fileName": "[file_name]"
          }
          ```
    - **If file is reuploaded:**
        - **Code:** 200 OK
        - **Content:**
          ```json
          {
            "message": "File reuploaded successfully.",
            "id": "[resource_id]",
            "fileName": "[file_name]"
          }
          ```
- **Error Response:**
    - **Code:** 400 BAD REQUEST
    - **Content:** `{ "message": "No file provided." }`
      OR
    - **Code:** 400 BAD REQUEST
    - **Content:** `{ "message": "Resource already exists." }`

## Remove a Multimedia Resource

- **URL:** `/api/multimedia-resource/{multimediaResourceId}/remove`
- **Method:** DELETE
- **Auth Required:** Yes
- **Permissions Required:** `MultimediaResourceVoter::EDIT_OR_REMOVE`
- **URL Parameters:**
    - `multimediaResourceId`: UUID of the multimedia resource to remove.

- **Success Response:**
    - **Code:** 200 OK
    - **Content:** `{ "message": "Resource deleted successfully." }`

- **Error Responses:**
    - **Code:** 404 NOT FOUND
    - **Content:** `{ "message": "Multimedia resource with ID [multimediaResourceId] not found." }`
  
      OR
  
    - **Code:** 400 BAD REQUEST
    - **Content:** `{ "message": "The multimedia resource ID [multimediaResourceId] is not valid!" }`

## Edit a Multimedia Resource

- **URL:** `/api/multimedia-resource/{multimediaResourceId}/edit`
- **Method:** PUT
- **Auth Required:** Yes
- **Permissions Required:** `MultimediaResourceVoter::EDIT_OR_REMOVE`
- **URL Parameters:**
    - `multimediaResourceId`: UUID of the multimedia resource to edit.
- **Data Constraints:**

  ```json
  {
    "fileName": "new file name",
    "ext": "new file extension"
  }

- **Success Response:**
  - **Code:** 200 OK
  - **Content:**
    ```json
    {
      "message": "Resource updated successfully.",
      "id": "[resource_id]",
      "fileName": "[new_file_name]",
      "ext": "[new_extension]"
    }

- **Error Response:**
  - **Code:** 404 NOT FOUND
  - **Content:**
    ```json
    {
      "message": "Multimedia resource with ID [multimediaResourceId] not found."
    }
    
  OR

  - **Code:** 400 BAD REQUEST
  - **Content:**
    ```json
    {
      "message": "The multimedia resource ID [multimediaResourceId] is not valid!"
    }
        
  OR

  - **Code:** 400 BAD REQUEST
  - **Content:**
    ```json
    {
      "message": "New file name and extension are required and need to be string values."
    }
    
  OR

  - **Code:** 500 SERVER ERROR
  - **Content:**
    ```json
    {
      "message": "An error occurred while updating the multimedia resource."
    }

## Show Multimedia Resources

- **URL:** `/api/multimedia-resource/show`
- **Method:** GET
- **Auth Required:** Yes
- **Permissions Required:** `MultimediaResourceVoter::VIEW`

- **Success Response:**
  - **Code:** 200 OK
  - **Content:**
    ```json
    {
      "multimediaResources": [
        {
          "id": "[resource_id]",
          "fileName": "[file_name]",
          "ext": "[extension]",
        },
      ]
    }

- **Error Response:**
    - **Code:** 403 FORBIDDEN
    - **Content:**
      ```json
      {
        "message": "You are not authorized to view these multimedia resources."
      }
      
    OR

    - **Code:** 500 SERVER ERROR
    - **Content:**
      ```json
      {
        "message": "An error occurred while retrieving the multimedia resources."
      }