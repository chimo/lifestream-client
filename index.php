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
  if ("WebSocket" in window ) {
    var ws = new WebSocket( window.location.origin.replace( /https?/, "ws" ) + ":8090" ),
        formatDate;

    formatDate = function( date ) {
        var d = new Date( date );

        // Fallback to "now" if date is invalid
        if ( isNaN( d.getTime() ) ) {
            d = new Date();
        }

        return d.toISOString().slice( 0, -5 ).replace( "T", " " );
    };

    ws.onopen = function( event ) {
      console.log( 'connection established' );
    };

    ws.onerror = function ( event ) {
        console.log( 'error' );
        console.log( event );
    };

    ws.onclose = function (event) {
        console.log('closed');
        console.log(event);
    }

    ws.onmessage = function( event ) {
        console.log(JSON.parse(event.data).published);
      var item = document.createElement( 'li' ),
          list = document.getElementById( 'list' ),
          event = JSON.parse( event.data );

      item.innerHTML = "<figure>" +
                           "<figcaption>" + event.title + "</figcaption>" +
                           "<blockquote cite='" + event.source + "'>" + event.content + "</blockquote>" +
                           "<footer><a href='" + event.source + "'>" + formatDate( event.published ) + "</a></footer>" +
                       "</figure>";
      list.insertBefore( item, list.firstChild );
    };
  }
</script>

</body>
</html>

