<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

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
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $order
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDynamicOrderBy($query, $order)
    {
        switch ($order) {
            case 'added_desc':
                return $query->orderBy('added_at', 'desc');
                break;
            case 'added_asc':
                return $query->orderBy('added_at', 'asc');
                break;
            case 'album':
                return $query->join('albums as a', 'a.id', '=', 'tracks.album_id')
                             ->select('tracks.*')
                             ->orderBy('a.name', 'asc');
                break;
            case 'artist':
                return $query->join('artists as a', 'a.id', '=', 'tracks.artist_id')
                             ->select('tracks.*')
                             ->orderBy('a.name', 'asc');
                break;
            case 'name':
                return $query->orderBy('name', 'asc');
                break;
            case 'year_desc':
                return $query->join('albums as a', 'a.id', '=', 'tracks.album_id')
                             ->join('artists as a1', 'a1.id', '=', 'tracks.artist_id')
                             ->select('tracks.*')
                             ->orderBy(DB::raw('DATE_FORMAT(a.released_at,"%Y")'), 'desc')
                             ->orderBy('a1.name', 'asc');
                break;
            case 'year_asc':
                return $query->join('albums as a', 'a.id', '=', 'tracks.album_id')
                             ->join('artists as a1', 'a1.id', '=', 'tracks.artist_id')
                             ->select('tracks.*')
                             ->orderBy(DB::raw('DATE_FORMAT(a.released_at,"%Y")'))
                             ->orderBy('a1.name', 'asc');
                break;
            case 'random':
                return $query->inRandomOrder();
                break;
        }

        return $query;
    }
}
