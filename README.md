# 🎯 Quiz System RESTful API

A comprehensive quiz management system built with Laravel, featuring role-based permissions, advanced quiz creation, and detailed analytics.

## ✨ Features

- 🔐 **Authentication & Authorization** - JWT-based auth with role-based permissions
- 📝 **Quiz Management** - Create, publish, and manage quizzes with various question types
- 👥 **User Management** - Handle students, teachers, and administrators
- 📊 **Analytics & Reports** - Detailed statistics and performance tracking
- 🏷️ **Category System** - Organize quizzes and questions by categories
- 📸 **File Upload** - Support for question images
- 🎨 **Flexible Question Types** - Multiple choice, true/false, text, and essay questions

---

## 🚀 Installation

### Prerequisites
- PHP 8.1+
- Composer
- MySQL/PostgreSQL
- Laravel 10+

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/OrkhanRG/Quiz-RESTful-Api.git
   cd Quiz-RESTful-Api
   ```

2. **Install dependencies**
   ```bash
   composer install
   composer dump-autoload
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   # For fresh installation with seed data
   php artisan migrate:fresh --seed
   
   # Or for existing database
   php artisan migrate --seed
   ```

5. **Start the server**
   ```bash
   php artisan serve
   ```

---

## 📊 System Status Enums

The system uses standardized status codes for consistency across all modules:

### CategoryStatus
| Value | Constant | Description |
|-------|----------|-------------|
| `'0'` | `DEACTIVATE` | Category is disabled/hidden |
| `'1'` | `ACTIVE` | Category is active and visible |

### QuestionDifficulty
| Value | Constant | Description |
|-------|----------|-------------|
| `'1'` | `EASY` | Basic level questions |
| `'2'` | `MEDIUM` | Intermediate level questions |
| `'3'` | `HARD` | Advanced level questions |

### QuestionType
| Value | Constant | Description |
|-------|----------|-------------|
| `'1'` | `MULTIPLE_CHOICE` | Multiple choice questions |
| `'2'` | `TRUE_FALSE` | True/False questions |
| `'3'` | `TEXT` | Short text answers |
| `'4'` | `ESSAY` | Long text answers |

### QuizAttemptStatus
| Value | Constant | Description |
|-------|----------|-------------|
| `'1'` | `IN_PROGRESS` | Quiz is currently being taken |
| `'2'` | `COMPLETED` | Quiz was successfully completed |
| `'3'` | `ABANDONED` | Quiz was started but not finished |
| `'4'` | `EXPIRED` | Quiz time limit exceeded |

### QuizStatus
| Value | Constant | Description |
|-------|----------|-------------|
| `'0'` | `DRAFT` | Quiz is being created/edited |
| `'1'` | `ACTIVE` | Quiz is published and available |
| `'2'` | `ARCHIVED` | Quiz is no longer active |

### UserStatus
| Value | Constant | Description |
|-------|----------|-------------|
| `'0'` | `INACTIVE` | User account is disabled |
| `'1'` | `ACTIVE` | User account is active |
| `'2'` | `SUSPENDED` | User account is temporarily suspended |

---

## 📮 Postman Collection

Import the complete API collection from:
```
/database/postman/Quiz API.postman_collection.json
```

The collection includes:
- ✅ Pre-configured authentication
- 🔄 Automatic token management
- 📋 Sample requests for all endpoints
- 🧪 Test scenarios

---

## 👤 Default Users

| Role | Name | Email | Password |
|------|------|-------|----------|
| **Super Admin** | Super Admin | superadmin@kidia.com | password |
| **Admin** | Admin İstifadəçi | admin@kidia.com | password |
| **Teacher** | Müəllim 1 | teacher@kidia.com | password |
| **Student** | Şagird 1 | student1@kidia.com | password |
| **Student** | Şagird 2 | student2@kidia.com | password |

---

## 🛣️ API Routes Documentation

### 🔐 Authentication Routes

| Method | Endpoint | Description | Body |
|--------|----------|-------------|------|
| `POST` | `/api/register` | Register new user | `name`, `email`, `password`, `password_confirmation`, `role` |
| `POST` | `/api/login` | User login | `email`, `password` |
| `POST` | `/api/logout` | Logout current session | - |
| `POST` | `/api/logout-all` | Logout from all devices | - |
| `GET` | `/api/me` | Get current user profile | - |
| `POST` | `/api/change-password` | Change user password | `current_password`, `password`, `password_confirmation` |
| `PUT` | `/api/update-profile` | Update user profile | `name`, `email` |
| `POST` | `/api/deactivate-account` | Deactivate user account | - |
| `POST` | `/api/send-email-verification` | Send verification email | - |
| `POST` | `/api/refresh-token` | Refresh authentication token | - |

---

### 🏷️ Category Management

| Method | Endpoint | Description | Permission Required | Body |
|--------|----------|-------------|-------------------|------|
| `GET` | `/api/categories` | Get all categories | - | - |
| `GET` | `/api/categories/{id}` | Get specific category | - | - |
| `GET` | `/api/categories/search/{search}` | Search categories | - | - |
| `POST` | `/api/categories` | Create new category | `category.create` | `name`, `description`, `color`, `status` |
| `PUT` | `/api/categories/{id}` | Update category | `category.update` | `name`, `description`, `color`, `status` |
| `DELETE` | `/api/categories/{id}` | Delete category | `category.delete` | - |
| `GET` | `/api/categories/{id}/statistics` | Get category statistics | - | - |

**Category Status:**
- `'0'` (DEACTIVATE) - Category is hidden from users
- `'1'` (ACTIVE) - Category is visible and functional

**Use Cases:**
- **Students/Teachers**: Browse available quiz categories
- **Admins**: Organize quizzes by subject areas
- **Analytics**: Track performance by category

---

### ❓ Question Management

| Method | Endpoint | Description | Permission Required | Body |
|--------|----------|-------------|-------------------|------|
| `GET` | `/api/questions` | Get all questions | `question.view` | - |
| `GET` | `/api/questions/{id}` | Get specific question | `question.view` | - |
| `GET` | `/api/questions/search/{search}` | Search questions | `question.view` | - |
| `GET` | `/api/questions/type/{type}` | Filter by question type | `question.view` | - |
| `GET` | `/api/questions/difficulty/{level}` | Filter by difficulty | `question.view` | - |
| `GET` | `/api/questions/random` | Get random questions | `question.view` | Query: `limit`, `category_id` |
| `GET` | `/api/my-questions` | Get user's questions | `question.view` | - |
| `POST` | `/api/questions` | Create new question | `question.create` | `question_text`, `type`, `difficulty`, `points`, `category_id`, `options[]` |
| `PUT` | `/api/questions/{id}` | Update question | `question.update` | Same as create |
| `DELETE` | `/api/questions/{id}` | Delete question | `question.delete` | - |

**Question Types:**
- `'1'` (MULTIPLE_CHOICE) - Multiple choice questions
- `'2'` (TRUE_FALSE) - True/False questions  
- `'3'` (TEXT) - Short text answers
- `'4'` (ESSAY) - Long text answers

**Difficulty Levels:**
- `'1'` (EASY) - Basic level
- `'2'` (MEDIUM) - Intermediate level
- `'3'` (HARD) - Advanced level

---

### 📝 Quiz Management

| Method | Endpoint | Description | Permission Required | Body |
|--------|----------|-------------|-------------------|------|
| `GET` | `/api/quizzes` | Get quizzes (user-specific) | - | - |
| `GET` | `/api/quizzes/{id}` | Get specific quiz | - | - |
| `GET` | `/api/quizzes/search/{search}` | Search quizzes | - | - |
| `GET` | `/api/quizzes/category/{id}` | Get quizzes by category | - | - |
| `GET` | `/api/popular-quizzes` | Get popular quizzes | - | - |
| `GET` | `/api/draft-quizzes` | Get draft quizzes | - | - |
| `POST` | `/api/quizzes` | Create new quiz | `quiz.create` | `title`, `description`, `category_id`, `time_limit`, `max_attempts`, `questions[]` |
| `PUT` | `/api/quizzes/{id}` | Update quiz | `quiz.update` | Same as create |
| `DELETE` | `/api/quizzes/{id}` | Delete quiz | `quiz.delete` | - |
| `POST` | `/api/quizzes/{id}/publish` | Publish quiz | `quiz.publish` | - |
| `POST` | `/api/quizzes/{id}/archive` | Archive quiz | `quiz.publish` | - |

**Quiz Status Flow:**
1. `'0'` (DRAFT) → Create and edit quiz
2. `'1'` (ACTIVE) → Published and available to students
3. `'2'` (ARCHIVED) → No longer active but preserved

---

### 🎮 Quiz Taking

| Method | Endpoint | Description | Permission Required | Body |
|--------|----------|-------------|-------------------|------|
| `POST` | `/api/quizzes/{id}/start` | Start quiz attempt | `quiz.take` | - |
| `POST` | `/api/attempts/{id}/answer` | Submit answer | `quiz.take` | `question_id`, `selected_options[]`, `text_answer` |
| `POST` | `/api/attempts/{id}/complete` | Complete quiz | `quiz.take` | - |
| `POST` | `/api/attempts/{id}/abandon` | Abandon quiz | `quiz.take` | - |
| `GET` | `/api/attempts/{id}/results` | Get attempt results | `quiz.take` | - |
| `GET` | `/api/my-attempts` | Get user's attempts | `quiz.take` | - |

**Quiz Attempt Status:**
- `'1'` (IN_PROGRESS) - Quiz is currently being taken
- `'2'` (COMPLETED) - Quiz was successfully completed
- `'3'` (ABANDONED) - Quiz was started but not finished
- `'4'` (EXPIRED) - Quiz time limit exceeded

**Quiz Taking Flow:**
1. **Start** → Begin quiz attempt, get questions
2. **Answer** → Submit answers one by one
3. **Complete** → Finish quiz and calculate score
4. **Results** → View detailed results and feedback

---

### 👥 User Management

| Method | Endpoint | Description | Permission Required | Body |
|--------|----------|-------------|-------------------|------|
| `GET` | `/api/users` | Get all users | `user.view` | - |
| `GET` | `/api/users/{id}` | Get specific user | `user.view` | - |
| `GET` | `/api/users/{id}/permissions` | Get user permissions | `user.view` | - |
| `GET` | `/api/users/{id}/statistics` | Get user statistics | `user.view` | - |
| `GET` | `/api/users/search/{search}` | Search users | `user.view` | - |
| `GET` | `/api/teachers` | Get all teachers | `user.view` | - |
| `GET` | `/api/students` | Get all students | `user.view` | - |
| `GET` | `/api/active-users` | Get active users | `user.manage` | - |
| `POST` | `/api/users/{id}/assign-role` | Assign role to user | `user.manage` | `role` |
| `POST` | `/api/users/{id}/remove-role` | Remove role from user | `user.manage` | `role` |
| `PUT` | `/api/users/{id}/status` | Update user status | `user.manage` | `status` |

**User Status:**
- `'0'` (INACTIVE) - User account is disabled
- `'1'` (ACTIVE) - User account is active and functional
- `'2'` (SUSPENDED) - User account is temporarily suspended

---

### 🛡️ Role Management

| Method | Endpoint | Description | Permission Required | Body |
|--------|----------|-------------|-------------------|------|
| `GET` | `/api/roles` | Get all roles | `role.view` | - |
| `GET` | `/api/roles/{id}` | Get specific role | `role.view` | - |
| `GET` | `/api/roles/{id}/users` | Get role users | `role.view` | - |
| `POST` | `/api/roles` | Create new role | `role.manage` | `name`, `display_name`, `description`, `permissions[]` |
| `PUT` | `/api/roles/{id}` | Update role | `role.manage` | Same as create |
| `DELETE` | `/api/roles/{id}` | Delete role | `role.manage` | - |
| `POST` | `/api/roles/{id}/assign-permissions` | Assign permissions | `role.manage` | `permissions[]` |
| `POST` | `/api/roles/{id}/remove-permissions` | Remove permissions | `role.manage` | `permissions[]` |
| `POST` | `/api/roles/{id}/assign-user` | Assign user to role | `role.manage` | `user_id` |

---

### 🔑 Permission Management

| Method | Endpoint | Description | Permission Required | Body |
|--------|----------|-------------|-------------------|------|
| `GET` | `/api/permissions` | Get all permissions | `permission.view` | - |
| `GET` | `/api/permissions/module/{module}` | Get permissions by module | `permission.view` | - |
| `POST` | `/api/permissions` | Create permission | `permission.manage` | `name`, `display_name`, `description`, `module` |
| `PUT` | `/api/permissions/{id}` | Update permission | `permission.manage` | Same as create |
| `DELETE` | `/api/permissions/{id}` | Delete permission | `permission.manage` | - |

**Permission Modules:**
- `quiz` - Quiz-related permissions
- `question` - Question management permissions
- `user` - User management permissions
- `role` - Role management permissions
- `category` - Category management permissions

---

### 📊 Analytics & Reports

| Method | Endpoint | Description | Permission Required |
|--------|----------|-------------|-------------------|
| `GET` | `/api/dashboard/statistics` | Get dashboard stats | - |
| `GET` | `/api/reports/quiz/{id}` | Get detailed quiz report | `quiz.view-attempts` |
| `GET` | `/api/quizzes/{id}/attempts` | Get quiz attempts | `quiz.view-attempts` |
| `GET` | `/api/quizzes/{id}/statistics` | Get quiz statistics | `quiz.view-attempts` |

---

### 📸 File Upload

| Method | Endpoint | Description | Permission Required | Body |
|--------|----------|-------------|-------------------|------|
| `POST` | `/api/upload/question-image` | Upload question image | `question.create` | `image` (file) |

**Supported formats:** JPEG, PNG, JPG, GIF (max 2MB)

---

## 🎯 Usage Examples

### 1. Creating a Quiz Flow

```javascript
// 1. Login first
POST /api/login
{
  "email": "teacher@kidia.com",
  "password": "password"
}

