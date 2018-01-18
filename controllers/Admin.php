<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));

        $this->load->model('Admin_model');
        $this->load->model('Pagination_model');

        $this->load->library('session');
        $this->load->library('pagination');


    }

    //Admin panel - last 5 users and pictures listed
    public function dashboard()
    {
        if (!empty($this->session->userdata('user'))) {

            //Getting the role id from DB
            $idS = $this->Admin_model->getRoleId();

            //Check if the user is authorized
            if ($idS['role_id'] == 2) {

                //Getting the last five users
                $lastFiveUsers = $this->Admin_model->lastFiveUsers();

                //Getting the last five images
                $images = $this->Admin_model->lastFiveImages();

                $data = [
                    'lastFiveUsers' => $lastFiveUsers,
                    'images' => $images
                ];

                $this->load->view('Admin/dashboard.php', $data);

                //Unauthorized
            } else {
                $this->load->view('unauthorized.php');
            }
        } else {
            redirect('login');
        }
    }

    //Updating usernames
    public function userUpdate($id)
    {
        if (!empty($this->session->userdata('user'))) {

            //Getting the role id from DB
            $idS = $this->Admin_model->getRoleId();

            //Check if the user is authorized
            if ($idS['role_id'] == 2) {

                //Update username
                $this->db->set('username', $_POST['username'])
                    ->where('id', $id);
                $this->db->update('users');

                //Unauthorized
            } else {
                $this->load->view('unauthorized.php');
            }
        } else {
            redirect('login');
        }
    }

    //Deleting a user
    public function userDelete($id)
    {
        if (!empty($this->session->userdata('user'))) {

            //Getting the role id from DB
            $idS = $this->Admin_model->getRoleId();

            //Check if the user is authorized
            if ($idS['role_id'] == 2) {

                //Delete user
                $this->db->delete('users', ['id' => $id]);

                redirect('admin/users/table');

                //Unauthorized
            } else {
                $this->load->view('unauthorized.php');
            }
        } else {
            redirect('login');
        }
    }

    //Listing all pictures in Admin panel
    public function allPictures()
    {
        if (!empty($this->session->userdata('user'))) {

            //Getting the role id from DB
            $idS = $this->Admin_model->getRoleId();

            //Check if the user is authorized
            if ($idS['role_id'] == 2) {

                //Config the pagination
                $config = array();
                $config["base_url"] = base_url() . "admin/users/allPictures";
                $total_row = $this->Pagination_model->record_count();
                $config["total_rows"] = $total_row;
                $config["uri_segment"] = 4;
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

                if ($this->uri->segment(4)) {
                    $page = ($this->uri->segment(4));
                } else {
                    $page = 1;
                }

                //Getting all users
                $data["results"] = $this->Pagination_model->fetch_categories($config["per_page"], $page);
                $str_links = $this->pagination->create_links();

                $data["links"] = $str_links;

                $this->load->view('Admin/admin_all_pictures.php', $data);

                //Unauthorized
            } else {
                $this->load->view('unauthorized.php');
            }
        } else {
            redirect('login');
        }


    }

    //Deleting picture
    public function pictureDelete($id)
    {
        if (!empty($this->session->userdata('user'))) {

            //Getting the role id from DB
            $idS = $this->Admin_model->getRoleId();

            //Check if the user is authorized
            if ($idS['role_id'] == 2) {

                //Delete picture
                $this->db->delete('images', ['id' => $id]);

                redirect('admin/users/allPictures');

                //Unauthorized
            } else {
                $this->load->view('unauthorized.php');
            }
        } else {
            redirect('login');
        }
    }

    //Deleting comment
    public function commentDelete($comment_id)
    {
        if (!empty($this->session->userdata('user'))) {

            //Getting the role id from DB
            $idS = $this->Admin_model->getRoleId();


            //Check if the user is authorized
            if ($idS['role_id'] == 2) {

                //Delete comment
                $this->db->delete('comments', ['comment_id' => $comment_id]);

                redirect('admin/users/allPictures');

                //Unauthorized
            } else {

                $this->load->view('unauthorized.php');
            }
        } else {
            redirect('login');
        }
    }

    //Admin - table with all users/ comments count/ date added/ actions(edit,delete,pictures)
    public function usersTable()
    {

        if (!empty($this->session->userdata('user'))) {
            //Getting the role id from DB
            $idS = $this->Admin_model->getRoleId();

            //Check if the user is authorized
            if ($idS['role_id'] == 2) {

                //Getting all the users
                $users = $this->db->select('id, username, date_added')->from('users')->get()->result_array();

                foreach ($users as &$user) {

                    //Setting comments count in the user[]
                    $user['comments_count'] = $this->db->select('id')
                        ->from('comments')
                        ->where('user_id', $user['id'])
                        ->count_all_results();
                }

                $data = [
                    'users' => $users
                ];

                $this->load->view('Admin/users_table.php', $data);

                //Unauthorized
            } else {
                $this->load->view('unauthorized.php');
            }
        } else {
            redirect('login');
        }
    }

    //Getting user pictures
    public function userPictures($userID)
    {

        //Getting the role id from DB
        $idS = $this->Admin_model->getRoleId();

        //Check if the user is authorized
        if ($idS['role_id'] == 2) {

            //Getting user`s pictures
            $images = $this->Admin_model->getImagesByUser($userID);

            $data = [
                'images' => $images
            ];

            $this->load->view('Admin/admin_user_pictures', $data);

          //Unauthorized
        } else {
            $this->load->view('unauthorized.php');
        }
    }

    //Getting the modal for updating usernames
    public function getUserModal($userID)
    {

        //Getting the role id from DB
        $idS = $this->Admin_model->getRoleId();

        //Check if the user is authorized
        if ($idS['role_id'] == 2) {

            //Getting the user`s data from DB
            $user = $this->db->select('*')->from('users')->where('id', $userID)->get()->row_array();

            $data = [
                'user' => $user
            ];

            $this->load->view('Admin/edit_user_modal', $data);

          //Unauthorized
        } else {
            $this->load->view('unauthorized.php');
        }
    }

    //Edit user
    public function editUser($userID)
    {

        //Getting the role id from DB
        $idS = $this->Admin_model->getRoleId();

        //Check if the user is authorized
        if ($idS['role_id'] == 2) {

            //Updating the user`s data in DB
            $this->db->where('id', $userID)->update('users', $_POST);

          //Unauthorized
        } else {
            $this->load->view('unauthorized.php');
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */