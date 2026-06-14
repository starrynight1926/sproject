<?php

namespace Database\Seeders;

use App\Models\EmployeeGoal;
use App\Models\OrgUnit;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class Company2026Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ---- Cơ cấu tổ chức ----
        $tongCongTy = OrgUnit::create(['name' => 'Tổng công ty', 'type' => 'company']);
        $congTyA = OrgUnit::create(['parent_id' => $tongCongTy->id, 'name' => 'Công ty A', 'type' => 'company']);
        OrgUnit::create(['parent_id' => $tongCongTy->id, 'name' => 'Công ty B', 'type' => 'company', 'sort_order' => 1]);

        $khoiKinhDoanh = OrgUnit::create(['parent_id' => $congTyA->id, 'name' => 'Khối Kinh Doanh', 'type' => 'division']);
        $khoiKdOffline = OrgUnit::create(['parent_id' => $khoiKinhDoanh->id, 'name' => 'Khối KD Offline', 'type' => 'department']);
        $khoiKdOnline = OrgUnit::create(['parent_id' => $khoiKinhDoanh->id, 'name' => 'Khối KD Online', 'type' => 'department', 'sort_order' => 1]);

        $khoiBackOffice = OrgUnit::create(['parent_id' => $congTyA->id, 'name' => 'Khối Back Office', 'type' => 'division', 'sort_order' => 1]);
        $phongKeToan = OrgUnit::create(['parent_id' => $khoiBackOffice->id, 'name' => 'Phòng Kế toán', 'type' => 'department']);
        $phongMarketing = OrgUnit::create(['parent_id' => $khoiBackOffice->id, 'name' => 'Phòng Marketing', 'type' => 'department', 'sort_order' => 1]);
        $phongNhanSu = OrgUnit::create(['parent_id' => $khoiBackOffice->id, 'name' => 'Phòng Nhân sự', 'type' => 'department', 'sort_order' => 2]);

        // ---- Vị trí (sơ đồ chức danh) ----
        Position::create(['name' => 'Tổng giám đốc']);
        Position::create(['name' => 'Cố vấn', 'sort_order' => 1]);

        $truongKhoiKD = Position::create(['name' => 'Trưởng khối KD', 'sort_order' => 2]);
        $truongKhoiKdOffline = Position::create(['parent_id' => $truongKhoiKD->id, 'name' => 'Trưởng khối KD Offline']);
        $truongKhoiKdOnline = Position::create(['parent_id' => $truongKhoiKD->id, 'name' => 'Trưởng khối KD Online', 'sort_order' => 1]);

        $keToanTruong = Position::create(['name' => 'Kế toán trưởng', 'sort_order' => 3]);
        $keToanThue = Position::create(['parent_id' => $keToanTruong->id, 'name' => 'Kế toán thuế']);
        $keToanNoiBo = Position::create(['parent_id' => $keToanTruong->id, 'name' => 'Kế toán nội bộ', 'sort_order' => 1]);

        $truongPhongMkt = Position::create(['name' => 'Trưởng phòng MKT', 'sort_order' => 4]);
        $truongNhomThietKe = Position::create(['parent_id' => $truongPhongMkt->id, 'name' => 'Trưởng nhóm thiết kế']);
        $nhanVienThietKe = Position::create(['parent_id' => $truongNhomThietKe->id, 'name' => 'Nhân viên thiết kế']);
        Position::create(['parent_id' => $truongPhongMkt->id, 'name' => 'Trưởng nhóm content', 'sort_order' => 1]);

        // ---- Tài khoản ----
        $tongGiamDoc = Position::where('name', 'Tổng giám đốc')->first();

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@sprojectmanage.test',
            'password' => Hash::make('lara2026'),
            'org_unit_id' => $tongCongTy->id,
            'position_id' => $tongGiamDoc->id,
        ]);

        $kdoffline = User::create([
            'name' => 'Trưởng khối KD Offline',
            'email' => 'kdoffline@sprojectmanage.test',
            'password' => Hash::make('lara2026'),
            'org_unit_id' => $khoiKdOffline->id,
            'position_id' => $truongKhoiKdOffline->id,
        ]);

        $kdonline = User::create([
            'name' => 'Trưởng khối KD Online',
            'email' => 'kdonline@sprojectmanage.test',
            'password' => Hash::make('lara2026'),
            'org_unit_id' => $khoiKdOnline->id,
            'position_id' => $truongKhoiKdOnline->id,
        ]);

        $thietke1 = User::create([
            'name' => 'Nhân viên thiết kế 1',
            'email' => 'thietke1@sprojectmanage.test',
            'password' => Hash::make('lara2026'),
            'org_unit_id' => $phongMarketing->id,
            'position_id' => $nhanVienThietKe->id,
        ]);

        $thietke2 = User::create([
            'name' => 'Nhân viên thiết kế 2',
            'email' => 'thietke2@sprojectmanage.test',
            'password' => Hash::make('lara2026'),
            'org_unit_id' => $phongMarketing->id,
            'position_id' => $nhanVienThietKe->id,
        ]);

        User::create([
            'name' => 'Nhân sự',
            'email' => 'nhansu@sprojectmanage.test',
            'password' => Hash::make('lara2026'),
            'org_unit_id' => $phongNhanSu->id,
        ]);

        $ketoan = User::create([
            'name' => 'Kế toán',
            'email' => 'ketoan@sprojectmanage.test',
            'password' => Hash::make('lara2026'),
            'org_unit_id' => $phongKeToan->id,
            'position_id' => $keToanNoiBo->id,
        ]);

        // ---- Goal nhân viên ----
        EmployeeGoal::create([
            'user_id' => $thietke1->id,
            'name' => 'Thiết kế file ấn phẩm',
            'type' => 'quantity',
            'unit' => 'file',
            'target_value' => 6000,
            'period' => '2026',
        ]);

        EmployeeGoal::create([
            'user_id' => $thietke2->id,
            'name' => 'Sản xuất video',
            'type' => 'quantity',
            'unit' => 'video',
            'target_value' => 1000,
            'period' => '2026',
        ]);

        EmployeeGoal::create([
            'user_id' => $ketoan->id,
            'position_id' => $keToanThue->id,
            'name' => 'Tất toán hợp đồng',
            'type' => 'quantity',
            'unit' => 'hợp đồng',
            'target_value' => 6000,
            'period' => '2026',
        ]);

        EmployeeGoal::create([
            'user_id' => $ketoan->id,
            'position_id' => $keToanNoiBo->id,
            'name' => 'Hoàn tất BHXH cho nhân sự',
            'type' => 'quantity',
            'unit' => 'nhân sự',
            'target_value' => 300,
            'period' => '2026',
        ]);

        EmployeeGoal::create([
            'user_id' => $ketoan->id,
            'position_id' => $keToanNoiBo->id,
            'name' => 'Trả lương đúng - đủ (300 nhân sự x 12 tháng)',
            'type' => 'quantity',
            'unit' => 'lượt trả lương',
            'target_value' => 3600,
            'period' => '2026',
        ]);
    }
}
