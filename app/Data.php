<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Data extends Model
{
    use SoftDeletes;

    /**
     * @var array - every attribute on this model can be mass assigned, so we indicate that here.
     */
    protected $guarded = [];

    /**
     * Return the document that is associated with this data point
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document()
    {
        return $this->belongsTo('App\Document');
    }
}
