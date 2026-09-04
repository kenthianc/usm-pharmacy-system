---
paths:
  - 'app/**'
---

# App

## RBAC Role Enforcement and users.role_id synchronization
The system uses spatie/laravel-permission synced with users.role_id. App\Http\Middleware\RoleMiddleware aliases 'role' and automatically allows the 'admin' role through any role-protected route. Self-registration defaults to the 'patient' role.
