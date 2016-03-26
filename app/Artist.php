<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    /**
     * The attributes that should be mutated to dates.
     * 
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spotify_id', 'name', 'spotify_href', 'spotify_uri'
    ];

    /**
     * Albums
     */
    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    /**
     * Tracks
     */
    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

}
