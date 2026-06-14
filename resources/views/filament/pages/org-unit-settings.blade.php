<x-filament-panels::page>
    @php
        $typeLabels = $this->getTypeLabels();
    @endphp

    <div class="org-unit-settings">
        <div class="org-unit-toolbar">
            <button type="button" class="org-unit-btn org-unit-btn-add-root" wire:click="addUnit(null)">
                + Thêm đơn vị gốc
            </button>
            <span class="org-unit-hint">Kéo biểu tượng ⠿ để sắp xếp hoặc thay đổi cấp bậc (kéo vào trong đơn vị khác để làm con).</span>
        </div>

        <div id="org-unit-tree-root" wire:ignore.self>
            <x-org-unit-branch :nodes="$this->getTree()" :type-labels="$typeLabels" />
        </div>
    </div>

    <style>
        .org-unit-settings { display: flex; flex-direction: column; gap: 1rem; }
        .org-unit-toolbar { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
        .org-unit-hint { color: rgb(107 114 128); font-size: 0.8rem; }
        .org-unit-list { list-style: none; margin: 0; padding-left: 1.5rem; display: flex; flex-direction: column; gap: 0.35rem; }
        #org-unit-tree-root > .org-unit-list { padding-left: 0; }
        .org-unit-item { border: 1px solid rgb(229 231 235); border-radius: 0.5rem; padding: 0.4rem; background: rgb(255 255 255); }
        .dark .org-unit-item { border-color: rgb(63 63 70); background: rgb(39 39 42); }
        .org-unit-row { display: flex; align-items: center; gap: 0.5rem; }
        .org-unit-handle { cursor: grab; font-size: 1.1rem; color: rgb(156 163 175); padding: 0 0.25rem; user-select: none; }
        .org-unit-name-input {
            flex: 1; min-width: 0; border: 1px solid transparent; border-radius: 0.375rem;
            padding: 0.3rem 0.5rem; background: transparent; font-weight: 500; font-size: 0.875rem;
        }
        .org-unit-name-input:hover, .org-unit-name-input:focus {
            border-color: rgb(209 213 219); background: rgb(249 250 251);
        }
        .dark .org-unit-name-input { color: rgb(244 244 245); }
        .dark .org-unit-name-input:hover, .dark .org-unit-name-input:focus {
            border-color: rgb(82 82 91); background: rgb(24 24 27);
        }
        .org-unit-type-select {
            border: 1px solid rgb(209 213 219); border-radius: 0.375rem; padding: 0.25rem 0.4rem;
            font-size: 0.8rem; background: rgb(255 255 255);
        }
        .dark .org-unit-type-select { background: rgb(24 24 27); border-color: rgb(82 82 91); color: rgb(244 244 245); }
        .org-unit-btn {
            border: 1px solid rgb(209 213 219); border-radius: 0.375rem; padding: 0.25rem 0.6rem;
            font-size: 0.8rem; background: rgb(255 255 255); cursor: pointer; white-space: nowrap;
        }
        .dark .org-unit-btn { background: rgb(24 24 27); border-color: rgb(82 82 91); color: rgb(244 244 245); }
        .org-unit-btn-add-root { background: rgb(79 70 229); color: white; border-color: rgb(79 70 229); }
        .org-unit-btn-delete:hover { background: rgb(254 226 226); color: rgb(185 28 28); border-color: rgb(252 165 165); }
        .org-unit-item.sortable-ghost { opacity: 0.4; }
        .org-unit-item.sortable-drag .org-unit-row { box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    </style>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    @endassets

    @script
    <script>
        const root = document.getElementById('org-unit-tree-root');

        function initSortable(el) {
            new Sortable(el, {
                group: 'org-units',
                handle: '.org-unit-handle',
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                onEnd: () => {
                    $wire.call('saveOrder', collectTree(root.querySelector(':scope > .org-unit-list')));
                },
            });
        }

        function initAllSortables(container) {
            container.querySelectorAll('.org-unit-list').forEach((list) => {
                if (!list.dataset.sortableInit) {
                    list.dataset.sortableInit = '1';
                    initSortable(list);
                }
            });
        }

        function collectTree(list) {
            if (!list) return [];

            return Array.from(list.children).map((li) => {
                const childList = li.querySelector(':scope > .org-unit-list');
                return {
                    id: parseInt(li.dataset.id, 10),
                    children: collectTree(childList),
                };
            });
        }

        initAllSortables(root);

        const observer = new MutationObserver(() => initAllSortables(root));
        observer.observe(root, { childList: true, subtree: true });

        window.addEventListener('org-unit-rename', (e) => {
            $wire.call('renameUnit', e.detail.id, e.detail.name);
        });

        window.addEventListener('org-unit-type', (e) => {
            $wire.call('changeType', e.detail.id, e.detail.type);
        });

        window.addEventListener('org-unit-add', (e) => {
            $wire.call('addUnit', e.detail.parentId);
        });

        window.addEventListener('org-unit-delete', (e) => {
            $wire.call('deleteUnit', e.detail.id);
        });
    </script>
    @endscript
</x-filament-panels::page>
