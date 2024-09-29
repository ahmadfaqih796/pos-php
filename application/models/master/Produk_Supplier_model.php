<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Produk_Supplier_model extends CI_Model
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



   public function queryProdukSupplierDtTb()
   {
      $column_order = array(null, 'nama_supplier', 'product_name', 'price');
      $column_search = array('nama_supplier', 'product_name', 'price');
      $order = array('id' => 'DESC');

      // $this->db->select('*')->from('product_supliers')->where('is_deleted', 0);
      $this->db->select('ps.*, s.name AS nama_supplier, p.product_name, p.price');
      $this->db->from('product_supliers ps');
      $this->db->join('supliers s', 'ps.suplier_id = s.id', 'left');
      $this->db->join('product p', 'ps.product_id = p.id', 'left');
      $this->db->where('ps.is_deleted', 0);
      // $query = $this->db->get();
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

   function dataProduk()
   {
      $this->queryProdukSupplierDtTb();
      if ($_POST['length'] != -1)
         $this->db->limit($_POST['length'], $_POST['start']);
      $query = $this->db->get();
      return $query->result();
   }

   function countDataProduk()
   {
      $this->queryProdukSupplierDtTb();
      $query = $this->db->get();
      return $query->num_rows();
   }
}
