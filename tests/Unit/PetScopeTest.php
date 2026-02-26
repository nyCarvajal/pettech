<?php

namespace Tests\Unit;

use App\Models\Pet;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

class PetScopeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $db = new DB();
        $db->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $db->setAsGlobal();
        $db->bootEloquent();

        Model::setConnectionResolver($db->getDatabaseManager());
    }

    public function test_scope_search_applies_like_filters(): void
    {
        $query = Pet::query()->search('luna');
        $sql = $query->toSql();

        $this->assertStringContainsString('"name" like', strtolower($sql));
    }
}
