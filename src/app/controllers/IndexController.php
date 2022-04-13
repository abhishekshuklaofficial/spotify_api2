<?php
// session_start();
use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
class IndexController extends Controller{
    public function indexAction()
    {
        $username =$this->session->get('username');
        $password =$this->session->get('password');


        if($username && $password){
            $client_id = '1217a9d42f0f43fa96fd9c29e8134cfc'; 
            $client_secret = '46aa9e481a1640c0afe6dd7fbb565051'; 
            
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token');
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($ch, CURLOPT_POST, 1);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials'); 
            // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.base64_encode($client_id.':'.$client_secret)));
            
            // $result=json_decode(curl_exec($ch),1);
            // $this->session->set("token",$result['access_token']);
            // // die($this->session->get("token"));

            // $this->view->token =$result;
            // echo $result;
            // die;


            // $client_id = 'daf810de4b2e49fc9b4bcd448c696946'; 
            // $client_secret = '021c0e97d9aa49a890425ec760e83e8b'; 
            $url = "https://accounts.spotify.com/authorize?";
            $headers = [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => 'http://localhost:8080/index/access',
                'scope' => 'playlist-modify-public playlist-read-private playlist-modify-private',
                'response_type' => 'code'
            ];

            $auth = $url . http_build_query($headers);
            // die($auth);
            $this->response->redirect($auth);
        }else{
            header('Location:http://localhost:8080/index/auth');
        }
  }
  public function accessAction()
  {
    $client_id = '1217a9d42f0f43fa96fd9c29e8134cfc'; 
    $client_secret = '46aa9e481a1640c0afe6dd7fbb565051'; 
    // $client_id = 'daf810de4b2e49fc9b4bcd448c696946'; 
    // $client_secret = '021c0e97d9aa49a890425ec760e83e8b';
    $code = $this->request->get('code');
    // die($code);
    $data = array(
        'redirect_uri' => 'http://localhost:8080/index/access',
        'grant_type'   => 'authorization_code',
        'code'         => $code,
    );
    $ch            = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret)));

    $result = json_decode(curl_exec($ch));
    // print_r($result->access_token);
    // die;
    $username = $this->session->get('username');
    $password = $this->session->get('password');
    if($username){
        // $user =
        // $data = Users::query()
        //         ->where("username = '$username'")
        //         ->andWhere("password = '$password'")
        //         ->execute();
        $user = Users :: findFirst(["username = '$username'","password = '$password'"]);
            // print_r($data);
        if(count($user)>0){
            if($user->token == ""){
                $user->token = $result->access_token;
                $user->save();
                // header('location:http://localhost:8080/');
            }else{
                // header('location:http://localhost:8080/');

            }
            // $this->session->set('username',$username);
            // $this->session->set('password',$password);
            // header('location:http://localhost:8080/');
            // die("ho");
        }else{
            header('location:http://localhost:8080/index/auth');
            
        }

    }
    $this->session->set('token_id',$result->access_token);
    $this->view->token =$result;

    // echo "<pre>";
    // print_r($result);
    // die();
    // $this->view->pick("spotify/index");
    // header('Location:http://localhost:8080/index/getplaylist');
  }
    public function authAction(){
        // if(isset($_POST['submit'])){
        //     $postdata = $_POST ?? array();
        //     print_r($postdata);
        // }
        
        

    }
    public function loginAction(){
        if(isset($_POST['submit'])){
            $postdata = $_POST ?? array();
            // print_r($postdata);
            $username = $_POST['username'];
            $password = $_POST["password"];
            // echo $password;
            // print_r($password);
            // die();
            $data = Users::query()
                ->where("username = '$username'")
                ->andWhere("password = '$password'")
                ->execute();
            // print_r($data);
            if(count($data)>0){
                $this->session->set('username',$username);
                $this->session->set('password',$password);
                header('location:http://localhost:8080/');
            }
        }
        

    }
    public function signupAction(){
        
        
    }
    public function registerAction(){
        if(isset($_POST['submit'])){
            $postdata = $_POST ?? array();
            print_r($postdata);
            $user = new Users();

            $user->assign(
                $postdata,
                [
                    'username',
                    'password'
                ]
            );

            $success = $user->save();

            // $this->view->success = $success;
            header('Location:http://localhost:8080/index/auth');
        }
        
    }
    // } 
    public function searchAction()
    {
        echo "hhh";
        // $request = new Request();type
        // echo "<pre>";
        // print_r($this->session->get('token_id'));1
        $token = $this->session->get('token_id');
        $type = urlencode($this->request->getPost('type'));
        $song = $this->request->getPost('song');
        $song = urlencode($song);
        echo $type,"<br>";
        echo $token,"<br>",$song;
        // die;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"https://api.spotify.com/v1/search?query=$song&type=$type&locale=en-GB%2Cen-US%3Bq%3D0.9%2Cen%3Bq%3D0.8&offset=0&limit=20");
        // curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $token"));

        $response = json_decode(curl_exec($ch),1);
        
        // $this->view->data = $response;
        $s = "s";
        $type = $type.$s;
        $this->view->data = $response;
        $this->view->type = $type;
        $this->view->song = $song;
        $this->view->token = $token;
        // echo "<pre>";
        // print_r( $response);
        // die;
    }
    public function playlistAction(){
        if(isset($_POST['createpalylist'])){
            $name = $this->request->getPost('createpalylist');
            // $tokenid = $this->request->getPost('token_id');
            $tokenid = $this->session->get('token_id');
            // echo $name;
            // die;
            $client_id = '1217a9d42f0f43fa96fd9c29e8134cfc'; 
            $client_secret = '46aa9e481a1640c0afe6dd7fbb565051'; 
            $user_id = "31cpa3cluydw3snbfn747p76wr6q";
            $data = array(
                'name' =>$name,
                'description' => 'New playlist description',
                'public' => false
            );
            $url = "https://api.spotify.com/v1/users/$user_id/playlists";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $tokenid", 'Content-Type:application/json'));
            $response = curl_exec($ch);
            // print_r($response);
            // die;


        }
        // $this->view->name = $n
    }
    public function getplaylistAction(){
        // https://api.spotify.com/v1/me/playlists" -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer BQCSLob_QVZEhxucKAanZtU_NbS86jCzRnh0SDEUX46ittR5ldMEOwbGSqwWyxLar_PmBEK6y4LI8DI3yqSCqqpGmeJYic3oPXVzScn11OXT_Xk4TWf24CNAr4yl9Yoza2PIX4kqtnjJSmkeTY5826n9DOZL8HN_AoAQvdsnF-62o6Fv"
        $tokenid = $this->session->get('token_id');
        // echo $tokenid;
        // die;
        $client_id = '1217a9d42f0f43fa96fd9c29e8134cfc'; 
        $client_secret = '46aa9e481a1640c0afe6dd7fbb565051'; 
        $user_id = "31cpa3cluydw3snbfn747p76wr6q";
        // $data = array(
        //     'tokenid'=>$this->session->get('token_id')
        // );
        $url = "https://api.spotify.com/v1/me/playlists";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $tokenid", 'Content-Type:application/json'));
        $response = json_decode(curl_exec($ch),1);
        // echo "<pre>";
        // print_r($response);
        // die;
        $this->view->playlist = $response;
    }
    public function addsongAction(){
        
        // https://api.spotify.com/v1/playlists/5rF1kXSycCEBGexohTlyGs/tracks?position=0&uris=spotify%3Atrack%3A5orNEFkFG4RP24goF02AuD
        $tokenid = $this->session->get('token_id');
        // echo $name;
        // die;
        $client_id = '1217a9d42f0f43fa96fd9c29e8134cfc'; 
        $client_secret = '46aa9e481a1640c0afe6dd7fbb565051'; 
        $user_id = "31cpa3cluydw3snbfn747p76wr6q";
        $uri = $this->request->getPost('uri');
        $data = array(
            'uri'=>array($this->request->getPost('uri'))
        );
        $url = "https://api.spotify.com/v1/playlists/5rF1kXSycCEBGexohTlyGs/tracks?position=0&uris=$uri";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $tokenid", 'Content-Type:application/json'));
        $response = curl_exec($ch);
        // echo "<pre>";
        // print_r($response);
        // die;
        header('Location:http://localhost:8080/index/search');
    }
    public function goplaylistAction(){
        if(isset($_POST['delete'])){
            $play_list_id = $this->request->getPost('uri');
            // die($play_list_id);
            // $play_list_id =  $this->session->set('play_list_id',$play_list_id);
            // die($this->session->get('play_list_id'));
            // https://api.spotify.com/v1/playlists/5rF1kXSycCEBGexohTlyGs/tracks
            // $data = array(
            //     'uri'=>array($this->request->getPost('uri'))
            // );
            $tokenid = $this->session->get('token_id');

            $url = "https://api.spotify.com/v1/playlists/$play_list_id/tracks";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $tokenid", 'Content-Type:application/json'));
            $response = json_decode(curl_exec($ch),1);
            // echo "<pre>";
            // print_r($response['items'][0]['track']['name']);
            $this->view->item_playlist = $response;
            
        }
    }
    public function removeitemAction(){

        $track_id = $this->request->getPost('uri');
        $playlist_id = '5rF1kXSycCEBGexohTlyGs';
        $data =  [
            "uris" => [
            $track_id
            ],
            "position" => 0
        ];
        $token = $this->session->get('token_id');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.spotify.com/v1/playlists/'.$playlist_id.'/tracks');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$token));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result=curl_exec($ch);
        // print_r($result);
        header('Location:http://localhost:8080/index/getplaylist');
    }
}