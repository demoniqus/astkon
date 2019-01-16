<?php
/**
 * Created by PhpStorm.
 * User: demoniqus
 * Date: 15.01.19
 * Time: 9:37
 */

namespace Astkon;


use phpDocumentor\Reflection\Types\Mixed_;

class QueryConfig
{
    /**
     * @var ?string
     */
    public $Condition;

    /**
     * @var ?array
     */
    public $RequiredFields;

    /**
     * @var ?array
     */
    public $Substitution;

    /**
     * @var ?int
     */
    public $Offset;

    /**
     * @var ?int
     */
    public $Limit;

    /**
     * Массив строк вида array('field_name DESC', 'field_name2 ASC')
     * @var ?array
     */
    public $OrderBy;

    public function __construct(
        ?string $condition = null,
        ?array $requiredFields = null,
        ?array $substitution = null,
        ?int $offset = null,
        ?int $limit = null,
        ?array $orderBy = null
    )
    {
        $this->Condition = $condition;
        $this->RequiredFields = $requiredFields;
        $this->Substitution = $substitution;
        $this->Offset = $offset;
        $this->Limit = $limit;
        $this->OrderBy = $orderBy;
    }

    public function Reset() : void {
        $this->Condition = null;
        $this->RequiredFields = null;
        $this->Substitution = null;
        $this->Offset = null;
        $this->Limit = null;
        $this->OrderBy = null;
    }
}