<?php

namespace App\Filament\Pages;

use App\Models\OrgUnit;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class OrgChart extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Thiết lập tổ chức';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Cơ cấu tổ chức';

    protected static ?string $title = 'Cơ cấu tổ chức';

    protected static ?string $slug = 'org-chart';

    protected string $view = 'filament.pages.org-chart';

    protected static array $typeLabels = [
        'company' => 'Công ty',
        'division' => 'Khối',
        'department' => 'Phòng',
    ];

    protected static array $typeIcons = [
        'company' => '🏢',
        'division' => '🗂️',
        'department' => '📁',
    ];

    public function getOrgTree(): array
    {
        $units = OrgUnit::with('users')->orderBy('sort_order')->get()->groupBy('parent_id');

        $roots = $units->get(null, collect());

        return $roots->map(fn ($unit) => $this->buildOrgNode($unit, $units))->values()->toArray();
    }

    protected function buildOrgNode(OrgUnit $unit, $units): array
    {
        $children = $units->get($unit->id, collect())
            ->map(fn ($child) => $this->buildOrgNode($child, $units))
            ->values()
            ->toArray();

        foreach ($unit->users as $user) {
            $children[] = [
                'label' => '🧑 '.$user->name,
                'subtitle' => $user->position->name ?? null,
                'badges' => [],
                'level' => 'leaf',
                'children' => [],
            ];
        }

        $icon = self::$typeIcons[$unit->type] ?? '📁';

        return [
            'label' => $icon.' '.$unit->name,
            'subtitle' => (self::$typeLabels[$unit->type] ?? $unit->type).' · '.$unit->users->count().' nhân sự',
            'badges' => [],
            'level' => $unit->parent_id === null ? 'root' : 'node',
            'children' => $children,
        ];
    }
}
