<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Category extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['name'];

    /** @var array<string,string> */
    protected static array $resolvedTables = [];

    public function getTable()
    {
        return static::resolveCategoryTable($this->getConnectionName());
    }

    public static function resolveCategoryTable(?string $connection = null): string
    {
        $connection = $connection ?? (new static)->getConnectionName() ?? config('database.default');

        if (isset(static::$resolvedTables[$connection])) {
            return static::$resolvedTables[$connection];
        }

        $database = DB::connection($connection)->getDatabaseName();
        $referencedTable = DB::connection($connection)->table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', 'products')
            ->where('COLUMN_NAME', 'category_id')
            ->value('REFERENCED_TABLE_NAME');

        if (in_array($referencedTable, ['categories', 'product_categories'], true)) {
            return static::$resolvedTables[$connection] = $referencedTable;
        }

        if (Schema::connection($connection)->hasTable('categories')) {
            return static::$resolvedTables[$connection] = 'categories';
        }

        return static::$resolvedTables[$connection] = 'product_categories';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
