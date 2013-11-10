<?php
# consume.php
$curl = curl_init('http://127.0.0.1/TecValles/Todo/json.php?id=2');
echo json_decode(curl_exec($curl));
curl_close($curl);
?>
