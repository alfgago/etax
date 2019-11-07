<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentReference extends Model
{
    use SoftDeletes;

    protected $table = 'document_reference';

    protected $guarded = [];
}
