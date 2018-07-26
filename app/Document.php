<?php

namespace App;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    /**
     * @var array - every attribute on this model can be mass assigned, so we indicate that here.
     */
    protected $guarded = [];

    /**
     * Return the user record that is associated with the creator of this document.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id');
    }

    /**
     * Return the user record that is associated with the user who last edited this document.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lastModifier()
    {
        return $this->belongsTo('App\User', 'last_modifier_id');
    }

    /**
     * Return the user record that is associated with the user who last exported this document.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lastExporter()
    {
        return $this->belongsTo('App\User', 'last_exporter_id');
    }

    /**
     * Return the data that is associated with this document.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function data()
    {
        return $this->hasMany('App\Data');
    }

    /**
     * Return the exports that are associated with this document.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exports()
    {
        return $this->hasMany('App\Export');
    }

    /**
     * This function is called whenever a document or its data is updated. It fetches the ID from
     * the currently authenticated user, and assigns it to the last_modifier_id of the document.
     * We then touch the document to guarantee that the updated_at column is changed.
     *
     * @return void
     */
    public function wasUpdated()
    {
        $this->last_modifier_id = Auth::id();
        $this->touch();
        $this->save();
    }

    /**
     * This function is called whenever a document is exported. It fetches the ID from
     * the currently authenticated user, and assigns it to the last_exported_id of the document.
     * It then updated the last_export column to the current time.
     *
     * @return void
     */
    public function wasExported()
    {
        $this->last_exporter_id = Auth::id();
        $this->last_export = now();
        $this->timestamps = false;
        $this->save();
        $this->timestamps = true;
    }

    public function generateCSVContents($parameters)
    {
        // Get our parameters for the document and create our empty array for the contents.
        $name = $parameters['name'];
        $dateFormat = $parameters['dateFormat'];
        $contents = [];

        // For the first row, write the creation date, and last modification date
        $created = Carbon::parse($this->created_at)->format($dateFormat);
        $modified = Carbon::parse($this->updated_at)->format($dateFormat);
        $contents []= [$created, $modified];

        // Next, we put the headers that are supposed to be there
        $contents []= ["key", "value"];

        // Finally, start looping through our data, and writing it to the file. Ultimately, I would
        // imagine that this application would have better type handling than just inferred types, so
        // the values are currently wrapped in a switch statement so that we could (if desired) have
        // custom functionality for each return type.
        foreach ($this->data as $datapoint) {
            $value = null;
            switch ($datapoint->type) {
                case 'string':
                    $value = $datapoint->value;
                    break;

                case 'number':
                    $value = $datapoint->value;
                    break;

                case 'date':
                    $value = date($dateFormat, $datapoint->value);
                    break;

                default:
                    $value = $datapoint->value;
                    break;
            }

            $contents []= [$datapoint->key, $value];
        }

        return $contents;
    }
}
