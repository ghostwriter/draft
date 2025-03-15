<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;
use Ghostwriter\Draft\Definition\ForeignIdColumnDefinition;
use Ghostwriter\Draft\Definition\RuntimeException;

use function array_key_exists;
use function array_key_last;
use function compact;
use function dd;
use function mb_strtolower;
use function sprintf;

final class MigrationDefinition implements DefinitionInterface
{
    public function __construct(
        private readonly string $name,
        private array $columns = [],
    ) {}

    public function __call(string $name, array $arguments): self
    {
        //        return match (true) {
        //            default => \dd($name, $arguments),
        //            'foreignIdFor' === $name => $this->addColumn($name, $arguments[1], $arguments),
        //            //            $this->addColumn($name, $arguments),
        //        };
        dd($name, $arguments);

        //        $column = $arguments[0];
        //
        //        return $this->addColumn($name, $column, $arguments);
        //        \dd($name, $arguments);
        //        $firstArgument = $arguments[0] ?? $name;
        //
        //        $this->columns[$name][$firstArgument] = [$name, $arguments];
        //
        return $this;
    }

    public function addColumn(string $type, string $column, array $parameters = []): self
    {
        $this->columns[$column] = [
            $type => $parameters,
        ];

        return $this;
    }

    public function bigIncrements(string $column): self
    {
        return $this->unsignedBigInteger($column, true);
    }

