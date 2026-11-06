# Backend Feature Testing Standards (Laravel + Pest/PHPUnit)

1. **File Organization:**
   - Organize feature tests under `tests/Feature/<Context>/` matching domain contexts.
   - **One test file per resource/controller:** (e.g., `ModuleTest.php`, `RoleTest.php`, `UserTest.php`). Never group distinct business entities into a monolithic test class.

2. **Standard Test Structure for Resource Endpoints:**
   For each CRUD resource, tests should cover:
   - **Authorization & Authentication:**
     - Guests are redirected to login (`assertRedirect(route('login'))`).
     - Unauthorized users receive 403 Forbidden (`assertForbidden()`).
     - Authorized users receive 200 OK (`assertOk()`).
   - **Inertia Page Assertions:**
     - Assert expected page component name (`assertInertia(fn ($page) => $page->component('...'))`).
     - Assert required props and verify unwrapped resources (`missing('item.data')`).
   - **State Persistence (Happy Path):**
     - Assert database records after store/update (`assertDatabaseHas('table', [...])`).
     - Assert redirects to index or intended route (`assertRedirect(...)`).
   - **Negative Validation Cases (Mandatory):**
     - Always test submitting empty/missing required fields (`assertSessionHasErrors(['name', 'email'])`).
     - Test unique constraints (e.g., duplicate email or unique key) return validation errors.
   - **Business Rules & Guards:**
     - Deletion restrictions (e.g., cannot delete superadmin role or active user deleting self).
     - Side effects (use `Notification::fake()`, `Event::fake()`, `Queue::fake()` to verify dispatched side effects without external calls).

3. **Database, Factories & Policy Hygiene:**
   - Always use `RefreshDatabase` trait.
   - **Do NOT seed demo users in tests:** Never run `UserSeeder` or look up hardcoded emails (`where('email', '...')`).
   - **Use Factory States for actors:**
     - Admin actor: `User::factory()->admin()->create()`
     - Specific role: `User::factory()->withRole('coordinador')->create()`
     - Specific permissions: `User::factory()->withPermissions(['modules.view'])->create()`
     - Regular actor: `User::factory()->create()`
   - **Avoid seeders in tests:** Decouple feature tests completely from seeders. Create models dynamically via factories (`Module::factory()->create()`, `Role::create([...])`, `User::factory()->withPermissions([...])`). This keeps tests fast, isolated, and resilient to seeder data changes.
   - **Complex & Attribute-Based Policies (ABAC):** When policies evaluate model attributes (e.g., ownership, tenancy, order status like draft vs approved), write dedicated Unit tests directly against the Policy class (`tests/Unit/Policies/`) for fast permutation coverage, and use Feature tests only for critical endpoint integration.
