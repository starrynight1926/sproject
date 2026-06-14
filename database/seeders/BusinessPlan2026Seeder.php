<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\Phase;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkItem;
use Illuminate\Database\Seeder;

class BusinessPlan2026Seeder extends Seeder
{
    public function run(): void
    {
        $owner = User::first();

        $project = Project::create([
            'owner_id' => $owner->id,
            'name' => 'Dự án kinh doanh 2026',
            'description' => 'Kế hoạch kinh doanh năm 2026: 10 tỷ doanh thu (10.000 sản phẩm), 8 tháng, 1000 nhân sự, kinh phí 3 tỷ.',
            'status' => 'active',
            'deadline' => '2026-08-31',
        ]);

        // Mục tiêu tổng dự án
        Goal::create(['project_id' => $project->id, 'metric_type' => 'target', 'name' => 'Doanh thu (10.000 sản phẩm)', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 10_000_000_000, 'current_value' => 1_600_000_000]);
        Goal::create(['project_id' => $project->id, 'metric_type' => 'time', 'name' => 'Thời gian thực hiện', 'type' => 'quantity', 'unit' => 'tháng', 'target_value' => 8, 'current_value' => 1.5]);
        Goal::create(['project_id' => $project->id, 'metric_type' => 'resource', 'name' => 'Nhân sự', 'type' => 'quantity', 'unit' => 'người', 'target_value' => 1000, 'current_value' => 750]);
        Goal::create(['project_id' => $project->id, 'metric_type' => 'cost', 'name' => 'Kinh phí', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 3_000_000_000, 'current_value' => 700_000_000]);

        // 1. Kinh Doanh Offline
        $offline = Task::create(['project_id' => $project->id, 'title' => 'Kinh Doanh Offline', 'status' => 'beta', 'priority' => 'high', 'start_date' => '2026-01-01', 'due_date' => '2026-08-31', 'sort_order' => 1]);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'metric_type' => 'target', 'name' => 'Doanh thu Offline (8.000 sản phẩm)', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 8_000_000_000, 'current_value' => 1_200_000_000]);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'metric_type' => 'time', 'name' => 'Thời gian', 'type' => 'quantity', 'unit' => 'tháng', 'target_value' => 8, 'current_value' => 1.5]);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'metric_type' => 'resource', 'name' => 'Nhân sự', 'type' => 'quantity', 'unit' => 'người', 'target_value' => 900, 'current_value' => 700]);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'metric_type' => 'cost', 'name' => 'Kinh phí', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 1_500_000_000, 'current_value' => 400_000_000]);

        // Phase 1 của Kinh Doanh Offline: Quý 1+2
        $phase1 = Phase::create(['project_id' => $project->id, 'name' => 'Offline - Quý 1+2 (01/01/2026 - 30/06/2026)', 'sort_order' => 1, 'start_date' => '2026-01-01', 'end_date' => '2026-06-30']);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'phase_id' => $phase1->id, 'metric_type' => 'target', 'name' => 'Doanh thu Phase 1', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 3_000_000_000, 'current_value' => 1_200_000_000]);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'phase_id' => $phase1->id, 'metric_type' => 'time', 'name' => 'Thời gian Phase 1', 'type' => 'quantity', 'unit' => 'tháng', 'target_value' => 6, 'current_value' => 1.5]);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'phase_id' => $phase1->id, 'metric_type' => 'resource', 'name' => 'Nhân sự Phase 1', 'type' => 'quantity', 'unit' => 'người', 'target_value' => 700, 'current_value' => 700]);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'phase_id' => $phase1->id, 'metric_type' => 'cost', 'name' => 'Kinh phí Phase 1', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 800_000_000, 'current_value' => 400_000_000]);

        // Đầu việc phát sinh trong Phase 1 (vượt kinh phí được phân bổ)
        $wiMienBac = WorkItem::create(['task_id' => $offline->id, 'title' => 'Chiến dịch bán hàng miền Bắc', 'status' => 'beta', 'start_date' => '2026-01-01', 'due_date' => '2026-06-30', 'sort_order' => 1]);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'work_item_id' => $wiMienBac->id, 'metric_type' => 'target', 'name' => 'Doanh thu miền Bắc', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 1_800_000_000, 'current_value' => 700_000_000]);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'work_item_id' => $wiMienBac->id, 'metric_type' => 'cost', 'name' => 'Kinh phí miền Bắc', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 500_000_000, 'current_value' => 250_000_000]);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'work_item_id' => $wiMienBac->id, 'metric_type' => 'resource', 'name' => 'Nhân sự miền Bắc', 'type' => 'quantity', 'unit' => 'người', 'target_value' => 400, 'current_value' => 400]);

        $wiMienNam = WorkItem::create(['task_id' => $offline->id, 'title' => 'Mở rộng đại lý miền Nam (phát sinh)', 'status' => 'idea', 'start_date' => '2026-03-01', 'due_date' => '2026-06-30', 'sort_order' => 2]);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'work_item_id' => $wiMienNam->id, 'metric_type' => 'target', 'name' => 'Doanh thu miền Nam', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 1_200_000_000, 'current_value' => 500_000_000]);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'work_item_id' => $wiMienNam->id, 'metric_type' => 'cost', 'name' => 'Kinh phí miền Nam', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 700_000_000, 'current_value' => 150_000_000]);
        Goal::create(['project_id' => $project->id, 'task_id' => $offline->id, 'work_item_id' => $wiMienNam->id, 'metric_type' => 'resource', 'name' => 'Nhân sự miền Nam', 'type' => 'quantity', 'unit' => 'người', 'target_value' => 300, 'current_value' => 300]);

        // 2. Kinh Doanh Online
        $online = Task::create(['project_id' => $project->id, 'title' => 'Kinh Doanh Online', 'status' => 'beta', 'priority' => 'medium', 'start_date' => '2026-01-01', 'due_date' => '2026-08-31', 'sort_order' => 2]);
        Goal::create(['project_id' => $project->id, 'task_id' => $online->id, 'metric_type' => 'target', 'name' => 'Doanh thu Online (2.000 sản phẩm)', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 2_000_000_000, 'current_value' => 350_000_000]);
        Goal::create(['project_id' => $project->id, 'task_id' => $online->id, 'metric_type' => 'time', 'name' => 'Thời gian', 'type' => 'quantity', 'unit' => 'tháng', 'target_value' => 8, 'current_value' => 1.5]);
        Goal::create(['project_id' => $project->id, 'task_id' => $online->id, 'metric_type' => 'resource', 'name' => 'Nhân sự', 'type' => 'quantity', 'unit' => 'người', 'target_value' => 50, 'current_value' => 45]);
        Goal::create(['project_id' => $project->id, 'task_id' => $online->id, 'metric_type' => 'cost', 'name' => 'Kinh phí', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 500_000_000, 'current_value' => 120_000_000]);

        // 3. Back Office
        $backOffice = Task::create(['project_id' => $project->id, 'title' => 'Back Office', 'status' => 'beta', 'priority' => 'medium', 'start_date' => '2026-01-01', 'due_date' => '2026-08-31', 'sort_order' => 3]);
        Goal::create(['project_id' => $project->id, 'task_id' => $backOffice->id, 'metric_type' => 'target', 'name' => 'Hoàn thành nhiệm vụ', 'type' => 'percent', 'unit' => '%', 'target_value' => 100, 'current_value' => 30]);
        Goal::create(['project_id' => $project->id, 'task_id' => $backOffice->id, 'metric_type' => 'time', 'name' => 'Thời gian', 'type' => 'quantity', 'unit' => 'tháng', 'target_value' => 8, 'current_value' => 1.5]);
        Goal::create(['project_id' => $project->id, 'task_id' => $backOffice->id, 'metric_type' => 'resource', 'name' => 'Nhân sự', 'type' => 'quantity', 'unit' => 'người', 'target_value' => 50, 'current_value' => 50]);
        Goal::create(['project_id' => $project->id, 'task_id' => $backOffice->id, 'metric_type' => 'cost', 'name' => 'Kinh phí', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 200_000_000, 'current_value' => 40_000_000]);

        // 4. Cộng tác viên
        $ctv = Task::create(['project_id' => $project->id, 'title' => 'Cộng tác viên', 'status' => 'idea', 'priority' => 'low', 'start_date' => '2026-01-01', 'due_date' => '2026-08-31', 'sort_order' => 4]);
        Goal::create(['project_id' => $project->id, 'task_id' => $ctv->id, 'metric_type' => 'target', 'name' => 'Hoàn thành nhiệm vụ', 'type' => 'percent', 'unit' => '%', 'target_value' => 100, 'current_value' => 20]);
        Goal::create(['project_id' => $project->id, 'task_id' => $ctv->id, 'metric_type' => 'time', 'name' => 'Thời gian', 'type' => 'quantity', 'unit' => 'tháng', 'target_value' => 8, 'current_value' => 1.5]);
        Goal::create(['project_id' => $project->id, 'task_id' => $ctv->id, 'metric_type' => 'resource', 'name' => 'Nhân sự', 'type' => 'quantity', 'unit' => 'người', 'target_value' => 50, 'current_value' => 50]);
        Goal::create(['project_id' => $project->id, 'task_id' => $ctv->id, 'metric_type' => 'cost', 'name' => 'Kinh phí', 'type' => 'money', 'unit' => 'VNĐ', 'target_value' => 200_000_000, 'current_value' => 30_000_000]);
    }
}
