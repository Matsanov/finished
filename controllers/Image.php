<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Image extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('pagination');

        $this->load->model('Pagination_model');
        $this->load->model('Image_model');

        $this->load->helper(array('form', 'url'));

    }

    //All images
    public function index()
    {

        //Config the pagination
        $config = array();
        $config["base_url"] = base_url() . "Image/index";
        $total_row = $this->Pagination_model->record_count();
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

        //Getting the data for the view
        $data["results"] = $this->Pagination_model->fetch_categories($config["per_page"], $page);
        $str_links = $this->pagination->create_links();

        //Setting links for the pages
        $data["links"] = $str_links;

        $this->load->view('images', $data);
    }


    //Uploading pictures
    public function upload()
    {

        //If logged user
        if ($this->session->userdata('user')) {
            //Getting the images count from the DB
            $imagesCount = $this->Image_model->imagesCount();

            //Checking if the user has less than 10 pictures
            if ($imagesCount['count(userID)'] <= 9) {

                //If no file selected
                if (!$_FILES) {

                    //If logged user
                    if ($this->session->userdata('user')) {
                        $this->load->view('upload_image');

                      //No user
                    } else {
                        redirect('login');
                    }

                  //If file selected
                } else {

                    //Configuration for the uploading
                    $config['upload_path'] = './data/images';
                    $config['allowed_types'] = 'gif|jpg|png';
                    $config['max_size'] = 500;
                    $config['max_width'] = 5000;
                    $config['max_height'] = 5000;
                    $config['encrypt_name'] = true;

                    $this->load->library('upload', $config);

                    //If error upload
                    if (!$this->upload->do_upload('image')) {

                        $error = array('error' => $this->upload->display_errors());

                        $this->load->view('upload_form_error', $error);

                      //If upload config is ok
                    } else {

                        //Preparing the data of the picture for DB
                        $data = array('upload_data' => $this->upload->data());
                        $insertData = [
                            'userID' => $this->session->userdata('user')['id'],
                            'file_name' => $data['upload_data']['raw_name'],
                            'file_ext' => $data['upload_data']['file_ext'],
                            'title' => $_POST['title'],
                            'description' => $_POST['description']
                        ];

                        $this->Image_model->addImage($insertData);

                        redirect('image/user');
                    }


                }

              //If user has more than 10 pictures
            } else {
                $this->load->view('max_pics_reached.php');
            }

          //If no logged user
        } else {
            redirect('login');
        }

    }

    //Getting user images
    public function userImages()
    {
        //If logged user
        if ($this->session->userdata('user')) {

            //Getting the user images from DB
            $images = $this->Image_model->getImagesByUser();

            $data = [
                'images' => $images
            ];

            $this->load->view('users_images', $data);

            //If no logged user
        } else {
            redirect('login');
        }

    }

    //Setting comments
    public function comment($image_id)
    {

        //Get comments count
        $comments_count = $this->Image_model->getCommentsCount($image_id);

        $errors = array();

        //Check comments count and setting errors
        if ($comments_count['comment_count'] >= 10) {
            $errors[] = 'Maximum number of comments reached !';
            echo json_encode(array('errors' => $errors));
            exit;
        }

        //If logged user
        if (!$this->session->userdata('user')) {
            $errors[] = 'You need to login first !';
        }

        if (!empty($errors)) {

            echo json_encode(array('errors' => $errors));
            exit;
        }

        //Is POST
        if (isset($_POST['comment'])) {

            //Preparing comment data from the form
            $commentData = [
                'comment_text' => $_POST['comment'],
                'image_id' => $image_id,
                'user_id' => $this->session->userdata('user')['id']
            ];

            //Add comment in DB
            $inserted = $this->Image_model->addComment($commentData);

            //Check if the comment is added in DB
            if ($inserted) {

                //Getting comment id from DB
                $comment_id = $this->db->insert_id();

                //Getting the comment
                $comment = $this->db->select("comment_text")->from('comments')->where('comment_id', $comment_id)->get()->row_array();

                echo json_encode(['status' => 1, 'comment' => $comment['comment_text']]);

              //Not added
            } else {

                echo json_encode(['status' => 0, 'errors' => 'Comment not inserted!']);
            }

          //No POST
        } else {

            echo json_encode(['status' => 'error', 'errors' => 'Comment not set!']);
        }


    }

    //Getting all comments for pictures
    public function allComments($image_id)
    {

        //If logged
        if ($this->session->userdata('user')) {

            //Getting Users and Comments from DB for the picture
            $commentsAndUsers = $this->Image_model->getCommentsByImageId($image_id);

            $data = [
                'comments' => $commentsAndUsers
            ];

            $comments = $this->load->view('comments', $data, true);

            $result = array('status' => 'ok', 'comments' => $comments);
            echo json_encode($result);

          //Not logged in
        } else {
            redirect('login');
        }
    }

    //Deleting picture
    public function pictureDelete($id)
    {

        //If logged
        if ($this->session->userdata('user')) {
            //Deleting picture
            $this->db->delete('images', ['id' => $id]);

            redirect('image/user');

          //Not logged
        } else {
            redirect('login');
        }

    }

    //View if user has more than 10 comments
    public function maxComments()
    {
        $this->load->view('max_comments_reached.php');
    }
}