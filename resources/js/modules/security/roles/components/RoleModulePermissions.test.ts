import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import type { ModuleItem, PermissionItem } from '../interfaces';
import RoleModulePermissions from './RoleModulePermissions.vue';

describe('RoleModulePermissions component', () => {
    const mockModule: ModuleItem = {
        id: '1',
        name: 'Usuarios',
        key: 'users',
        description: 'Gestión de usuarios',
    };

    const mockPermissions: PermissionItem[] = [
        {
            id: 1,
            name: 'users.view',
            description: 'Ver usuarios',
            module_key: 'users',
        },
        {
            id: 2,
            name: 'users.create',
            description: 'Crear usuarios',
            module_key: 'users',
        },
    ];

    it('renders module name and description', () => {
        const wrapper = mount(RoleModulePermissions, {
            props: {
                module: mockModule,
                permissions: mockPermissions,
                selectedPermissions: [],
            },
        });

        expect(wrapper.text()).toContain('Usuarios');
        expect(wrapper.text()).toContain('Gestión de usuarios');
    });

    it('shows "Seleccionar todos" when not all permissions are selected', () => {
        const wrapper = mount(RoleModulePermissions, {
            props: {
                module: mockModule,
                permissions: mockPermissions,
                selectedPermissions: ['users.view'],
            },
        });

        const selectAllButton = wrapper.find('button');
        expect(selectAllButton.text()).toContain('Seleccionar todos');
    });

    it('shows "Deseleccionar todos" when all permissions are selected', () => {
        const wrapper = mount(RoleModulePermissions, {
            props: {
                module: mockModule,
                permissions: mockPermissions,
                selectedPermissions: ['users.view', 'users.create'],
            },
        });

        const selectAllButton = wrapper.find('button');
        expect(selectAllButton.text()).toContain('Deseleccionar todos');
    });

    it('emits toggleModule when clicking select all button', async () => {
        const wrapper = mount(RoleModulePermissions, {
            props: {
                module: mockModule,
                permissions: mockPermissions,
                selectedPermissions: [],
            },
        });

        const button = wrapper.find('button');
        await button.trigger('click');

        expect(wrapper.emitted('toggleModule')).toHaveLength(1);
    });

    it('emits togglePermission when clicking on a permission item', async () => {
        const wrapper = mount(RoleModulePermissions, {
            props: {
                module: mockModule,
                permissions: mockPermissions,
                selectedPermissions: [],
            },
        });

        const permItem = wrapper.findAll('.grid > div')[0];
        await permItem.trigger('click');

        expect(wrapper.emitted('togglePermission')).toBeTruthy();
        expect(wrapper.emitted('togglePermission')![0]).toEqual(['users.view']);
    });

    it('passes correct modelValue to Checkbox based on selectedPermissions', () => {
        const wrapper = mount(RoleModulePermissions, {
            props: {
                module: mockModule,
                permissions: mockPermissions,
                selectedPermissions: ['users.view'],
            },
        });

        const checkboxes = wrapper.findAllComponents({ name: 'Checkbox' });
        expect(checkboxes[0].props('modelValue')).toBe(true);
        expect(checkboxes[1].props('modelValue')).toBe(false);
    });
});
