<?php

namespace App\Filament\Support;

use Illuminate\Contracts\Support\Arrayable;
use Filament\Forms\Components\Select;
use ReflectionProperty;

class SelectCreateOption
{
    const VALUE = '__create_new_option__';

    /**
     * Adds a "+ Tạo mới ..." row to the bottom of the select's dropdown
     * (instead of a separate "+" button next to the field). Selecting it
     * opens the same create-option modal configured via createOptionForm().
     */
    public static function inline(Select $select, string $label = '+ Tạo mới...'): Select
    {
        self::appendToOptions($select, $label);
        self::appendToSearchResults($select, $label);

        return $select
            ->live()
            ->createOptionAction(static fn ($action) => $action->extraAttributes(['style' => 'display: none']))
            ->afterStateUpdated(static function ($state, callable $set, Select $component) {
                $statePath = $component->getStatePath();

                if ($component->isMultiple()) {
                    if (! is_array($state) || ! in_array(self::VALUE, $state, true)) {
                        return;
                    }

                    $set($component, array_values(array_diff($state, [self::VALUE])));
                } else {
                    if ($state !== self::VALUE) {
                        return;
                    }

                    $set($component, null);
                }

                $component->getLivewire()->mountAction(
                    $component->getCreateOptionActionName(),
                    [],
                    context: [
                        'schemaComponent' => $component->getKey(),
                    ],
                );
            });
    }

    private static function appendToOptions(Select $select, string $label): void
    {
        $property = new ReflectionProperty(Select::class, 'options');
        $property->setAccessible(true);
        $original = $property->getValue($select);

        $select->options(function (Select $component) use ($original, $label) {
            $options = $original instanceof \Closure
                ? $component->evaluate($original)
                : ($original ?? []);

            if ($options instanceof Arrayable) {
                $options = $options->toArray();
            }

            $options ??= [];

            if ($component->hasCreateOptionActionFormSchema()) {
                $options[self::VALUE] = $label;
            }

            return $options;
        });
    }

    private static function appendToSearchResults(Select $select, string $label): void
    {
        $property = new ReflectionProperty(Select::class, 'getSearchResultsUsing');
        $property->setAccessible(true);
        $original = $property->getValue($select);

        $select->getSearchResultsUsing(function (Select $component, ?string $search) use ($original, $label) {
            $results = $original
                ? $component->evaluate($original, [
                    'query' => $search,
                    'search' => $search,
                    'searchQuery' => $search,
                ])
                : [];

            if ($results instanceof Arrayable) {
                $results = $results->toArray();
            }

            $results ??= [];

            if ($component->hasCreateOptionActionFormSchema()) {
                $results[self::VALUE] = $label;
            }

            return $results;
        });
    }
}
