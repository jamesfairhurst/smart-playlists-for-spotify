<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Track extends Model
{
    /**
     * The attributes that should be mutated to dates.
     * 
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'added_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'artist_id', 'album_id', 'spotify_id', 'name', 'added_at'
    ];

    /**
     * User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Artist
     */
    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    /**
     * Album
     */
    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    /**
     * Dynamic order
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDynamicOrderBy($query, $order)
    {
        switch ($order)
        {
            case 'added_desc':
                return $query->orderBy('added_at', 'desc');
                break;
            case 'added_asc':
                return $query->orderBy('added_at', 'asc');
                break;
            case 'artist':
                return $query->orderBy('artist', 'asc');
                break;
            case 'name':
                return $query->orderBy('name', 'asc');
                break;
            case 'random':
                return $query->orderBy(DB::raw('RAND()'));
                break;
        }

        return $query;
    }

}
