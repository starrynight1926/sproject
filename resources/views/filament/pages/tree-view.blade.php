<x-filament-panels::page>
    @php
        $tree = $this->getTree();
    @endphp

    <div
        style="
            --bg:#0f1117; --bg2:#181c27; --bg3:#1e2335;
            --border:rgba(255,255,255,0.08); --border2:rgba(255,255,255,0.14);
            --text:#e2e8f0; --text2:#8892a4; --text3:#5a6478;
            --green:#22d3a0; --green-dim:rgba(34,211,160,0.12);
            --amber:#f59e0b; --amber-dim:rgba(245,158,11,0.12);
            --red:#f87171; --red-dim:rgba(248,113,113,0.10);
            --blue:#60a5fa; --blue-dim:rgba(96,165,250,0.12);
            --purple:#a78bfa; --purple-dim:rgba(167,139,250,0.12);
            background:var(--bg); border-radius:14px; padding:20px;
            font-family:'Inter',-apple-system,sans-serif; color:var(--text);
        "
    >
        {{-- Filter --}}
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
            <select
                wire:change="setFilterProject($event.target.value)"
                style="background:var(--bg2);border:1px solid var(--border);color:var(--text);font-size:12px;padding:6px 12px;border-radius:6px;"
            >
                <option value="">Tất cả dự án</option>
                @foreach($this->getProjects() as $id => $name)
                    <option value="{{ $id }}" @selected($this->filterProject == $id)>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        @if(count($tree) === 0)
            <div style="text-align:center;padding:60px 0;color:var(--text3);font-size:13px;">
                Chưa có dữ liệu.
            </div>
        @endif

        <div class="org-tree-scroll" style="overflow:auto;padding-bottom:20px;cursor:grab;">
            @foreach($tree as $project)
                <ul class="org-tree" style="margin-bottom:30px;">
                    <x-tree-node :node="$project" />
                </ul>
            @endforeach
        </div>
    </div>

    <script>
        (function () {
            document.querySelectorAll('.org-tree-scroll').forEach(function (el) {
                if (el.dataset.dragInit) return;
                el.dataset.dragInit = '1';

                let isDown = false;
                let startX, startY, scrollLeft, scrollTop;

                el.addEventListener('mousedown', function (e) {
                    isDown = true;
                    el.style.cursor = 'grabbing';
                    startX = e.pageX;
                    startY = e.pageY;
                    scrollLeft = el.scrollLeft;
                    scrollTop = el.scrollTop;
                });

                ['mouseleave', 'mouseup'].forEach(function (evt) {
                    el.addEventListener(evt, function () {
                        isDown = false;
                        el.style.cursor = 'grab';
                    });
                });

                el.addEventListener('mousemove', function (e) {
                    if (!isDown) return;
                    e.preventDefault();
                    el.scrollLeft = scrollLeft - (e.pageX - startX);
                    el.scrollTop = scrollTop - (e.pageY - startY);
                });
            });
        })();
    </script>

    <style>
        .org-tree {
            padding-top: 20px;
            position: relative;
            display: flex;
            justify-content: center;
            width: max-content;
            margin: 0 auto;
        }
        .org-tree ul {
            padding-top: 24px;
            position: relative;
            display: flex;
            justify-content: center;
        }
        .org-tree li {
            display: flex;
            flex-direction: column;
            align-items: center;
            list-style-type: none;
            position: relative;
            padding: 24px 10px 0 10px;
        }
        .org-tree > li {
            padding-top: 0;
        }
        .org-tree li::before,
        .org-tree li::after {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            border-top: 1px solid var(--border2);
            width: 50%;
            height: 24px;
        }
        .org-tree li::after {
            right: auto;
            left: 50%;
            border-left: 1px solid var(--border2);
        }
        .org-tree li:only-child::after,
        .org-tree li:only-child::before {
            display: none;
        }
        .org-tree li:only-child {
            padding-top: 0;
        }
        .org-tree li:first-child::before,
        .org-tree li:last-child::after {
            border: 0 none;
        }
        .org-tree li:last-child::before {
            border-right: 1px solid var(--border2);
            border-radius: 0 6px 0 0;
        }
        .org-tree li:first-child::after {
            border-radius: 6px 0 0 0;
        }
        .org-tree ul ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            border-left: 1px solid var(--border2);
            width: 0;
            height: 24px;
        }
        .org-tree > li::before,
        .org-tree > li::after,
        .org-tree > li > ul::before {
            display: none;
        }
    </style>
</x-filament-panels::page>
