<?php

namespace App\Filament\Pages;

use App\Models\OrgUnit;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class OrgUnitSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Cài đặt hệ thống';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Cơ cấu phòng ban';

    protected static ?string $title = 'Cơ cấu phòng ban';

    protected static ?string $slug = 'org-unit-settings';

    protected string $view = 'filament.pages.org-unit-settings';

    protected static array $typeLabels = [
        'company' => 'Công ty',
        'division' => 'Khối',
        'department' => 'Phòng',
        'team' => 'Nhóm',
    ];

    public function getTypeLabels(): array
    {
        return self::$typeLabels;
    }

    public function getTree(): array
    {
        $units = OrgUnit::orderBy('sort_order')->get()->groupBy('parent_id');

        $build = function ($parentId) use (&$build, $units) {
            return $units->get($parentId, collect())->map(function (OrgUnit $unit) use (&$build) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'type' => $unit->type,
                    'children' => $build($unit->id),
                ];
            })->values()->toArray();
        };

        return $build(null);
    }

    public function addUnit(?int $parentId = null): void
    {
        $maxSort = OrgUnit::where('parent_id', $parentId)->max('sort_order');

        OrgUnit::create([
            'parent_id' => $parentId,
            'name' => 'Đơn vị mới',
            'type' => $parentId ? 'department' : 'company',
            'sort_order' => $maxSort === null ? 0 : $maxSort + 1,
        ]);
    }

    public function deleteUnit(int $id): void
    {
        $unit = OrgUnit::find($id);

        if (! $unit) {
            return;
        }

        // Gỡ liên kết nhân sự/vị trí trước khi xoá để tránh lỗi khoá ngoại
        $unit->users()->update(['org_unit_id' => null]);
        $unit->positions()->update(['org_unit_id' => null]);

        $this->deleteRecursive($unit);
    }

    protected function deleteRecursive(OrgUnit $unit): void
    {
        foreach ($unit->children as $child) {
            $child->users()->update(['org_unit_id' => null]);
            $child->positions()->update(['org_unit_id' => null]);
            $this->deleteRecursive($child);
        }

        $unit->delete();
    }

    public function renameUnit(int $id, string $name): void
    {
        OrgUnit::where('id', $id)->update(['name' => trim($name) ?: 'Đơn vị mới']);
    }

    public function changeType(int $id, string $type): void
    {
        if (! array_key_exists($type, self::$typeLabels)) {
            return;
        }

        OrgUnit::where('id', $id)->update(['type' => $type]);
    }

    /**
     * Nhận cấu trúc cây mới sau khi kéo-thả (id, parent_id, sort_order) và lưu lại.
     */
    public function saveOrder(array $tree): void
    {
        $this->applyOrder($tree, null);
    }

    protected function applyOrder(array $nodes, ?int $parentId): void
    {
        foreach ($nodes as $index => $node) {
            OrgUnit::where('id', $node['id'])->update([
                'parent_id' => $parentId,
                'sort_order' => $index,
            ]);

            if (! empty($node['children'])) {
                $this->applyOrder($node['children'], (int) $node['id']);
            }
        }
    }
}