// 2. Create questions
POST /api/questions
{
  "question_text": "What is 2+2?", 
  "type": "1", // MULTIPLE_CHOICE
  "difficulty": "1", // EASY
  "points": 10,
  "category_id": 1,
  "options": [
    {"option_text": "3", "is_correct": false},
    {"option_text": "4", "is_correct": true},
    {"option_text": "5", "is_correct": false}
  ]
}

// 3. Create quiz
POST /api/quizzes
{
  "title": "Math Basic Test",
  "description": "Basic mathematics test",
  "category_id": 1,
  "time_limit": 30,
  "max_attempts": 3,
  "questions": [1, 2, 3]
}

// 4. Publish quiz (changes status from '0' DRAFT to '1' ACTIVE)
POST /api/quizzes/1/publish
```

### 2. Taking a Quiz Flow

```javascript
// 1. Start quiz attempt (creates attempt with status '1' IN_PROGRESS)
POST /api/quizzes/1/start

// 2. Submit answers
POST /api/attempts/1/answer
{
  "question_id": 1,
  "selected_options": [2]
}

// 3. Complete quiz (changes attempt status to '2' COMPLETED)
POST /api/attempts/1/complete

// 4. Get results
GET /api/attempts/1/results
```

### 3. Managing User Status

```javascript
// 1. Suspend user account
PUT /api/users/5/status
{
  "status": "2" // SUSPENDED
}

