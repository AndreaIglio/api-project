# API Customer

The following APIs pertain to operations on customers within the system.

## Add a New Customer

- **URL:** `/api/customer/add`
- **Method:** `POST`
- **Auth Required:** Yes
- **Permissions Required:** `UserVoter::CREATE_CUSTOMER`
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
      "message": "Customer successfully created with email [email] and Id [customer_id] related to Manager [manager_email]"
    }

- **Error Response:**

  - **Code:** 400 BAD REQUEST 
  - **Content:** 

    ```json
    {
      "message": "Email and password are needed."
    }

## Edit a Customer

- **URL:** `/api/customer/{customerId}/edit`
- **Method:** `PUT`
- **Auth Required:** Yes
- **Permissions Required:** `UserVoter::EDIT_OR_REMOVE_CUSTOMER`
- **URL Parameters:**
    - `customerId`: UUID of the customer to edit.
- **Required Data:**
  ```json
  {
    "email": "[new valid email address]",
    "password": "[new password]"
  }
  
- **Success Response:**
  - **Code:** 200 OK 
  - **Content:**
    ```json
    {
      "message": "Customer successfully updated."
    }
    
- **Error Response:**

  - **Code:** 404 NOT FOUND 
  - **Content:**
    ```json
    {
      "message": "Customer with ID [customerId] not found."
    }

  OR

  - **Code:** 400 BAD REQUEST
  - **Content:**
    ```json
    {
      "message": "Customer with ID [customerId] not found."
    }

## Remove a Customer

- **URL:** `/api/customer/{customerId}/remove`
- **Method:** `DELETE`
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
      ```

- **Error Response:**
    - **Code:** 404 NOT FOUND
    - **Content:**
      ```json
      {
        "message": "Customer with ID [customerId] not found."
      }
      ```

  OR

    - **Code:** 400 BAD REQUEST
    - **Content:**
      ```json
      {
        "message": "The customer ID [customerId] is not valid!"
      }
      ```