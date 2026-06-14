<x-filament-panels::page>
    @php
        $modules = $this->modules;
        $months = ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'];
        $currentMonth = now()->month - 1;
        $totalMonths = 12;
        $editingIndex = $this->editingModuleIndex;
        $editingModule = $editingIndex !== null ? ($modules[$editingIndex] ?? null) : null;
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
            showNewSubRow: false,
            newSubName: '',
            showAddModuleModal: false,
            newModuleName: '',
            newModuleDesc: '',

            openAddModule() {
                this.newModuleName = '';
                this.newModuleDesc = '';
                this.showAddModuleModal = true;
                this.$nextTick(() => this.$refs.moduleNameInput?.focus());
            },
            saveModule() {
                if (!this.newModuleName.trim()) return;
                $wire.addModule(this.newModuleName.trim(), this.newModuleDesc.trim());
                this.showAddModuleModal = false;
            },

            pctColor(pct) {
                if (pct === 100) return '#22d3a0';
                if (pct >= 60) return '#60a5fa';
                if (pct >= 20) return '#f59e0b';
                return '#f87171';
            }
        }"
    >
        {{-- Top bar --}}
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:14px 18px;border:1px solid var(--border);border-bottom:none;border-radius:12px 12px 0 0;background:var(--bg2);">
            <div style="font-size:14px;font-weight:600;color:var(--text);">🗓 Kế hoạch phát triển — {{ date('Y') }}</div>
            <div style="display:flex;gap:14px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--text3);"><div style="width:8px;height:8px;border-radius:2px;background:#22d3a0;"></div>Đang làm</div>
                <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--text3);"><div style="width:8px;height:8px;border-radius:2px;background:#60a5fa;"></div>Sắp tới</div>
                <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--text3);"><div style="width:8px;height:8px;border-radius:2px;background:#a78bfa;"></div>Chờ</div>
                <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--text3);"><div style="width:8px;height:8px;border-radius:2px;background:#94a3b8;"></div>Hoàn thành</div>
            </div>
            <div style="font-size:11px;color:var(--text3);">Nhấn thanh để cập nhật %</div>
            <button
                x-on:click="openAddModule()"
                style="background:var(--bg3);border:1px solid var(--border);color:var(--text2);font-size:12px;padding:6px 16px;border-radius:6px;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all .15s;"
                onmouseover="this.style.borderColor='var(--green)';this.style.color='var(--green)';"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text2)';"
            >＋ Thêm kế hoạch</button>
        </div>

        @if(empty($modules))
            <div style="text-align:center;padding:48px;color:var(--text3);border:1px solid var(--border);border-radius:0 0 12px 12px;background:var(--bg2);">
                <p style="font-size:16px;font-weight:500;color:var(--text2);">Chưa có dữ liệu Gantt</p>
                <p style="font-size:13px;margin-top:4px;">Thêm start_date và due_date cho các task.</p>
            </div>
        @else
            <div style="background:var(--bg2);border:1px solid var(--border);border-radius:0 0 12px 12px;overflow:hidden;">
                <div style="overflow-x:auto;">
                    <div style="min-width:900px;">
                        {{-- Month header --}}
                        <div style="display:grid;grid-template-columns:200px 1fr;border-bottom:1px solid var(--border);background:var(--bg3);">
                            <div style="padding:10px 16px;font-size:11px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;border-right:1px solid var(--border);display:flex;align-items:center;">
                                Module
                            </div>
                            <div style="display:flex;">
                                @foreach($months as $i => $m)
                                    <div style="flex:1;text-align:center;padding:10px 4px;font-size:11px;font-weight:500;border-right:1px solid var(--border);{{ $i === count($months) - 1 ? 'border-right:none;' : '' }}{{ $i === $currentMonth ? 'color:var(--green);' : 'color:var(--text3);' }}">
                                        {{ $m }}{{ $i === $currentMonth ? ' ◀' : '' }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Rows --}}
                        @foreach($modules as $idx => $module)
                            @php
                                $pct = $this->calcPercent($idx);
                                $doneSubs = count(array_filter($module['subtasks'], fn($s) => $s['done']));
                                $totalSubs = count($module['subtasks']);
                                $leftPct = ($module['start'] / $totalMonths) * 100;
                                $widthPct = max((($module['end'] - $module['start']) / $totalMonths) * 100, 3);
                            @endphp
                            <div
                                style="display:grid;grid-template-columns:200px 1fr;border-bottom:1px solid var(--border);min-height:52px;transition:background .15s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'"
                            >
                                {{-- Label --}}
                                <div style="padding:0 16px;border-right:1px solid var(--border);display:flex;flex-direction:column;justify-content:center;gap:3px;min-width:0;">
                                    <div style="font-size:13px;color:var(--text);font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $module['name'] }}">
                                        {{ $module['name'] }}
                                    </div>
                                    <div style="font-size:11px;color:var(--text3);">
                                        {{ $doneSubs }}/{{ $totalSubs }} việc · {{ $pct }}%
                                    </div>
                                </div>

                                {{-- Track --}}
                                <div style="position:relative;display:flex;align-items:center;padding:0 8px;min-width:0;">
                                    {{-- Grid lines --}}
                                    <div style="position:absolute;inset:0;display:flex;pointer-events:none;">
                                        @foreach($months as $i => $m)
                                            <div style="flex:1;border-right:1px solid rgba(255,255,255,0.04);{{ $i === count($months) - 1 ? 'border-right:none;' : '' }}"></div>
                                        @endforeach
                                    </div>

                                    {{-- Bar --}}
                                    <div
                                        wire:click="openModule({{ $idx }})"
                                        style="position:absolute;top:50%;transform:translateY(-50%);height:28px;border-radius:6px;overflow:hidden;left:{{ $leftPct }}%;width:{{ $widthPct }}%;background:{{ $module['color'] }};cursor:pointer;display:flex;align-items:stretch;transition:filter .15s;"
                                        onmouseover="this.style.filter='brightness(1.1)'" onmouseout="this.style.filter='none'"
                                        title="{{ $module['name'] }} — {{ $pct }}% · Click để xem chi tiết"
                                    >
                                        {{-- Progress fill --}}
                                        <div style="height:100%;background:rgba(255,255,255,0.18);width:{{ $pct }}%;flex-shrink:0;"></div>
                                        <span style="position:absolute;inset:0;left:8px;right:8px;display:flex;align-items:center;justify-content:space-between;gap:8px;font-size:11px;font-weight:600;white-space:nowrap;overflow:hidden;color:{{ $module['textColor'] }};pointer-events:none;">
                                            <span style="overflow:hidden;text-overflow:ellipsis;">{{ $module['name'] }}</span>
                                            <span style="flex-shrink:0;">{{ $pct }}%</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- PROGRESS MODAL --}}
        @if($editingModule)
            @php
                $modalPct = $this->calcPercent($editingIndex);
                $modalDoneSubs = count(array_filter($editingModule['subtasks'], fn($s) => $s['done']));
                $modalTotalSubs = count($editingModule['subtasks']);
                $modalTotalWeight = array_sum(array_column($editingModule['subtasks'], 'weight'));
                $pctColor = $modalPct === 100 ? '#22d3a0' : ($modalPct >= 60 ? '#60a5fa' : ($modalPct >= 20 ? '#f59e0b' : '#f87171'));
            @endphp
            <div
                wire:click.self="closeModule"
                style="position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9999;display:flex;align-items:flex-start;justify-content:center;padding-top:60px;overflow-y:auto;"
                x-data="{ showNewSub: false, newSubName: '' }"
            >
                <div
                    wire:click.stop
                    style="background:var(--bg2);border:1px solid var(--border2);border-radius:14px;width:500px;max-width:95vw;margin-bottom:40px;display:flex;flex-direction:column;font-family:'Inter',sans-serif;"
                >
                    {{-- Header --}}
                    <div style="padding:20px 24px 16px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:16px;font-weight:600;color:var(--text);margin-bottom:4px;">{{ $editingModule['name'] }}</div>
                            <div style="font-size:12px;color:var(--text3);display:flex;gap:12px;flex-wrap:wrap;">
                                <span style="display:flex;align-items:center;gap:4px;">📅 T{{ $editingModule['start'] + 1 }} → T{{ $editingModule['end'] }}</span>
                            </div>
                        </div>
                        <button wire:click="closeModule" style="background:none;border:none;color:var(--text3);font-size:20px;cursor:pointer;padding:0 2px;line-height:1;transition:color .15s;" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--text3)'">×</button>
                    </div>

                    {{-- Progress Summary --}}
                    <div style="padding:14px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px;">
                        <div style="font-size:28px;font-weight:700;min-width:64px;line-height:1;color:{{ $pctColor }};">
                            {{ $modalPct }}%
                        </div>
                        <div style="flex:1;">
                            <div style="height:8px;background:var(--bg3);border-radius:4px;overflow:hidden;margin-bottom:5px;">
                                <div style="height:100%;border-radius:4px;transition:width .3s ease;background:{{ $pctColor }};width:{{ $modalPct }}%;"></div>
                            </div>
                            <div style="font-size:11px;color:var(--text3);">
                                {{ $modalDoneSubs }} / {{ $modalTotalSubs }} đầu việc hoàn thành · tổng trọng số {{ $modalTotalWeight }}%
                            </div>
                        </div>
                    </div>

                    {{-- Subtask List --}}
                    <div style="padding:16px 24px;flex:1;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                            <span style="font-size:11px;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--text3);">Danh sách đầu việc</span>
                            <button
                                x-on:click="showNewSub = true; $nextTick(() => $refs.newSubInput?.focus())"
                                style="background:none;border:1px dashed var(--border);color:var(--text3);font-size:11px;padding:3px 10px;border-radius:5px;cursor:pointer;display:flex;align-items:center;gap:4px;transition:all .15s;"
                                onmouseover="this.style.borderColor='var(--green)';this.style.color='var(--green)';"
                                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text3)';"
                            >+ Thêm đầu việc</button>
                        </div>

                        @foreach($editingModule['subtasks'] as $sIdx => $sub)
                            <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;margin-bottom:6px;background:var(--bg3);border:1px solid var(--border);{{ $sub['done'] ? 'opacity:.65;' : '' }}">
                                {{-- Checkbox --}}
                                <div
                                    wire:click="toggleSubtask({{ $editingIndex }}, {{ $sIdx }})"
                                    style="width:18px;height:18px;border-radius:50%;border:1.5px solid {{ $sub['done'] ? 'var(--green)' : 'var(--border2)' }};background:{{ $sub['done'] ? 'var(--green)' : 'var(--bg2)' }};cursor:pointer;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s;"
                                >
                                    @if($sub['done'])
                                        <span style="font-size:10px;color:#0a2e24;font-weight:700;">✓</span>
                                    @endif
                                </div>

                                {{-- Name + Note --}}
                                <div style="flex:1;min-width:0;">
                                    <input
                                        type="text"
                                        value="{{ $sub['name'] }}"
                                        wire:change="updateSubtaskName({{ $editingIndex }}, {{ $sIdx }}, $event.target.value)"
                                        style="width:100%;background:none;border:none;font-size:13px;color:var(--text);font-weight:500;outline:none;padding:0;{{ $sub['done'] ? 'text-decoration:line-through;color:var(--text3);' : '' }}box-sizing:border-box;font-family:'Inter',sans-serif;"
                                        placeholder="Tên đầu việc..."
                                    >
                                    <input
                                        type="text"
                                        value="{{ $sub['note'] }}"
                                        wire:change="updateSubtaskNote({{ $editingIndex }}, {{ $sIdx }}, $event.target.value)"
                                        style="width:100%;background:none;border:none;font-size:11px;color:var(--text3);outline:none;padding:0;margin-top:2px;box-sizing:border-box;font-family:'Inter',sans-serif;line-height:1.5;"
                                        placeholder="Ghi chú thêm..."
                                    >
                                </div>

                                {{-- Weight --}}
                                <div style="position:relative;flex-shrink:0;" x-data="{ open: false }">
                                    <div
                                        x-on:click="open = !open"
                                        style="font-size:11px;font-weight:600;color:var(--text2);background:var(--bg2);border:1px solid var(--border);border-radius:5px;padding:2px 7px;min-width:42px;text-align:center;cursor:pointer;"
                                        title="Trọng số · click để chỉnh"
                                    >{{ $sub['weight'] }}%</div>
                                    <div
                                        x-show="open"
                                        x-on:click.outside="open = false"
                                        x-cloak
                                        style="position:absolute;right:0;top:calc(100% + 4px);background:var(--bg2);border:1px solid var(--border2);border-radius:8px;padding:10px 12px;box-shadow:0 4px 16px rgba(0,0,0,.3);z-index:10;min-width:160px;"
                                    >
                                        <label style="font-size:11px;color:var(--text3);display:block;margin-bottom:5px;">Trọng số: <strong style="color:var(--text2);">{{ $sub['weight'] }}%</strong></label>
                                        <input
                                            type="range" min="5" max="60" step="5" value="{{ $sub['weight'] }}"
                                            wire:change="updateSubtaskWeight({{ $editingIndex }}, {{ $sIdx }}, $event.target.value)"
                                            x-on:change="open = false"
                                            style="width:100%;accent-color:var(--green);"
                                        >
                                    </div>
                                </div>

                                {{-- Delete --}}
                                <button
                                    wire:click="deleteSubtask({{ $editingIndex }}, {{ $sIdx }})"
                                    style="background:none;border:none;color:var(--text3);font-size:14px;cursor:pointer;flex-shrink:0;padding:0;line-height:1;transition:color .15s;"
                                    onmouseover="this.style.color='var(--red)'" onmouseout="this.style.color='var(--text3)'"
                                    title="Xóa"
                                >×</button>
                            </div>
                        @endforeach

                        {{-- New subtask row --}}
                        <div x-show="showNewSub" x-cloak style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:8px;margin-bottom:6px;background:var(--bg3);border:1px dashed var(--border2);">
                            <div style="width:18px;height:18px;border-radius:50%;border:1.5px solid var(--border2);flex-shrink:0;"></div>
                            <input
                                x-ref="newSubInput"
                                x-model="newSubName"
                                x-on:keydown.enter="if(newSubName.trim()) { $wire.addSubtask({{ $editingIndex }}, newSubName.trim()); newSubName = ''; }"
                                x-on:keydown.escape="showNewSub = false; newSubName = ''"
                                type="text"
                                placeholder="Tên đầu việc..."
                                style="flex:1;background:none;border:none;outline:none;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;padding:0;"
                            >
                            <button
                                x-on:click="if(newSubName.trim()) { $wire.addSubtask({{ $editingIndex }}, newSubName.trim()); newSubName = ''; }"
                                style="background:var(--green);border:none;color:#0f1117;font-size:12px;font-weight:600;padding:4px 12px;border-radius:6px;cursor:pointer;"
                            >Thêm</button>
                            <button
                                x-on:click="showNewSub = false; newSubName = ''"
                                style="background:none;border:1px solid var(--border);color:var(--text2);font-size:12px;padding:4px 10px;border-radius:6px;cursor:pointer;"
                            >✕</button>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:10px;">
                        <div style="font-size:11px;color:var(--text3);">Click ô tròn để đánh dấu xong · Click tên để sửa</div>
                        <button
                            wire:click="closeModule"
                            style="background:none;border:1px solid var(--border);color:var(--text2);font-size:13px;padding:7px 18px;border-radius:7px;cursor:pointer;transition:all .15s;"
                            onmouseover="this.style.background='var(--bg3)';this.style.color='var(--text)';"
                            onmouseout="this.style.background='none';this.style.color='var(--text2)';"
                        >Đóng</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ADD MODULE MODAL --}}
        <template x-teleport="body">
        <div
            x-show="showAddModuleModal"
            x-transition.opacity
            x-cloak
            style="position:fixed;inset:0;z-index:99999;"
        >
        <div
            x-on:click.self="showAddModuleModal = false"
            style="position:absolute;inset:0;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;"
        >
            <div
                x-on:click.stop
                style="
                    --bg2:#181c27; --bg3:#1e2335;
                    --border:rgba(255,255,255,0.08); --border2:rgba(255,255,255,0.14);
                    --text:#e2e8f0; --text2:#8892a4; --text3:#5a6478; --green:#22d3a0;
                    background:var(--bg2);border:1px solid var(--border2);border-radius:14px;padding:24px 28px;width:380px;max-width:90vw;font-family:'Inter',sans-serif;
                "
            >
                <div style="font-size:16px;font-weight:600;color:var(--text);margin-bottom:16px;">Thêm kế hoạch mới</div>
                <input
                    x-ref="moduleNameInput"
                    x-model="newModuleName"
                    x-on:keydown.enter="saveModule()"
                    type="text"
                    placeholder="Tên dự án / kế hoạch..."
                    style="width:100%;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:13px;color:var(--text);outline:none;margin-bottom:10px;box-sizing:border-box;"
                >
                <textarea
                    x-model="newModuleDesc"
                    placeholder="Mô tả ngắn (tuỳ chọn)..."
                    rows="2"
                    style="width:100%;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:13px;color:var(--text);outline:none;margin-bottom:16px;box-sizing:border-box;font-family:'Inter',sans-serif;resize:vertical;"
                ></textarea>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button
                        x-on:click="showAddModuleModal = false"
                        style="background:none;border:1px solid var(--border);color:var(--text2);font-size:13px;padding:7px 18px;border-radius:7px;cursor:pointer;"
                    >Hủy</button>
                    <button
                        x-on:click="saveModule()"
                        style="background:var(--green);border:none;color:#0f1117;font-size:13px;font-weight:600;padding:7px 20px;border-radius:7px;cursor:pointer;"
                    >Thêm kế hoạch</button>
                </div>
            </div>
        </div>
        </div>
        </template>
    </div>
</x-filament-panels::page>
