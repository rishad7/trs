<?php
require('header.php');
?>

<!DOCTYPE html lang="en-US">
<html>

<head>
    <title>TRS</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>

    <?php
    $msg = '';

    if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {

        $file = "./data/users.json";
        $json_array = json_decode(file_get_contents($file), true);

        $users = array();

        if (is_array($json_array)) {
            $users = $json_array;
        }

        $id = 0;
        $username = $_POST['username'];
        $password = $_POST['password'];
        $flag = false;
        $doc_name = "";

        foreach ($json_array as $key => $value) {
            if (strtolower(str_replace(' ', '', $value['username'])) == strtolower(str_replace(' ', '', $username)) && $value['password'] == $password) {
                $flag = true;
                $id = $value['id'];
                $doc_name = $value['doc_name'];
                break;
            }
        }

        if ($flag) {
            $_SESSION['id'] = $id;
            $_SESSION['valid'] = true;
            $_SESSION['timeout'] = time();
            $_SESSION['username'] = $username;
            $_SESSION['doc_name'] = $doc_name;
            header('Location: home.php');
            exit();
        } else {
            $msg = 'Invalid username or password';
        }
    }
    ?>

    <section class="h-screen">
        <div class="px-6 h-full text-gray-800">
            <div class="flex xl:justify-center lg:justify-between justify-center items-center flex-wrap h-full g-6">
                <div class="grow-0 shrink-1 md:shrink-0 basis-auto xl:w-6/12 lg:w-6/12 md:w-9/12 mb-12 md:mb-0">
                    <img src="./assets/images/login_bg.jpg" class="w-full" alt="Sample image" />
                </div>
                <div class="xl:ml-20 xl:w-5/12 lg:w-5/12 md:w-8/12 mb-12 md:mb-0">
                    <?php if ($msg) { ?>
                        <div class="bg-red-100 rounded-lg py-5 px-6 mb-4 text-base text-red-700 mb-3" role="alert">
                            <?php echo $msg; ?>
                        </div>
                    <?php } ?>
                    <form class="form-signin" role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

                        <!-- Username input -->
                        <div class="mb-6">
                            <input type="text" class="form-control block w-full px-4 py-2 text-xl font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" name="username" id="username" placeholder="Username" required autofocus />
                        </div>

                        <!-- Password input -->
                        <div class="mb-6">
                            <input type="password" class="form-control block w-full px-4 py-2 text-xl font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" name="password" id="password" placeholder="Password" required />
                        </div>

                        <div class="flex justify-between items-center mb-6">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" name="remember_me" id="remember_me" />
                                <label class="form-check-label inline-block text-gray-800" for="remember_me">Remember me</label>
                            </div>
                            <a href="#" class="text-gray-800">Forgot password?</a>
                        </div>

                        <div class="text-center lg:text-left">
                            <button type="submit" class="inline-block px-6 py-3 bg-blue-600 text-white font-medium text-sm leading-snug uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out" name="login">
                                Login
                            </button>
                            <p class="text-sm font-semibold mt-2 pt-1 mb-0">
                                Don't have an account?
                                <a href="#" class="text-red-600 hover:text-red-700 focus:text-red-700 transition duration-200 ease-in-out">Register</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>


</body>

</html>