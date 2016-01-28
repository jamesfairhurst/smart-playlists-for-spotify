<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    /**
     * The attributes that should be mutated to dates.
     * 
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'added_at'];
    protected $dates = [
        'created_at', 'updated_at', 'added_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'album_id', 'spotify_id', 'artist', 'name', 'added_at'
    ];

    /**
     * User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Album
     */
    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
