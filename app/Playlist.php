<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon\Carbon;

class Playlist extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'spotify_id', 'name', 'order', 'limit'
    ];

    /**
     * User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Rules
     */
    public function rules()
    {
        return $this->hasMany(Rule::class);
    }

    /**
     * Get the Order for human display
     * 
     * @param  string $value
     * @return string
     */
    public function getOrderNiceAttribute($value)
    {
        return ucwords(str_replace('_', ' ', $this->order));
    }

    /**
     * Get Tracks in this Playlist
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTracks()
    {
        // Get all Tracks
        $tracks = Track::with('album','artist')
            ->where('user_id', Auth::user()->id)
            // ->whereHas('album', function ($query) {
            //     $query->where(DB::raw('YEAR(released_at)'), 2015);
            // })
            // ->orderBy('added_at', 'desc')
            ->dynamicOrderBy($this->order)
            ->get();

        // Sort Tracks & sort by decade
        // $grouped = $tracks->sortBy('album.released_at')->groupBy(function ($item, $key) {
        //     return (int) floor($item->album->released_at->format('Y') / 10) * 10;
        // });

        // Loop and create decade Playlist & add Tracks
        // foreach ($grouped->toArray() as $decade => $tracks) {
        //     $playlist = $api->createUserPlaylist(Auth::user()->spotify_id, [
        //         'name' => $decade . 's'
        //     ]);

        //     foreach ($tracks as $track) {
        //         $api->addUserPlaylistTracks(Auth::user()->spotify_id, $playlist->id, $track['spotify_id']);
        //     }
        // }

        // Filter Tracks by Playlist Rules
        foreach ($this->rules as $rule) {
            switch ($rule->key)
            {
                case 'artist':
                    $tracks = $tracks->filter(function ($track, $key) use ($rule) {
                        if ($rule->comparison_operator == 'contains') {
                            return (stripos($track->artist->name, $rule->value) !== false);
                        } elseif ($rule->comparison_operator == 'not_contains') {
                            return (stripos($track->artist->name, $rule->value) === false);
                        } elseif ($rule->comparison_operator == '=') {
                            return (strcmp($track->artist->name, $rule->value) === 0);
                        } elseif ($rule->comparison_operator == '!=') {
                            return (strcmp($track->artist->name, $rule->value) !== 0);
                        } elseif ($rule->comparison_operator == 'begins_with') {
                            return (stripos($track->artist->name, $rule->value) === 0);
                        } elseif ($rule->comparison_operator == 'ends_with') {
                            return (stripos(strrev($track->artist->name), strrev($rule->value)) === 0);
                        }
                    });
                    break;
                case 'album':
                    $tracks = $tracks->filter(function ($track, $key) use ($rule) {
                        if ($rule->comparison_operator == 'contains') {
                            return (stripos($track->album->name, $rule->value) !== false);
                        } elseif ($rule->comparison_operator == 'not_contains') {
                            return (stripos($track->album->name, $rule->value) === false);
                        } elseif ($rule->comparison_operator == '=') {
                            return (strcmp($track->album->name, $rule->value) === 0);
                        } elseif ($rule->comparison_operator == '!=') {
                            return (strcmp($track->album->name, $rule->value) !== 0);
                        } elseif ($rule->comparison_operator == 'begins_with') {
                            return (stripos($track->album->name, $rule->value) === 0);
                        } elseif ($rule->comparison_operator == 'ends_with') {
                            return (stripos(strrev($track->album->name), strrev($rule->value)) === 0);
                        }
                    });
                    break;
                case 'date_added':
                    $tracks = $tracks->filter(function ($track, $key) use ($rule) {
                        if ($rule->comparison_operator == '=') {
                            return $track->added_at->hour(0)->minute(0)->second(0)->eq(Carbon::parse($rule->value));
                        } elseif ($rule->comparison_operator == '!=') {
                            return $track->added_at->hour(0)->minute(0)->second(0)->ne(Carbon::parse($rule->value));
                        } elseif ($rule->comparison_operator == '<') {
                            return $track->added_at->hour(0)->minute(0)->second(0)->lt(Carbon::parse($rule->value));
                        } elseif ($rule->comparison_operator == '>') {
                            return $track->added_at->hour(0)->minute(0)->second(0)->gt(Carbon::parse($rule->value));
                        }
                    });
                    break;
                case 'year':
                    $tracks = $tracks->filter(function ($track, $key) use ($rule) {
                        if ($rule->comparison_operator == '=') {
                            return $track->album->released_at->format('Y') == $rule->value;
                        } elseif ($rule->comparison_operator == '!=') {
                            return $track->album->released_at->format('Y') != $rule->value;
                        } elseif ($rule->comparison_operator == '<') {
                            return $track->album->released_at->format('Y') < $rule->value;
                        } elseif ($rule->comparison_operator == '>') {
                            return $track->album->released_at->format('Y') > $rule->value;
                        }
                    });
                    break;
            }
        }

        /*$tracks = $tracks->sortBy(function ($track, $key) {
            return $track->album->released_at->format('Y');
        });*/
        /*$tracks = $tracks->sortBy(function ($track, $key) {
            return sprintf('%-12s%s', $track->album->released_at->format('Y'), $track->album->name);
        });*/

        // Sort the playlist manually
        /*if ($this->order == 'album') {
            // Compare Album name
            $tracks = $tracks->sort(function ($a, $b) {
                return strcmp($a->album->name, $b->album->name);
            });
        } elseif ($this->order == 'year_asc') {
            $tracks = $tracks->sort(function ($first, $second) {
                // Track Years are the same so sort by Artist
                if (strcmp($first->album->released_at->format('Y'), $second->album->released_at->format('Y')) === 0) {
                    return strcmp($first->artist, $second->artist);
                }

                // Track Years are the same so sort by Album
                // if (strcmp($first->album->released_at->format('Y'), $second->album->released_at->format('Y')) === 0) {
                //     return strcmp($first->album->name, $second->album->name);
                // }

                // Compare Years
                return strcmp($first->album->released_at->format('Y'), $second->album->released_at->format('Y'));
            });
        } elseif ($this->order == 'year_desc') {
            $tracks = $tracks->sort(function ($first, $second) {
                // Track Years are the same so sort by Artist
                if (strcmp($first->album->released_at->format('Y'), $second->album->released_at->format('Y')) === 0) {
                    return strcmp($first->artist, $second->artist);
                }

                // Compare Years 2nd then 1st to reverse
                return strcmp($second->album->released_at->format('Y'), $first->album->released_at->format('Y'));
            });
        }*/

        // Finally limit the playlist
        if ($this->limit) {
            $tracks = $tracks->take($this->limit);
        }

        return $tracks;
    }

}
