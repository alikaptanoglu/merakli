<?php
/*
*
* Yöneticilere verileri listeleyen sayfa
*
* Verileri bu tablo vasıtasıyla listeleyip,
* ID'ye göre silme ve düzenleme linklerini oluşturacağız.
*
* @author Midori Kocak 2014
*
*/
?>
<h2>Girdiler</h2>
<table>
    <thead>
        <tr>
            <th>Id</th>
            <th>Başlık</th>
            <th>Oluşturulma Tarihi</th>
            <th>Güncelleme Tarihi</th>
            <th>Kategori</th>
            <th>İşlemler</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($posts as $post):
            ?>
            <tr>
                <td><?=$post['id']?></td>
                <td><?=$post['title']?></td>
                <td><?=$post['created']?></td>
                <td><?=$post['updated']?></td>
                <td><?=$post['category_id']?></td>
                <td><a href="/Cms/index.php/Posts/edit/<?=$post['id']?>">Güncelle</a>  <a href="/Cms/index.php/Posts/delete/<?=$post['id']?>">Sil</a></td>
            </tr>
            <?php
        endforeach;
        ?>
    </tbody>
</table>