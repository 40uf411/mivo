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

class UsersController extends Controller
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

    public function login()
    {
        if(! array_key_exists("email", $_POST) && ! array_key_exists("pass",$_POST))
            return json_encode([
                "status" => "error",
                "msg" => "wrong request."
            ]);
        $email = $_POST["email"];
        $pass = $_POST["pass"];
        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);
        $query = "SELECT * 
                  FROM `persons` 
                  WHERE (`email` = '$email' AND `password` = MD5('$pass') )";

        $q = $c->query($query);
        $d = $q->fetch();
        //dump($d);
        if($d > 0)
        {
            $req = Request::instance();
            $token = md5(date("YMDHIS") . rand(10,90));
            $query = "INSERT INTO `tokens` (`id_person`, `token`, `agent`, `ip`, `exp_time`) 
                      VALUES (".$d["id"].", '". $token ."','". $_SERVER['HTTP_USER_AGENT'] ."','". $req->client_ip() ."', '". date("Y-m-d H:i:s")."')";
            $q = $c->query($query);
            return json_encode([
                "status" => "ok",
                "token" => $token,
                "id" => $d["id"],
                "data" => [
                    "name" => $d["full_name"],
                    "profile_pic" => $d["profile_pic"],
                    "action" => $d["action"],
                    "animated" => $d["animated"],
                    "adventure" => $d["adventure"],
                    "bio" => $d["bio"],
                    "crime" => $d["crime"],
                    "comedy" => $d["comedy"],
                    "drama" => $d["drama"],
                    "fantasy" => $d["fantasy"],
                    "historical" => $d["historical"],
                    "horror" => $d["horror"],
                    "mystery" => $d["mystery"],
                    "political" => $d["political"],
                    "romance" => $d["romance"],
                    "science_fiction" => $d["science_fiction"],
                    "war" => $d["war"],
                    "group_id" => $d["group_id"],
                ]   
            ]);
        }
        else
            return json_encode([
                "status" => "error",
                "msg" => "wrong email or password."
            ]);
    }

    public function follow()
    {
        header('Content-Type: application/json');

        if(!array_key_exists("token",$_POST) || !array_key_exists("user_id",$_POST))
            return json_encode(["status" => "error", "msg" => "bad request."]);

        $id = $_POST["token"]; 
        $id = self::check_token($id);

        if(!$id)
            return json_encode(["status" => "error", "msg" => "token wrong or expired."]);

        $user_id = intval($_POST["user_id"]);

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);

        $query = "SELECT id, full_name FROM `persons` WHERE id = $user_id";
    
        $q = $c->query($query);
        $following = $d = $q->fetch(PDO::FETCH_ASSOC);

        if(!$d)
            return json_encode(["status" => "error", "msg" => "wrong user id."]);

        $query = "SELECT * FROM `follow` WHERE id_follower = $id AND id_followed = $user_id";
    
        $q = $c->query($query);
        $d = $q->fetch(PDO::FETCH_ASSOC);

        if (empty($d)) {
            $query = "INSERT INTO `follow` (`id_follower`, `id_followed`) VALUES ( $id, $user_id)";

            $q = $c->query($query);
            $d = $q->fetch(PDO::FETCH_ASSOC);

            return json_encode(["status" => "ok", "action" => "following", "data" => $following]);
        }
        else {
            $query =  "DELETE FROM `follow` WHERE `follow`.`id_follower` = $id AND `follow`.`id_followed` = $user_id";

            $q = $c->query($query);
            $d = $q->fetch(PDO::FETCH_ASSOC);

            return json_encode(["status" => "ok", "action" => "unfollowing", "data" => $following]);
        }
    }

    public function block()
    {
        header('Content-Type: application/json');

        if(!array_key_exists("token",$_POST) || !array_key_exists("user_id",$_POST))
            return json_encode(["status" => "error", "msg" => "bad request."]);

        $id = $_POST["token"]; 
        $id = self::check_token($id);

        if(!$id)
            return json_encode(["status" => "error", "msg" => "token wrong or expired."]);

        $user_id = intval($_POST["user_id"]);

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);

        $query = "SELECT id, full_name FROM `persons` WHERE id = $user_id";
    
        $q = $c->query($query);
        $following = $d = $q->fetch(PDO::FETCH_ASSOC);

        if(!$d)
            return json_encode(["status" => "error", "msg" => "wrong user id."]);

        $query = "SELECT * FROM `users_blocks` WHERE id_person1 = $id AND id_person2 = $user_id";
    
        $q = $c->query($query);
        $d = $q->fetch(PDO::FETCH_ASSOC);

        if (empty($d)) {
            $query = "INSERT INTO `users_blocks` (`id_person1`, `id_person2`) VALUES ( $id, $user_id)";

            $q = $c->query($query);
            $d = $q->fetch(PDO::FETCH_ASSOC);

            return json_encode(["status" => "ok", "action" => "block", "data" => $following]);
        }
        else {
            $query =  "DELETE FROM `users_blocks` WHERE `users_blocks`.`id_person1` = $id AND `users_blocks`.`id_person2` = $user_id";

            $q = $c->query($query);
            $d = $q->fetch(PDO::FETCH_ASSOC);

            return json_encode(["status" => "ok", "action" => "unblock", "data" => $following]);
        }
    }

    public function block_movie()
    {
        header('Content-Type: application/json');

        if(!array_key_exists("token",$_POST) || !array_key_exists("movie_id",$_POST))
            return json_encode(["status" => "error", "msg" => "bad request."]);

        $id = $_POST["token"]; 
        $id = self::check_token($id);

        if(!$id)
            return json_encode(["status" => "error", "msg" => "token wrong or expired."]);

        $movie_id = intval($_POST["movie_id"]);

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);

        $query = "SELECT id, title FROM `movies` WHERE id = $movie_id";
    
        $q = $c->query($query);
        $following = $d = $q->fetch(PDO::FETCH_ASSOC);

        if(!$d)
            return json_encode(["status" => "error", "msg" => "wrong movie id."]);

        $query = "SELECT * FROM `movies_blocks` WHERE id_person = $id AND id_movie = $movie_id";
    
        $q = $c->query($query);
        $d = $q->fetch(PDO::FETCH_ASSOC);

        if (empty($d)) {
            $query = "INSERT INTO `movies_blocks` (`id_person`, `id_movie`) VALUES ( $id, $movie_id)";

            $q = $c->query($query);
            $d = $q->fetch(PDO::FETCH_ASSOC);

            return json_encode(["status" => "ok", "action" => "block_movie", "data" => $following]);
        }
        else {
            $query =  "DELETE FROM `movies_blocks` WHERE `movies_blocks`.`id_person` = $id AND `movies_blocks`.`id_movie` = $movie_id";

            $q = $c->query($query);
            $d = $q->fetch(PDO::FETCH_ASSOC);

            return json_encode(["status" => "ok", "action" => "unblock_movie", "data" => $following]);
        }
    }
    
    public function favorite()
    {
        header('Content-Type: application/json');

        if(!array_key_exists("token",$_POST) || !array_key_exists("movie_id",$_POST))
            return json_encode(["status" => "error", "msg" => "bad request."]);

        $id = $_POST["token"]; 
        $id = self::check_token($id);

        if(!$id)
            return json_encode(["status" => "error", "msg" => "token wrong or expired."]);

        $movie_id = intval($_POST["movie_id"]);

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);

        $query = "SELECT * FROM `movies` WHERE id = $movie_id";
    
        $q = $c->query($query);
        $movie = $d = $q->fetch(PDO::FETCH_ASSOC);

        if(!$d)
            return json_encode(["status" => "error", "msg" => "wrong movie id."]);

        $query = "SELECT * FROM `favorites` WHERE id_person = $id AND id_movie = $movie_id";
    
        $q = $c->query($query);
        $d = $q->fetch(PDO::FETCH_ASSOC);

        if (empty($d)) {
            $query = "INSERT INTO `favorites` (`id_person`, `id_movie`) VALUES ( $id, $movie_id)";

            $q = $c->query($query);
            $d = $q->fetch(PDO::FETCH_ASSOC);

            $query = "UPDATE `persons` SET 
                                            `action` = (`action` + ".$movie["action"].")/2, 
                                            `animated` = (`animated` + ".$movie["animated"].")/2, 
                                            `adventure` = (`adventure` + ".$movie["adventure"].")/2, 
                                            `bio` = (`bio` + ".$movie["bio"].")/2, 
                                            `crime` = (`crime` + ".$movie["crime"].")/2, 
                                            `comedy` = (`comedy` + ".$movie["comedy"].")/2, 
                                            `drama` = (`drama` + ".$movie["drama"].")/2, 
                                            `fantasy` = (`fantasy` + ".$movie["fantasy"].")/2, 
                                            `historical` = (`historical` + ".$movie["historical"].")/2, 
                                            `horror` = (`horror` + ".$movie["horror"].")/2, 
                                            `mystery` = (`mystery` + ".$movie["mystery"].")/2, 
                                            `political` = (`political` + ".$movie["political"].")/2, 
                                            `romance` = (`romance` + ".$movie["romance"].")/2, 
                                            `science_fiction` = (`science_fiction` + ".$movie["science_fiction"].")/2, 
                                            `war` = (`war` + ".$movie["war"].")/2 
                      WHERE `persons`.`id` = $id";

            $q = $c->query($query);

            return json_encode(["status" => "ok", "id" => $id,"movie_id" =>$movie_id, "action" => "fav", "data" => $movie]);
        }
        else {
            $query =  "DELETE FROM `favorites` WHERE `favorites`.`id_person` = $id AND `favorites`.`id_movie` = $movie_id";

            $q = $c->query($query);
            $d = $q->fetch(PDO::FETCH_ASSOC);

            return json_encode(["status" => "ok", "id" => $id,"movie_id" =>$movie_id,  "action" => "unfav", "data" => $movie]);
        }
    }

    public function wish_list()
    {
        header('Content-Type: application/json');

        if(!array_key_exists("token",$_POST) || !array_key_exists("movie_id",$_POST))
            return json_encode(["status" => "error", "msg" => "bad request."]);

        $id = $_POST["token"]; 
        $id = self::check_token($id);

        if(!$id)
            return json_encode(["status" => "error", "msg" => "token wrong or expired."]);

        $movie_id = intval($_POST["movie_id"]);

        $c = Andromeda::connect("","mysql",[
            "host"=>host,
            "name"=>name,
            "user"=>user,
            "pass"=>pass
        ]);

        $query = "SELECT id, title FROM `movies` WHERE id = $movie_id";
    
        $q = $c->query($query);
        $movie = $d = $q->fetch(PDO::FETCH_ASSOC);

        if(!$d)
            return json_encode(["status" => "error", "msg" => "wrong movie id."]);

        $query = "SELECT * FROM `wishlists` WHERE id_person = $id AND id_movie = $movie_id";
    
        $q = $c->query($query);
        $d = $q->fetch(PDO::FETCH_ASSOC);

        if (empty($d)) {
            $query = "INSERT INTO `wishlists` (`id_person`, `id_movie`) VALUES ( $id, $movie_id)";

            $q = $c->query($query);
            $d = $q->fetch(PDO::FETCH_ASSOC);

            return json_encode(["status" => "ok", "action" => "added", "data" => $movie]);
        }
        else {
            $query =  "DELETE FROM `wishlists` WHERE `favorites`.`id_person` = $id AND `favorites`.`id_movie` = $movie_id";

            $q = $c->query($query);
            $d = $q->fetch(PDO::FETCH_ASSOC);

            return json_encode(["status" => "ok", "action" => "removed", "data" => $movie]);
        }
    }
}