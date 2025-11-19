# 🏛️ Church Management System - Development Prompt

## 📋 Project Context

You are working on a **comprehensive Church Management System** built with Laravel 12 that serves as a complete digital platform for church community management. This system handles Bible study groups, assignments, prayer requests, resource sharing, and member engagement with enterprise-level security and modern UI/UX.

## 🎯 System Architecture Overview

### **Core Technology Stack:**
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Inertia.js + Vue 3 + TypeScript + Tailwind CSS
- **UI Components**: Reka UI + Lucide Icons
- **Database**: SQLite (development) / MySQL/PostgreSQL (production)
- **Authentication**: Laravel Fortify + Two-Factor Authentication
- **File Storage**: Laravel Storage (to be implemented with UUID naming)
- **Testing**: PHPUnit (to be implemented)
- **Security**: To be implemented with enterprise-level standards

### **Multi-Role System:**
1. **Admin** - Full system management and oversight
2. **Leader** - Group management and member coordination  
3. **Co-Leader** - Assistant leadership with limited permissions
4. **Member** - Community participation and learning

## 🗄️ Database Schema Understanding

### **Core Entities & Relationships:**
```php
User (Admin/Leader/Member)
├── hasMany(Groups) [as leader]
├── belongsToMany(Groups) [as member via group_user pivot]
├── hasMany(AssignmentSubmissions)
├── hasMany(PrayerRequests)
├── hasMany(Resources)
├── hasMany(BanAppeals)
└── hasMany(Comments) [polymorphic]

Group
├── belongsTo(User) [leader]
├── belongsToMany(Users) [members with status: pending/approved/rejected/banned]
├── hasMany(Assignments)
├── hasMany(Resources)
├── hasMany(Meetings)
└── hasMany(BanAppeals) [through memberships]

Assignment
├── belongsTo(Group)
├── hasMany(AssignmentSubmissions)
├── attachments (JSON field)
└── belongsTo(User) [created_by]

PrayerRequest
├── belongsTo(User)
├── hasMany(Comments) [polymorphic]
├── belongsToMany(Users) [prayers pivot table]
└── status (pending/answered/closed)
```

### **Key Database Tables:**
- `users` - Multi-role user system with capabilities
- `groups` - Bible study groups with leader assignment
- `group_user` - Pivot with status (pending/approved/rejected/banned)
- `assignments` - Group assignments with file attachments
- `assignment_submissions` - Member submissions with grading
- `prayer_requests` - Community prayer system
- `resources` - File/link sharing across groups
- `meetings` - Virtual/in-person meeting management
- `comments` - Polymorphic commenting system
- `ban_appeals` - Formal ban appeal process
- `activity_log` - Comprehensive audit trail (Spatie)

## 🔒 Security Framework

### **Authentication & Authorization:**
- **Policy-Based Security**: All controllers use `$this->authorize()`
- **Group-Based Data Isolation**: Users only see their group's content
- **File Security**: UUID naming, path traversal protection, type restrictions
- **Input Validation**: Comprehensive request validation classes
- **CSRF Protection**: All forms protected
- **Role Middleware**: `['auth', 'admin']`, `['auth', 'leader']`, `['auth', 'member']`

### **File Upload Security:**
```php
// Secure file handling pattern
$fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
$filePath = $file->storeAs('assignments', $fileName, 'public');

// Path traversal protection
if (strpos($attachment['path'], '..') !== false || strpos($attachment['path'], '//') !== false) {
    abort(403, 'Unauthorized file access');
}
```

## 🎨 UI/UX Design System

