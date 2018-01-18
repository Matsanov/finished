<?php

/**
 * Created by PhpStorm.
 * User: mac_v
 * Date: 12/9/2017
 * Time: 4:38 PM
 */
class Admin_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

    }

    //Getting the users count
    public function countUsers(){

        return $this->db->select('count(id)')
            ->from('Users')
            ->count_all_results();
    }

    //Getting the last 5 registered users
    public function lastFiveUsers(){

        return $this->db->select('username')
            ->from('Users')
            ->order_by('id','DESC')
            ->limit(5)
            ->get()
            ->result_array();
    }

    //Getting the last 5 pictures uploaded with the username of the user who uploaded them
    public function lastFiveImages(){

        return $this->db->select('i.*, u.username')
            ->from('images as i')->join('users as u', 'i.userID = u.id')
            ->order_by('i.id', 'DESC')
            ->limit(5)
            ->get()
            ->result_array();
    }

    //Getting the pictures for the logged user
    public function getImagesByUser($userID) {

        $this->db->select('*');
        $this->db->from('Images');
        $this->db->where('userID',$userID)->order_by('id','DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Getting the role_id of the user (admin or user)
    public function getRoleId(){

       return $this->db->select('role_id')
            ->from('Users')
            ->where('id',$this->session->userdata('user')['id'])
            ->get()
            ->row_array();
    }

    //Getting all the comments count
    public function countComments(){

        return $this->db->select('count(comment_id)')->from('Comments')->count_all_results();
    }

    //Getting the last posted comment
    public function lastComment(){
        return $this->db->select('comment_text')->from('Comments')->order_by('comment_id','DESC')->limit(1)->get()->row_array();
    }
}