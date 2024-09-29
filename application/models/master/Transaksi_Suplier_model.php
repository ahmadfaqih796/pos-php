<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Transaksi_Suplier_model extends CI_Model
{

   function __construct()
   {
      parent::__construct();
      date_default_timezone_set('Asia/Jakarta');
   }

   public function insert($table, $data)
   {
      $this->db->trans_start();
      $this->db->insert($table, $data);
      $this->db->trans_complete();
   }

   public function update($table, $data, $where)
   {
      $this->db->trans_start();
      $this->db->update($table, $data, $where);
      $this->db->trans_complete();
   }

   public function delete($table, $where)
   {
      $this->db->trans_start();
      $this->db->delete($table, $where);
      $this->db->trans_complete();
   }

   public function getData($table, $where)
   {
      return $this->db->select('*')->from($table)->where($where)->get();
   }

   public function getDataAll($table)
   {
      return $this->db->select('*')->from($table)->get();
   }



   public function querySuplierDtTb()
   {
      $column_order = array(null, 'id_transaksi', 'nama_supplier');
      $column_search = array('id_transaksi', 'nama_supplier');
      $order = array('id' => 'DESC');

      // $this->db->select('*')->from('tr_new_supliers');

      $this->db->select('ts.*, s.name AS nama_supplier');
      $this->db->from('tr_new_supliers ts');
      $this->db->join('supliers s', 'ts.suplier_id = s.id', 'left');

      $i = 0;

      foreach ($column_search as $item) // looping awal
      {
         if ($_POST['search']['value']) // Jika datatable mengirimkan pencarian dengan metode POST
         {
            if ($i == 0) // looping awal
            {
               $this->db->group_start();
               $this->db->like($item, $_POST['search']['value']);
            } else {
               $this->db->or_like($item, $_POST['search']['value']);
            }

            if (count($column_search) - 1 == $i)
               $this->db->group_end();
         }
         $i++;
      }

      if (isset($_POST['order'])) {
         $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
      } else if (isset($order)) {
         $order = $order;
         $this->db->order_by(key($order), $order[key($order)]);
      }
   }

   public function get_last_id()
   {
      $this->db->select('id');
      $this->db->from('tr_supliers');
      $this->db->order_by('id', 'DESC');
      $this->db->limit(1);
      $query = $this->db->get();

      if ($query->num_rows() > 0) {
         return $query->row()->id;
      } else {
         return 0;
      }
   }

   function dataTransaksiSuplier()
   {
      $this->querySuplierDtTb();
      if ($_POST['length'] != -1)
         $this->db->limit($_POST['length'], $_POST['start']);
      $query = $this->db->get();
      return $query->result();
   }

   function countTransaksiSuplier()
   {
      $this->querySuplierDtTb();
      $query = $this->db->get();
      return $query->num_rows();
   }
}
