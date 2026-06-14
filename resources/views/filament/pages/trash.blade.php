<x-filament-panels::page>
    @php
        $tasks = $this->getTrashedTasks();
        $statusLabels = [
            'idea' => 'Idea', 'draft' => 'Draft', 'beta' => 'In Progress',
            'edit' => 'Review', 'done' => 'Done',
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
            background:var(--bg); border-radius:14px; padding:20px;
            font-family:'Inter',-apple-system,sans-serif; color:var(--text);
        "
        x-data="{
            confirmEmpty: false,
            confirmDeleteId: null,
        }"
    >
        {{-- Toolbar --}}
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
            <div style="font-size:13px;color:var(--text2);">{{ count($tasks) }} task đã xóa</div>
            <div style="flex:1"></div>
            @if(count($tasks) > 0)
                <button
                    x-on:click="confirmEmpty = true"
                    style="background:var(--red-dim);border:1px solid rgba(248,113,113,0.3);color:var(--red);font-size:12px;padding:6px 16px;border-radius:6px;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all .15s;"
                >🗑 Xóa hết vĩnh viễn</button>
            @endif
        </div>

        {{-- List --}}
        @if(count($tasks) === 0)
            <div style="text-align:center;padding:60px 0;color:var(--text3);font-size:13px;">
                Thùng rác trống.
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach($tasks as $task)
                    <div
                        wire:key="trash-{{ $task['id'] }}"
                        style="background:var(--bg2);border:1px solid var(--border);border-radius:9px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;"
                    >
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:13px;font-weight:500;color:var(--text);margin-bottom:4px;">
                                {{ $task['title'] }}
                            </div>
                            <div style="font-size:11px;color:var(--text3);display:flex;gap:10px;flex-wrap:wrap;">
                                @if($task['project_name'])
                                    <span>{{ $task['project_name'] }}</span>
                                @endif
                                <span>{{ $statusLabels[$task['status']] ?? $task['status'] }}</span>
                                <span>Đã xóa: {{ $task['deleted_at'] }}</span>
                            </div>
                        </div>
                        <div style="display:flex;gap:8px;flex-shrink:0;">
                            <button
                                wire:click="restoreTask({{ $task['id'] }})"
                                style="background:var(--green-dim);border:1px solid rgba(34,211,160,0.3);color:var(--green);font-size:12px;padding:6px 14px;border-radius:6px;cursor:pointer;"
                            >↺ Khôi phục</button>
                            <button
                                x-on:click="confirmDeleteId = {{ $task['id'] }}"
                                style="background:var(--red-dim);border:1px solid rgba(248,113,113,0.3);color:var(--red);font-size:12px;padding:6px 14px;border-radius:6px;cursor:pointer;"
                            >Xóa vĩnh viễn</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- EMPTY TRASH CONFIRM MODAL --}}
        <template x-teleport="body">
        <div
            x-show="confirmEmpty"
            x-transition.opacity
            x-cloak
            style="position:fixed;inset:0;z-index:99999;"
        >
        <div
            x-on:click.self="confirmEmpty = false"
            style="position:absolute;inset:0;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;"
        >
            <div
                x-on:click.stop
                style="
                    --bg2:#181c27; --bg3:#1e2335;
                    --border:rgba(255,255,255,0.08); --border2:rgba(255,255,255,0.14);
                    --text:#e2e8f0; --text2:#8892a4; --text3:#5a6478; --red:#f87171;
                    background:var(--bg2);border:1px solid var(--border2);border-radius:14px;padding:24px 28px;width:360px;max-width:90vw;font-family:'Inter',sans-serif;
                "
            >
                <div style="font-size:16px;font-weight:600;color:var(--text);margin-bottom:8px;">Xóa hết vĩnh viễn?</div>
                <div style="font-size:13px;color:var(--text2);margin-bottom:20px;">Toàn bộ {{ count($tasks) }} task trong thùng rác sẽ bị xóa vĩnh viễn. Không thể hoàn tác.</div>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button
                        x-on:click="confirmEmpty = false"
                        style="background:none;border:1px solid var(--border);color:var(--text2);font-size:13px;padding:7px 18px;border-radius:7px;cursor:pointer;"
                    >Hủy</button>
                    <button
                        x-on:click="$wire.emptyTrash(); confirmEmpty = false"
                        style="background:var(--red);border:none;color:#1a0a0a;font-size:13px;font-weight:600;padding:7px 20px;border-radius:7px;cursor:pointer;"
                    >Xóa hết vĩnh viễn</button>
                </div>
            </div>
        </div>
        </div>
        </template>

        {{-- DELETE ONE CONFIRM MODAL --}}
        <template x-teleport="body">
        <div
            x-show="confirmDeleteId !== null"
            x-transition.opacity
            x-cloak
            style="position:fixed;inset:0;z-index:99999;"
        >
        <div
            x-on:click.self="confirmDeleteId = null"
            style="position:absolute;inset:0;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;"
        >
            <div
                x-on:click.stop
                style="
                    --bg2:#181c27; --bg3:#1e2335;
                    --border:rgba(255,255,255,0.08); --border2:rgba(255,255,255,0.14);
                    --text:#e2e8f0; --text2:#8892a4; --text3:#5a6478; --red:#f87171;
                    background:var(--bg2);border:1px solid var(--border2);border-radius:14px;padding:24px 28px;width:360px;max-width:90vw;font-family:'Inter',sans-serif;
                "
            >
                <div style="font-size:16px;font-weight:600;color:var(--text);margin-bottom:8px;">Xóa vĩnh viễn?</div>
                <div style="font-size:13px;color:var(--text2);margin-bottom:20px;">Task này sẽ bị xóa vĩnh viễn. Không thể hoàn tác.</div>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button
                        x-on:click="confirmDeleteId = null"
                        style="background:none;border:1px solid var(--border);color:var(--text2);font-size:13px;padding:7px 18px;border-radius:7px;cursor:pointer;"
                    >Hủy</button>
                    <button
                        x-on:click="$wire.forceDeleteTask(confirmDeleteId); confirmDeleteId = null"
                        style="background:var(--red);border:none;color:#1a0a0a;font-size:13px;font-weight:600;padding:7px 20px;border-radius:7px;cursor:pointer;"
                    >Xóa vĩnh viễn</button>
                </div>
            </div>
        </div>
        </div>
        </template>
    </div>
</x-filament-panels::page>
