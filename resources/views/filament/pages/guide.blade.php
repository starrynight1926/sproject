<x-filament-panels::page>
    @php
        $steps = [
            [
                'number' => 1,
                'title' => 'Tạo tài khoản',
                'desc' => 'Tạo tài khoản cho từng nhân sự trong hệ thống. Mỗi tài khoản có thể gán sẵn Phòng ban và Vị trí (có thể bổ sung sau khi đã tạo cơ cấu tổ chức ở bước 2).',
                'items' => [
                    'Vào menu Người dùng → Tạo mới.',
                    'Nhập tên, email, mật khẩu cho nhân sự.',
                    'Chọn Phòng ban (Org Unit) và Vị trí (Position) nếu đã có sẵn.',
                    'Phân quyền (role) phù hợp với vai trò của nhân sự.',
                ],
                'link' => ['url' => '/admin/users', 'label' => 'Đi tới Người dùng'],
            ],
            [
                'number' => 2,
                'title' => 'Tạo phòng ban, cơ cấu tổ chức',
                'desc' => 'Xây dựng cây cơ cấu tổ chức: Công ty → Khối/Phòng → Vị trí. Đây là nền tảng để phân bổ công việc/ngân sách ở bước 4.',
                'items' => [
                    'Vào Cơ cấu tổ chức hoặc Thiết lập Phòng ban để tạo các Org Unit theo cấp (company / division / department).',
                    'Vào Thiết lập Vị trí để tạo các Vị trí (Position) và gán vào từng phòng ban.',
                    'Quay lại bước 1 để gán Phòng ban/Vị trí cho từng tài khoản nếu chưa làm.',
                    'Có thể xem lại toàn bộ cây tổ chức tại trang Cơ cấu tổ chức.',
                ],
                'link' => ['url' => '/admin/org-chart', 'label' => 'Xem Cơ cấu tổ chức'],
            ],
            [
                'number' => 3,
                'title' => 'Tạo dự án',
                'desc' => 'Mỗi dự án có chủ sở hữu (owner), trạng thái, thời hạn và tổng ngân sách. Ngân sách dự án là cơ sở để tính % phân bổ ở bước 4.',
                'items' => [
                    'Vào menu Dự án → Tạo mới.',
                    'Nhập tên, mô tả, chọn Owner, trạng thái (active/done/archive), deadline.',
                    'Nhập Tổng ngân sách của dự án — dùng để tự tính % khi phân bổ kinh phí.',
                    '(Tuỳ chọn) Thêm Phases, Goals, Tasks, Work Items cho dự án.',
                ],
                'link' => ['url' => '/admin/projects', 'label' => 'Đi tới Dự án'],
            ],
            [
                'number' => 4,
                'title' => 'Cách phân bổ',
                'desc' => 'Phân bổ Mục tiêu, Nhân sự, Kinh phí của dự án xuống từng Phòng ban, rồi chia tiếp xuống Vị trí/Nhân sự cụ thể.',
                'items' => [
                    'Vào menu Phân bổ → Tạo mới.',
                    'Bước 1 (cấp phòng ban): chọn Dự án, chọn Phòng ban, để trống "Thuộc phân bổ phòng ban", nhập Mục tiêu / Nhân sự / Kinh phí.',
                    'Bước 2 (cấp vị trí/nhân sự): tạo bản ghi mới, chọn "Thuộc phân bổ phòng ban" tương ứng, chọn Vị trí và/hoặc Nhân sự, nhập số liệu chia tiếp cho phần đó.',
                    'Cột "% Ngân sách" sẽ tự tính dựa trên Kinh phí nhập vào so với Tổng ngân sách dự án (có thể nhập vượt 100% để theo dõi/điều chỉnh).',
                ],
                'link' => ['url' => '/admin/allocations', 'label' => 'Đi tới Phân bổ'],
            ],
            [
                'number' => 5,
                'title' => 'Xem báo cáo',
                'desc' => 'Theo dõi tiến độ, cơ cấu công việc và dữ liệu đã xoá thông qua các trang báo cáo.',
                'items' => [
                    'Tree View: xem cây Project → Phase → Task → Work Item.',
                    'Gantt Chart: xem tiến độ theo timeline.',
                    'Kanban Board: theo dõi trạng thái công việc theo cột.',
                    'Thùng rác: xem/khôi phục các Task đã bị xoá.',
                ],
                'link' => ['url' => '/admin/tree-view', 'label' => 'Đi tới Báo cáo (Tree View)'],
            ],
        ];
    @endphp

    <div
        style="
            --bg:#0f1117; --bg2:#181c27; --bg3:#1e2335;
            --border:rgba(255,255,255,0.08); --border2:rgba(255,255,255,0.14);
            --text:#e2e8f0; --text2:#8892a4; --text3:#5a6478;
            --green:#22d3a0; --green-dim:rgba(34,211,160,0.12);
            font-family:'Inter',-apple-system,sans-serif; color:var(--text);
        "
    >
        <div style="background:var(--bg); border-radius:14px; padding:20px;">
            <p style="color:var(--text2); margin:0 0 20px;">
                Quy trình sử dụng hệ thống theo 5 bước cơ bản — thực hiện lần lượt từ trên xuống dưới.
            </p>

            <div style="display:flex; flex-direction:column; gap:14px;">
                @foreach ($steps as $step)
                    <div style="background:var(--bg2); border:1px solid var(--border); border-radius:12px; padding:18px 20px;">
                        <div style="display:flex; align-items:flex-start; gap:14px;">
                            <div style="flex-shrink:0; width:36px; height:36px; border-radius:10px; background:var(--green-dim); color:var(--green); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:16px;">
                                {{ $step['number'] }}
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                                    <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--text);">{{ $step['title'] }}</h3>
                                    @if (! empty($step['link']))
                                        <a href="{{ $step['link']['url'] }}" style="color:var(--green); font-size:13px; font-weight:600; text-decoration:none; white-space:nowrap;">
                                            {{ $step['link']['label'] }} →
                                        </a>
                                    @endif
                                </div>
                                <p style="margin:6px 0 10px; color:var(--text2); font-size:14px;">{{ $step['desc'] }}</p>
                                <ul style="margin:0; padding-left:18px; color:var(--text3); font-size:13px; line-height:1.7;">
                                    @foreach ($step['items'] as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>
