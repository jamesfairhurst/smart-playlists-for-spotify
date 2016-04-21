<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PlaylistTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $user = factory(App\User::class)->create();
        $this->actingAs($user);
    }

    public function testTracksWithArtistContains()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'artist',
            'comparison_operator' => 'contains',
            'value' => 'AAA'
        ]);

        $artist1 = factory(App\Artist::class)->create([
            'name' => 'AAAaaa'
        ]);
        $artist2 = factory(App\Artist::class)->create([
            'name' => 'BBB'
        ]);
        $artist3 = factory(App\Artist::class)->create([
            'name' => 'CCC'
        ]);

        factory(App\Track::class)->create([
            'artist_id' => $artist1->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist2->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(1, $tracks->count());
        $this->assertEquals('AAAaaa', $tracks->first()->artist->name);
    }

    public function testTracksWithArtistNotContains()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'artist',
            'comparison_operator' => 'not_contains',
            'value' => 'AAA'
        ]);

        $artist1 = factory(App\Artist::class)->create([
            'name' => 'AAAaaa'
        ]);
        $artist2 = factory(App\Artist::class)->create([
            'name' => 'BBB'
        ]);
        $artist3 = factory(App\Artist::class)->create([
            'name' => 'CCC'
        ]);

        factory(App\Track::class)->create([
            'artist_id' => $artist1->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist2->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(2, $tracks->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->artist->name === 'BBB'; })->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->artist->name === 'CCC'; })->count());
    }

    public function testTracksWithArtistEquals()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'artist',
            'comparison_operator' => '=',
            'value' => 'AAA'
        ]);

        $artist1 = factory(App\Artist::class)->create([
            'name' => 'AAA'
        ]);
        $artist2 = factory(App\Artist::class)->create([
            'name' => 'AAAaaa'
        ]);
        $artist3 = factory(App\Artist::class)->create([
            'name' => 'BBB'
        ]);

        factory(App\Track::class)->create([
            'artist_id' => $artist1->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist2->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(1, $tracks->count());
        $this->assertEquals('AAA', $tracks->first()->artist->name);
    }

    public function testTracksWithArtistNotEquals()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'artist',
            'comparison_operator' => '!=',
            'value' => 'AAA'
        ]);

        $artist1 = factory(App\Artist::class)->create([
            'name' => 'AAA'
        ]);
        $artist2 = factory(App\Artist::class)->create([
            'name' => 'AAAaaa'
        ]);
        $artist3 = factory(App\Artist::class)->create([
            'name' => 'BBB'
        ]);

        factory(App\Track::class)->create([
            'artist_id' => $artist1->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist2->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(2, $tracks->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->artist->name === 'AAAaaa'; })->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->artist->name === 'BBB'; })->count());
    }

    public function testTracksWithArtistBeginsWith()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'artist',
            'comparison_operator' => 'begins_with',
            'value' => 'AAA'
        ]);

        $artist1 = factory(App\Artist::class)->create([
            'name' => 'AAAaaa'
        ]);
        $artist2 = factory(App\Artist::class)->create([
            'name' => 'aaaAAA'
        ]);
        $artist3 = factory(App\Artist::class)->create([
            'name' => 'BBB'
        ]);

        factory(App\Track::class)->create([
            'artist_id' => $artist1->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist2->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(1, $tracks->count());
        $this->assertEquals('AAAaaa', $tracks->first()->artist->name);
    }

    public function testTracksWithArtistEndsWith()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'artist',
            'comparison_operator' => 'ends_with',
            'value' => 'AAA'
        ]);

        $artist1 = factory(App\Artist::class)->create([
            'name' => 'AAAaaa'
        ]);
        $artist2 = factory(App\Artist::class)->create([
            'name' => 'aaaAAA'
        ]);
        $artist3 = factory(App\Artist::class)->create([
            'name' => 'BBB'
        ]);

        factory(App\Track::class)->create([
            'artist_id' => $artist1->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist2->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(1, $tracks->count());
        $this->assertEquals('aaaAAA', $tracks->first()->artist->name);
    }

    public function testTracksWithAlbumContains()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'album',
            'comparison_operator' => 'contains',
            'value' => 'AAA'
        ]);

        $artist1 = factory(App\Artist::class)->create();
        $artist2 = factory(App\Artist::class)->create();

        $album1 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'AAAaaa',
            'released_at' => Carbon::now()
        ]);

        $album2 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'BBB',
            'released_at' => Carbon::now()
        ]);

        $album3 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'CCC',
            'released_at' => Carbon::now()
        ]);

        factory(App\Track::class)->create([
            'artist_id' => $artist1->id,
            'album_id' => $album1->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist1->id,
            'album_id' => $album2->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist2->id,
            'album_id' => $album3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(1, $tracks->count());
        $this->assertEquals('AAAaaa', $tracks->first()->album->name);
    }

    public function testTracksWithAlbumNotContains()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'album',
            'comparison_operator' => 'not_contains',
            'value' => 'AAA'
        ]);

        $artist1 = factory(App\Artist::class)->create();
        $artist2 = factory(App\Artist::class)->create();

        $album1 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'AAAaaa',
            'released_at' => Carbon::now()
        ]);

        $album2 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'BBB',
            'released_at' => Carbon::now()
        ]);

        $album3 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'CCC',
            'released_at' => Carbon::now()
        ]);

        factory(App\Track::class)->create([
            'artist_id' => $artist1->id,
            'album_id' => $album1->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist1->id,
            'album_id' => $album2->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist2->id,
            'album_id' => $album3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(2, $tracks->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->album->name === 'BBB'; })->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->album->name === 'CCC'; })->count());
    }

    public function testTracksWithAlbumEquals()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'album',
            'comparison_operator' => '=',
            'value' => 'AAA'
        ]);

        $artist1 = factory(App\Artist::class)->create();
        $artist2 = factory(App\Artist::class)->create();

        $album1 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'AAAaaa',
            'released_at' => Carbon::now()
        ]);

        $album2 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'AAA',
            'released_at' => Carbon::now()
        ]);

        $album3 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'BBB',
            'released_at' => Carbon::now()
        ]);

        factory(App\Track::class)->create([
            'artist_id' => $artist1->id,
            'album_id' => $album1->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist1->id,
            'album_id' => $album2->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist2->id,
            'album_id' => $album3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(1, $tracks->count());
        $this->assertEquals('AAA', $tracks->first()->album->name);
    }

    public function testTracksWithAlbumNotEquals()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'album',
            'comparison_operator' => '!=',
            'value' => 'AAA'
        ]);

        $artist1 = factory(App\Artist::class)->create();
        $artist2 = factory(App\Artist::class)->create();

        $album1 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'AAAaaa',
            'released_at' => Carbon::now()
        ]);

        $album2 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'AAA',
            'released_at' => Carbon::now()
        ]);

        $album3 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'BBB',
            'released_at' => Carbon::now()
        ]);

        factory(App\Track::class)->create([
            'artist_id' => $artist1->id,
            'album_id' => $album1->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist1->id,
            'album_id' => $album2->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist2->id,
            'album_id' => $album3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(2, $tracks->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->album->name === 'AAAaaa'; })->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->album->name === 'BBB'; })->count());
    }

    public function testTracksWithAlbumBeginsWith()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'album',
            'comparison_operator' => 'begins_with',
            'value' => 'AAA'
        ]);

        $artist1 = factory(App\Artist::class)->create();
        $artist2 = factory(App\Artist::class)->create();

        $album1 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'AAAaaa',
            'released_at' => Carbon::now()
        ]);

        $album2 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'aaaAAA',
            'released_at' => Carbon::now()
        ]);

        $album3 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'BBB',
            'released_at' => Carbon::now()
        ]);

        factory(App\Track::class)->create([
            'artist_id' => $artist1->id,
            'album_id' => $album1->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist1->id,
            'album_id' => $album2->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist2->id,
            'album_id' => $album3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(1, $tracks->count());
        $this->assertEquals('AAAaaa', $tracks->first()->album->name);
    }

    public function testTracksWithAlbumEndsWith()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'album',
            'comparison_operator' => 'ends_with',
            'value' => 'AAA'
        ]);

        $artist1 = factory(App\Artist::class)->create();
        $artist2 = factory(App\Artist::class)->create();

        $album1 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'AAAaaa',
            'released_at' => Carbon::now()
        ]);

        $album2 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'aaaAAA',
            'released_at' => Carbon::now()
        ]);

        $album3 = factory(App\Album::class)->create([
            'artist_id' => $artist1->id,
            'name' => 'BBB',
            'released_at' => Carbon::now()
        ]);

        factory(App\Track::class)->create([
            'artist_id' => $artist1->id,
            'album_id' => $album1->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist1->id,
            'album_id' => $album2->id
        ]);
        factory(App\Track::class)->create([
            'artist_id' => $artist2->id,
            'album_id' => $album3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(1, $tracks->count());
        $this->assertEquals('aaaAAA', $tracks->first()->album->name);
    }

    public function testTracksWithYearEquals()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'year',
            'comparison_operator' => '=',
            'value' => '2016'
        ]);

        $album1 = factory(App\Album::class)->create([
            'released_at' => Carbon::parse('first day of Jan 2016')->format('Y-m-d')
        ]);

        $album2 = factory(App\Album::class)->create([
            'released_at' => Carbon::parse('first day of Jan 2015')->format('Y-m-d')
        ]);

        $album3 = factory(App\Album::class)->create([
            'released_at' => Carbon::parse('first day of Jan 2017')->format('Y-m-d')
        ]);

        factory(App\Track::class)->create([
            'album_id' => $album1->id
        ]);
        factory(App\Track::class)->create([
            'album_id' => $album2->id
        ]);
        factory(App\Track::class)->create([
            'album_id' => $album3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(1, $tracks->count());
        $this->assertEquals('2016-01-01', $tracks->first()->album->released_at->format('Y-m-d'));
    }

    public function testTracksWithYearNotEquals()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'year',
            'comparison_operator' => '!=',
            'value' => '2016'
        ]);

        $album1 = factory(App\Album::class)->create([
            'released_at' => Carbon::parse('first day of Jan 2016')->format('Y-m-d')
        ]);

        $album2 = factory(App\Album::class)->create([
            'released_at' => Carbon::parse('first day of Jan 2015')->format('Y-m-d')
        ]);

        $album3 = factory(App\Album::class)->create([
            'released_at' => Carbon::parse('first day of Jan 2017')->format('Y-m-d')
        ]);

        factory(App\Track::class)->create([
            'album_id' => $album1->id
        ]);
        factory(App\Track::class)->create([
            'album_id' => $album2->id
        ]);
        factory(App\Track::class)->create([
            'album_id' => $album3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(2, $tracks->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->album->released_at->format('Y') == '2015'; })->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->album->released_at->format('Y') == '2017'; })->count());
    }

    public function testTracksWithYearGreaterThan()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'year',
            'comparison_operator' => '>',
            'value' => '2015'
        ]);

        $album1 = factory(App\Album::class)->create([
            'released_at' => Carbon::parse('first day of Jan 2016')->format('Y-m-d')
        ]);

        $album2 = factory(App\Album::class)->create([
            'released_at' => Carbon::parse('first day of Jan 2015')->format('Y-m-d')
        ]);

        $album3 = factory(App\Album::class)->create([
            'released_at' => Carbon::parse('first day of Jan 2017')->format('Y-m-d')
        ]);

        factory(App\Track::class)->create([
            'album_id' => $album1->id
        ]);
        factory(App\Track::class)->create([
            'album_id' => $album2->id
        ]);
        factory(App\Track::class)->create([
            'album_id' => $album3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(2, $tracks->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->album->released_at->format('Y') == '2016'; })->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->album->released_at->format('Y') == '2017'; })->count());
    }

    public function testTracksWithYearLessThan()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'year',
            'comparison_operator' => '<',
            'value' => '2017'
        ]);

        $album1 = factory(App\Album::class)->create([
            'released_at' => Carbon::parse('first day of Jan 2016')->format('Y-m-d')
        ]);

        $album2 = factory(App\Album::class)->create([
            'released_at' => Carbon::parse('first day of Jan 2015')->format('Y-m-d')
        ]);

        $album3 = factory(App\Album::class)->create([
            'released_at' => Carbon::parse('first day of Jan 2017')->format('Y-m-d')
        ]);

        factory(App\Track::class)->create([
            'album_id' => $album1->id
        ]);
        factory(App\Track::class)->create([
            'album_id' => $album2->id
        ]);
        factory(App\Track::class)->create([
            'album_id' => $album3->id
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(2, $tracks->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->album->released_at->format('Y') == '2016'; })->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->album->released_at->format('Y') == '2015'; })->count());
    }

    public function testTracksWithDateAddedEquals()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'date_added',
            'comparison_operator' => '=',
            'value' => '02-01-2016'
        ]);

        factory(App\Track::class)->create([
            'added_at' => Carbon::parse('1st Jan 2016')
        ]);
        factory(App\Track::class)->create([
            'added_at' => Carbon::parse('2nd Jan 2016')
        ]);
        factory(App\Track::class)->create([
            'added_at' => Carbon::parse('3rd Jan 2016')
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(1, $tracks->count());
        $this->assertEquals('2016-01-02', $tracks->first()->added_at->format('Y-m-d'));
    }

    public function testTracksWithDateAddedNotEquals()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'date_added',
            'comparison_operator' => '!=',
            'value' => '02-01-2016'
        ]);

        factory(App\Track::class)->create([
            'added_at' => Carbon::parse('1st Jan 2016')
        ]);
        factory(App\Track::class)->create([
            'added_at' => Carbon::parse('2nd Jan 2016')
        ]);
        factory(App\Track::class)->create([
            'added_at' => Carbon::parse('3rd Jan 2016')
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(2, $tracks->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->added_at->format('Y-m-d') == '2016-01-01'; })->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->added_at->format('Y-m-d') == '2016-01-03'; })->count());
    }

    public function testTracksWithDateAddedLessThan()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'date_added',
            'comparison_operator' => '<',
            'value' => '03-01-2016'
        ]);

        factory(App\Track::class)->create([
            'added_at' => Carbon::parse('1st Jan 2016')
        ]);
        factory(App\Track::class)->create([
            'added_at' => Carbon::parse('2nd Jan 2016')
        ]);
        factory(App\Track::class)->create([
            'added_at' => Carbon::parse('3rd Jan 2016')
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(2, $tracks->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->added_at->format('Y-m-d') == '2016-01-01'; })->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->added_at->format('Y-m-d') == '2016-01-02'; })->count());
    }

    public function testTracksWithDateAddedGreaterThan()
    {
        $playlist = factory(App\Playlist::class)->create();
        $rule1 = factory(App\Rule::class)->create([
            'playlist_id' => $playlist->id,
            'key' => 'date_added',
            'comparison_operator' => '>',
            'value' => '01-01-2016'
        ]);

        factory(App\Track::class)->create([
            'added_at' => Carbon::parse('1st Jan 2016')
        ]);
        factory(App\Track::class)->create([
            'added_at' => Carbon::parse('2nd Jan 2016')
        ]);
        factory(App\Track::class)->create([
            'added_at' => Carbon::parse('3rd Jan 2016')
        ]);

        $tracks = $playlist->tracks();

        $this->assertEquals(2, $tracks->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->added_at->format('Y-m-d') == '2016-01-02'; })->count());
        $this->assertEquals(1, $tracks->filter(function ($v, $k) { return $v->added_at->format('Y-m-d') == '2016-01-03'; })->count());
    }

    public function testPlaylistWithLimit()
    {
        $playlist = factory(App\Playlist::class)->create([
            'limit' => 2
        ]);

        factory(App\Track::class, 4)->create([]);

        $tracks = $playlist->tracks();

        $this->assertEquals(2, $tracks->count());
    }
}
