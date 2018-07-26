<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Export extends Model
{
    use SoftDeletes;

    /**
     * @var array - every attribute on this model can be mass assigned, so we indicate that here.
     */
    protected $guarded = [];

    /**
     * Return the document associated with this export
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document()
    {
        return $this->belongsTo('App\Document');
    }

    /**
     * Return the user who created this export
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
