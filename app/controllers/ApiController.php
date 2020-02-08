<?php
if (!defined("host")) {
    define("host","localhost");
    define("name","mivo");
    define("user","admin");
    define("pass","6534");
}  
use Luna\Core\Controller;
use Luna\Services\Http\{Request, Response};
use Luna\Services\{
    Cookie,
    Timer\Time,
    Schedule,
    Schedule\Task,
    Storage,
    View
};
use Luna\Andromeda\Andromeda;

class ApiController extends Controller
{
    public static function check_token($id)
    {

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT id_person FROM `tokens` WHERE token = '$id' AND token IN  (SELECT token FROM `tokens` WHERE exp_time >= now() - interval 30 minute)";
    
        $q = $c->query($query);
        $data = $q->fetch();
        return ($data)? $data["id_person"] : false;
    }
    public function __invoke()
    {
        $v = new View();
        //$v->assign("subjects",$d);       
        return $v->render("index", []);
    }

    public function similar_users()
    {
        header('Content-Type: application/json');

        if(!array_key_exists("token",$_POST))
            return json_encode(["status" => "error", "msg" => "missing the token."]);

        $id = $_POST["token"]; 
        $id = self::check_token($id);

        if(!$id)
            return json_encode(["status" => "error", "msg" => "token wrong or expired."]);

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT m.id,m.full_name, m.profile_pic,
        (
            Abs(m.`action` - p.`action`)+
            Abs(m.`animated` - p.`animated`)+
            Abs(m.`adventure` - p.`adventure`)+
            Abs(m.`bio` - p.`bio`)+
            Abs(m.`crime` - p.`crime`)+
            Abs(m.`comedy` - p.`comedy`)+
            Abs(m.`drama` - p.`drama`)+
            Abs(m.`fantasy` - p.`fantasy`)+
            Abs(m.`historical` - p.`historical`)+
            Abs(m.`horror` - p.`horror`)+
            Abs(m.`mystery` - p.`mystery`)+
            Abs(m.`political` - p.`political`)+
            Abs(m.`romance` - p.`romance`)+
            Abs(m.`science_fiction` - p.`science_fiction`)+
            Abs(m.`war` - p.`war`)
        )/14 moy
        FROM persons m, (SELECT * from persons where id = $id) p
        WHERE m.gender = p.intrested_in
        AND m.id NOT IN (SELECT id_person2 FROM `users_blocks` WHERE id_person1 = $id)
        ORDER BY moy DESC LIMIT 6";

        $q = $c->query($query);
        $d = $q->fetchAll(PDO::FETCH_ASSOC);
        

        return json_encode(["status" => "ok", "data" => $d]);
    }

    public function similar_movies_based_on_taste()
    {
        header('Content-Type: application/json');

        if(!array_key_exists("token",$_POST))
            return json_encode(["status" => "error", "msg" => "missing the token."]);

        $id = $_POST["token"]; 
        $id = self::check_token($id);

        if(!$id)
            return json_encode(["status" => "error", "msg" => "token wrong or expired."]);

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT m.id,m.title, m.poster, m.poster_big,
        (
            Abs(m.`action` - p.`action`)+
            Abs(m.`animated` - p.`animated`)+
            Abs(m.`adventure` - p.`adventure`)+
            Abs(m.`bio` - p.`bio`)+
            Abs(m.`crime` - p.`crime`)+
            Abs(m.`comedy` - p.`comedy`)+
            Abs(m.`drama` - p.`drama`)+
            Abs(m.`fantasy` - p.`fantasy`)+
            Abs(m.`historical` - p.`historical`)+
            Abs(m.`horror` - p.`horror`)+
            Abs(m.`mystery` - p.`mystery`)+
            Abs(m.`political` - p.`political`)+
            Abs(m.`romance` - p.`romance`)+
            Abs(m.`science_fiction` - p.`science_fiction`)+
            Abs(m.`war` - p.`war`)
        )/1 moy
        FROM movies m, (SELECT * from persons where id = $id) p
        WHERE m.id NOT IN (SELECT id_movie FROM `movies_blocks` WHERE id_person = $id)
        ORDER BY moy DESC LIMIT 6";

        $q = $c->query($query);
        $d = $q->fetchAll(PDO::FETCH_ASSOC);
        

        return json_encode(["status" => "ok", "data" => $d]);
    }
    
