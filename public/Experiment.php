<?PHP

$db = new PDO("mysql:host=localhost;dbname=mybudget;charset=utf8" , "root", "");

$sql = "SELECT email FROM users";

$stmt = $db->prepare($sql);

$stmt->bindValue(':id', $_POST['user_id'], PDO::PARAM_INT);
        
$stmt->setFetchMode(PDO::FETCH_ASSOC);

$stmt->execute();

$result = [];

while($resultRow = $stmt->fetch()) {
    $result[] = $resultRow;
}

var_dump($result);


?>