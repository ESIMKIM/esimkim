<?php
defined('BASEPATH') or die('No direct script access allowed!');

class M_Report extends CI_Model
{

    public function get_report_AllHistory()
    {
        $value = $this->db->query(
            "SELECT p.`products_id`, p.name ,ph.`prices`, ph.`total` ,ph.sisa, ph.terkirim, ph.created_date , ph.`date_po`
            FROM `tbl_products` AS p
            JOIN `tbl_products_stock_history` AS ph
            ON p.`products_id` = ph.`products_id`
            ORDER BY `products_id` ASC"
        )->result();

        return $value;
    }

    public function get_report_this_month($dari, $sampai)
    {
        $value = $this->db->query(
            "SELECT p.`products_id`, p.name ,ph.`prices`, ph.`total` ,ph.sisa, ph.terkirim, ph.created_date , ph.`date_po`
            FROM `tbl_products` AS p
            JOIN `tbl_products_stock_history` AS ph
            ON p.`products_id` = ph.`products_id`
            WHERE ph.`date_po` BETWEEN '$dari' AND '$sampai'
            ORDER BY `products_id` ASC"
        )->result();

        return $value;
    }

    public function get_report_this_monthOri($dari, $sampai)
    {
        $value = $this->db->query(
            "SELECT p.`products_id`, p.`name`, p.`prices`, pp.`images`, pm.`name_merk`, ph.total ,SUM(ph.total) as total_jumlah , SUM(ph.`terkirim`) as total_terkirim, SUM(ph.`sisa`) as total_sisa
            FROM `tbl_products` AS p
            JOIN `tbl_products_image` AS pp
            ON p.`products_id` = pp.`products_id`
            JOIN `tbl_products_merk` AS pm
            ON p.`merk_id` = pm.`merk_id`
            JOIN `tbl_products_stock_history` AS ph
            ON p.`products_id` = ph.`products_id`
            WHERE ph.`date_po` BETWEEN '$dari' AND '$sampai'
            GROUP BY p.`name`"
        )->result();

        return $value;
    }

    public function get_report_Direktorat_Periode($dari, $sampai)
    {
        $value = $this->db->query(
            "SELECT d.`department_name`, th.`no_surat`, th.`tgl_surat` , th.`total_jenis`, th.`total_quantity`
            FROM `tbl_transaction_header`AS th
            JOIN `tbl_user` AS u
            ON u.`user_id` = th.`user_id`
            JOIN `tbl_department` AS d
            ON u.`dept_id` = d.`id_department`
            WHERE th.`is_done` = 1 AND th.`tgl_surat` BETWEEN '$dari' AND '$sampai'
            "
        )->result();

        return $value;
    }

    public function get_list_Bagian()
    {
        $value = $this->db->query(
            "SELECT * FROM tbl_department"
        )->result();
        return $value;
    }

    public function get_dataReport_listItem_Dir($depId)
    {
        // if (empty($depId)) {
        //     $depId = '1';
        // }

        $value = $this->db->query(
            "SELECT p.name, SUM(td.quantity) AS quantity , SUM(td.approval) AS approval , dep.alias_dept, MONTH(th.tgl_surat) AS bulan
        FROM `tbl_transaction_detail` AS td
        JOIN `tbl_transaction_header` AS th
        ON td.th_id = th.th_id
        JOIN `tbl_user` AS u
        ON th.user_id = u.user_id
        JOIN `tbl_department` AS dep
        ON dep.id_department = u.dept_id
        join `tbl_products` as p
        on p.products_id = td.products_id
        WHERE td.th_id IN(SELECT th_id FROM `tbl_transaction_header` WHERE `user_id` IN(SELECT user_id FROM `tbl_user` WHERE `dept_id` = $depId)) AND MONTH(th.tgl_surat) = 1 
        GROUP BY p.name"

        )->result();

        return $value;
    }

