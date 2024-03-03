# JWT Authentication

To authenticate an user already registered or created via endpoint:

## Authentication

- **URL:** `/authentication`
- **Method:** `POST`
- **Auth Required:** No
- **Permissions Required:** `PUBLIC_ACCESS`
- **Required Data:**

  ```json
  {
    "username": "[valid email address of already registered user]",
    "password": "[password]"
  }