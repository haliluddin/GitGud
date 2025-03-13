<?php
    session_start();
    include_once './links.php'; 
    include_once './secondheader.php'; 
    require_once './classes/db.class.php';
    $userObj = new User();
    $email = $password = '';
    $err = $email_err = $password_err = '';

    if (isset($_SESSION['user'])) {
        if ($userObj->isVerified($_SESSION['user']['id']) == 1) {
            header('Location: index.php');
            exit();
        } else {
            header('Location: email/verify_email.php');
            exit();
        }
    }

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            if (isset($_POST['email']) && isset($_POST['password'])) {
                $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
                $password = trim($_POST['password']);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $email_err = 'Invalid email format';
                } else {
                    $userObj->email = $email;
                    $userObj->password = $password;
                    
                    $user = $userObj->checkUser();
                    if ($user) {
                        $_SESSION['user'] = [];
                        $_SESSION['user']['id'] = $user['id'];
                        
                        // Save user['user_session] in localStorage
                        echo "<script>
                            localStorage.setItem('xdata', '" . $user['user_session'] . "');
                        </script>";

                        $remember = $_POST['remember_me'] ?? false;

                        if ($remember) {
                            $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
                            echo "<script>
                                localStorage.setItem('email', '$safeEmail');
                                </script>";
                                // localStorage.setItem('password', '" . htmlspecialchars($password, ENT_QUOTES, 'UTF-8') . "');
                        } else {
                            echo "<script>
                                    localStorage.removeItem('email');
                                    </script>";
                                    // localStorage.removeItem('password');
                        }

                        if ($userObj->isVerified($_SESSION['user']['id']) == 1) {
                            // header('Location: index.php');
                            // exit();
                            echo "<script>
                                window.location.href = 'index.php';
                            </script>";
                        } else {
                            // header('Location: email/verify_email.php');
                            // exit();
                            echo "<script>
                                window.location.href = 'email/verify_email.php';
                            </script>";
                        }
                    } else {
                        $err = 'Invalid email or password';
                    }
                }
            } else {
                if (empty($_POST['email'])) {
                    $email_err = 'Email is required';
                }
                if (empty($_POST['password'])) {
                    $password_err = 'Password is required';
                }
            }
            break;
        case 'GET':
            if (isset($_GET['email']))
                $email = filter_var(trim($_GET['email']), FILTER_SANITIZE_EMAIL);
            break;
    }
?>

<title>Sign In</title>
<style>
    main {
        background-image: url('assets/images/customer.jpg');
        background-size: cover;
        background-repeat: no-repeat;
    }
</style>
<main class="whole">
    <div class="leftside">
        <img src="./assets/images/logo.png" alt="">
        <p>A streamlined ordering platform connecting customers to various food stalls.</p>
    </div>
    <form action="" class="form" method="POST">
        <h4 class="fw-bold">Sign In</h4>
        <span class="text-danger"><?php echo $err; ?></span>
        <div class="input-group">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" placeholder="Enter your email" required/>
            <span class="text-danger"><?php echo $email_err; ?></span>
        </div>
        <div class="input-group mb-2">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required/>
            <span class="text-danger"><?php echo $password_err; ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember_me" id="rememberMeCheckbox">
                <label class="form-check-label" for="rememberMeCheckbox">Remember Me</label>
            </div>
            <a href="resetpassword.php">Forgot Password?</a>
        </div><br>
        <div class="btns-group d-block text-center">
            <input type="submit" value="Sign In" class="button">
        </div><br>
        <span class="d-block text-center">Don't have an account? <a href="./signup.php">Sign Up</a></span>
    </form>
</main>
<script src="./assets/js/rememberme.js"></script>
<?php
    include_once './footer.php'; 
?>
