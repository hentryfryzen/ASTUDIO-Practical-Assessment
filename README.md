# 📖 Timesheet API Documentation

## 📂 Base URL:
```
http://localhost:8000/api
```

---

## 📝 1. GET - List Timesheets
🔗 **Endpoint:** `/api/timesheets`

### ✅ **Request:**
```bash
curl -X GET "http://localhost:8000/api/timesheets?filters[task_name][value]=Development" \
-H "Authorization: Bearer <your_access_token>" \
-H "Content-Type: application/json"
```

### 📦 **Response:**
- **Status Code:** `200 OK`
- **Description:** Returns a list of timesheets with applied filters.

---

## 📝 2. POST - Create Timesheet
🔗 **Endpoint:** `/api/timesheets`

### ✅ **Request:**
```bash
curl -X POST http://localhost:8000/api/timesheets \
-H "Authorization: Bearer <your_access_token>" \
-H "Content-Type: application/json" \
-d '{
  "project_id": 1,
  "date": "2025-02-24",
  "hours": 8,
  "task_name": "Feature development"
}'
```

### 📦 **Response:**
- **Status Code:** `201 Created`
- **Description:** Successfully created a new timesheet.

---

## 📝 3. GET - Show Timesheet
🔗 **Endpoint:** `/api/timesheets/{id}`

### ✅ **Request:**
```bash
curl -X GET http://localhost:8000/api/timesheets/1 \
-H "Authorization: Bearer <your_access_token>" \
-H "Content-Type: application/json"
```

### 📦 **Response:**
- **Status Code:** `200 OK`
- **Description:** Returns details of the specified timesheet.

---

## 📝 4. PUT - Update Timesheet
🔗 **Endpoint:** `/api/timesheets/{id}`

### ✅ **Request:**
```bash
curl -X PUT http://localhost:8000/api/timesheets/1 \
-H "Authorization: Bearer <your_access_token>" \
-H "Content-Type: application/json" \
-d '{
  "hours": 10,
  "task_name": "Bug fixing"
}'
```

### 📦 **Response:**
- **Status Code:** `200 OK`
- **Description:** Updates the specified timesheet.

---

## 📝 5. DELETE - Remove Timesheet
🔗 **Endpoint:** `/api/timesheets/{id}`

### ✅ **Request:**
```bash
curl -X DELETE http://localhost:8000/api/timesheets/1 \
-H "Authorization: Bearer <your_access_token>" \
-H "Content-Type: application/json"
```

### 📦 **Response:**
- **Status Code:** `200 OK`
- **Description:** Successfully deletes the specified timesheet.

---

## 🔒 **Authentication:**
- All endpoints require Bearer Token authentication.
- Replace `<your_access_token>` with a valid token.

## 🛡️ **Headers:**
```json
{
  "Authorization": "Bearer <your_access_token>",
  "Content-Type": "application/json"
}
```

## 🚀 **Tips:**
- Use tools like **Postman** for easy testing.
- Ensure the server is running at `localhost:8000` before making requests.
- Replace placeholders like `{id}` with actual resource IDs.

---

✅ **Ready to test?** Just copy, paste, and run the commands! 🚀

🏷️ Attribute API Endpoints

📝 1. GET - List Attributes

🔗 Endpoint: /api/attributes📄 Description: Fetch all attributes.

🖥️ cURL Command:

curl -X GET "http://localhost:8000/api/attributes" \
-H "Authorization: Bearer <your_access_token>" \
-H "Content-Type: application/json"

📝 2. POST - Create Attribute

🔗 Endpoint: /api/attributes📄 Description: Create a new attribute.

🖥️ cURL Command:

curl -X POST http://localhost:8000/api/attributes \
-H "Authorization: Bearer <your_access_token>" \
-H "Content-Type: application/json" \
-d '{
  "name": "Priority",
  "type": "select"
}'

📝 3. GET - Show Attribute

🔗 Endpoint: /api/attributes/{id}📄 Description: Retrieve details of a specific attribute.

🖥️ cURL Command:

curl -X GET http://localhost:8000/api/attributes/1 \
-H "Authorization: Bearer <your_access_token>" \
-H "Content-Type: application/json"

📝 4. PUT - Update Attribute

🔗 Endpoint: /api/attributes/{id}📄 Description: Update an existing attribute.

🖥️ cURL Command:

curl -X PUT http://localhost:8000/api/attributes/1 \
-H "Authorization: Bearer <your_access_token>" \
-H "Content-Type: application/json" \
-d '{
  "name": "Updated Priority",
  "type": "text"
}'

📝 5. DELETE - Remove Attribute

🔗 Endpoint: /api/attributes/{id}📄 Description: Delete an attribute.

🖥️ cURL Command:

curl -X DELETE http://localhost:8000/api/attributes/1 \
-H "Authorization: Bearer <your_access_token>" \
-H "Content-Type: application/json"

✅ All endpoints require Bearer Token Authentication.✅ Ensure Content-Type: application/json header is included.