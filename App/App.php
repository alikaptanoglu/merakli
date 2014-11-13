<?php
/**
* Uygulamamızı çalıştıracak olan sınıf
*
* Sistemdeki tüm sınıfların içermeleri gereken veritabanı ve diğer bilgileri tutan sınıf.
*
* @author   Midori Kocak <mtkocak@mtkocak.net>
*/

namespace Midori\Cms;

use Midori\Cms;

/**
* Class App
* @package Midori\Cms
*/
class App
{

    /**
    * Veritabanı bağlantısını tutacak olan değişken.
    *
    * @var PDO
    */
    private $db = false;

    /**
    * Sistem ayarlarını çeken değişken.
    *
    * @var array
    */
    private $settings = false;
    

    /**
    * Veritabanına bağlanmaya yarayan kurucu metod
    *
    * @param BasicDBObject $dbConnection Veritabanı işlemleri sınıfı
    * @return BasicDB object or False.
    */
    public function getDb($dbConnection)
    {
        if(!$dbConnection){
            return false;
        }
        else
        {
            $this->db = $dbConnection;
            return $this->db;
        }
    }

    /**
    * Nesnelere bağlı olan bilgileri çektiğimiz metod
    *
    * @return array
    */
    public function getCategories()
    {
        $categories = new Categories($this->db);
        return $categories->index();
    }
    
    public function getPosts()
    {
        $posts = new Posts($this->db);
        return $posts->index();
    }
    
    public function login($data=null){
        $users = new Users($this->db);
        if($data!=null)
        {
            return $users->login($data['username'],$data['password']);
        }
        else {
            echo $this->render('./View/Install/login.php', '');
        }
    }
    