// 2. Reactivate user account
PUT /api/users/5/status
{
  "status": "1" // ACTIVE
}
```

### 4. Category Management

```javascript
// 1. Create active category
POST /api/categories
{
  "name": "Mathematics",
  "description": "Math related quizzes",
  "color": "#ff5722",
  "status": "1" // ACTIVE
}

// 2. Deactivate category
PUT /api/categories/1
{
  "status": "0" // DEACTIVATE
}
```

---

## 🔐 Permission System

### Role Hierarchy
- **Super Admin** - Full system access
- **Admin** - Administrative functions
- **Teacher** - Quiz and question management
- **Student** - Take quizzes and view results

### Permission Categories

#### Quiz Permissions
- `quiz.create` - Create new quizzes
- `quiz.update` - Edit existing quizzes
- `quiz.delete` - Delete quizzes
- `quiz.publish` - Publish/archive quizzes
- `quiz.take` - Take quizzes as student
- `quiz.view-attempts` - View quiz attempts and results

#### Question Permissions
- `question.view` - View questions
- `question.create` - Create new questions
- `question.update` - Edit questions
- `question.delete` - Delete questions

#### User Management
- `user.view` - View user information
- `user.manage` - Manage user roles and status

#### System Administration
- `role.view` - View roles
- `role.manage` - Manage roles and permissions
- `permission.view` - View permissions
- `permission.manage` - Manage permissions
- `category.create` - Create categories
- `category.update` - Edit categories
- `category.delete` - Delete categories

---

## 📊 Response Formats

### Success Response
```json
{
  "message": "Test uğurla yaradıldı",
  "quiz": {
    "id": 1,
    "title": "Math Test",
    "status": "0", // DRAFT
    "created_at": "2025-08-09T10:00:00Z"
  }
}
```

### Error Response
```json
{
  "message": "Test yaradılarkən səhv baş verdi",
  "error": "Validation failed",
  "errors": {
    "title": ["Title field is required"]
  }
}
```

### Paginated Response
```json
{
  "data": [...],
  "current_page": 1,
  "per_page": 15,
  "total": 100,
  "last_page": 7
}
```

### Status Response Examples
```json
// Quiz with status
{
  "id": 1,
  "title": "Math Test",
  "status": "1", // ACTIVE
  "attempts": [
    {
      "id": 1,
      "status": "2", // COMPLETED
      "score": 85
    }
  ]
}

