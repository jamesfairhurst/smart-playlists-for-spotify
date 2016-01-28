<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'playlist_id', 'key', 'comparison_operator', 'value'
    ];

    /**
     * Playlist
     */
    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

}
