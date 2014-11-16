<?php
/**
 * Tüm sistemdeki girdileri yönetecek olan girdi sınıfıdır.
 *
 * Sistemdeki girdilerin düzenlenmesini, silinmesini, görüntülenmesini,
 * listelenmesini ve eklenmesini kontrol eden sınıftır.
 *
 * @author     Midori Kocak <mtkocak@mtkocak.net>
 */

namespace Midori\Cms;

use \PDO;

class Posts extends Assets
{


    /**
     * Girdi başlığı
     *
     * @var string
     */
    public $title;

    /**
     * Girdinin içeriği
     *
     * @var string
     */
    public $content;

    /**
     * Girdinin ait olduğu benzersiz kategori kimliği
     *
     * @var int
     */
    public $category_id;


    /**
     * Girdinin hangi tarihte oluşturulduğunu gösteren değişken
     *
     * @var string
     */
    private $created;

    /**
     * Girdinin hangi tarihte güncellendiğini gösteren değişken
     *
     * @var string
     */
    private $updated;

    /**
     * Şu anki tarihi döndüren yardımcı metod
     *
     * @return string tarihi mysql formatında döndürür
     */
    public function getDate()
    {
        date_default_timezone_set('Europe/Istanbul');
        $currentDate = date("Y-m-d H:i:s");

        return $currentDate;
    }

    /**
     * Girdi ekleyen metod, verilerin kaydedilmesini sağlar.
     *
     * @param string $title Girdi başlığı
     * @param string $content Girdi içeriği
     * @param int $category_id Girdi kategorisinin benzersiz kimliği
     * @return bool eklendiyse doğru, eklenemediyse yanlış değer döndürsün
     */
    public function add($title = null, $content = null, $category_id = null)
    {
        if (!$this->checkLogin()) {
            return false;
        }
        if ($title != null) {
            // Tarih içeren alanları elle girmiyoruz. Sistemden doğrudan isteyen fonksiyonumuz var.
            $date = $this->getDate();

            // insert
            $insert = $this->db->insert('posts')
                        ->set(array(
                            "title" => $title,
                            "content" => $content,
                            "category_id" => $category_id,
                            "created" => $date,
                            "updated" => $date
                        ));

            if ($insert) {
                // Veritabanı işlemi başarılı ise sınıfın objesine ait değişkenleri değiştirelim
                $this->id = $this->db->lastInsertId();
                $this->title = $title;
                $this->content = $content;
                $this->created = $date;
                $this->updated = $date;
                $this->category_id = $category_id;

                return true;
            } else {
                return false;
            }


        } else {
            $files = $this->db->select('files')
                ->run();
            if(empty($files)){
                $files = array();
            }
            return array('render' => true, 'template' => 'admin', 'files'=>$files ,'categories' => $this->related['categories']);
        }
    }

    /**
     * Tek bir girdinin gösterilmesini sağlayan method
     *
     * @param int $id Girdinin benzersiz index'i
     * @return array gösterilebildyise dizi türünde verileri döndürsün, gösterilemediyse false, yanlış değeri döndürsün
     */
    public function view($id)
    {

        // Eğer daha önceden sorgu işlemi yapıldıysa, sınıf objesine yazılmıştır.
        if ($id == $this->id) {
            return array("id" => $this->id, "title" => $this->title, "content" => $this->content, "category_id" => $this->category_id, "created" => $this->created, "updated" => $this->updated);
        } else {
            // Buradan anlıyoruz ki veri henüz çekilmemiş. Veriyi çekmeye başlayalım
            $query = $this->db->select('posts')
                ->where('id',$id)
                    ->run();
            if ($query) {
                $post = $query[0];

                $this->id = $post['id'];
                $this->title = $post['title'];
                $this->content = $post['content'];
                $this->created = $post['created'];
                $this->updated = $post['updated'];
                $this->category_id = $post['updated'];

                $result = array('template' => 'public', 'post' => $post, 'render' => true);

                return $result;
            }
        }

        // Eğer iki işlem de başarısız olduysa, false, yanlış değer döndürelim.
        return false;
    }

    /**
     * Tüm girdilerin listelenmesini sağlayan metod.
     *
     * @return bool listelenebildiyse doğru, listelenemediyse yanlış değer döndürsün
     */
    public function index()
    {
        $query = $this->db->select('posts')
                ->run();

        if ($query) {
            // Buradaki fetchAll metoduyla tüm değeleri diziye çektik.
            $result = array('render' => true, 'template' => 'public', 'posts' => $query);
            return $result;
        } else {
           $result = array('render' => true, 'template' => 'public', 'posts' => array());
        }
    }

    /**
     * Tüm girdilerin listelenmesini sağlayan metod.
     *
     * @return bool listelenebildiyse doğru, listelenemediyse yanlış değer döndürsün
     */
    public function show()
    {
        if (!$this->checkLogin()) {
            return false;
        }
        $query = $this->db->select('posts')
                ->run();
        if ($query) {
            // Buradaki fetchAll metoduyla tüm değeleri diziye çektik.
            $result = array('render' => true, 'template' => 'admin', 'posts' => $query);
            return $result;
        } else {
            return array('render' => true, 'template' => 'admin', 'posts' => array());
        }
    }


    /**
     * Girdi düzenleyen metod. Verilen Id bilginse göre, alınan bilgi ile sistemdeki bilgiyi değiştiren
     * güncelleyen metod.
     *
     * @param int $id Girdinin benzersiz index'i
     * @return bool düzenlendiyse doğru, eklenemediyse yanlış değer döndürsün
     */
    public function edit($id = null, $title = null, $content = null, $category_id = null)
    {
        if (!$this->checkLogin()) {
            return false;
        }
        if ($title != null) {

            // Tarih içeren alanları elle girmiyoruz. Sistemden doğrudan isteyen fonksiyonumuz var.
            $date = $this->getDate();
            
            $update = $this->db->update('posts')
                        ->where('id', $id)
                        ->set(array(
                            "title" => $title,
                            "content" => $content,
                            "category_id" => $category_id,
                            "updated" => $date,
                            "id" => $id
                        ));

            if ($update) {
                return true;
            } else {
                return false;
            }

        } else {
            $oldData = $this->view($id);
            return array('template' => 'admin', 'render' => true, 'categories' => $this->related['categories'], 'post' => $oldData['post']);
        }
    }

    /**
     * Girdi silen metod, verilerin silinmesini sağlar.
     * Geri dönüşü yoktur.
     *
     * @param int $id Girdinin benzersiz index'i
     * @return bool silindiyse doğru, eklenemediyse yanlış değer döndürsün
     */
    public function delete($id)
    {
        if (!$this->checkLogin()) {
            return false;
        }
        $query = $this->db->delete('posts')
                    ->where('id', $id)
                    ->done();
        return array('template' => 'admin', 'render' => false);
    }

}

?>

