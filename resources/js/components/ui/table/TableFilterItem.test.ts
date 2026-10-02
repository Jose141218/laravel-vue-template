import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import TableFilterItem from './TableFilterItem.vue';

describe('TableFilterItem component', () => {
    it('renders with vertical orientation by default (label on top without colon)', () => {
        const wrapper = mount(TableFilterItem, {
            props: {
                label: 'Módulo',
            },
        });

        expect(wrapper.text()).toBe('Módulo');
        expect(wrapper.classes()).toContain('flex-col');
        expect(wrapper.classes()).toContain('w-full');

        const label = wrapper.find('label');
        expect(label.classes()).toContain('uppercase');
        expect(label.classes()).toContain('font-semibold');
        expect(label.classes()).toContain('tracking-wider');
    });

    it('renders with horizontal orientation when specified (label on left with colon)', () => {
        const wrapper = mount(TableFilterItem, {
            props: {
                label: 'Ordenar',
                orientation: 'horizontal',
            },
        });

        expect(wrapper.text()).toBe('Ordenar:');
        expect(wrapper.classes()).toContain('items-center');
        expect(wrapper.classes()).not.toContain('flex-col');

        const label = wrapper.find('label');
        expect(label.classes()).toContain('font-normal');
        expect(label.classes()).toContain('whitespace-nowrap');
    });

    it('binds forId attribute to label', () => {
        const wrapper = mount(TableFilterItem, {
            props: {
                label: 'Rol',
                forId: 'role-select-id',
            },
        });

        const label = wrapper.find('label');
        expect(label.exists()).toBe(true);
        expect(label.attributes('for')).toBe('role-select-id');
    });

    it('renders default slot content', () => {
        const wrapper = mount(TableFilterItem, {
            props: {
                label: 'Estado',
            },
            slots: {
                default: '<input id="test-input" type="text" />',
            },
        });

        expect(wrapper.find('#test-input').exists()).toBe(true);
    });
});
