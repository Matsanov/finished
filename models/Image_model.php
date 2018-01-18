<?php

class Image_model extends CI_Model{

    private $id;
    private $username;
    private $password;
    private $passwordRepeat;
    private $email;


    public function __construct()
    {
        parent::__construct();

    }


    public function getImage($where) {
        $query = $this->db->get_where('images', $where);
    }

    //Getting all the pictures
    public function getAll() {

        return $this->db->get_where('images')->result_array();
    }

    //Getting the last 10 uploaded pictures
    public function getLimitImages() {

        return $this->db->select('i.*, u.username')
            ->from('images as i')->join('users as u', 'i.userID = u.id')
            ->order_by('i.id', 'DESC')
            ->limit(10)
            ->get()
            ->result_array();
    }

    //Getting the pictures count of the user
    public function imagesCount(){

        return $this->db->select('count(userID)')->from('Images')->where('userID',$this->session->userdata('user')['id'])
            ->get()->row_array();
    }

    //Getting all the uploaded pictures of the user
    public function getImagesByUser() {
        $this->db->select('i.*,u.username');
        $this->db->from('Images as i');
        $this->db->join('Users as u', 'i.userID = u.id');
        $this->db->where('userID',$this->session->userdata('user')['id']);
        $query = $this->db->get();
        return $query->result_array();
    }

    //Getting the comments and users usernames for a picture
    public function getCommentsByImageId($image_id) {
        // Only comments
        //return $this->db->select("*")->from('Images')->where('image_id', $image_id)->get()->result_array();

        // Comments with users
        $this->db->select('c.comment_text, u.username, c.image_id, c.comment_id');
        $this->db->from('Comments as c');
        $this->db->join('Users as u', 'c.user_id = u.id');
        $this->db->order_by('c.comment_id', 'DESC');
        $this->db->where(['c.image_id' => $image_id]);
        return $this->db->get()->result_array();
    }

    //Getting the comments count for a picture
    public function getCommentsCount($image_id){
        return $this->db->select('count(image_id) as comment_count')
            ->from('comments')
            ->where('image_id',$image_id)
            ->get()
            ->row_array();
    }

    //Adding picture
    public function addImage($postData)
    {
        $this->db->insert('images', $postData);
    }

    //Adding comment
    public function addComment($commentData)
    {
        return $this->db->insert('comments', $commentData);
        //$this->db->update('images', $commentData, array('id' => $id));
        //$this->db->where('id' == $id)->insert('images',$commentData);
    }
}