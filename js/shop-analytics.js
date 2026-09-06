(function () {
    'use strict';
    const root = document.querySelector('.ds-analytics');
    if (!root) return;
    root.querySelectorAll('input[type="date"]').forEach(function (input) {
        input.addEventListener('change', function () { root.querySelector('[name="period"]').value = 'custom'; });
    });
    const tabs = Array.from(root.querySelectorAll('[role="tab"]'));
    function activate(tab) {
        tabs.forEach(function (item) {
            const active = item === tab;
            item.setAttribute('aria-selected', String(active));
            item.tabIndex = active ? 0 : -1;
            document.getElementById(item.getAttribute('aria-controls')).hidden = !active;
        });
    }
    tabs.forEach(function (tab, index) {
        tab.addEventListener('click', function () { activate(tab); });
        tab.addEventListener('keydown', function (event) {
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
            event.preventDefault();
            const next = event.key === 'Home' ? 0 : event.key === 'End' ? tabs.length - 1 : (index + (event.key === 'ArrowRight' ? 1 : -1) + tabs.length) % tabs.length;
            activate(tabs[next]); tabs[next].focus();
        });
    });
})();
