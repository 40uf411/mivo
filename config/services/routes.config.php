<?php
use Luna\services\Router;
use Luna\services\Http\{Response, Request};

/***************************
 * you can add here all the routes
 * you need.
 *
 *
 * Notice: only one router will be in charge.
 *          to set which one go to [ config / web.config.php / 'router' ]
 *²²
 ***************************/

Router::home("Users");
Router::any("/login/", "Users@login");
Router::any("/follow/", "Users@follow");
Router::any("/block/", "Users@block");
Router::any("/block_movie/", "Users@block_movie");
Router::any("/favorite/", "Users@favorite");
Router::any("/wish_list/", "Users@wish_list");

Router::any("/user/", "Api@user");

Router::any("/followers_likes/", "Api@followers_likes");

Router::any("/similar_users/", "Api@similar_users");

Router::any("/similar_to_taste/", "Api@similar_movies_based_on_taste");
Router::any("/similar_to_movie/", "Api@similar_movies_based_on_a_movie");
Router::any("/similar_to_fav/", "Api@similar_movies_based_on_a_fav");
Router::any("/similar_to_emo/", "Api@similar_movies_based_on_emotion");
Router::any("/similar_to_gender/", "Api@similar_movies_based_on_gender");


Router::any("/search_results/", "Api@search_results");
Router::any("/intro/", "Api@intro");
Router::any("/movie/", "Api@movie");
Router::any("/new/", "Api@new");
Router::any("/best/", "Api@best");
Router::any("/most_liked/", "Api@most_liked");


Router::any("/emo_reco", "Api@emo_reco");
Router::any("/upload_audio", "Api@upload_audio");
Router::any("/show", "Api@show");


/*
Router::any('/hi/$name',function($data){
   dump($data);
})->pattern([
    "name" => "/ali|7a9o/"
]);

/*
Router::home()->view("index");

Router::any('/hi/$name/$(int)age', function($data){

    echo "hello " . $data['name'];
    echo "<br>";
    echo "your age is " . $data['age'];

});
;

/*
Router::home('Users@login')->name('home');

Router::get('/hi/$name')
    ->view('test')
    ->name('hello')
    ->pattern(['name'=>'/karim|ali/']);

Router::any('/u', "Users@upload");

Router::any('/upload',"Users@upload");

Router::get('hi/zino',function (){
    echo "zeno";
});*/