# Form Composable Testing Standards (Vitest + Inertia)

1. **Location:** Tests must be colocated with the source file (`useX.ts` -> `useX.test.ts`).
2. **Global Setup:** `route()` and `afterEach(vi.clearAllMocks)` are globally configured in `resources/js/tests/setup.ts`. Do not redeclare them in individual test files.
3. **Inertia Mock (`useForm` & `router`):**
   Use the shared mock module in a single line:
   ```ts
   import { spies } from '@/tests/mocks/inertia';
   vi.mock('@inertiajs/vue3', () => import('@/tests/mocks/inertia'));
   ```
   - `spies` provides spies for `post`, `put`, `patch`, `delete`, `reset`, `clearErrors`, etc.
   - `router` methods (`get`, `post`, `put`, `delete`) are also available if the composable imports `router`.
   - To trigger and assert captured callbacks, use `getSubmitOptions(spies.post)` from `@/tests/helpers`.
4. **Data Factories:** Use factory helpers from `@/tests/factories` (`makeUser`, `makeRole`, `makeModule`, `makePermission`, etc.) rather than repeating raw object literals.
5. **Lifecycle Hooks:** If a composable requires Vue lifecycle hooks (`onMounted`, `watch`, `inject`), wrap execution using `withSetup(() => useComposable())` from `@/tests/helpers`.

## What NOT to Test in Form Composables
- **Avoid tautological tests:** Do not write repetitive tests verifying that `submit()` calls `post` with a hardcoded URL string if there is no branching logic.
- **Limit per form composable:** At most 1 test for initial state, 1 test for submit (method + route), and 1 test per callback containing actual business logic (e.g., `reset('password')` on `onFinish`).
- Do not test trivial getters or standard Inertia options (`preserveScroll`) in isolation.
- Do not duplicate identical tests between create and edit modes unless behavior changes (e.g., POST vs PUT).

## What TO Test
- Composable-specific business logic: toggles, derivations, role mapping, default values.
- Guards: e.g., "does not call update if entity ID is missing".
- Callbacks that mutate state or reset fields.

## Quality Gate
Before adding a test, ask: *"What realistic regression would this catch that `vue-tsc` (type check) or backend feature tests wouldn't?"* If none, omit the test.
