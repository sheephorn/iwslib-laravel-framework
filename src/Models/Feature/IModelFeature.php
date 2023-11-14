<?php

namespace IwslibLaravel\Models\Feature;

use IwslibLaravel\Models\HistoryModel;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * @property ?Carbon $updated_at
 * @property ?Carbon $created_at
 * @property ?string $updated_by
 * @property ?string $created_by
 */
interface IModelFeature
{

    public static function getBuilder(string $name = 'main'): Builder;

    public static function getTableName(): string;

    public function copy(IModelFeature $from): static;

    public function getAttributeKeys(): array;

    public function isNotSavedModel(): bool;

    public function getHistory(): HistoryModel|null;

    /**
     * モデルの和名を取得する
     *
     * @return string
     */
    public function getModelName(): string;

    public function getChangeLogMessage($before, $after): string|null;
}
