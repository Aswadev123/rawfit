<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div id="chat-box"></div>
    <input type="text" id="message" placeholder="Type a message...">
    <button onclick="sendMessage()">Send</buttoon>
        <script>
            function sendMessage() {
    var message = document.getElementById('message').value;
    
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "send.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("message=" + encodeURIComponent(message));
    
    xhr.onload = function() {
        if (xhr.status == 200) {
            loadMessages(); // refresh messages after sending
        }
    }
}

function loadMessages() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "load.php", true);
    xhr.onload = function() {
        if (xhr.status == 200) {
            document.getElementById('chat-box').innerHTML = xhr.responseText;
        }
    }
    xhr.send();
}

setInterval(loadMessages, 1000); // refresh chat every 1 sec


        </script>
        <?php
$con = new mysqli("localhost", "root", "", "chatdb");

$message = $_POST['message'];
// Assume sender = 'User1' for simplicity
$con->query("INSERT INTO messages (sender, message) VALUES ('User1', '$message')");
?>

</body>
</html>