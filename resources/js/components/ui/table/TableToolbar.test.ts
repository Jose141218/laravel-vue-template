import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import TableToolbar from './TableToolbar.vue';

describe('TableToolbar component', () => {
    it('renders search input and responds to modelValue', async () => {
        const wrapper = mount(TableToolbar, {
            props: {
                modelValue: {
                    search: 'test query',
                    rows: 10,
                    order: '',
                    direction: 'asc',
                },
                placeholder: 'Buscar items...',
            },
        });

        const inputs = wrapper.findAll('input');
        expect(inputs.length).toBeGreaterThan(0);
        expect((inputs[0].element as HTMLInputElement).value).toBe('test query');
    });

    it('renders 2-row responsive layout classes on tablet and 1-row on desktop (md:flex-col xl:flex-row)', () => {
        const wrapper = mount(TableToolbar, {
            props: {
                modelValue: {
                    search: '',
                    rows: 10,
                    order: '',
                    direction: 'asc',
                },
            },
        });

        const desktopContainer = wrapper.find('.hidden.md\\:flex');
        expect(desktopContainer.exists()).toBe(true);
        expect(desktopContainer.classes()).toContain('md:flex-col');
        expect(desktopContainer.classes()).toContain('xl:flex-row');
    });

    it('renders total count when provided in both desktop and mobile layouts', () => {
        const wrapper = mount(TableToolbar, {
            props: {
                modelValue: {
                    search: '',
                    rows: 10,
                    order: '',
                    direction: 'asc',
                },
                total: 42,
            },
        });

        expect(wrapper.text()).toContain('Total: 42');
        const totalElements = wrapper.findAll('span').filter((s) => s.text().includes('Total: 42'));
        expect(totalElements.length).toBe(2); // One in desktop, one in mobile
    });

    it('emits clear event when clear button is clicked', async () => {
        const wrapper = mount(TableToolbar, {
            props: {
                modelValue: {
                    search: 'something',
                    rows: 10,
                    order: '',
                    direction: 'asc',
                },
            },
        });

        const clearBtn = wrapper.find('button[title="Limpiar filtros"]');
        expect(clearBtn.exists()).toBe(true);
        await clearBtn.trigger('click');

        expect(wrapper.emitted('clear')).toBeTruthy();
    });

    it('sets id, name, and autocomplete="off" on search inputs with unique IDs', () => {
        const wrapper = mount(TableToolbar, {
            props: {
                modelValue: {
                    search: '',
                    rows: 10,
                    order: '',
                    direction: 'asc',
                },
            },
        });

        const inputs = wrapper.findAll('input[type="search"]');
        expect(inputs.length).toBe(2); // desktop and mobile
        inputs.forEach((input) => {
            expect(input.attributes('id')).toBeTruthy();
            expect(input.attributes('name')).toBeTruthy();
            expect(input.attributes('autocomplete')).toBe('off');
        });

        expect(inputs[0].attributes('id')).not.toBe(inputs[1].attributes('id'));
    });
});