    public function bigInteger(string $column, bool $autoIncrement = false, bool $unsigned = false): self
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'autoIncrement' => $autoIncrement,
            'unsigned' => $unsigned,
        ]);
    }

    public function binary(string $column, ?int $length = null, bool $fixed = false)
    {
        return $this->addColumn('binary', $column, [
            'length' => $length,
            'fixed' => $fixed,
        ]);
    }

    public function boolean(string $column): self
    {
        return $this->addColumn('boolean', $column);
    }

    public function char(string $column, int $length = 255): self
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'length' => $length,
        ]);
    }

    public function charset(string $value): self
    {
        return $this->with('charset', $value);
    }

    public function comment($comment)
    {
        return $this->addCommand('tableComment', compact('comment'));
    }

    public function date(string $column): self
    {
        return $this->addColumn('date', $column);
    }

    public function dateTime(string $column, int $precision = 0): self
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'precision' => $precision,
        ]);
    }

    public function dateTimeTz(string $column, int $precision = 0): self
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'precision' => $precision,
        ]);
    }

    public function decimal(string $column, int $total = 8, int $places = 2): self
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'total' => $total,
            'places' => $places,
        ]);
    }

    public function defaultStringLength(): int
    {
        return 255;
    }

    public function defaultTimePrecision(): int
    {
        return 0;
    }

    public function double(string $column): self
    {
        return $this->addColumn(__FUNCTION__, $column);
    }

    public function enum(string $column, array $allowed): self
    {
        return $this->addColumn('enum', $column, [
            'allowed' => $allowed,
        ]);
    }

    public function float(string $column, int $precision = 53): self
    {
        return $this->addColumn('float', $column, [
            'precision' => $precision,
        ]);
    }

    public function foreignId(string $column): self
    {
        return $this->bigInteger($column, false, true);

        return $this->addColumn(new ForeignIdColumnDefinition($this, [
            'type' => 'bigInteger',
            'name' => $column,
            'autoIncrement' => false,
            'unsigned' => true,
        ]));
    }

    public function foreignIdFor(string $model, ?string $column = null): self
    {
        //        $this->fields[$foreignId] = ['foreignIdFor', $model];

        // $model = 'User';
        $singular = mb_strtolower($model);

        $column ??= $singular . '_id';

        return $this->addColumn(__FUNCTION__, $column, [
            'model' => $model,
            'column' => $column,
        ]);
        $this->columns[$key] = [__FUNCTION__, $model, $foreignId];

        return $this;
    }

    public function foreignUlid(string $column, int $length = 26): self
    {
        return $this->addColumn('foreignUlid', $column, [
            'type' => 'char',
            'name' => $column,
            'length' => $length,
        ]);
    }

    public function foreignUuid(string $column)
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'type' => 'uuid',
            'name' => $column,
        ]);

        return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
            'type' => 'uuid',
            'name' => $column,
        ]));
    }

    public function geography(string $column, ?string $subtype = null, int $srid = 4326): self
    {
        return $this->addColumn('geography', $column, compact('subtype', 'srid'));
    }

    public function geometry(string $column, ?string $subtype = null, int $srid = 4326)
    {
        return $this->addColumn('geometry', $column, [
            'subtype' => $subtype,
            'srid' => $srid,
        ]);
    }

    public function id(string $column = 'id'): self
    {
        $this->bigIncrements($column);
        //        $this->increments($column);

        return $this;
    }

    public function increments(string $column): self
    {
        return $this->unsignedInteger($column, true);
    }

    public function index(array $columns, ?string $name = null, ?string $algorithm = null): self
    {
        return $this->with('index', [
            'columns' => $columns,
            'name' => $name,
            'algorithm' => $algorithm,
        ]);
    }

    public function integer(string $column, bool $autoIncrement = false, bool $unsigned = false): self
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'autoIncrement' => $autoIncrement,
            'unsigned' => $unsigned,
        ]);
    }

    public function ipAddress(string $column = 'ip_address')
    {
        return $this->addColumn('ipAddress', $column);
    }

    public function json(string $column): self
    {
        return $this->addColumn('json', $column);
    }

    public function jsonb(string $column): self
    {
        return $this->addColumn('jsonb', $column);
    }

    public function longText(string $column): self
    {
        return $this->addColumn(__FUNCTION__, $column);
    }

    public function macAddress(string $column = 'mac_address'): self
    {
        return $this->addColumn('macAddress', $column);
    }

    public function mediumIncrements(string $column): self
    {
        return $this->unsignedMediumInteger($column, true);
    }

    public function mediumInteger(string $column, bool $autoIncrement = false, bool $unsigned = false): self
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'autoIncrement' => $autoIncrement,
            'unsigned' => $unsigned,
        ]);
    }

    public function mediumText(string $column): self
    {
        return $this->addColumn(__FUNCTION__, $column);
    }

    public function morphs(string $name, ?string $indexName = null): void
    {
        //        if (Builder::$defaultMorphKeyType === 'uuid') {
        //            $this->uuidMorphs($name, $indexName);
        //        } elseif (Builder::$defaultMorphKeyType === 'ulid') {
        //            $this->ulidMorphs($name, $indexName);
        //        } else {
        $this->numericMorphs($name, $indexName);
        //        }
    }

    public function name(): string
    {
        return $this->name;
    }

    public function nullable(): self
    {
        return $this->with('nullable');
    }

    public function nullableMorphs(string $name, ?string $indexName = null): void
    {
        //        if (Builder::$defaultMorphKeyType === 'uuid') {
        //            $this->nullableUuidMorphs($name, $indexName);
        //        } elseif (Builder::$defaultMorphKeyType === 'ulid') {
        //            $this->nullableUlidMorphs($name, $indexName);
        //        } else {
        $this->nullableNumericMorphs($name, $indexName);
        //        }
    }

    public function nullableNumericMorphs(string $name, ?string $indexName = null): void
    {
        $this->string("{$name}_type")->nullable();

        $this->unsignedBigInteger("{$name}_id")->nullable();

        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    public function nullableTimestamps(int $precision = 0): void
    {
        $this->timestamps($precision);
    }

    public function nullableUlidMorphs(string $name, ?string $indexName = null): void
    {
        $this->string("{$name}_type")->nullable();

        $this->ulid("{$name}_id")->nullable();

        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    public function nullableUuidMorphs(string $name, ?string $indexName = null): void
    {
        $this->string("{$name}_type")->nullable();

        $this->uuid("{$name}_id")->nullable();

        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    public function numericMorphs(string $name, ?string $indexName = null): void
    {
        $this->string("{$name}_type");

        $this->unsignedBigInteger("{$name}_id");

        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    public function rememberToken(): self
    {
        return $this->string('remember_token', 100)->nullable();
    }

    public function set(string $column, array $allowed): self
    {
        return $this->addColumn('set', $column, [
            'allowed' => $allowed,
        ]);
    }

    public function smallIncrements(string $column): self
    {
        return $this->unsignedSmallInteger($column, true);
    }

    public function smallInteger(string $column, bool $autoIncrement = false, bool $unsigned = false): self
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'autoIncrement' => $autoIncrement,
            'unsigned' => $unsigned,
        ]);
    }

    public function softDeletes(): self
    {
        $this->timestamp('deleted_at')->nullable();

        return $this;
    }

    public function softDeletesDatetime(string $column = 'deleted_at', int $precision = 0): self
    {
        return $this->dateTime($column, $precision)->nullable();
    }

    public function softDeletesTz(string $column = 'deleted_at', int $precision = 0): self
    {
        return $this->timestampTz($column, $precision)->nullable();
    }

    public function string(string $column, int $length = 255): self
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'length' => $length,
        ]);
    }

    public function table(): string
    {
        return $this->name;
    }

    public function text(string $column): self
    {
        return $this->addColumn(__FUNCTION__, $column);
    }

    public function time(string $column, int $precision = 0): self
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'precision' => $precision,
        ]);
    }

    public function timeTz(string $column, int $precision = 0): self
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'precision' => $precision,
        ]);
    }

    public function timestamp(string $column, int $precision = 0): self
    {
        if (array_key_exists($column, $this->columns)) {
            throw new RuntimeException(sprintf('Column "%s" already exists', $column));
        }

        return $this->addColumn(__FUNCTION__, $column, [
            'precision' => $precision,
        ]);
    }

    public function timestamps(int $precision = 0): self
    {
        $this->timestamp('created_at', $precision)->nullable();
        $this->timestamp('updated_at', $precision)->nullable();

        return $this;
    }

    public function timestampsTz(int $precision = 0): self
    {
        $this->timestampTz('created_at', $precision)->nullable();
        $this->timestampTz('updated_at', $precision)->nullable();

        return $this;
    }

    public function tinyIncrements(string $column): self
    {
        return $this->unsignedTinyInteger($column, true);
    }

    public function tinyInteger(string $column, bool $autoIncrement = false, bool $unsigned = false): self
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'autoIncrement' => $autoIncrement,
            'unsigned' => $unsigned,
        ]);
    }

    public function tinyText(string $column): self
    {
        return $this->addColumn(__FUNCTION__, $column);
    }

    public function ulid(string $column = 'ulid', int $length = 26): self
    {
        return $this->char($column, $length);
    }

    public function ulidMorphs(string $name, ?string $indexName = null): void
    {
        $this->string("{$name}_type");

        $this->ulid("{$name}_id");

        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    public function unique(): self
    {
        return $this->with('unique');
    }

    public function unsignedBigInteger(string $column, bool $autoIncrement = false): self
    {
        return $this->bigInteger($column, $autoIncrement, true);
    }

    public function unsignedInteger(string $column, bool $autoIncrement = false): self
    {
        return $this->integer($column, $autoIncrement, true);
    }

    public function unsignedMediumInteger(string $column, bool $autoIncrement = false): self
    {
        return $this->mediumInteger($column, $autoIncrement, true);
    }

    public function unsignedSmallInteger(string $column, bool $autoIncrement = false): self
    {
        return $this->smallInteger($column, $autoIncrement, true);
    }

    public function unsignedTinyInteger(string $column, bool $autoIncrement = false): self
    {
        return $this->tinyInteger($column, $autoIncrement, true);
    }

    public function uuid(string $column = 'uuid'): self
    {
        return $this->addColumn(__FUNCTION__, $column);
    }

    public function uuidMorphs(string $name, ?string $indexName = null): void
    {
        $this->string("{$name}_type");

        $this->uuid("{$name}_id");

        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    public function vector(string $column, ?int $dimensions = null): self
    {
        $options = $dimensions ? [
            'dimensions' => $dimensions,
        ] : [];

        return $this->addColumn('vector', $column, $options);
    }

    public function with(string $flag, mixed $value = true): self
    {
        $column = array_key_last($this->columns);

        $last = array_key_last($this->columns[$column]);

        $this->columns[$column][$last][$flag] = $value;

        return $this;
    }

    public function year(string $column): self
    {
        return $this->addColumn('year', $column);
    }

    private function timestampTz(string $column, int $precision = 0): self
    {
        return $this->addColumn(__FUNCTION__, $column, [
            'precision' => $precision,
        ]);
    }
}