    /**
    * Tüm sitedeki ayarları çektiğimiz metod
    *
    * @return array
    */
    public function getSettings()
    {
        $settings = new Settings($this->db);
        $setting = $settings->view();
        $this->settings = $setting['setting'];
        if($this->settings !=null){
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function installSettings($settings = null)
    {
        if($settings==null)
        {
            return $this->render('./View/Install/settings.php', '');
        }
        else
        {
            $insert = $this->db->insert('settings')
                        ->set(array(
                             'title' => $settings['title'],
                             'description'=>$settings['description'],
                             'copyright'=>$settings['copyright']
                        ));
        
            if ($insert) {
                header('Location:'.LINK_PREFIX);
            } else {
                return $this->render('./View/Install/setting.php', '');
            }
        }
    }
    

    

    
    public function getUsers()
    {
        $user = $this->db->select('users')
                    ->run();
        if(!$user){
            return false;
        }
        return true;
    }
    
    public function installDatabase($config=null)
    {
        $comments = "";
        $tokens = token_get_all(file_get_contents('./Config/config.inc.php'));
        foreach($tokens as $token)
        {
            if($token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT) {
                $comments.= $token[1]."\n";
            }
    
        }
    
        if($config!=null)
        {
            file_put_contents('./Config/config.inc.php','<?php'."\n".$comments."\n".'$config = '.var_export($config, true).';');
            header('Location:'.LINK_PREFIX);
        }
        else
        {
            return $this->render('./View/Install/database.php', '');
        }
    }
    
    public function installUser($userInfo = null)
    {
        if($userInfo==null)
        {
            return $this->render('./View/Install/user.php', '');
        }
        else
        {
                    $insert = $this->db->insert('users')
                        ->set(array(
                            "username" => $userInfo['username'],
                            "password" => md5($userInfo['password1']),
                            "email" => $userInfo['email'],
                        ));

            if ($insert) {
                header('Location:'.LINK_PREFIX);
            } else {
                return $this->render('./View/Install/user.php', '');
            }
        }
    }

    /**
    * Sistemdeki bütün görüntüleme hesaplama işlemlerini yapan metod
    *
    * @param $request
    * @param $data
    * @return string
    */
    public function calculate($request, $data)
    {
        // /posts/add gibi bir request geldi.
        $params = explode("/", $request);
        $className = __NAMESPACE__ . '\\' . $params[1];
        $extension =  explode('.',end($params));

        //call_user_func_array
        $class = new $className($this->db);
        $class->getRelatedData($this->getCategories());

        // Bu sınıfı tamamen değiştirmemiz gerek. Kullanıcının oturum açıp açmadığını
        // açtıysa, oturum bilgilerine göre neyin nasıl görüntüleneceğini belirlemeliyiz.
        //  Mesajlar uçuyordu halloldu

        if (empty($data)) {
            if(!isset($params[2])){
                $params[2]='index';
            }
            if ($params[2] != null) {
                if (isset($params[3])) {
                    $data = $class->$params[2]($params[3]);
                } else {
                    $data = $class->$params[2]();
                }
                if (isset($data['message'])) {
                    $message = $data['message'];
                } else {
                    $message = null;
                }

                if (isset($data['renderFile'])) {
                    $params[2] = $data['renderFile'];
                    $renderFile = $data['renderFile'];
                } else {
                    $renderFile = 'show';
                }

                if (isset($data['render']) && $data['render'] != false) {
                    $content = array('message' => $message, 'related' => $this->getCategories(), 'content' => $this->render('./View/' . $params[1] . '/' . mb_strtolower($params[2]) . '.php', $data));
                    return $this->render('./www/' . $data['template'] . '.php', $content);
                } else {
                    if (isset($data['message'])) {
                        $message = $data['message'];
                    } else {
                        $message = null;
                    }
                    if (($class->show() != false) && $data['template']!='admin' && $_SESSION==null) {
                        // login sayfasına gitsin
                        $data = $class->show();
                        $content = array('message' => $message, 'related' => $this->getCategories(), 'content' => $this->render('./View/' . $params[1] . '/' . $renderFile . '.php', $data));
                        return $this->render('./www/' . $data['template'] . '.php', $content);
                    } else {
                        $content = array('message' => $message, 'related' => $this->getCategories(), 'content' => $this->render('./View/Users/login.php', $data));
                        return $this->render('./www/public.php', $content);
                    }
                }
            } else {


                $data = $class->index();

                if (isset($data['renderFile'])) {
                    $params[2] = $data['renderFile'];
                    $renderFile = $data['renderFile'];
                } else {
                    $renderFile = 'index';
                }


                if (isset($data['message'])) {
                    $message = $data['message'];
                } else {
                    $message = null;
                }
                $content = array('message' => $message, 'related' => $this->getCategories(), 'content' => $this->render('./View/' . $params[1] . '/' . $renderFile . '.php', $data));
                if(!isset($data['template'])){
                    var_dump($params);
                    return $this->render('./www/' . $data['template'] . '.php', $content);
                }
                else {
                    var_dump($data);
                    return $this->render('./www/' . $data['template'] . '.php', $content);                    
                }
            }
        } else {
            $data = call_user_func_array(array($class, $params[2]), $data);

            if (isset($data['renderFile'])) {
                $params[2] = $data['renderFile'];
                $renderFile = $data['renderFile'];
            } else {
                $renderFile = 'show';
            }

            if (isset($data['message'])) {
                $message = $data['message'];
            } else {
                $message = null;
            }

            if ($class->show() != false) {
                // login sayfasına gitsin
                $data = $class->show();
                $content = array('message' => $message, 'related' => $this->getCategories(), 'content' => $this->render('./View/' . $params[1] . '/' . $renderFile . '.php', $data));
            } else {
                $content = array('message' => $message, 'related' => $this->getCategories(), 'content' => $this->render('./View/Users/login.php', $data));
            }
            return $this->render('./www/' . $data['template'] . '.php', $content);
        }
    }

    /**
    * Tema dosyalarının ihtiyaç duyulan değişkenlerle gösterilmesini sağlayan metod
    *
    * @param $file
    * @param $vars
    * @return string
    */
    public function render($file, $vars)
    {
        if (is_array($vars) && !empty($vars)) {
            extract($vars);
        }

        if ($this->settings != false && !isset($title)) {
            extract($this->settings);
        }

        ob_start();
        include $file;
        return ob_get_clean();
    }

}


?>