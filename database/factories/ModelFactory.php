<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'spotify_id' => $faker->uuid,
        'name' => $faker->name,
        // 'email' => $faker->email,
        // 'password' => bcrypt(str_random(10)),
        // 'remember_token' => str_random(10),
    ];
});

$factory->define(App\Track::class, function (Faker\Generator $faker) {
    return [
        'user_id' => 1,
        'artist_id' => function () {
            return factory(App\Artist::class)->create()->id;
        },
        'album_id' => function () {
            return factory(App\Album::class)->create()->id;
        },
        'spotify_id' => $faker->uuid,
        'name' => $faker->name,
        'added_at' => $faker->dateTimeThisYear,
    ];
});

$factory->define(App\Album::class, function (Faker\Generator $faker) {
    return [
        'artist_id' => function () {
            return factory(App\Artist::class)->create()->id;
        },
        'spotify_id' => $faker->uuid,
        'name' => $faker->name,
        'released_at' => $faker->date(),
    ];
});

$factory->define(App\Artist::class, function (Faker\Generator $faker) {
    return [
        'spotify_id' => $faker->uuid,
        'name' => $faker->name,
    ];
});

$factory->define(App\Playlist::class, function (Faker\Generator $faker) {
    return [
        'user_id' => 1,
        'spotify_id' => $faker->uuid,
        'name' => $faker->name,
        'order' => 'added_desc',
        'limit' => 5
    ];
});

$factory->define(App\Rule::class, function (Faker\Generator $faker) {
    return [
        'playlist_id' => 1,
        'key' => 'artist',
        'comparison_operator' => 'contains',
        'value' => $faker->name
    ];
});