### **Design Themes by Role:**
- **Admin**: Purple gradient theme (#6366f1 to #8b5cf6)
- **Leader**: Blue gradient theme (#3b82f6 to #1d4ed8)  
- **Member**: Green gradient theme (#10b981 to #059669)

### **Modern Design Features:**
- **Glass Morphism**: Backdrop blur effects and transparency
- **Responsive Design**: Mobile-first with 260px sidebar on desktop
- **Touch Optimization**: 44px desktop, 48px mobile touch targets
- **Progressive Web App**: PWA capabilities with proper meta tags
- **Accessibility**: WCAG compliant with proper focus states

## 🧪 Testing Framework

### **Test Coverage (To Be Implemented):**
- **Feature Tests**: Complete workflow testing
- **Unit Tests**: Model relationships and business logic
- **Security Tests**: Authorization, file upload security, XSS protection
- **API Tests**: Endpoint functionality and validation
- **Integration Tests**: Multi-user scenarios and complex workflows
- **Frontend Tests**: Vue component testing with Vitest

### **Test Categories:**
```bash
# Run all tests
php artisan test

# Run specific categories
php artisan test tests/Feature/Admin/
php artisan test tests/Feature/Auth/
php artisan test tests/Unit/Models/
php artisan test --filter SecurityTest
```

## 🚀 Development Patterns

### **Controller Pattern (Inertia.js):**
```php
class AdminUserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);
        
        $users = User::with(['groups'])
            ->when(request('search'), function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(15);
            
        return Inertia::render('admin/Users/Index', [
            'users' => $users,
            'filters' => request()->only(['search'])
        ]);
    }
}
```

### **Policy Pattern:**
```php
class AssignmentPolicy
{
    public function view(User $user, Assignment $assignment)
    {
        return $user->isAdmin() || 
               $user->isLeaderOf($assignment->group) ||
               $user->isMemberOf($assignment->group);
    }
}
```

### **Request Validation Pattern:**
```php
class CreateAssignmentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date|after:now',
            'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,ppt,pptx'
        ];
    }
}
```

## 📊 Key Features (Implementation Status)

### **✅ Currently Implemented:**
- **Authentication System**: Complete with Fortify (login, register, 2FA, password reset)
- **User Management**: Basic profile settings and password management
- **Modern UI Framework**: Vue 3 + TypeScript + Tailwind CSS + Reka UI
- **Responsive Layout**: Sidebar navigation with breadcrumbs

### **🚧 To Be Implemented:**

### **1. Group Management System:**
- Leaders can create and manage Bible study groups
- Members request to join groups (pending → approved/rejected)
- Group-based data isolation (members only see their group's content)
- Ban system with formal appeal process

### **2. Assignment System:**
- Leaders create assignments with file attachments
- Members submit assignments with files and text content
- Grading system with feedback
- Progress tracking and analytics

### **3. Prayer Wall Community:**
- Members post prayer requests (public/private/anonymous options)
- Interactive prayer system (members can "pray" for requests)
- Comment system for community support
- Group-based visibility (only see requests from group members)

### **4. Resource Library:**
- File and link sharing across groups
- Progress tracking (viewed/completed status)
- Category organization
- Cross-group sharing when published

### **5. Meeting Management:**
- Virtual and in-person meeting scheduling
- Attendance tracking
- Meeting status workflow (scheduled → in_progress → completed)

## 🔧 Development Guidelines

### **When Adding New Features:**

1. **Security First**: Always implement authorization policies
2. **Group Isolation**: Ensure data filtering by group membership
3. **File Security**: Use UUID naming and path validation
4. **Input Validation**: Create request classes for validation
5. **Testing**: Write feature and unit tests
6. **UI Consistency**: Follow role-based theme patterns

### **Code Standards:**
- **PSR-12**: PHP coding standards
- **Laravel Conventions**: Follow Laravel naming conventions
- **Policy Authorization**: Use `$this->authorize()` in controllers
- **Eloquent Relationships**: Proper model relationships
- **Request Validation**: Form request classes for validation

### **Performance Considerations:**
- **Eager Loading**: Prevent N+1 queries with `with()`
- **Caching**: Use `Cache::remember()` for expensive operations
- **Pagination**: Always paginate large datasets
- **Query Optimization**: Use raw SQL for complex statistics

## 🎯 Common Development Tasks

### **Adding a New Feature:**
1. Create migration for database changes
2. Update models with relationships
3. Create policy for authorization
4. Create request classes for validation
5. Implement controller with proper authorization
6. Create views following design system
7. Add routes with appropriate middleware
8. Write feature and unit tests

### **Debugging Issues:**
1. Check Laravel logs in `storage/logs/`
2. Use `php artisan telescope:install` for query debugging
3. Run `php artisan test` to ensure no regressions
4. Check authorization policies for access issues
5. Verify group membership for data visibility issues

### **Security Checklist:**
- [ ] Authorization policy implemented
- [ ] Group-based data filtering
- [ ] Input validation with request classes
- [ ] File upload security (UUID naming, type validation)
- [ ] CSRF protection on forms
- [ ] XSS protection with proper escaping

## 📚 Key Files to Understand

### **Core Controllers:**
- `app/Http/Controllers/Admin/` - Admin management
- `app/Http/Controllers/Leader/` - Group leadership
- `app/Http/Controllers/Member/` - Member participation

### **Models:**
- `app/Models/User.php` - Multi-role user system
- `app/Models/Group.php` - Group management with relationships
- `app/Models/Assignment.php` - Assignment system
- `app/Models/PrayerRequest.php` - Prayer wall functionality

### **Policies:**
- `app/Policies/` - Authorization logic for all resources

### **Vue Components:**
- `resources/js/pages/admin/` - Admin interface (purple theme)
- `resources/js/pages/leader/` - Leader interface (blue theme)
- `resources/js/pages/member/` - Member interface (green theme)
- `resources/js/components/` - Reusable UI components
- `resources/js/layouts/` - Layout components for different user roles

### **Routes:**
- `routes/admin.php` - Admin routes
- `routes/leader.php` - Leader routes  
- `routes/member.php` - Member routes
- `routes/auth.php` - Authentication routes

## 🚀 Getting Started

### **Development Setup:**
```bash
# Clone and setup
composer install
npm install
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Start development
php artisan serve
npm run dev
```

### **Default Credentials:**
- **Admin**: admin@victoryf.com / password
- **Leader**: leader@victoryf.com / password  
- **Member**: member@victoryf.com / password

### **Testing:**
```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific tests
php artisan test --filter AdminDashboardTest
```

## 🎯 Development Mindset

### **Think Like a Church Community:**
- **Groups are central** - Everything revolves around Bible study groups
- **Privacy matters** - Prayer requests and group data must be protected
- **Community engagement** - Features should encourage participation
- **Spiritual growth** - System should support learning and development
- **Leadership hierarchy** - Respect the admin → leader → member structure

### **Security-First Approach:**
- Always ask: "Who should have access to this data?"
- Implement authorization before functionality
- Test security scenarios thoroughly
- Protect sensitive information (prayer requests, personal data)

### **User Experience Focus:**
- **Mobile-first** - Many users will access via mobile devices
- **Intuitive navigation** - Church members may not be tech-savvy
- **Clear feedback** - Success/error messages should be helpful
- **Consistent design** - Follow established patterns and themes

## 🏆 Success Metrics

### **Code Quality:**
- All tests passing (228 tests)
- Security score 9.2/10
- No critical vulnerabilities
- Proper authorization throughout

### **User Experience:**
- Responsive design on all devices
- Fast page load times (<300ms)
- Intuitive navigation
- Accessible to all users

### **System Performance:**
- Handle 100+ concurrent users
- Efficient database queries
- Proper caching implementation
- Scalable architecture

---

**Remember**: This is a **community-focused spiritual platform** where security, privacy, and user experience are paramount. Every feature should serve the goal of building stronger church communities and supporting spiritual growth.

**Development Philosophy**: "Build with love, secure with wisdom, design with empathy."
