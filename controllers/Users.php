<?php
require 'C:\xampp\htdocs\codeigniter\application\libraries\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\codeigniter\application\libraries\PHPMailer-master\src\SMTP.php';
require 'C:\xampp\htdocs\codeigniter\application\libraries\PHPMailer-master\src\Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *        http://example.com/index.php/welcome
     *    - or -
     *        http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('pagination');
        $this->load->library('form_validation');
        $this->load->model('Pagination_model');
        $this->load->model('Users_model');
        $this->load->helper(array('form', 'url'));


    }

    //Register user
    public function register()
    {
        // Check if there`s a logged user
        if (empty($this->session->userdata('user'))) {

            $this->load->model("Users_model");

            $this->load->helper(array('form', 'url'));
            $this->load->library('form_validation');

            $errors = array(
                'error' => ''
            );

            //Is POST
            if ($this->input->server('REQUEST_METHOD') == 'POST') {

                //Counting the whitespaces in the username and password
                $counts = array(
                    'usernameSpaces' => substr_count($_POST['username'],' ',2),
                    'passwordSpaces' => substr_count($_POST['password'],' ',2)
                );

                //If no whitespaces in the username or password
                if ($counts['usernameSpaces'] < 1 && $counts['passwordSpaces'] < 1) {

                    // Setting validations for the register form - username
                    $this->form_validation->set_rules(
                        'username', 'Username',
                        'required|min_length[5]|max_length[12]|is_unique[users.username]',
                        array(
                            'required' => 'You have not provided %s.',
                            'is_unique' => 'This %s already exists.'
                        )
                    );

                    // Setting validations for the register form - password, password repeat, email
                    $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
                    $this->form_validation->set_rules('passwordRepeat', 'Password Confirmation', 'required|matches[password]');
                    $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]',
                        array(
                            'required' => 'You have not provided %s.',
                            'is_unique' => 'This %s already exists.',
                            'valid_email' => 'You have not provided a valid %s.'
                        )
                    );

                    $password = $this->input->post('password');
                    if (preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,20}$/',$password)) {


                        // If the form validation(run) is false
                        if ($this->form_validation->run() == false) {
                            $this->load->view("registration.php", $errors);

                            // If the form validation(run) is true get the user data from the form and insert in DB
                        } else {
                            $postDataForDB = array(
                                'username' => $this->input->post('username'),
                                'password' => md5($this->input->post('password')),
                                'email' => $this->input->post('email'),
                            );

                            $this->Users_model->insert_User($postDataForDB);
                            redirect('login');
                        }

                    }else{
                        //Setting an error message
                        $errors = array(
                            'error' => 'Your password must contain at least one lowercase letter uppercase letter
                             at least one digit and at least one special sign'
                        );

                        //Loading the view with error
                        $this->load->view('registration.php',$errors);
                    }

                 //If whitespaces in the username or password
                }else{

                    //Setting an error message
                    $errors = array(
                        'error' => 'You can`t use SPACE in username or password'
                    );

                    //Loading the view with error
                    $this->load->view('registration.php',$errors);
                }

              //Is not POST
            } else {
                $this->load->view('registration.php',$errors);
            }

          //Logged user
        } else {
            redirect('image/user');
        }

    }


    //Login user
    public function login()
    {
        $this->load->model("Users_model");

        // Check if there`s a logged user
        if (empty($this->session->userdata('user'))) {

            //Is POST email
            if (isset($_POST['user_email'])) {
                $user_email = trim($_POST['user_email']);
                $user_password = md5($_POST['password']);

                //Is there an email
                if (!$user_email) {
                    $errors[] = 'No mail';
                }
                //Is there a password
                if (!$user_password) {
                    $errors[] = 'No password';
                }
                //Is there an errors
                if (!empty($errors)) {
                    echo json_encode(array('errors' => $errors));
                    exit;
                }


                $errors = array();

                //Getting the user from the DB with his email from the form
                $this->db->select('*');
                $this->db->from('Users');
                $this->db->where('email', $user_email);
                $query = $this->db->get();
                $user = $query->row_array();

                //Checking the user info in the DB
                if (empty($user)) {
                    $errors[] = 'User not found';
                    echo json_encode(array('errors' => $errors));
                    exit;
                }

                //Checking password
                if ($user['password'] != $user_password) {
                    $errors[] = 'Passwords do not match';
                }

                //Is errors
                if (!empty($errors)) {

                    echo json_encode(array('errors' => $errors));
                    exit;
                }

                //Get the user data from the form and set it in the session
                $userData = array(
                    'email' => $this->input->post('user_email'),
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role_id' => $user['role_id']
                );

                $this->session->set_userdata(['user' => $userData]);

                echo json_encode(array('status' => 'ok'));

              //Not POST
            } else {
                $this->load->view("login.php");
            }
            //Logged user
        } else {
            redirect('image/user');
        }
    }

    //Logout user
    public function logout()
    {
        //Destroying the session
        $this->session->sess_destroy();
        redirect('/');
    }


    //Home page with last 10 uploaded pictures
    public function home()
    {
        $this->load->model('Image_model');

        //Getting the last 10 images from DB/date-added/DESC
        $images = $this->Image_model->getLimitImages();

        $data = [
            'images' => $images
        ];

        $this->load->view('home.php', $data);
    }

    //All users
    public function allUsers()
    {

        // Check if there`s a logged user
        if (!empty($this->session->userdata('user'))) {

            //Config for the pagination
            $config = array();
            $config["base_url"] = base_url() . "Users/allUsers";
            $total_row = $this->Pagination_model->record_count_users();
            $config["total_rows"] = $total_row;
            $config["uri_segment"] = 3;
            $config["per_page"] = 10;
            $config["use_page_numbers"] = TRUE;
            $config["num_links"] = $total_row;
            $config["cur_tag_open"] = '';
            $config["cur_tag_close"] = '';
            $config["next_link"] = 'Next';
            $config["prev_link"] = 'Previous';

            $config["full_tag_open"] = '<ul class="pagination">';
            $config["full_tag_close"] = '</ul>';
            $config["first_link"] = "&laquo;";
            $config["first_tag_open"] = "<li>";
            $config["first_tag_close"] = "</li>";
            $config["last_link"] = "&raquo;";
            $config["last_tag_open"] = "<li>";
            $config["last_tag_close"] = "</li>";
            $config['next_link'] = '&gt;';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '<li>';
            $config['prev_link'] = '&lt;';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '<li>';
            $config['cur_tag_open'] = '<li class="active"><a href="#">';
            $config['cur_tag_close'] = '</a></li>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';

            $this->pagination->initialize($config);

            if ($this->uri->segment(3)) {
                $page = ($this->uri->segment(3));
            } else {
                $page = 1;
            }

            //Getting all the users from the DB and putting them in the data["results"]
            $data["results"] = $this->Pagination_model->getAllUsers($config["per_page"], $page);
            $str_links = $this->pagination->create_links();

            $data["links"] = $str_links;

            $this->load->view('users.php', $data);

          //No logged user
        } else {
            redirect('login');
        }
    }

    //User updating username
    public function updateUserUsername()
    {
        // Check if there`s a logged user
        if (!empty($this->session->userdata('user'))) {

            //Getting the data for the user from the DB
            $userData = $this->Users_model->dataForUpdate();

            //Is POST
            if ($this->input->server('REQUEST_METHOD') == 'POST') {

                //Setting rules for the form
                $this->form_validation->set_rules(
                    'username', 'Username',
                    'required|min_length[5]|max_length[12]|is_unique[users.username]',
                    array(
                        'required' => 'You have not provided %s.',
                        'is_unique' => 'This %s already exists.'
                    )
                );

                //Checking if the password is the same
                if (md5($_POST['password']) == $userData['password']) {

                    //Set the new username in the DB
                    $this->db->set('username', $_POST['username'])
                        ->where('id', $this->session->userdata('user')['id']);
                    $this->db->update('users');

                    //Redirect for new login
                    redirect('/user/logout');

                  //Not same password
                } else {
                    $this->load->view('password_mismatch.php');
                }

              //Not POST
            } else {
                $this->load->view('Update_Profile/update_username.php');
            }

          //No user
        } else {
            redirect('login');
        }
    }

    //User update password
    public function updateUserPassword()
    {
        // Check if there`s a logged user
        if (!empty($this->session->userdata('user'))) {

            //Getting the data for the user from the DB
            $userData = $this->Users_model->dataForUpdate();

            //Is POST
            if ($this->input->server('REQUEST_METHOD') == 'POST') {

                //Setting rules for the form
                $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
                $this->form_validation->set_rules('repeat_password', 'Password Confirmation', 'required|matches[password]',
                    array(
                        'required' => 'You have not provided %s.'
                    ));

                //Checking if the password is the same
                if (md5($_POST['old_password']) == $userData['password']) {

                    //Set the new password in the DB
                    $this->db->set('password', md5($_POST['password']))
                        ->where('id', $this->session->userdata('user')['id']);
                    $this->db->update('users');

                    //Redirect for new login
                    redirect('/user/logout');

                  //Not same password
                } else {
                    $this->load->view('password_mismatch.php');
                }

              //No Post
            } else {
                $this->load->view('Update_Profile/update_password.php');
            }

          //No user
        } else {
            redirect('login');
        }
    }

    //User update email
    public function updateUserEmail()
    {
        // Check if there`s a logged user
        if (!empty($this->session->userdata('user'))) {

            //Getting the data for the user from the DB
            $userData = $this->Users_model->dataForUpdate();

            //Is POST
            if ($this->input->server('REQUEST_METHOD') == 'POST') {

                //Setting rules for the form
                $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]',
                    array(
                        'required' => 'You have not provided %s.',
                        'is_unique' => 'This %s already exists.',
                        'valid_email' => 'You have not provided a valid %s.'
                    )
                );

                //Checking if the password is the same
                if (md5($_POST['password']) == $userData['password']) {

                    //Set the new email in the DB
                    $this->db->set('email', $_POST['email'])
                        ->where('id', $this->session->userdata('user')['id']);
                    $this->db->update('users');

                    //Redirect for new login
                    redirect('/user/logout');

                  //Not same password
                } else {
                    $this->load->view('password_mismatch.php');
                }

              //Not POST
            } else {
                $this->load->view('Update_Profile/update_email.php');
            }

          //No user
        } else {
            redirect('login');
        }
    }

    //Contact us form
    public function contactEmail()
    {

        // Check if there`s a logged user
        if (!empty($this->session->userdata('user'))) {

            //Is POST
            if ($this->input->server('REQUEST_METHOD') == 'POST') {

                //Is set email in POST
                if (isset($_POST['email'])) {

                    //If not valid email
                    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                        echo 'Enter a valid email';

                      //Valid email
                    } else {
                        $mail = new PHPMailer();
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                        $mail->SMTPAuth = true;                               // Enable SMTP authentication
                        $mail->Username = 'viktor.matsanov@gmail.com';                 // SMTP username
                        $mail->Password = '';                           // SMTP password
                        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                        $mail->Port = 587;
                        $mail->setFrom('viktor.matsanov@gmail.com', 'Viktor Matsanov');
                        $mail->addAddress($_POST['email']);
                        $mail->isHTML(true);
                        $mail->Subject = $_POST['subject'];
                        $mail->Body = "<h3>" . $_POST['message'] . "</h3>";

                        //Is email send
                        if ($mail->send()) {
                            echo "Email send !";

                          //Not send
                        } else {
                            echo "Error";
                        }
                    }
                }

              //Not POST
            } else {
                $this->load->view('email_form.php');
            }

          //No user
        } else {
            redirect('login');
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */