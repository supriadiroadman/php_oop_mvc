<?php
class Pages extends Controller
{
  public function __construct()
  {
  }

  public function index()
  {
    if (isLoggedIn()) {
      redirect('posts');
    }

    $data = [
      'title' => 'SharePosts',
      'description' => 'This is a simple social newtword built on oop php MVC PHP Framework'
    ];
    $this->view('pages/index', $data);
  }

  public function about()
  {
    $data = [
      'title' => 'About us',
      'description' => 'App to share posts with other users'
    ];
    $this->view('pages/about', $data);
  }
}