    public function similar_movies_based_on_a_movie()
    {
        header('Content-Type: application/json');

        $logedin = false;         $id = 0;
        if(array_key_exists("token",$_POST))
        {
            $id = $_POST["token"]; 
            $id = self::check_token($id);
    
            if(!$id)
                return json_encode(["status" => "error", "msg" => "token wrong or expired."]);
            $logedin = true;
        }

        if(!array_key_exists("movie_id",$_POST))
            return json_encode(["status" => "error", "msg" => "bad request."]);

        $movie_id = intval($_POST["movie_id"]);

        $query = "SELECT id, title FROM `movies` WHERE id = $movie_id";
    
        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);

        $q = $c->query($query);
        $movie = $d = $q->fetch(PDO::FETCH_ASSOC);

        if(!$d)
            return json_encode(["status" => "error", "msg" => "wrong movie id."]);

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT m.id,m.title, m.poster, m.poster_big, m.description, m.year,
        (
            (
                Abs(m.`action` - p.`action`)+
                Abs(m.`animated` - p.`animated`)+
                Abs(m.`adventure` - p.`adventure`)+
                Abs(m.`bio` - p.`bio`)+
                Abs(m.`crime` - p.`crime`)+
                Abs(m.`comedy` - p.`comedy`)+
                Abs(m.`drama` - p.`drama`)+
                Abs(m.`fantasy` - p.`fantasy`)+
                Abs(m.`historical` - p.`historical`)+
                Abs(m.`horror` - p.`horror`)+
                Abs(m.`mystery` - p.`mystery`)+
                Abs(m.`political` - p.`political`)+
                Abs(m.`romance` - p.`romance`)+
                Abs(m.`science_fiction` - p.`science_fiction`)+
                Abs(m.`war` - p.`war`)
            )/1 +
            (
                ( 
                    case 
                        when m.`lang` = p.`lang` then 1
                        else 0
                    end        
                ) +
                (m.`rate` - p.`rate`) +
                ( 
                    case 
                        when m.`rank` = p.`rank` then 1
                        else 0
                    end        
                ) +
                ( 
                    case 
                        when m.`year` > p.`year` - 4 AND m.`year` < p.`year` + 4  then 1
                        else 0
                    end        
                ) 
            )/3

        )/2 moy
        FROM movies m, (SELECT * from movies where id = $movie_id) p ";
        if($logedin)
            $query.="WHERE m.id NOT IN (SELECT id_movie FROM `movies_blocks` WHERE id_person = $id)"; 
        $query.=" ORDER BY moy limit 20";

        $q = $c->query($query);
        $d = $q->fetchAll(PDO::FETCH_ASSOC);
        
