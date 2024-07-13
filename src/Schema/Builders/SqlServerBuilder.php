<?php

namespace Staudenmeir\LaravelMigrationViews\Schema\Builders;

use Illuminate\Database\Schema\SqlServerBuilder as Base;

class SqlServerBuilder extends Base
{
    use ManagesViews {
        createView as createViewParent;
    }

    /**
     * Create a new view on the schema.
     *
     * @param string $name
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|string $query
     * @param array|null $columns
     * @param bool $orReplace
     * @param bool $materialized
     * @return void
     */
    public function createView($name, $query, ?array $columns = null, $orReplace = false, bool $materialized = false)
    {
        if ($orReplace) {
            $this->dropViewIfExists($name);
        }

        $this->createViewParent($name, $query, $columns);
    }

    /**
     * Drop a view from the schema.
     *
     * @param string $name
     * @param bool $ifExists
     * @return void
     */
    public function dropView($name, $ifExists = false)
    {
        $this->connection->statement(
            $this->grammar->compileDropView($name, $ifExists),
            $ifExists ? [$this->connection->getTablePrefix().$name] : []
        );
    }

    /**
     * Get the column listing for a given view.
     *
     * @param string $name
     * @return array
     */
    public function getViewColumnListing($name)
    {
        $results = $this->connection->selectFromWriteConnection(
            $this->grammar->compileViewColumnListing(),
            [$this->connection->getTablePrefix().$name]
        );

        return array_map(fn ($result) => ((object) $result)->name, $results);
    }
}
