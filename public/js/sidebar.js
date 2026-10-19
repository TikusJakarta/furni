(function () {
    var html = document.documentElement;
    var sidebar = document.getElementById('adminSidebar');
    var collapseBtn = document.getElementById('sidebarCollapse');
    var KEY_COLLAPSE = 'adminSidebar';
    var KEY_GROUPS = 'adminSidebarGroups';

    function readGroups() {
        try { return JSON.parse(localStorage.getItem(KEY_GROUPS)) || []; } catch (e) { return []; }
    }
    function saveGroups(list) {
        try { localStorage.setItem(KEY_GROUPS, JSON.stringify(list)); } catch (e) { }
    }

    // --- Submenu: grup aktif selalu terbuka; sisanya mengikuti pilihan terakhir admin
    var saved = readGroups();
    sidebar.querySelectorAll('.sidebar-group').forEach(function (group) {
        var key = group.getAttribute('data-group');
        var toggle = group.querySelector('.sidebar-toggle');

        function setOpen(open) {
            group.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        setOpen(group.getAttribute('data-active') === '1' || saved.indexOf(key) !== -1);

        toggle.addEventListener('click', function () {
            var open = !group.classList.contains('is-open');
            setOpen(open);

            var list = readGroups().filter(function (k) { return k !== key; });
            if (open) { list.push(key); }
            saveGroups(list);
        });
    });

    // --- Ciutkan / perluas sidebar
    function syncButton() {
        var collapsed = html.classList.contains('sb-collapsed');
        var label = collapsed ? 'Perluas sidebar' : 'Ciutkan sidebar';
        collapseBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        collapseBtn.setAttribute('aria-label', label);
        collapseBtn.setAttribute('title', label);
    }

    collapseBtn.addEventListener('click', function () {
        var collapsed = html.classList.toggle('sb-collapsed');
        try { localStorage.setItem(KEY_COLLAPSE, collapsed ? 'collapsed' : 'expanded'); } catch (e) { }
        syncButton();
    });

    syncButton();
})();