// User with status
{
  "id": 1,
  "name": "John Doe",
  "status": "1", // ACTIVE
  "role": "student"
}

// Question with type and difficulty
{
  "id": 1,
  "question_text": "What is 2+2?",
  "type": "1", // MULTIPLE_CHOICE
  "difficulty": "1", // EASY
  "points": 10
}
```

---

## 🎯 API Features

### Smart Quiz Assignment
- **Teachers** see only their own quizzes
- **Students** see only active, published quizzes (status = '1')
- **Admins** see all quizzes with management capabilities

### Attempt Management
- Automatic attempt validation
- Time limit enforcement with status updates
- Maximum attempt restrictions
- Progress tracking with status monitoring

### Advanced Analytics
- Real-time quiz statistics
- Student performance tracking by status
- Category-wise analytics (only active categories)
- Completion rates and averages

### File Management
- Secure image upload for questions
- Automatic file validation
- Organized storage structure

---

## 🛠️ Technical Stack

- **Framework**: Laravel 10+
- **Authentication**: Laravel Sanctum
- **Database**: MySQL/PostgreSQL
- **File Storage**: Laravel Storage
- **Validation**: Form Requests
- **API Resources**: Eloquent Resources
- **Architecture**: Repository Pattern + Service Layer

---

## 🔧 Configuration

### Environment Variables
```env
APP_NAME="Quiz System"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quiz_system
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:3000
```

### Storage Configuration
- Quiz images: `storage/app/public/questions/`
- Logs: `storage/logs/`
- Cache: `storage/framework/cache/`

---

## 🚦 Status Codes

| Code | Description |
|------|-------------|
| `200` | Success |
| `201` | Created successfully |
| `400` | Bad request / Validation error |
| `401` | Unauthorized |
| `403` | Forbidden / Insufficient permissions |
| `404` | Resource not found |
| `500` | Internal server error |

---

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 🆘 Support

For questions and support:
- 📧 Email: orxanismayilov851@gmail.com
- 📖 Documentation: [API Docs](https://github.com/OrkhanRG/Quiz-RESTful-Api)
- 🐛 Issues: [GitHub Issues](https://github.com/OrkhanRG/Quiz-RESTful-Api/issues)

---

**Built with ❤️ by the OrkhaN**