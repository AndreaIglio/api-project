# API Manager

The following APIs pertain to operations on managers within the system.

## Add a New Manager

- **URL:** `/api/manager/add`
- **Method:** `POST`
- **Auth Required:** Yes
- **Permissions Required:** `UserVoter::CREATE_MANAGER`
- **Required Data:**
  ```json
  {
    "email": "[valid email address]",
    "password": "[password]"
  }


- **Success Response:**
    - **Code:** 200 OK
    - **Content:**

      ```json
      {
        "message": "Manager successfully created with email [email] and Id [manager_id]"
      }

- **Error Response:**

    - **Code:** 400 BAD REQUEST
    - **Content:**

      ```json
      {
        "message": "Email and password are needed."
      }

## Edit a Manager

- **URL:** `/api/manager/{managerId}/edit`
- **Method:** PUT
- **Auth Required:** Yes
- **Permissions Required:** `UserVoter::EDIT_OR_REMOVE_MANAGER`
- **URL Parameters:**
    - `managerId`: UUID of the manager to edit.
- **Data Constraints:**
  ```json
  {
    "email": "new valid email address",
    "password": "new password"
  }

- **Success Response:**
  - **Code:** 200 OK
  - **Content:**
    ```json
    {
      "message": "Manager successfully updated."
    }

- **Error Response:**
    - **Code:** 404 NOT FOUND
    - **Content:**
      ```json
      {
        "message": "Manager with ID [managerId] not found."
      }
      
    OR

    - **Code:** 400 BAD REQUEST
    - **Content:**
      ```json
      {
        "message": "The manager ID [managerId] is not valid!"
      }

## Remove a Customer

- **URL:** `/api/customer/{customerId}/remove`
- **Method:** DELETE
- **Auth Required:** Yes
- **Permissions Required:** `UserVoter::EDIT_OR_REMOVE_CUSTOMER`
- **URL Parameters:**
    - `customerId`: UUID of the customer to remove.

- **Success Response:**
  - **Code:** 200 OK
  - **Content:**
    ```json
    {
      "message": "Customer successfully removed."
    }

- **Error Response:**
  - **Code:** 404 NOT FOUND
  - **Content:**
    ```json
    {
      "message": "Customer with ID [customerId] not found."
    }