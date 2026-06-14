@props(['nodes' => [], 'typeLabels' => []])

<ul class="org-unit-list" data-list>
    @foreach ($nodes as $node)
        <li class="org-unit-item" data-id="{{ $node['id'] }}">
            <div class="org-unit-row">
                <span class="org-unit-handle" title="Kéo để sắp xếp">⠿</span>

                <input
                    type="text"
                    class="org-unit-name-input"
                    value="{{ $node['name'] }}"
                    onchange="window.dispatchEvent(new CustomEvent('org-unit-rename', { detail: { id: {{ $node['id'] }}, name: this.value } }))"
                />

                <select
                    class="org-unit-type-select"
                    onchange="window.dispatchEvent(new CustomEvent('org-unit-type', { detail: { id: {{ $node['id'] }}, type: this.value } }))"
                >
                    @foreach ($typeLabels as $value => $label)
                        <option value="{{ $value }}" @selected($node['type'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <button
                    type="button"
                    class="org-unit-btn org-unit-btn-add"
                    title="Thêm đơn vị con"
                    onclick="window.dispatchEvent(new CustomEvent('org-unit-add', { detail: { parentId: {{ $node['id'] }} } }))"
                >+ Con</button>

                <button
                    type="button"
                    class="org-unit-btn org-unit-btn-delete"
                    title="Xoá"
                    onclick="if(confirm('Xoá đơn vị này và toàn bộ đơn vị con?')) window.dispatchEvent(new CustomEvent('org-unit-delete', { detail: { id: {{ $node['id'] }} } }))"
                >Xoá</button>
            </div>

            <x-org-unit-branch :nodes="$node['children']" :type-labels="$typeLabels" />
        </li>
    @endforeach
</ul>
