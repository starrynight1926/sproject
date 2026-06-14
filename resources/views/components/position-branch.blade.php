@props(['nodes' => [], 'orgUnitOptions' => []])

<ul class="org-unit-list" data-list>
    @foreach ($nodes as $node)
        <li class="org-unit-item" data-id="{{ $node['id'] }}">
            <div class="org-unit-row">
                <span class="org-unit-handle" title="Kéo để sắp xếp">⠿</span>

                <input
                    type="text"
                    class="org-unit-name-input"
                    value="{{ $node['name'] }}"
                    onchange="window.dispatchEvent(new CustomEvent('position-rename', { detail: { id: {{ $node['id'] }}, name: this.value } }))"
                />

                <select
                    class="org-unit-type-select"
                    onchange="window.dispatchEvent(new CustomEvent('position-org-unit', { detail: { id: {{ $node['id'] }}, orgUnitId: this.value } }))"
                >
                    @foreach ($orgUnitOptions as $value => $label)
                        <option value="{{ $value }}" @selected((string) ($node['org_unit_id'] ?? '') === (string) $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <button
                    type="button"
                    class="org-unit-btn org-unit-btn-add"
                    title="Thêm vị trí con"
                    onclick="window.dispatchEvent(new CustomEvent('position-add', { detail: { parentId: {{ $node['id'] }} } }))"
                >+ Con</button>

                <button
                    type="button"
                    class="org-unit-btn org-unit-btn-delete"
                    title="Xoá"
                    onclick="if(confirm('Xoá vị trí này và toàn bộ vị trí con?')) window.dispatchEvent(new CustomEvent('position-delete', { detail: { id: {{ $node['id'] }} } }))"
                >Xoá</button>
            </div>

            <x-position-branch :nodes="$node['children']" :org-unit-options="$orgUnitOptions" />
        </li>
    @endforeach
</ul>
