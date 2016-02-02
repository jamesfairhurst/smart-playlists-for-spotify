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

    /**
     * Get the Key for human display
     * 
     * @param  string $value
     * @return string
     */
    public function getKeyNiceAttribute($value)
    {
        return ucfirst(str_replace('_', ' ', $this->key));
    }

    /**
     * Get the Comparison Operator for human display
     * 
     * @param  string $value
     * @return string
     */
    public function getComparisonOperatorNiceAttribute($value)
    {
        switch ($this->comparison_operator)
        {
            case 'not_contains':
                return 'does not contain';
                break;
            case '=':
                return 'is';
                break;
            case '!=':
                return 'is not';
                break;
            case '>':
                if ($this->key == 'year') {
                    return 'is greater than';
                }

                return 'is after';
                break;
            case '<':
                if ($this->key == 'year') {
                    return 'is less than';
                }

                return 'is before';
                break;
        }

        return str_replace('_', ' ', $this->comparison_operator);
    }

}