        return json_encode(["status" => "ok", "title" => $movie["title"], "data" => $d]);
    }    

    public function similar_movies_based_on_a_fav()
    {
        header('Content-Type: application/json');

        $logedin = false;         $id = 0;
        if(array_key_exists("token",$_POST))
        {
            $id = $_POST["token"]; 
            $id = self::check_token($id);
    
            if(!$id)
                return json_encode(["status" => "error", "msg" => "token wrong or expired."]);
            $logedin = true;
        }

        $query = "SELECT id, title FROM `movies` WHERE id IN  (SELECT id_movie FROM `favorites` WHERE id_person IN  (SELECT id_followed FROM `follow` WHERE id_follower = $id)) ORDER BY RAND()";
    
        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);

        $q = $c->query($query);
        $movie = $d = $q->fetch(PDO::FETCH_ASSOC);
        $movie_id = $movie["id"];
        if(!$d)
            return json_encode(["status" => "error", "msg" => "wrong movie id."]);

        $query = "SELECT m.id,m.title, m.poster, m.poster_big, m.description,
        (
            (
                Abs(m.`action` - p.`action`)+
                Abs(m.`animated` - p.`animated`)+
                Abs(m.`adventure` - p.`adventure`)+
                Abs(m.`bio` - p.`bio`)+
                Abs(m.`crime` - p.`crime`)+
                Abs(m.`comedy` - p.`comedy`)+
                Abs(m.`drama` - p.`drama`)+
                Abs(m.`fantasy` - p.`fantasy`)+
                Abs(m.`historical` - p.`historical`)+
                Abs(m.`horror` - p.`horror`)+
                Abs(m.`mystery` - p.`mystery`)+
                Abs(m.`political` - p.`political`)+
                Abs(m.`romance` - p.`romance`)+
                Abs(m.`science_fiction` - p.`science_fiction`)+
                Abs(m.`war` - p.`war`)
            )/1 +
            (
                ( 
                    case 
                        when m.`lang` = p.`lang` then 1
                        else 0
                    end        
                ) +
                (m.`rate` - p.`rate`) +
                ( 
                    case 
                        when m.`rank` = p.`rank` then 1
                        else 0
                    end        
                ) +
                ( 
                    case 
                        when m.`year` > p.`year` - 4 AND m.`year` < p.`year` + 4  then 1
                        else 0
                    end        
                ) 
            )/3

        )/2 moy
        FROM movies m, (SELECT * from movies where id = $movie_id) p";
        if($logedin)
            $query.=" WHERE m.id NOT IN (SELECT id_movie FROM `movies_blocks` WHERE id_person = $id)"; 
        $query.=" ORDER BY moy limit 5";

         $query;
        $q = $c->query($query);
        $d = $q->fetchAll(PDO::FETCH_ASSOC);
        

        return json_encode(["status" => "ok", "title" => $movie["title"], "data" => $d]);
    }   
    
    public function similar_movies_based_on_emotion()
    {
        header('Content-Type: application/json');

        if(!array_key_exists("emotion",$_POST) and in_array($_POST["emotion"], [0,1,2]))
            return json_encode(["status" => "error", "msg" => "missing emotion."]);

        $emo = intval($_POST["emotion"]);

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT  m.id, m.title, m.description, m.year, m.poster, m.poster_big, SUM(r.like_when_on_$emo) - SUM(r.dislike_when_on_$emo) moy
                  FROM movies m, relations r
                  WHERE m.id = r.id_movie
                  GROUP BY m.id
                  ORDER BY moy DESC";

        $q = $c->query($query);
        $d = $q->fetch(PDO::FETCH_ASSOC);

        return json_encode([
            "status" => "ok",
            "data" => $d
        ]);
    }

    public function similar_movies_based_on_gender()
    {
        header('Content-Type: application/json');

        if(!array_key_exists("gender",$_POST) and in_array($_POST["gender"], [0,1]))
            return json_encode(["status" => "error", "msg" => "missing gender."]);

        $gender = intval($_POST["gender"]);

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "  SELECT  m.id, m.title, m.description, m.year, m.poster, m.poster_big, SUM(r.like_when_on_0) + SUM(r.like_when_on_1) + SUM(r.like_when_on_2) - SUM(r.dislike_when_on_0) - SUM(r.dislike_when_on_1) - SUM(r.dislike_when_on_2) moy
                    FROM movies m, relations r, persons p
                    WHERE m.id = r.id_movie
                    AND p.id = r.id_person
                    AND p.gender = $gender
                    GROUP BY m.id
                    ORDER BY moy DESC";

        $q = $c->query($query);
        $d = $q->fetch(PDO::FETCH_ASSOC);
        
    }

    public function followers_likes()
    {
        header('Content-Type: application/json');

        if(!array_key_exists("token",$_POST))
            return json_encode(["status" => "error", "msg" => "missing the token."]);

        $id = $_POST["token"];
        $id = self::check_token($id);

        if(!$id)
            return json_encode(["status" => "error", "msg" => "token wrong or expired."]);

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT * FROM `movies` 
                  WHERE id IN  (SELECT id_movie FROM `favorites` WHERE id_person IN  (SELECT id_followed FROM `follow` WHERE id_follower = $id)) 
                  AND id NOT IN (SELECT id_movie FROM `movies_blocks` WHERE id_person = $id)
                  ORDER BY RAND() LIMIT 5";

        $q = $c->query($query);
        $d = $q->fetchAll(PDO::FETCH_ASSOC);
        

        return json_encode(["status" => "ok", "data" => $d]);
    }
    public function movie()
    {
        header('Content-Type: application/json');

        if(!array_key_exists("movie_id",$_POST))
            return json_encode(["status" => "error", "msg" => "missing the id."]);

        $id = intval($_POST["movie_id"]);

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT `title`, `year`, `description`, `rate`, `rank`, `lang`, `poster`, `poster_big` FROM `movies` WHERE `id` = $id";
    
        $q = $c->query($query);
        $data = $q->fetch();

        $query = "SELECT count(*) as count FROM `favorites` WHERE `id_movie` = $id";
    
        $q = $c->query($query);
        $data["fl"] = $q->fetch()["count"];

        $query = "SELECT count(*) as count FROM `favorites` WHERE `id_movie` = $id";
    
        $q = $c->query($query);
        $data["wl"] = $q->fetch()["count"];

        $query = "SELECT `action`, `animated`, `adventure`, `bio`, `crime`, `comedy`, `drama`, `fantasy`, `historical`, `horror`, `mystery`, `political`, `romance`, `science_fiction`, `war` FROM `movies` WHERE `id` = $id";
    
        $q = $c->query($query);
        $d = array_values($q->fetch(PDO::FETCH_ASSOC));
        $data["cmp"] = array_map('floatval', $d);

        $query = "SELECT AVG( like_when_on_0 ), AVG( dislike_when_on_0 ), AVG(dislike_when_on_1), AVG(like_when_on_1), AVG(like_when_on_2), AVG(dislike_when_on_2) FROM `relations` WHERE id_movie = $id";
    
        $q = $c->query($query);
        $d = array_values($q->fetch(PDO::FETCH_ASSOC));
        $data["exp"] = array_map('floatval', $d);

        return json_encode(["status" => "ok", "data" => $data]);
    }

    public function user($with_data = true)
    {
        header('Content-Type: application/json');
        $logedin = false;
        $id = false;
        $with_data = ($with_data == [])? true : $with_data;
        
        
        if (array_key_exists("user_id",$_POST)) {
            $id = intval($_POST["user_id"]);
        }
        elseif(array_key_exists("token",$_POST) and $with_data)
        {
            $id = $_POST["token"]; 
            $id = self::check_token($id);
    
            if(!$id)
                return json_encode(["status" => "error", "msg" => "token wrong or expired."]);
            $logedin = true;
        }
        else 
        {
            return json_encode(["status" => "error", "msg" => "missing the id."]);
        }


        if (!$id)
            return json_encode(["status" => "error", "msg" => "no id found."]);

        
        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT * FROM `persons` WHERE `id` = $id";

        $q = $c->query($query);
        $d = $q->fetch(PDO::FETCH_ASSOC);
        
        if (count($d) < 1)
            return json_encode(["status" => "error", "msg" => "user does not exist."]);

        $data["person"] = $d;
        $data["person"]["in"] = false;

        if ($logedin and $with_data) 
        {
            $data["person"]["in"] = true;
            $query = "SELECT exp_time FROM `tokens` WHERE id_person = $id ORDER BY `tokens`.`exp_time` DESC LIMIT 1";

            $q = $c->query($query);
            $d = $q->fetch(PDO::FETCH_ASSOC);
    
            $data["person"]["last_login"] = $d["exp_time"];

            $query = "SELECT exp_time FROM `tokens` WHERE token = '" . $_POST["token"] . "' ORDER BY `tokens`.`exp_time` DESC LIMIT 1";

            $q = $c->query($query);
            $d = $q->fetch(PDO::FETCH_ASSOC);
    
            $data["person"]["session_creation"] =$d["exp_time"];

            $query = "SELECT id, title, description, year, poster, poster_big FROM `movies` WHERE `id` in (SELECT `id_movie` from `favorites` where `id_person` = $id) AND id NOT IN (SELECT id_movie FROM `movies_blocks` WHERE id_person = $id)";

            $q = $c->query($query);
            $d = $q->fetchAll(PDO::FETCH_ASSOC);
    
            $data["fav"] = $d;
    
            $query = "SELECT id, title, description, year, poster, poster_big FROM `movies` WHERE `id` in (SELECT `id_movie` from `wishlists` where `id_person` = $id) AND id NOT IN (SELECT id_movie FROM `movies_blocks` WHERE id_person = $id)";
    
            $q = $c->query($query);
            $d = $q->fetchAll(PDO::FETCH_ASSOC);
    
            $data["wl"] = $d;

            $query = "SELECT id, full_name, profile_pic FROM `persons` WHERE `id` in (SELECT id_followed FROM `follow` WHERE id_follower = $id) AND id NOT IN (SELECT id_person2 FROM `users_blocks` WHERE id_person1 = $id)";
    
            $q = $c->query($query);
            $d = $q->fetchAll(PDO::FETCH_ASSOC);
    
            $data["followers"] = $d;
            
            $query = "SELECT id, full_name, profile_pic FROM `persons` WHERE `id` IN (SELECT id_person2 FROM `users_blocks` WHERE id_person1 = $id)";
    
            $q = $c->query($query);
            $d = $q->fetchAll(PDO::FETCH_ASSOC);
    
            $data["blocked_users"] = $d;

            $query = "SELECT id, title, description, year, poster, poster_big FROM `movies` WHERE `id` IN (SELECT id_movie FROM `movies_blocks` WHERE id_person = $id)";
            $q = $c->query($query);
            $d = $q->fetchAll(PDO::FETCH_ASSOC);
    
            $data["blocked_movies"] = $d;
        }
        $query = "SELECT AVG( like_when_on_0 ), AVG( dislike_when_on_0 ), AVG(dislike_when_on_1), AVG(like_when_on_1), AVG(like_when_on_2), AVG(dislike_when_on_2) FROM `relations` WHERE id_person = $id";
    
        $q = $c->query($query);
        $d = $q->fetch(PDO::FETCH_ASSOC);
        $d = array_values( is_array($d)? $d : [0,0,0,0,0,0]);
        $data["mood"] = array_map('floatval', $d);

        $query = "SELECT COUNT(id_follower)as follower FROM `follow` WHERE id_follower = $id";

        $q = $c->query($query);
        $d = $q->fetch(PDO::FETCH_ASSOC);

        $data["person"]["follower"] = $d["follower"];

        $query = "SELECT COUNT(id_followed) as followed FROM `follow` WHERE id_followed = 143";

        $q = $c->query($query);
        $d = $q->fetch(PDO::FETCH_ASSOC);

        $data["person"]["followed"] = $d["followed"];

        return json_encode(["status" => "ok", "data" => $data, "action" => "profile"]);
    }

    public function search_results()
    {
        $logedin = false;         $id = 0;
        if(array_key_exists("token",$_POST))
        {
            $id = $_POST["token"]; 
            $id = self::check_token($id);
    
            if(!$id)
                return json_encode(["status" => "error", "msg" => "token wrong or expired."]);
            $logedin = true;
        }

        $movie = $_GET["movie"];
        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT title FROM `movies` WHERE `title` LIKE '%$movie%' ";
        if($logedin)
            $query.="AND id NOT IN (SELECT id_movie FROM `movies_blocks` WHERE id_person = $id)";
        $query.=" LIMIT 6";

        $q = $c->query($query);
        $d = $q->fetchAll(PDO::FETCH_ASSOC);
        //dump($d);
        header('Content-Type: application/json');
        return json_encode($d);
    }

    public function new()
    {
        $logedin = false;         $id = 0;
        if(array_key_exists("token",$_POST))
        {
            $id = $_POST["token"]; 
            $id = self::check_token($id);
    
            if(!$id)
                return json_encode(["status" => "error", "msg" => "token wrong or expired."]);
            $logedin = true;
        }

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT * FROM `movies` ";
        if($logedin)
            $query.="WHERE id NOT IN (SELECT id_movie FROM `movies_blocks` WHERE id_person = $id)";
        $query.=" ORDER BY `movies`.`year` DESC LIMIT 5";

        $q = $c->query($query);
        $d = $q->fetchAll(PDO::FETCH_ASSOC);
        //dump($d);
        header('Content-Type: application/json');
        return json_encode($d);
    }

    public function best()
    {
        $logedin = false;         $id = 0;
        if(array_key_exists("token",$_POST))
        {
            $id = $_POST["token"]; 
            $id = self::check_token($id);
    
            if(!$id)
                return json_encode(["status" => "error", "msg" => "token wrong or expired."]);
            $logedin = true;
        }
        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT * FROM `movies` ";
        if($logedin)
            $query.="WHERE id NOT IN (SELECT id_movie FROM `movies_blocks` WHERE id_person = $id)";
        $query.=" ORDER BY `movies`.`rate` DESC LIMIT 5";

        $q = $c->query($query);
        $d = $q->fetchAll(PDO::FETCH_ASSOC);
        //dump($d);
        header('Content-Type: application/json');
        return json_encode($d);
    }
    
    public function most_liked()
    { 
        $logedin = false;         $id = 0;
        if(array_key_exists("token",$_POST))
        {
            $id = $_POST["token"]; 
            $id = self::check_token($id);
    
            if(!$id)
                return json_encode(["status" => "error", "msg" => "token wrong or expired."]);
            $logedin = true;
        }
        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "  SELECT m.id, m.title, m.description, m.year, m.poster, m.poster_big, SUM(r.like_when_on_0) + SUM(r.like_when_on_1) + SUM(r.like_when_on_2) - SUM(r.dislike_when_on_0) - SUM(r.dislike_when_on_1) - SUM(r.dislike_when_on_2) moy
                    FROM movies m, relations r
                    WHERE m.id = r.id_movie ";
        if($logedin)
            $query.="AND m.id NOT IN (SELECT id_movie FROM `movies_blocks` WHERE id_person = $id)";
        $query.="GROUP BY m.id ORDER BY moy DESC LIMIT 5";

        $q = $c->query($query);
        $d = $q->fetchAll(PDO::FETCH_ASSOC);
        //dump($d);
        header('Content-Type: application/json');
        return json_encode($d);
    }

    public function intro()
    {
        $logedin = false;
        $id = 0;
        if(array_key_exists("token",$_POST))
        {
            $id = $_POST["token"]; 
            $id = self::check_token($id);
    
            if(!$id)
                return json_encode(["status" => "error", "msg" => "token wrong or expired."]);
            $logedin = true;
        }
        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT * FROM `movies`  ";
        if($logedin)
            $query.="WHERE id NOT IN (SELECT id_movie FROM `movies_blocks` WHERE id_person = $id)";
        $query.="  ORDER BY RAND() DESC"; //`movies`.`year`
        $query;
        $q = $c->query($query);
        $d = $q->fetch(PDO::FETCH_ASSOC);
        //dump($d);
        header('Content-Type: application/json');
        return json_encode($d);
    }

    public function followers()
    {
        header('Content-Type: application/json');

        if(!array_key_exists("id",$_POST))
            return json_encode(["status" => "error", "msg" => "missing the id."]);

        $id = intval($_POST["id"]);

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT * FROM `persons` WHERE `id` = $id";

        $q = $c->query($query);
        $d = $q->fetch(PDO::FETCH_ASSOC);
        
        if (count($d) < 1)
            return json_encode(["status" => "error", "msg" => "user does not exist."]);

        $data["person"] = $d;

        $query = "SELECT * FROM `movies` WHERE `id` in (SELECT `id_movie` from `favorites` where `id_person` = $id)";

        $q = $c->query($query);
        $d = $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function upload_audio()
    {

        header('Content-Type: application/json');
        
        if(!array_key_exists("audio",$_FILES))
            return json_encode(["status" => "error", "msg" => "no file sent."]);

        $newname = date("Y_m_d|H_i_s|") . uniqid() . ".wav"; 

        $target = PUBLIC_PATH .'uploaded_audios/'.$newname;

        if (! move_uploaded_file( $_FILES['audio']['tmp_name'], $target)) 
            return json_encode(["status" => "error", "msg" => "Could not save the uploaded file."]);

        // Output headers and body for debugging purposes

        $url = 'http://127.0.0.1:5000/reco';
        //$data = array('file' => "goforward.raw");
        $data = array('file' => $newname);

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) { 
            return json_encode(["status" => "error", "msg" => "failed while recognizing the file."]);
        }

        $r = self::emo(json_decode($result), true);
        //$r = self::emo(["Lam_yu3djibni-0-2","hada-0-1", "el_film-0-2"], true);

        if($r["emotion"] == -1 || $r["context"] == -1){
            return json_encode(array(
                "status" => "error",
                "msg" => "corrupt audio, or no emotion detected",
                "reco" => $r
            ));
        }

        $another_result = [];

        $action = "";
        
        $allow = true;
        
        $ok_token;

        $result = "";

        if(array_key_exists("token", $_POST))
        {
            if (self::check_token($_POST["token"])) 
            {
                require_once CONTROLLERS_PATH . "UsersController.php";
    
                if(array_key_exists("movie_id",$_POST))
                {
                    if ($r["emotion"] == 2 and $r["context"] == 1) 
                    {
                        $result = (new UsersController())->favorite();
                        $another_result = json_decode($result, true);
                        $another_result["reco"] = $r;
                        $action = $another_result["action"];
                    }
                    elseif($r["context"] == 0)
                    {
                        $result = (new UsersController())->block_movie();
                        $another_result = json_decode($result, true);
                        $another_result["reco"] = $r;
                        $action = $another_result["action"];
                    }

                    if ($r["emotion"] != -1 and in_array($r["context"],[0,1])) 
                    {
                        $id = $this->check_token($_POST["token"]);
                        $like = ($r["context"] == 1)? "like_when_on_" . $r["emotion"] : "dislike_when_on_" . $r["emotion"];
                        $c = Andromeda::connect("","mysql",[
                            "host"=>host,
                            "name"=>name,
                            "user"=>user,
                            "pass"=>pass
                        ]);
                
                        //$query = "INSERT INTO `relations` (`id_person`, `id_movie`, `$like`) VALUES (" . $id .", " . $_POST["movie_id"] .", 1)";
                        $query = "UPDATE `relations` SET `$like`= 1 WHERE `id_person` = $id AND `id_movie` = '" . $_POST["movie_id"] ."'
                                  IF @@ROWCOUNT=0
                                    INSERT INTO `relations` (`id_person`, `id_movie`, `$like`) VALUES (" . $id .", " . $_POST["movie_id"] .", 1);";
                        $c->query($query);
                    }
                    $allow = ($another_result != [] && $another_result["status"] == "ok" )? true : false;
                }
                elseif (array_key_exists("user_id",$_POST)) 
                {
                    if ($r["emotion"] >= 0) 
                    {
                        if( $r["context"] == 1 && $r["emotion"] == 2)
                        {
                            $result = (new UsersController())->follow();
                            $another_result = json_decode($result, true);
                            $another_result["reco"] = $r;
                            $action = $another_result["action"];
                        }
                        elseif($r["context"] == 0)
                        {
                            $result = (new UsersController())->block();
                            $another_result = json_decode($result, true);
                            $another_result["reco"] = $r;
                            $action = $another_result["action"];
                        }
                        $allow = ($another_result != [] && $another_result["status"] == "ok" )? true : false;
                    }
                }
                else
                    return json_encode(["status" => "error", "msg" => "no id sent.", "reco" => $r]);
            }
            else 
            {
                $allow = false;
                return json_encode(array(
                    "status" => "error",
                    "msg" => "unvalid session sent.",
                    "reco" => $r
                ));  
            }         
        }

        if (array_key_exists("movie_id",$_POST) and $r["context"] > 0 )
        {
            $m = json_decode($this->similar_movies_based_on_a_movie(), true);
            if($m["status"] == "ok")
            {
                if($action != "")
                {
                    $action = "recommendation";
                    $result = json_encode(
                        array(
                            "status" => "ok",
                            "action" => $action,
                            "recommendation" => true,
                            "data" => $m["data"],
                            "reco" => $r
                        )
                    );
                }
                else
                {
                    $action = "recommendation";
                    $m["recommendation"] = true;
                    $m["reco"] = $r;
                    $result = json_encode($m);
                }
            }
            else
                $result = json_encode(
                    array(
                        "action" => $action,
                        "status" => "ok",
                        "recommendation" => false,
                        'msg' => "could not fetch recommended movies.",
                        "reco" => $r
                    )
                );
            
        }
        elseif (array_key_exists("user_id",$_POST) and $r["context"] > 0 )
        {
            $m = json_decode($this->user(false), true);
            
            if($m["status"] == "ok")
            {
                if($action != "")
                {
                    $result = json_encode(
                        array(
                            "status" => "ok",
                            "action" => $action,
                            "profile" => true,
                            "data" => $m["data"],
                            "reco" => $r
                        )
                    );
                }
                else
                {
                    $action = "profile";
                    $m["profile"] = true;
                    $m["reco"] = $r;
                    $result = json_encode($m);
                }
            }
            else
                $result = json_encode(
                    array(
                        "action" => $action,
                        "status" => "ok",
                        "profile" => false,
                        'msg' => "could not fetch user profile.",
                        "reco" => $r
                    )
                );
            
        }
        elseif($action == "" ) 
        {
            if (array_key_exists("token", $_POST)) {
                $result = json_encode(array(
                    "status" => "error",
                    "msg" => "sorry, due to the level of emotion and the context we don't have mush to do.",
                    "text" => $result,
                    "reco" => $r
                ));
            } else {
                if ( $r["context"] == 0) {
                    $result = json_encode(array(
                        "status" => "error",
                        "msg" => "we can't do much, untill you login.",
                        "reco" => $r
                    ));
                }
            }
        }

        return $result;
    }

    public function emo_reco()
    {

        header('Content-Type: application/json');
        
        if(!array_key_exists("audio",$_FILES))
            return json_encode(["status" => "error", "msg" => "no file sent."]);

        $newname = date("Y_m_d|H_i_s|") . uniqid() . ".wav"; 

        $target = PUBLIC_PATH .'uploaded_audios/'.$newname;

        if (! move_uploaded_file( $_FILES['audio']['tmp_name'], $target)) 
            return json_encode(["status" => "error", "msg" => "Could not save the uploaded file."]);

        // Output headers and body for debugging purposes
        $url = 'http://127.0.0.1:5000/reco';
        //$data = array('file' => "goforward.raw");
        $data = array('file' => $newname);

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) { 
            return json_encode(["status" => "error", "msg" => "failed while recognizing the file."]);
        }

        $r = self::emo(json_decode($result), true);
        //$r = self::emo(["Lam_yu3djibni-0-2","hada-0-1", "el_film-0-2"], true);

        return json_encode(["status" => "ok", "data" => $r, "text" => htmlspecialchars($result)]);
    }
    public function show()
    {
        echo "
        <head>
        <meta charset='UTF-8'>
        <title>Record the audio input from your microphone</title>
      </head>
      <body>
        <div class='wrapper'>
          <div class='audio-wrapper'>
            <audio src='' controls class='js-audio audio'></audio>
          </div>
          <div class='toolbar'>
            <button class='js-start button button--start'>Start</button>
            <button class='js-stop button button--stop'>Stop</button>
          </div>
        </div>
        <script src='http://127.0.0.1:8000/assets/js/jquery.js'></script>
        <script src='http://127.0.0.1:8000/assets/js/audio.js'></script>
      </body>
      </html>
        ";
    }

    public static function emo($data, $emo_test = true, $gender_test = false) {
        $emotion = -1;
        $gender = -1;
        $context = -1;
        /*
        if ($emo_test) {
            $e0 = 0;
            $e1 = 0;
            $e2 = 0;
            foreach($data as $v) {
                $n = substr($v, -1);
                switch ($n) {
                    case 0:
                        $e0++;
                        break;
                    case 1:
                        $e1++;
                        break;
                    case 2:
                        $e2++;
                        break;
                }
            }
            $emotion = array_keys(["0" => $e0, "1" => $e1, "2" => $e2], max(["0" => $e0, "1" => $e1, "2" => $e2]))[0];
        }*/
    
        if ($gender_test) {
            $g0 = 0;
            $g1 = 0;
            foreach($data as $v) {
                $n = substr($v, -3, 1);
                switch ($n) {
                    case 0:
                        $g0++;
                        break;
                    case 1:
                        $g1++;
                        break;
                }
            }
            $gender = array_keys(["0" => $g0, "1" => $g1], max(["0" => $g0, "1" => $g1]))[0];
        }

        foreach ($data as $element) {
            if (in_array($element,["<s>", "<sil>", "</s>", "</sil>"])) 
                continue;
    
            if (substr($element,0,9) == "A3djabani" or substr($element,0,6) == "Rai3on") {
                $context = 1;
                if ($emo_test)
                    $emotion = substr($element, -1);
                break;
            }
            elseif (substr($element,0,13) == "Lam_yu3djibni" or substr($element,0,6) == "Sayion") {
                $context = 0;
                if ($emo_test)
                    $emotion = substr($element, -1);
                break;
            }
            elseif (substr($element,0,9) == "Ma9boulon") {
                $context = 0.5;
                if ($emo_test)
                    $emotion = substr($element, -1);
                break;
            }
        }
    
        return [
            "emotion" => $emotion,
            "gender" => $gender,
            "context" => $context
        ];
    }
}