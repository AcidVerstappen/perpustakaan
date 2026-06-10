import './bootstrap';
import * as bootstrap from 'bootstrap';
import { destroySelectSearch, initSelectSearch } from './select-search';

window.bootstrap = bootstrap;
window.initSelectSearch = initSelectSearch;
window.destroySelectSearch = destroySelectSearch;

document.addEventListener('DOMContentLoaded', () => {
    initSelectSearch();
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay?.classList.toggle('show');
        });
    }

    if (overlay && sidebar) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
});