    public function get_report_itemTrans_eachMonth($depId)
    {
        // if (empty($depId)) {
        //     $depId = '1';
        // }

        $value = $this->db->query(
            "SELECT dep.alias_dept, p.name, 
            SUM(CASE WHEN MONTH(th.tgl_surat) = 1 THEN td.approval END) AS acc_1,
            SUM(CASE WHEN MONTH(th.tgl_surat) = 2 THEN td.approval END) AS acc_2,
            SUM(CASE WHEN MONTH(th.tgl_surat) = 3 THEN td.approval END) AS acc_3,
            SUM(CASE WHEN MONTH(th.tgl_surat) = 4 THEN td.approval END) AS acc_4,
            SUM(CASE WHEN MONTH(th.tgl_surat) = 5 THEN td.approval END) AS acc_5,
            SUM(CASE WHEN MONTH(th.tgl_surat) = 6 THEN td.approval END) AS acc_6,
            SUM(CASE WHEN MONTH(th.tgl_surat) = 7 THEN td.approval END) AS acc_7,
            SUM(CASE WHEN MONTH(th.tgl_surat) = 8 THEN td.approval END) AS acc_8,
            SUM(CASE WHEN MONTH(th.tgl_surat) = 9 THEN td.approval END) AS acc_9,
            SUM(CASE WHEN MONTH(th.tgl_surat) = 10 THEN td.approval END) AS acc_10,
            SUM(CASE WHEN MONTH(th.tgl_surat) = 11 THEN td.approval END) AS acc_11,
            SUM(CASE WHEN MONTH(th.tgl_surat) = 12 THEN td.approval END) AS acc_12
     FROM `tbl_transaction_detail` AS td
     JOIN `tbl_transaction_header` AS th
     ON td.th_id = th.th_id
     JOIN `tbl_user` AS u
     ON th.user_id = u.user_id
     JOIN `tbl_department` AS dep
     ON dep.id_department = u.dept_id
     JOIN `tbl_products` AS p
     ON p.products_id = td.products_id
     WHERE td.th_id IN(SELECT th_id FROM `tbl_transaction_header` WHERE `user_id` IN(SELECT user_id FROM `tbl_user` WHERE `dept_id` = '$depId'))
     GROUP BY p.name"

        )->result();

        return $value;
    }



    public function get_total_sisa_asset_global()
    {
        $years = date("Y");

        $value = $this->db->query(
            "SELECT SUM(sisa * prices) AS total
             FROM tbl_products_stock_history"
        )->result();

        return $value;
    }

    public function get_total_sisa_asset_tahun()
    {
        $years = date("Y");

        $value = $this->db->query(
            "SELECT SUM(sisa * prices) AS total
             FROM tbl_products_stock_history
             WHERE YEAR(date_po) = '$years'"
        )->result();

        return $value;
    }


    public function get_total_jenis_barang_tahun()
    {
        $years = date("Y");
        $value = $this->db->query(
            "SELECT COUNT(DISTINCT(`products_id`)) as jenis
            FROM tbl_products_stock_history
            WHERE year(date_po) = '$years'"
        )->result();

        return $value;
    }

    public function get_pengiriman_barang_tahun()
    {
        $years = date("Y");
        $value = $this->db->query(
            "SELECT SUM(`terkirim`) as pengiriman
            FROM tbl_products_stock_history
            WHERE year(date_po) = '$years'"
        )->result();

        return $value;
    }

    public function get_penerimaan_barang_tahun()
    {
        $years = date("Y");
        $value = $this->db->query(
            "SELECT SUM(`total`) as penerimaan
            FROM tbl_products_stock_history
            WHERE year(date_po) = '$years'"
        )->result();

        return $value;
    }

    public function get_sisa_barang_tahun()
    {
        $years = date("Y");
        $value = $this->db->query(
            "SELECT SUM(`sisa`) as sisaTahun
            FROM tbl_products_stock_history
            WHERE year(date_po) = '$years'"
        )->result();

        return $value;
    }

    public function get_reason_byId($id)
    {
        $years = date("Y");
        $value = $this->db->query(
            "SELECT * 
            FROM `tbl_transaction_reason` 
            WHERE `reason_id` = $id"
        )->result();

        return $value;
    }
}
