# Composable Design Conventions (YAGNI & State Access)

1. **YAGNI Principle (You Aren't Gonna Need It):**
   - Expose and parameterize only what the current consumer requires.
   - **Do not add preemptive callbacks or options "just in case"** (e.g., `onSuccess`, `onFinish`, `onError`, `preserveScroll`), unless a concrete caller already passes or consumes them.

2. **Inertia Callbacks Handling:**
   - If a composable needs to react to an Inertia request lifecycle event (e.g., resetting password fields on `onFinish` or clearing a code on `onError`), handle that callback internally within the composable.
   - Do not re-expose callbacks as composable parameters unless the caller strictly needs to execute external logic after the request (such as closing a parent modal or emitting a custom event).

3. **Direct Form State Access (`form.errors`, `form.processing`):**
   - In Vue 3 with Inertia, `form` returned by `useForm()` is deeply reactive.
   - **Default convention:** Access errors and processing state directly on the `form` object in components (`form.errors.field`, `form.processing`).
   - **Do not create redundant computed properties** (e.g., `processing: computed(() => form.processing)` or `errors: computed(() => form.errors)`), unless:
     a) The state is derived or composed from multiple sources (e.g., `processing: computed(() => form.processing || isUploading.value)`).
     b) The composable does not expose the `form` object directly.

4. **Parameters and Types:**
   - Always type composable props using dedicated interfaces (stored in the module's `interfaces/` directory).
   - Accept only minimal required data (`routeName`, the entity to edit if applicable).

5. **Standard Form Return:**
   - A typical form composable returns: `{ form, submit, ...customActionHelpers }`.

6. **Test Colocation:**
   - Any composable managing `useForm` or `router` calls must have its companion test file (`useX.test.ts`) adhering to `testing-composables.md`.
