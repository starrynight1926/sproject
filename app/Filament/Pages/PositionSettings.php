<?php

namespace App\Filament\Pages;

use App\Models\OrgUnit;
use App\Models\Position;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class PositionSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Cài đặt hệ thống';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Cơ cấu nhân sự';

    protected static ?string $title = 'Cơ cấu nhân sự';

    protected static ?string $slug = 'position-settings';

    protected string $view = 'filament.pages.position-settings';

    public function getOrgUnitOptions(): array
    {
        $units = OrgUnit::orderBy('sort_order')->get()->groupBy('parent_id');

        $options = ['' => '— Chưa gán phòng ban —'];

        $build = function ($parentId, $prefix) use (&$build, $units, &$options) {
            foreach ($units->get($parentId, collect()) as $unit) {
                $options[$unit->id] = $prefix.$unit->name;
                $build($unit->id, $prefix.'— ');
            }
        };

        $build(null, '');

        return $options;
    }

    public function getTree(): array
    {
        $positions = Position::orderBy('sort_order')->get()->groupBy('parent_id');

        $build = function ($parentId) use (&$build, $positions) {
            return $positions->get($parentId, collect())->map(function (Position $position) use (&$build) {
                return [
                    'id' => $position->id,
                    'name' => $position->name,
                    'org_unit_id' => $position->org_unit_id,
                    'children' => $build($position->id),
                ];
            })->values()->toArray();
        };

        return $build(null);
    }

    public function addPosition(?int $parentId = null): void
    {
        $maxSort = Position::where('parent_id', $parentId)->max('sort_order');

        Position::create([
            'parent_id' => $parentId,
            'name' => 'Vị trí mới',
            'sort_order' => $maxSort === null ? 0 : $maxSort + 1,
        ]);
    }

    public function deletePosition(int $id): void
    {
        $position = Position::find($id);

        if (! $position) {
            return;
        }

        $this->deleteRecursive($position);
    }

    protected function deleteRecursive(Position $position): void
    {
        foreach ($position->children as $child) {
            $this->deleteRecursive($child);
        }

        $position->users()->update(['position_id' => null]);
        $position->goals()->update(['position_id' => null]);
        $position->delete();
    }

    public function renamePosition(int $id, string $name): void
    {
        Position::where('id', $id)->update(['name' => trim($name) ?: 'Vị trí mới']);
    }

    public function assignOrgUnit(int $id, ?string $orgUnitId): void
    {
        Position::where('id', $id)->update(['org_unit_id' => $orgUnitId ?: null]);
    }

    /**
     * Nhận cấu trúc cây mới sau khi kéo-thả (id, sort_order, lồng cấp) và lưu lại.
     */
    public function saveOrder(array $tree): void
    {
        $this->applyOrder($tree, null);
    }

    protected function applyOrder(array $nodes, ?int $parentId): void
    {
        foreach ($nodes as $index => $node) {
            Position::where('id', $node['id'])->update([
                'parent_id' => $parentId,
                'sort_order' => $index,
            ]);

            if (! empty($node['children'])) {
                $this->applyOrder($node['children'], (int) $node['id']);
            }
        }
    }
}
