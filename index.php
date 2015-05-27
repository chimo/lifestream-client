<!DOCTYPE html>
<html>
<head>
    <title>lifestream</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>



<?php

require_once('config.php');

$dsn = 'mysql:dbname=' . $config['db']['name'] . ';host=' . $config['db']['host'];
$user = $config['db']['user'];
$password = $config['db']['password'];

try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

$sql = 'SELECT title, content, published, foreign_url, subscription.type FROM event INNER JOIN subscription ON event.subscription_id = subscription.id ORDER BY published DESC';

$res = $dbh->query($sql);
$html = '<ol id="list">';

while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
    $html .= '<li class="' . $row['type'] . '"><figure><figcaption>';
    $html .= $row['title'];
    $html .= '</figcaption>';
    $html .= '<blockquote cite="' . $row['foreign_url'] . '">' . $row['content'] . '</blockquote>';
    $html .= '<footer><a href="' . $row['foreign_url'] . '">' . $row['published'] . '</a></footer>';
    $html .= '</figure></li>';
}

$html .= '</ol>';

echo $html;

?>

<script>
( function() {
    "use strict";

    window.ls = {
        "port": <?php echo $config['websockets']['port'] ?>
    }
}() );
</script>
<script src="js/websockets.js"></script>

</body>
</html>

