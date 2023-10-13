<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\YamlFrontMatter\YamlFrontMatter;

use Illuminate\Support\Facades\File;

class Post{

    public $title;

    public $excerpt;

    public $date;

    public $body;

    public $slug;

    public function __construct($title, $excerpt, $date, $body, $slug){
        $this-> title = $title;
        $this-> excerpt = $excerpt;
        $this-> date = $date;
        $this ->body = $body;
        $this -> slug = $slug;
    }
    public static function all(){
        return cache()->rememberForever('post.all',function(){
            return collect(File::files(resource_path("posts/")))
            ->map(function($file){
                return YamlFrontMatter::parseFile($file);
            })
            ->map(function($document) {
                return new Post(
                    $document->title,
                    $document->excerpt,
                    $document->date,
                    $document->body(),
                    $document->slug
                );
            })
            ->sortByDesc('date');
        });



    }
    public static function find($slug){

        return static::all()->FirstWhere('slug', $slug);
        

    }

    public static function findOrFail($slug){

        $post =  static::find($slug);
        if (!$post){
            throw new ModelNotFoundException();
        }
        return $post;

    }

    

    
}