<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'spotify_id', 'name'
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

}
