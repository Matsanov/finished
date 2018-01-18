<?php

/**
 * Created by PhpStorm.
 * User: mac_v
 * Date: 12/4/2017
 * Time: 6:31 PM
 */
class Pagination_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

    }

    public function record_count()
    {
        return $this->db->count_all("images");
    }

    public function record_count_users()
    {
        return $this->db->count_all("users");
    }

    //Getting username for the pictures - who added the picture
    public function getUsername($image_id)
    {
        $this->db->select('i.userID, u.username, u.id');
        $this->db->from('Users as u');
        $this->db->join('Images as i', 'i.userID = u.id');
        $this->db->where(['i.userID' => $image_id]);
        return $this->db->get()->result_array();
    }

    //Getting the config for the pagination
    public function fetch_categories($limit, $start)
    {

        $offset = ($start - 1) * $limit;

        //Getting all pictures with usernames and limiting them in the controller
        $query = $this->db->select('i.*, u.username')->from('images as i')->join('users as u', 'i.userID = u.id')->order_by('i.id', 'DESC')->limit($limit, $offset)->get();

        if ($query->num_rows() > 0) {

            foreach ($query->result() as $row) {

                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    //Getting all the users
    public function getAllUsers($limit, $start)
    {

        $offset = ($start - 1) * $limit;

        //Getting all the users and count of uploaded pictures and ordering them
        $this->db->select('count(userID) AS images_count,userID, u.username, u.id');
        $this->db->from('Users as u');
        $this->db->join('Images', 'userID = u.id', 'left');
        $this->db->group_by('u.id');
        $this->db->order_by('images_count', 'DESC')->limit($limit, $offset);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            foreach ($query->result() as $row) {

                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

}



