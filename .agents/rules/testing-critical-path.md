# Testing Policy: Critical Path Focus

1. **Testing Pyramid Priority:**
   - **Backend Feature Tests (PHPUnit/Pest):** Highest priority. All authorization rules, business validation, and database state transitions must be covered here.
   - **Frontend Composables:** Cover stateful business logic, data mappings, guards, and essential form submit actions.

2. **Component Testing (Vue):**
   - **When to test:** Only for critical path components containing non-trivial interactive logic (e.g., complex multi-step forms, hierarchical permission pickers) or permission-driven conditional rendering (`can`).
   - **When NOT to test:** Pure presentational components (`components/ui/*`), static layouts, simple tables without complex local state, CSS classes, or Tailwind styling.
   - **Query Strategy:** Always query by accessible role or visible text (`getByRole`, `findByText`, `findComponent`), never by CSS classes or styling selectors.

3. **Operational Modules:**
   - When building new operational modules, write backend feature tests for every endpoint and unit tests for composables with custom business logic.
