<?php
class Users extends Controller
{
  public function __construct()
  {
    $this->userModel = $this->model('User');
  }

  public function register()
  {
    // Check for POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Proccess form
      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Init data
      $data = [
        'name'                 => trim($_POST['name']),
        'email'                => trim($_POST['email']),
        'password'             => trim($_POST['password']),
        'confirm_password'     => trim($_POST['confirm_password']),
        'name_err'             => '',
        'email_err'            => '',
        'password_err'         => '',
        'confirm_password_err' => '',
      ];
      // Validate name
      if (empty($data['name'])) {
        $data['name_err'] = "Please enter name";
      }

      // Validate email
      if (empty($data['email'])) {
        $data['email_err'] = "Please enter email";
        // Email has registered in database 
      } elseif ($this->userModel->findUserByEmail($data['email'])) {
        $data['email_err'] = "Email is  already taken";
      }

      // Validate password
      if (empty($data['password'])) {
        $data['password_err'] = 'Please enter password';
      } elseif (strlen($data['password']) < 6) {
        $data['password_err'] = 'Password must be at least 6 characters';
      }

      // Validate confirm password
      if (empty($data['confirm_password'])) {
        $data['confirm_password_err'] = "Please confirm password";
      } elseif ($data['password'] != $data['confirm_password']) {
        $data['confirm_password_err'] = "Password do not match";
      }

      // Make sure errors are empty
      if (empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
        // Validated / no errors

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Register User
        if ($this->userModel->register($data)) {
          flash('register_success', 'You are registered and can log in');
          redirect('users/login');
        } else {
          die('Somethinf went wrong');
        }
      } else {
        // Load view with errors
        $this->view('users/register', $data);
      }
    } else {
      // Init data
      $data = [
        'name'                 => '',
        'email'                => '',
        'password'             => '',
        'confirm_password'     => '',
        'name_err'             => '',
        'email_err'            => '',
        'password_err'         => '',
        'confirm_password_err' => '',
      ];

      // Load view without submit
      $this->view('users/register', $data);
    }
  }


  public function login()
  {
    // Check for POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Proccess form
      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Init data
      $data = [
        'email'                => trim($_POST['email']),
        'password'             => trim($_POST['password']),
        'email_err'            => '',
        'password_err'         => '',
      ];

      // Validate email
      if (empty($data['email'])) {
        $data['email_err'] = "Please enter email";
      }

      // Validate password
      if (empty($data['password'])) {
        $data['password_err'] = 'Please enter password';
      }

      if ($this->userModel->findUserByEmail($data['email'])) {
        // User found

      } else {
        // User not found
        $data['email_err'] = 'No user found';
      }

      // Make sure errors are empty
      if (empty($data['email_err']) && empty($data['password_err'])) {
        // Validated
        // Check and set logged in user
        $loggedInUser = $this->userModel->login($data['email'], $data['password']);

        // $loggedInUser = object data user
        if ($loggedInUser) {
          // Create Session
          $this->createUserSession($loggedInUser);
        } else {
          $data['password_err'] = 'Password incorrect';

          $this->view('users/login', $data);
        }
      } else {
        // Load view with errors
        $this->view('users/login', $data);
      }
    } else {
      // Init data
      $data = [
        'email'                => '',
        'password'             => '',
        'email_err'            => '',
        'password_err'         => '',
      ];

      // Load view without submit
      $this->view('users/login', $data);
    }
  }

  // Create session after login
  public function createUserSession($user)
  {
    $_SESSION['user_id'] = $user->id;
    $_SESSION['user_email'] = $user->email;
    $_SESSION['user_name'] = $user->name;
    redirect('posts');
  }

  // Unset session after logout
  public function logout()
  {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    session_destroy();
    redirect('users/login');
  }
}
