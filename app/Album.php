<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    /**
     * The attributes that should be mutated to dates.
     * 
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'released_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'artist_id', 'track_id', 'spotify_id', 'name', 'released_at', 'spotify_data'
    ];

    /**
     * Artist
     */
    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    /**
     * Tracks
     */
    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

}
