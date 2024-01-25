<?php

namespace App\Traits\Migration;

trait CreatorOwnerColumnsMigration
{
    public static function creatorOwnerColmuns($table)
    {
        $table->string('creatorable_type')->nullable();
        $table->string('creatorable_id', 36)->nullable();
        $table->string('ownerable_type')->nullable();
        $table->string('ownerable_id', 36)->nullable();
    }
}
