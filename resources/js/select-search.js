import TomSelect from 'tom-select/dist/esm/tom-select.complete.js';

const defaultOptions = {
    allowEmptyOption: true,
    create: false,
    sortField: { field: '$order' },
    maxOptions: null,
    dropdownParent: 'body',
    plugins: ['dropdown_input'],
    render: {
        no_results: () => '<div class="no-results px-2 py-1 text-muted small">Tidak ditemukan</div>',
    },
};

/**
 * Inisialisasi dropdown dengan fitur pencarian.
 * @param {Document|HTMLElement} root
 */
export function initSelectSearch(root = document) {
    const selects = root instanceof HTMLSelectElement
        ? [root]
        : root.querySelectorAll('select.select-search');

    selects.forEach((select) => {
        if (select.tomselect || select.dataset.tsInitialized === '1') {
            return;
        }

        const placeholder = select.dataset.placeholder
            || select.querySelector('option[value=""]')?.textContent?.trim()
            || 'Ketik untuk mencari...';

        const instance = new TomSelect(select, {
            ...defaultOptions,
            placeholder,
        });

        select.dataset.tsInitialized = '1';

        if (select.classList.contains('is-invalid')) {
            instance.wrapper.classList.add('is-invalid');
        }
    });

    return selects;
}

export function destroySelectSearch(select) {
    if (select?.tomselect) {
        select.tomselect.destroy();
        delete select.dataset.tsInitialized;
    }
}
