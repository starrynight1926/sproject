<x-filament-panels::page>
    @php
        $tasksByColumn = $this->getTasksByColumn();
        $columns = $this->columns;
        $projects = $this->getProjects();
        $users = $this->getUsers();

        $avatarColors = [
            'T' => ['#22d3a0','#0f4037'], 'A' => ['#60a5fa','#0c2a4a'],
            'H' => ['#a78bfa','#2a1e4a'], 'M' => ['#f59e0b','#3d2800'],
        ];
        $labelColors = ['#22d3a0','#60a5fa','#a78bfa','#f59e0b','#f87171'];
        $priorityStyles = [
            'low' => 'background:var(--green-dim);color:var(--green);',
            'medium' => 'background:var(--amber-dim);color:var(--amber);',
            'high' => 'background:var(--red-dim);color:var(--red);',
        ];
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
        x-data="{
            dragging: null,
            dragOverCol: null,
            showAddModal: false,
            addModalColId: null,
            addModalColTitle: '',
            newTaskTitle: '',
            newTaskProject: '',
            newTaskPriority: 'medium',
            newTaskAssignee: '',
            showAddColumnModal: false,
            newColumnTitle: '',
            editingColId: null,
            editingColTitle: '',

            contextMenu: { visible: false, x: 0, y: 0, taskId: null },

            openContextMenu(e, taskId) {
                e.preventDefault();
                this.contextMenu = { visible: true, x: e.clientX, y: e.clientY, taskId: taskId };
            },
            closeContextMenu() {
                this.contextMenu.visible = false;
            },
            deleteTaskFromMenu() {
                if (this.contextMenu.taskId) {
                    $wire.deleteTask(this.contextMenu.taskId);
                }
                this.closeContextMenu();
            },

            startDrag(e, taskId, fromCol) {
                this.dragging = { id: taskId, from: fromCol };
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', taskId);
                e.target.closest('[data-card]').style.opacity = '0.45';
            },
            endDrag(e) {
                const card = e.target.closest('[data-card]');
                if (card) { card.style.opacity = '1'; }
                this.dragging = null;
                this.dragOverCol = null;
            },
            dragOver(e, colId) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                this.dragOverCol = colId;
            },
            dragLeave(e, colId) {
                const rect = e.currentTarget.getBoundingClientRect();
                if (e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom) {
                    this.dragOverCol = null;
                }
            },
            drop(e, toCol) {
                e.preventDefault();
                this.dragOverCol = null;
                if (!this.dragging || this.dragging.from === toCol) { this.dragging = null; return; }
                $wire.moveTask(this.dragging.id, toCol);
                this.dragging = null;
            },

            openAddTask(colId, colTitle) {
                this.addModalColId = colId;
                this.addModalColTitle = colTitle;
                this.newTaskTitle = '';
                this.newTaskProject = '';
                this.newTaskPriority = 'medium';
                this.newTaskAssignee = '';
                this.showAddModal = true;
                this.$nextTick(() => this.$refs.taskTitleInput?.focus());
            },
            saveTask() {
                if (!this.newTaskTitle.trim()) return;
                $wire.createTask(
                    this.newTaskTitle.trim(),
                    this.addModalColId,
                    this.newTaskProject ? parseInt(this.newTaskProject) : null,
                    this.newTaskPriority,
                    this.newTaskAssignee ? parseInt(this.newTaskAssignee) : null
                );
                this.showAddModal = false;
            },

            openAddColumn() {
                this.newColumnTitle = '';
                this.showAddColumnModal = true;
                this.$nextTick(() => this.$refs.colTitleInput?.focus());
            },
            saveColumn() {
                if (!this.newColumnTitle.trim()) return;
                $wire.addColumn(this.newColumnTitle.trim());
                this.showAddColumnModal = false;
            },

            startEditCol(colId, title) {
                this.editingColId = colId;
                this.editingColTitle = title;
                this.$nextTick(() => {
                    const input = document.getElementById('edit-col-' + colId);
                    if (input) { input.focus(); input.select(); }
                });
            },
            saveColTitle(colId) {
                if (this.editingColTitle.trim()) {
                    $wire.renameColumn(colId, this.editingColTitle.trim());
                }
                this.editingColId = null;
            }
        }"
    >
        {{-- Toolbar --}}
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;flex-wrap:wrap;">
            <span style="font-size:12px;color:var(--text3);">Dự án:</span>
            <select
                style="background:var(--bg2);border:1px solid var(--border);color:var(--text2);font-size:12px;padding:5px 12px;border-radius:6px;outline:none;"
                x-on:change="$wire.setFilterProject($event.target.value)"
            >
                <option value="">Tất cả</option>
                @foreach($projects as $id => $name)
                    <option value="{{ $id }}" {{ $this->filterProject == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>

            <span style="font-size:12px;color:var(--text3);">Ưu tiên:</span>
            <select
                style="background:var(--bg2);border:1px solid var(--border);color:var(--text2);font-size:12px;padding:5px 12px;border-radius:6px;outline:none;"
                x-on:change="$wire.setFilterPriority($event.target.value)"
            >
                <option value="">Tất cả</option>
                <option value="low" {{ $this->filterPriority === 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ $this->filterPriority === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ $this->filterPriority === 'high' ? 'selected' : '' }}>High</option>
            </select>

            <div style="flex:1"></div>
            <a
                href="{{ \App\Filament\Pages\Trash::getUrl() }}"
                style="background:var(--bg2);border:1px solid var(--border);color:var(--text2);font-size:12px;padding:6px 16px;border-radius:6px;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all .15s;text-decoration:none;"
                onmouseover="this.style.background='var(--bg3)';this.style.color='var(--text)';this.style.borderColor='var(--border2)';"
                onmouseout="this.style.background='var(--bg2)';this.style.color='var(--text2)';this.style.borderColor='var(--border)';"
            >🗑 Thùng rác @if($this->getTrashCount() > 0)<span style="background:var(--red-dim);color:var(--red);font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;">{{ $this->getTrashCount() }}</span>@endif</a>
            <button
                x-on:click="openAddColumn()"
                style="background:var(--bg2);border:1px solid var(--border);color:var(--text2);font-size:12px;padding:6px 16px;border-radius:6px;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all .15s;"
                onmouseover="this.style.background='var(--bg3)';this.style.color='var(--text)';this.style.borderColor='var(--border2)';"
                onmouseout="this.style.background='var(--bg2)';this.style.color='var(--text2)';this.style.borderColor='var(--border)';"
            >＋ Thêm cột</button>
        </div>

        {{-- Kanban Board --}}
        <div style="display:flex;gap:12px;overflow-x:auto;padding-bottom:16px;align-items:flex-start;">
            @foreach($columns as $col)
                @php $colTasks = $tasksByColumn[$col['id']] ?? []; @endphp
                <div
                    style="min-width:265px;width:265px;flex-shrink:0;background:var(--bg2);border:1px solid var(--border);border-radius:12px;display:flex;flex-direction:column;max-height:calc(100vh - 240px);"
                    x-on:dragover="dragOver($event, '{{ $col['id'] }}')"
                    x-on:dragleave="dragLeave($event, '{{ $col['id'] }}')"
                    x-on:drop="drop($event, '{{ $col['id'] }}')"
                    :style="dragOverCol === '{{ $col['id'] }}' ? 'min-width:265px;width:265px;flex-shrink:0;background:var(--bg2);border:1px solid {{ $col['color'] }};border-radius:12px;display:flex;flex-direction:column;max-height:calc(100vh - 240px);box-shadow:0 0 0 1px {{ $col['color'] }};' : ''"
                >
                    {{-- Column Header --}}
                    <div style="padding:12px 14px 10px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
                        <div style="display:flex;align-items:center;gap:7px;flex:1;min-width:0;">
                            <div style="width:9px;height:9px;border-radius:50%;background:{{ $col['color'] }};flex-shrink:0;"></div>

                            {{-- Editable title --}}
                            <template x-if="editingColId === '{{ $col['id'] }}'">
                                <input
                                    id="edit-col-{{ $col['id'] }}"
                                    type="text"
                                    x-model="editingColTitle"
                                    x-on:blur="saveColTitle('{{ $col['id'] }}')"
                                    x-on:keydown.enter="saveColTitle('{{ $col['id'] }}')"
                                    x-on:keydown.escape="editingColId = null"
                                    style="font-weight:600;font-size:12px;background:var(--bg3);color:var(--text);border:1px solid var(--border2);border-radius:4px;padding:2px 6px;width:100%;outline:none;"
                                >
                            </template>
                            <template x-if="editingColId !== '{{ $col['id'] }}'">
                                <span
                                    style="font-weight:600;font-size:12px;color:var(--text);cursor:pointer;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                                    x-on:dblclick="startEditCol('{{ $col['id'] }}', '{{ $col['title'] }}')"
                                    title="Double-click để đổi tên"
                                >{{ $col['title'] }}</span>
                            </template>

                            <span style="font-size:10px;font-weight:600;padding:1px 7px;border-radius:10px;background:var(--bg3);color:var(--text3);min-width:22px;text-align:center;flex-shrink:0;">
                                {{ count($colTasks) }}
                            </span>
                        </div>
                        <button
                            x-on:click="openAddTask('{{ $col['id'] }}', '{{ $col['title'] }}')"
                            style="background:none;border:none;font-size:18px;color:var(--text3);cursor:pointer;padding:0 2px;line-height:1;transition:color .15s;"
                            onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text3)'"
                            title="Thêm task"
                        >+</button>
                    </div>

                    {{-- Cards --}}
                    <div style="flex:1;padding:4px 10px 10px;display:flex;flex-direction:column;gap:8px;overflow-y:auto;">
                        @foreach($colTasks as $task)
                            @php
                                $labelColor = $labelColors[$task['id'] % count($labelColors)];
                            @endphp
                            <div
                                data-card
                                draggable="true"
                                x-on:dragstart="startDrag($event, {{ $task['id'] }}, '{{ $col['id'] }}')"
                                x-on:dragend="endDrag($event)"
                                x-on:contextmenu="openContextMenu($event, {{ $task['id'] }})"
                                style="background:var(--bg3);border:1px solid var(--border);border-radius:9px;padding:11px 13px;cursor:grab;transition:border-color .15s, box-shadow .15s;"
                                onmouseover="this.style.borderColor='var(--border2)';this.style.boxShadow='0 2px 12px rgba(0,0,0,.25)';"
                                onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='none';"
                            >
                                {{-- Title --}}
                                <div style="font-size:13px;font-weight:500;color:var(--text);margin-bottom:8px;line-height:1.45;">
                                    {{ $task['title'] }}
                                </div>

                                {{-- Label bar + priority --}}
                                <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;flex-wrap:wrap;">
                                    <span style="font-size:10px;padding:2px 9px;border-radius:20px;font-weight:500;text-transform:capitalize;{{ $priorityStyles[$task['priority']] ?? '' }}">
                                        {{ $task['priority'] }}
                                    </span>
                                    @if($task['project_name'])
                                        <span style="font-size:10px;color:var(--text3);background:var(--bg2);padding:2px 8px;border-radius:10px;">
                                            {{ $task['project_name'] }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Footer: due date + checklist + avatars --}}
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;">
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        @if($task['due_date'])
                                            <span style="font-size:10px;display:flex;align-items:center;gap:3px;{{ $task['is_overdue'] ? 'color:var(--red);' : 'color:var(--text3);' }}">
                                                📅 {{ $task['due_date'] }}
                                            </span>
                                        @endif
                                        <span style="font-size:10px;display:flex;align-items:center;gap:3px;{{ $task['checklist'][0] >= $task['checklist'][1] ? 'color:var(--green);' : 'color:var(--text3);' }}">
                                            ☑ {{ $task['checklist'][0] }}/{{ $task['checklist'][1] }}
                                        </span>
                                    </div>

                                    @if(!empty($task['assignees']))
                                        <div style="display:flex;">
                                            @foreach(array_slice($task['assignees'], 0, 3) as $i => $assignee)
                                                @php
                                                    $ac = $avatarColors[$assignee['initial']] ?? ['#5a6478','#e2e8f0'];
                                                @endphp
                                                <div
                                                    title="{{ $assignee['name'] }}"
                                                    style="width:22px;height:22px;border-radius:50%;background:{{ $ac[0] }};color:{{ $ac[1] }};font-size:9px;display:flex;align-items:center;justify-content:center;font-weight:700;border:1.5px solid var(--bg3);{{ $i > 0 ? 'margin-left:-4px;' : '' }}"
                                                >{{ $assignee['initial'] }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        {{-- Add task button at bottom --}}
                        <button
                            x-on:click="openAddTask('{{ $col['id'] }}', '{{ $col['title'] }}')"
                            style="background:none;border:1px dashed var(--border);color:var(--text3);font-size:12px;padding:7px 10px;border-radius:7px;cursor:pointer;text-align:left;width:100%;display:flex;align-items:center;gap:6px;transition:all .15s;"
                            onmouseover="this.style.background='var(--bg2)';this.style.borderColor='var(--border2)';this.style.color='var(--text2)';"
                            onmouseout="this.style.background='none';this.style.borderColor='var(--border)';this.style.color='var(--text3)';"
                        >
                            <span style="font-size:14px;line-height:1;">＋</span> Thêm task
                        </button>
                    </div>
                </div>
            @endforeach

            {{-- Add column button --}}
            <div
                x-on:click="openAddColumn()"
                style="min-width:220px;width:220px;flex-shrink:0;background:rgba(255,255,255,0.03);border:1.5px dashed var(--border);border-radius:12px;padding:16px;cursor:pointer;color:var(--text3);font-size:13px;display:flex;align-items:center;gap:8px;transition:all .15s;height:fit-content;"
                onmouseover="this.style.background='var(--bg2)';this.style.color='var(--text2)';this.style.borderColor='var(--border2)';"
                onmouseout="this.style.background='rgba(255,255,255,0.03)';this.style.color='var(--text3)';this.style.borderColor='var(--border)';"
            >
                <span style="font-size:18px;">＋</span> Thêm cột
            </div>
        </div>

        {{-- ADD TASK MODAL --}}
        <template x-teleport="body">
        <div
            x-show="showAddModal"
            x-transition.opacity
            x-cloak
            style="position:fixed;inset:0;z-index:99999;"
        >
        <div
            x-on:click.self="showAddModal = false"
            style="position:absolute;inset:0;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;"
        >
            <div
                x-on:click.stop
                style="
                    --bg:#0f1117; --bg2:#181c27; --bg3:#1e2335;
                    --border:rgba(255,255,255,0.08); --border2:rgba(255,255,255,0.14);
                    --text:#e2e8f0; --text2:#8892a4; --text3:#5a6478; --green:#22d3a0;
                    background:var(--bg2);border:1px solid var(--border2);border-radius:14px;padding:24px 28px;width:380px;max-width:90vw;font-family:'Inter',sans-serif;
                "
            >
                <div style="font-size:16px;font-weight:600;color:var(--text);margin-bottom:4px;">Thêm task mới</div>
                <div style="font-size:12px;color:var(--text3);margin-bottom:16px;">Cột: <span x-text="addModalColTitle" style="color:var(--text2);font-weight:500;"></span></div>

                <input
                    x-ref="taskTitleInput"
                    x-model="newTaskTitle"
                    x-on:keydown.enter="saveTask()"
                    type="text"
                    placeholder="Tên task..."
                    style="width:100%;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:13px;color:var(--text);outline:none;margin-bottom:12px;box-sizing:border-box;"
                >

                <div style="display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap;">
                    <select x-model="newTaskProject" style="flex:1;min-width:120px;background:var(--bg3);border:1px solid var(--border);border-radius:6px;padding:6px 10px;font-size:12px;color:var(--text2);">
                        <option value="">-- Dự án --</option>
                        @foreach($projects as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <select x-model="newTaskPriority" style="flex:1;min-width:100px;background:var(--bg3);border:1px solid var(--border);border-radius:6px;padding:6px 10px;font-size:12px;color:var(--text2);">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div style="margin-bottom:18px;">
                    <select x-model="newTaskAssignee" style="width:100%;background:var(--bg3);border:1px solid var(--border);border-radius:6px;padding:6px 10px;font-size:12px;color:var(--text2);box-sizing:border-box;">
                        <option value="">-- Gán cho --</option>
                        @foreach($users as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button
                        x-on:click="showAddModal = false"
                        style="background:none;border:1px solid var(--border);color:var(--text2);font-size:13px;padding:7px 18px;border-radius:7px;cursor:pointer;"
                    >Hủy</button>
                    <button
                        x-on:click="saveTask()"
                        style="background:var(--green);border:none;color:#0f1117;font-size:13px;font-weight:600;padding:7px 20px;border-radius:7px;cursor:pointer;"
                    >Thêm task</button>
                </div>
            </div>
        </div>
        </div>
        </template>

        {{-- ADD COLUMN MODAL --}}
        <template x-teleport="body">
        <div
            x-show="showAddColumnModal"
            x-transition.opacity
            x-cloak
            style="position:fixed;inset:0;z-index:99999;"
        >
        <div
            x-on:click.self="showAddColumnModal = false"
            style="position:absolute;inset:0;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;"
        >
            <div
                x-on:click.stop
                style="
                    --bg2:#181c27; --bg3:#1e2335;
                    --border:rgba(255,255,255,0.08); --border2:rgba(255,255,255,0.14);
                    --text:#e2e8f0; --text2:#8892a4; --text3:#5a6478; --green:#22d3a0;
                    background:var(--bg2);border:1px solid var(--border2);border-radius:14px;padding:24px 28px;width:340px;max-width:90vw;font-family:'Inter',sans-serif;
                "
            >
                <div style="font-size:16px;font-weight:600;color:var(--text);margin-bottom:16px;">Thêm cột mới</div>
                <input
                    x-ref="colTitleInput"
                    x-model="newColumnTitle"
                    x-on:keydown.enter="saveColumn()"
                    type="text"
                    placeholder="Tên cột..."
                    style="width:100%;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:13px;color:var(--text);outline:none;margin-bottom:16px;box-sizing:border-box;"
                >
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button
                        x-on:click="showAddColumnModal = false"
                        style="background:none;border:1px solid var(--border);color:var(--text2);font-size:13px;padding:7px 18px;border-radius:7px;cursor:pointer;"
                    >Hủy</button>
                    <button
                        x-on:click="saveColumn()"
                        style="background:var(--green);border:none;color:#0f1117;font-size:13px;font-weight:600;padding:7px 20px;border-radius:7px;cursor:pointer;"
                    >Thêm cột</button>
                </div>
            </div>
        </div>
        </div>
        </template>

        {{-- CONTEXT MENU --}}
        <template x-teleport="body">
        <div
            x-show="contextMenu.visible"
            x-cloak
            x-on:click.away="closeContextMenu()"
            x-on:contextmenu.prevent="closeContextMenu()"
            :style="`position:fixed;top:${contextMenu.y}px;left:${contextMenu.x}px;z-index:99999;`"
            style="
                --bg2:#181c27; --bg3:#1e2335;
                --border:rgba(255,255,255,0.08); --border2:rgba(255,255,255,0.14);
                --text:#e2e8f0; --red:#f87171; --red-dim:rgba(248,113,113,0.10);
                background:var(--bg2);border:1px solid var(--border2);border-radius:8px;padding:4px;min-width:150px;font-family:'Inter',sans-serif;box-shadow:0 8px 24px rgba(0,0,0,.4);
            "
        >
            <button
                x-on:click="deleteTaskFromMenu()"
                style="width:100%;text-align:left;background:none;border:none;color:var(--red);font-size:13px;padding:8px 12px;border-radius:6px;cursor:pointer;display:flex;align-items:center;gap:8px;"
                onmouseover="this.style.background='var(--red-dim)'"
                onmouseout="this.style.background='none'"
            >🗑 Xóa task</button>
        </div>
        </template>
    </div>
</x-filament-panels::page>
