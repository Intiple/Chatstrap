<?php
session_start();
require_once "Parsedown.php";

$Parsedown = new Parsedown();
require_once "config.php";


function generateLoginModal()
{
    die("Create an account on Intiple and start generating!");
}


if (isset($_POST['user_input'])) {
	setcookie('selected_model', $_POST['model_select'], time() + (30 * 24 * 60 * 60), '/'); // Cookie expires in 30 days
    $user = $_SESSION['username']; // Assign a value to $user

    $selected_model = htmlspecialchars(strip_tags($_POST['model_select'])); // Define selected_model here
    $user_input = $_POST['user_input'];

    // Retrieve previous messages from the database
    $fetch_messages_query = "SELECT user_reply, ai_reply FROM chats WHERE user = ? AND ai_model = ?";
    $fetch_messages_stmt = $mysqli->prepare($fetch_messages_query);
    $fetch_messages_stmt->bind_param("ss", $user, $selected_model);
    $fetch_messages_stmt->execute();
    $fetch_messages_result = $fetch_messages_stmt->get_result();

    $messages = array();

    $file_content = file_get_contents("sysmsg");
    if ($file_content !== false) {
        $messages[] = array("role" => "system", "content" => $file_content);
    } else {
        // Handle the error, e.g., log it or display an error message.
        echo "Error reading the file 'sysmsg'";
    }

    // Check the connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    while ($fetch_messages_row = $fetch_messages_result->fetch_assoc()) {
        $messages[] = array("role" => "user", "content" => $fetch_messages_row['user_reply']);
        $messages[] = array("role" => "assistant", "content" => $fetch_messages_row['ai_reply']);
    }

    // Add the new user input as a message
    $messages[] = array("role" => "user", "content" => $user_input);

    // Initialize bot reply with an empty string
    $bot_reply = "";
   
if ($selected_model === 'xyz') {
    
    if ($selected_model === 'xyz') {
        $bot_reply = "Hello World!";
    } 
} else {
    $bot_reply = 'The model you selected is non-existent or you don\'t have access to it.';
}


    // Insert the user and bot replies into the database
    $insert_query = "INSERT INTO chats (user_reply, ai_reply, user, ai_model) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($insert_query);

    if ($stmt) {
        $stmt->bind_param("ssss", $user_input, $bot_reply, $user, $selected_model);
        if ($stmt->execute()) {
            $stmt->close();
            $mysqli->close();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $stmt->error;
            $stmt->close();
            $mysqli->close();
        }
    } else {
        echo "Error preparing insert statement: " . $mysqli->error;
        $mysqli->close();
    }
}

$dark_mode = isset($_POST['dark_mode']);
$body_class = $dark_mode ? 'dark-mode' : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chatstrap AI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body class="<?php echo $body_class; ?>">
<div class="container mt-5">
    <h1>Chatstrap</h1>
    <p>I'm a chatbot powered by advanced artificial intelligence model XYZ. These technologies enable me to understand and respond to your messages.</p>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-3">
                <div class="card-header">
                    My messages
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php
                    $user = $_SESSION['username']; // Replace with the user's identifier

                    $fetch_query = "SELECT user_reply, ai_reply, user, ai_model FROM chats WHERE user = ?";
                    $stmt = $mysqli->prepare($fetch_query);

                    if ($stmt) {
                        $stmt->bind_param("s", $user);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            echo '<ul class="list-group mb-3">'; // Added margin-bottom
                            while ($row = $result->fetch_assoc()) {
                                $user_reply = $row['user_reply'];
                                $ai_reply = $row['ai_reply'];
                                $ai_model = $row['ai_model'];
                                $array = array(
                                    "xyz" => "XYZ",
                                );

                                $trueModel = $array[$ai_model];

                                echo '<li class="list-group-item mb-2">User: ' . htmlspecialchars($user_reply) . '</li>'; // Added margin-bottom
                                echo '<li class="list-group-item mb-2">' . $trueModel . ': ' . $Parsedown->text($ai_reply) . '</li>'; // Added margin-bottom and <pre> element
                            }
                            echo '</ul><br>';
                        } else {
                            echo '<p>No message history available. Send a message to Intiple now!</p>';
                        }

                        $stmt->close();
                    } else {
                        echo "Error preparing fetch statement: " . $mysqli->error;
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mt-3">
                <div class="card-header">
                    Send new messages
                </div>
                <div class="card-body">
                    <?php if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) { ?>
                        <p class="text-center"> Login with your Intiple account to continue and start generating! </p>
                        <div class="text-center">
                            <a href="login" class="btn btn-primary">Login</a>
                            <a href="register" class="btn btn-secondary">Sign Up</a>
                        </div>
                    <?php } else { ?>

					        <div class="alert alert-warning">
                        Please note that XYZ models have a knowledge cutoff of October 0000.
                    </div>
<?php
$check_history_query = "SELECT COUNT(*) AS num_rows FROM chats WHERE user = ?";
$check_history_stmt = $mysqli->prepare($check_history_query);
$check_history_stmt->bind_param("s", $_SESSION['username']);
$check_history_stmt->execute();
$check_history_result = $check_history_stmt->get_result();
$chatHistoryExists = ($check_history_result && $check_history_result->fetch_assoc()["num_rows"] > 0);
$trueModel2 = $array[$_COOKIE['selected_model']];
?>

                        <form method="POST" action="" id="chat-form">
<div class="form-group">
<?php if ($chatHistoryExists): ?>
<p> <?php echo $trueModel2 ?>: The model can't be changed unless you reset the thread. </p>
<?php endif; ?>
<select class="form-control" name="model_select" id="model_select" <?php echo ($chatHistoryExists) ? 'hidden' : ''; ?>>
    <?php if ($chatHistoryExists): ?>
        <option value="<?php echo $_COOKIE['selected_model'] ?>" <?php echo ($_COOKIE['selected_model'] === 'gpt-3.5-turbo') ? 'selected' : ''; ?> hidden selected><?php echo $trueModel2 ?></option>
    <?php else: ?>
	        <option value="xyz" <?php echo ($_COOKIE['selected_model'] === 'chat-bison-001') ? 'selected' : ''; ?>>XYZ </option>
    <?php endif; ?>
</select>

</div>










                            <div class="form-group">
                                <input type="text" class="form-control" name="user_input" id="user_input"
                                       placeholder="Enter your message" value="<?php echo isset($_GET['int_msg']) ? htmlspecialchars($_GET['int_msg']) : ''; ?>" required>
                            </div>
							



                            <div class="form-group">
                                <button class="btn btn-primary btn-block" type="submit">Send</button>
                            </div>
                        </form>
                        
				  <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

