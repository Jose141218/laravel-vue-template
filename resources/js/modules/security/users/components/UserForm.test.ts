import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import type { RoleOption, UserFormData } from '../interfaces';
import UserForm from './UserForm.vue';

describe('UserForm component', () => {
    const mockRoles: RoleOption[] = [
        { id: 1, name: 'admin', description: 'Administrador del sistema' },
        { id: 2, name: 'editor', description: 'Editor de contenido' },
    ];

    const createMockForm = (overrides: Partial<UserFormData> = {}) => {
        return {
            name: 'Juan Perez',
            email: 'juan@example.com',
            password: '',
            roles: ['admin'],
            errors: {},
            hasErrors: false,
            processing: false,
            wasSuccessful: false,
            recentlySuccessful: false,
            ...overrides,
        } as any;
    };

    const globalOptions = {
        mocks: {
            route: (name: string) => `http://localhost/${name}`,
        },
        stubs: {
            Link: { template: '<a><slot /></a>' },
        },
    };

    it('renders role options and passes modelValue=true for selected roles and false for unselected', () => {
        const form = createMockForm({ roles: ['admin'] });
        const isRoleSelected = (name: string) => form.roles.includes(name);
        const toggleRole = vi.fn();

        const wrapper = mount(UserForm, {
            props: {
                routeName: 'security.users.',
                form,
                roles: mockRoles,
                isEditing: true,
                isRoleSelected,
                toggleRole,
            },
            global: globalOptions,
        });

        expect(wrapper.text()).toContain('admin');
        expect(wrapper.text()).toContain('editor');

        const checkboxes = wrapper.findAllComponents({ name: 'Checkbox' });
        expect(checkboxes).toHaveLength(2);
        expect(checkboxes[0].props('modelValue')).toBe(true);
        expect(checkboxes[1].props('modelValue')).toBe(false);
    });

    it('triggers toggleRole when clicking a role card', async () => {
        const form = createMockForm({ roles: ['admin'] });
        const isRoleSelected = (name: string) => form.roles.includes(name);
        const toggleRole = vi.fn();

        const wrapper = mount(UserForm, {
            props: {
                routeName: 'security.users.',
                form,
                roles: mockRoles,
                isEditing: true,
                isRoleSelected,
                toggleRole,
            },
            global: globalOptions,
        });

        const cards = wrapper.findAll('.cursor-pointer.rounded-lg.border');
        expect(cards).toHaveLength(2);

        await cards[1].trigger('click');
        expect(toggleRole).toHaveBeenCalledWith('editor');
    });

    it('renders invitation checkbox and hides password field in create mode when send_invitation is true', () => {
        const form = createMockForm({ send_invitation: true });
        const isRoleSelected = vi.fn().mockReturnValue(false);
        const toggleRole = vi.fn();

        const wrapper = mount(UserForm, {
            props: {
                routeName: 'security.users.',
                form,
                roles: mockRoles,
                isEditing: false,
                isRoleSelected,
                toggleRole,
            },
            global: globalOptions,
        });

        expect(wrapper.text()).toContain(
            'Enviar invitación por correo electrónico',
        );
        expect(wrapper.find('input#password').exists()).toBe(false);
    });

    it('shows password field in create mode when send_invitation is false', () => {
        const form = createMockForm({ send_invitation: false });
        const isRoleSelected = vi.fn().mockReturnValue(false);
        const toggleRole = vi.fn();

        const wrapper = mount(UserForm, {
            props: {
                routeName: 'security.users.',
                form,
                roles: mockRoles,
                isEditing: false,
                isRoleSelected,
                toggleRole,
            },
            global: globalOptions,
        });

        expect(wrapper.text()).toContain(
            'Enviar invitación por correo electrónico',
        );
        expect(wrapper.find('input#password').exists()).toBe(true);
    });
});
