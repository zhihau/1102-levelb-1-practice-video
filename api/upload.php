    <?php
    include_once "../base.php";
    $table=$_POST['table'];
    $db=new DB($table);
    $data=$db->find($_POST['id']);

    if(!empty($_FILES['img']['tmp_name'])){
        $fn=$_FILES['img']['name'];
        move_uploaded_file($_FILES['img']['tmp_name'],'../img/'.$fn);
        $data['img']=$fn;
    }
    $db->save($data);
    to("../admin.php?do=$table");