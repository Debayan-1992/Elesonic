<?php
defined('BASEPATH') or exit('No direct script access allowed');
class User extends MX_Controller
{

    private $data;
    private $database;
    
    public function __construct()
    {
        parent::__construct();
        $this->data = [];
        $this
            ->load
            ->library('dompdf_gen');
        $this
            ->load
            ->helper('download');

        //$this->db->save_queries = false;
        $this->load->library('firebase');
  
       
         $this->load->config('email');
         $this->load->library('email');
        /* $this->load->model("Home_model");
        $this->data['cartCount']=$this->Home_model->cartCount();
        $this->data['categories']=$this->Home_model->category();
        $this->data['wishlist']=$this->Home_model->wisthlist_count();*/
    }

    function chkHiddenSS()
    {
        $hiddenSs = $this->input->post('hiddenSs');
        $userDtls = $this->User->findBy("id", encryptor('decrypt',$hiddenSs));
       // pre($userDtls);
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if(empty($dtl)){
            $userDtls = $this->User->findBy("id", encryptor('decrypt',$hiddenSs));
            $this->session->set_userdata(CUSTOMER_SESS, [
                                'status'    => 1, 
                                'id'        => $userDtls['id'], 
                                'name'        => $userDtls['name'], 
                                'company'        => $userDtls['company_name'], 
                                'data'      =>  [
                                                    'name'  => $userDtls['name'], 
                                                    'email' => $userDtls['email'],
                                                    'phone' => $userDtls['phone']
                                                ]
                            ]);

        }        
        
    }

    public function index()
    {
  /*  $firebase = $this->firebase->init();
    $database = $firebase->getDatabase();
   $rows= $database->getReference('bids')->getvalue();
    
 echo '<pre>';
    print_r($rows);
    echo '</pre>';*/

        $breadcrumb = [['url' => BASE_URL, 'page' => "My Account"]];
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);

        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['buyer_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this
            ->layout
            ->set_breadcumb($breadcrumb);
        $this
            ->layout
            ->view('buyer-dashboard', $this->data);
    }
      function status_change_active()
    {
        $sellerId= $this
            ->input
            ->post('sellerId');
        $sheet_id = $this
            ->input
            ->post('sheet_id');
        $status = $this
            ->input
            ->post('status');
        $this
            ->form_validation
            ->set_rules('sheet_id', 'sheet id', 'trim|required');
        $this
            ->form_validation
            ->set_rules('status', 'status', 'trim|required');

        if ($this
            ->form_validation
            ->run() == false)
        {
            die(json_encode(['status' => 0, 'msg' => validation_errors() ]));
        }

         $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $data_array = array(
            'is_recv' => $status
        );

        $this
            ->db
            ->where('sheet_id', $sheet_id);
        $this
            ->db
            ->where('buyer_id', @$dtl['id']);
        $this
            ->db
            ->update(BUYER_SHEET_ASSIGNED, $data_array);

        $sheet_details = $this
                ->Common
                ->find(
                    [
                      'table' => OFFER_INVOICE, 
                      'select' => "*", 
                      'where' => "sheet_id = {$sheet_id}",
                     ]);

         $data_array = array(
            'buyer_price' => 0,
            'buyer_id' => @$dtl['id'],
            'seller_id' => $sellerId,
            'sheet_id' => $sheet_id,
            'seller_price' => @$sheet_details[0]['price_idea'],
            'invoice_id' => @$sheet_details[0]['invoice_id'],
            'comment_buyer' =>"",
            'bid_time' => date('Y-m-d H:i:s')
        );

       

        $decodeJson[$sheet_details[0]['invoice_id']]['buyer_bid'][@$dtl['id']] =array(
                        'bid'         => 0,
                        'bid_time'    => date('Y-m-d H:i:s'),
                        'buyer_id'    =>@$dtl['id'],
                        
                    );
         $mainArryEncode=json_encode($decodeJson);
        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
        file_put_contents($file_to_save, $mainArryEncode);

        //$this->db->insert(BID_DETAILS, $data_array);

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from('tt_offer_sheets');
        $this
            ->db
            ->where('sheet_id', $sheet_id);
        $qry = $this
            ->db
            ->get();
        $sheet_details_msg = $qry->result();

                 

        $msg_sheet_name = @$sheet_details_msg[0]->sheet_name;
        $msg_sheet_no = @$sheet_details_msg[0]->sheet_no;
        $msg_sheet_created = @$sheet_details_msg[0]->created_by;

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from('tt_users');
        $this
            ->db
            ->where('id', $msg_sheet_created);
        $qry = $this
            ->db
            ->get();
        $sheet_details_created = $qry->result();


        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from('tt_users');
        $this
            ->db
            ->where('id', @$dtl['id']);
        $qry = $this
            ->db
            ->get();
        $sheet_details_place = $qry->result();

          
        $msg_sheet_created_number = trim(@$sheet_details_created[0]->phone);
        $msg_buyer_name = @$sheet_details_place[0]->company_name;

           

            /////////////////////////////////////MESSAGE//////////////////////////////////////////
            // /qq|22|sht|
           // $body = $msg_buyer_name . " has placed a bid and activated offer sheet " . $msg_sheet_no . "-" . $msg_sheet_name;
            $messageId='111592';
            $to = $msg_sheet_created_number;
            $variables=$msg_buyer_name.'|'.$msg_sheet_no.'|'.$msg_sheet_name;
            send_sms($to, $messageId, $variables);

             

        /*    for ($i = 0;$i < count($sheet_details);$i++)
            {

                $data_array = array(
                    'buyer_price' => 0,
                    'buyer_id' => @$dtl['id'],
                    'seller_id' => $sellerId,
                    'sheet_id' => $sheet_id,
                    'seller_price' => $sheet_details[$i]['price_idea'],
                    'invoice_id' => $sheet_details[$i]['invoice_id'],
                    'comment_buyer' =>"",
                    'bid_time' => date('Y-m-d H:i:s')
                );

                $this
                    ->db
                    ->insert(BID_DETAILS, $data_array);
                }*/

        $encId=encrypt($sheet_id);      
        $result = 1;
        echo json_encode(array('encid'=>$encId,'result'=>$result));

    }
    function buyer_password_change()
    {
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);

        if (empty($dtl))
        {
            redirect(BASE_URL);
        }
        $this->data['buyer_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this
            ->layout
            ->view('buyer-password', $this->data);

    }
    function seller_password_change()
    {
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);

        if (empty($dtl))
        {
            redirect(BASE_URL);
        }
        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this
            ->layout
            ->view('seller-password', $this->data);

    }
    function change_password_action()
    {
        if (!empty($this
            ->input
            ->post()))
        {

            $this
                ->form_validation
                ->set_rules('old_password', 'Old Password', 'trim|required|min_length[6]|max_length[64]');
            $this
                ->form_validation
                ->set_rules('password', 'password', 'trim|required|min_length[6]|max_length[64]');
            $this
                ->form_validation
                ->set_rules('confirm_password', 'confirm password', 'trim|required|min_length[6]|max_length[64]');

            if ($this
                ->form_validation
                ->run() == false)
            {
                die(json_encode(['status' => 0, 'msg' => validation_errors() ]));
            }
            $inputs = $this
                ->input
                ->post();
            $dtl = $this
                ->session
                ->userdata(CUSTOMER_SESS);
            $userDtls = $this
                ->Common
                ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);
            if (passwordmatch($inputs['old_password'], $userDtls['password']))
            {

                if ($inputs['password'] == $inputs['confirm_password'])
                {
                    $data_pass = createpassword($inputs['password']);

                    $data_pass_array = array(
                        'password' => $data_pass
                    );

                    $this
                        ->db
                        ->where('id', @$dtl['id']);
                    $this
                        ->db
                        ->update(USERS, $data_pass_array);
                    die(json_encode(['status' => 1, 'msg' => "Password successfully updated"]));
                }
                else
                {
                    die(json_encode(['status' => 0, 'msg' => "Password doesn't match properly."]));
                }

            }
            else
            {
                die(json_encode(['status' => 0, 'msg' => "Invalid old password entered"]));
            }

        }
        die(json_encode(['status' => 1, 'msg' => "Invalid request"]));
    }

    function seller_change_password_action()
    {
        if (!empty($this
            ->input
            ->post()))
        {

            $this
                ->form_validation
                ->set_rules('old_password', 'Old Password', 'trim|required|min_length[6]|max_length[64]');
            $this
                ->form_validation
                ->set_rules('password', 'password', 'trim|required|min_length[6]|max_length[64]');
            $this
                ->form_validation
                ->set_rules('confirm_password', 'confirm password', 'trim|required|min_length[6]|max_length[64]');

            if ($this
                ->form_validation
                ->run() == false)
            {
                die(json_encode(['status' => 0, 'msg' => validation_errors() ]));
            }
            $inputs = $this
                ->input
                ->post();
            $dtl = $this
                ->session
                ->userdata(SUPPLIER_SESS);
            $userDtls = $this
                ->Common
                ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);
            if (passwordmatch($inputs['old_password'], $userDtls['password']))
            {

                if ($inputs['password'] == $inputs['confirm_password'])
                {
                    $data_pass = createpassword($inputs['password']);

                    $data_pass_array = array(
                        'password' => $data_pass
                    );

                    $this
                        ->db
                        ->where('id', @$dtl['id']);
                    $this
                        ->db
                        ->update(USERS, $data_pass_array);
                    die(json_encode(['status' => 1, 'msg' => "Password successfully updated"]));
                }
                else
                {
                    die(json_encode(['status' => 0, 'msg' => "Password doesn't match properly."]));
                }

            }
            else
            {
                die(json_encode(['status' => 0, 'msg' => "Invalid old password entered"]));
            }

        }
        die(json_encode(['status' => 1, 'msg' => "Invalid request"]));
    }

    function seller_change_account_action()
    {

        $this
            ->form_validation
            ->set_rules('name', 'Name', 'trim|required|min_length[3]|max_length[128]');
        $this
            ->form_validation
            ->set_rules('company_name', 'Company Name', 'trim|required');
        $this
            ->form_validation
            ->set_rules('company_address', 'Company address', 'trim|required');

        $this
            ->form_validation
            ->set_rules('pincode', 'Pincode', 'trim|required|min_length[6]');
        $this
            ->form_validation
            ->set_rules('pan', 'Pan', 'trim|required');
        $this
            ->form_validation
            ->set_rules('gst', 'Gst', 'trim|required');

        if ($this
            ->form_validation
            ->run() == false)
        {
            die(json_encode(['status' => 0, 'msg' => validation_errors() ]));
        }
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        $data = $this
            ->input
            ->post();
        if (!empty($dtl))
        {
            $formData = [

            'name' => $data['name'], 'company_name' => $data['company_name'], 'company_address' => $data['company_address'], 'pincode' => $data['pincode'], 'bussiness_phone' => $data['bussiness_phone'], 'pan_no' => $data['pan'], 'gst_no' => $data['gst'],

            ];

            $this
                ->db
                ->where('id', @$dtl['id']);
            $this
                ->db
                ->update(USERS, $formData);
            die(json_encode(['status' => 1, 'msg' => "Profile successfully Updated "]));
        }
        else
        {
            die(json_encode(['status' => 0, 'msg' => "Invalid request"]));
        }

    }

    function buyer_change_account_action()
    {

        $this
            ->form_validation
            ->set_rules('name', 'Name', 'trim|required|min_length[3]|max_length[128]');
        $this
            ->form_validation
            ->set_rules('company_name', 'Company Name', 'trim|required');
        $this
            ->form_validation
            ->set_rules('company_address', 'Company address', 'trim|required');

        $this
            ->form_validation
            ->set_rules('pincode', 'Pincode', 'trim|required|min_length[6]');
        $this
            ->form_validation
            ->set_rules('pan', 'Pan', 'trim|required');
        $this
            ->form_validation
            ->set_rules('gst', 'Gst', 'trim|required');

        if ($this
            ->form_validation
            ->run() == false)
        {
            die(json_encode(['status' => 0, 'msg' => validation_errors() ]));
        }
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $data = $this
            ->input
            ->post();
        if (!empty($dtl))
        {
            $formData = [

            'name' => $data['name'], 'company_name' => $data['company_name'], 'company_address' => $data['company_address'], 'pincode' => $data['pincode'], 'bussiness_phone' => $data['bussiness_phone'], 'pan_no' => $data['pan'], 'gst_no' => $data['gst'],

            ];

            $this
                ->db
                ->where('id', @$dtl['id']);
            $this
                ->db
                ->update(USERS, $formData);
            die(json_encode(['status' => 1, 'msg' => "Profile successfully Updated "]));
        }
        else
        {
            die(json_encode(['status' => 0, 'msg' => "Invalid request"]));
        }

    }

    public function seller_account()
    {
        $breadcrumb = [['url' => BASE_URL, 'page' => "My Account"]];

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this
            ->layout
            ->set_breadcumb($breadcrumb);
        $this
            ->layout
            ->view('seller-dashboard', $this->data);
    }

    function generate_offer_sheet()
    {

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);

        if (empty($dtl))
        {
            redirect(BASE_URL);
        }
        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['location'] = $this
            ->Common
            ->find(['table' => LOCATION, 'select' => "*", 'where' => "status = 'A'",

        ]);

        $this->data['contract_type'] = $this
            ->Common
            ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "status = 'A'",

        ]);

        $this->data['payment_type'] = $this
            ->Common
            ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "status = 'A'",

        ]);

        $this->data['buyer'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "status = 'A' AND email_verified='1' AND role='2'",

        ]);

        $rand=microtime();
        $getValue=substr($rand,'4','4');

        $offerSheetNo = $this
            ->Common
            ->find(
                [

                'table'  => OFFER_SHEET, 
                'select' => "*", 
                'where'  => "sheet_no = '{$getValue}'",
                'query'  => 'count'

                ]);
            if($offerSheetNo > 0)
            {
                $rand=microtime();
                $this->data['getValue']=substr($rand,'4','4');

            }
            else
            {
                $this->data['getValue']=$getValue;

            }



        $this
            ->layout
            ->view('generate-offer-sheet', $this->data);

    }

    function seller_offergenerate_action()
    {

        if (!empty($this
            ->input
            ->post()))
        {
            $this
                ->form_validation
                ->set_rules('sheet_no', 'sheet no', 'trim|required');
            $this
                ->form_validation
                ->set_rules('sheet_name', 'sheet name', 'trim|required');
            $this
                ->form_validation
                ->set_rules('expiry_date', 'expiry date', 'trim|required');

            $this
                ->form_validation
                ->set_rules('location', 'location', 'trim|required');
            //$this->form_validation->set_rules('garden', 'garden', 'trim|required');
            if ($this
                ->form_validation
                ->run() == false)
            {
                die(json_encode(['status' => 0, 'msg' => validation_errors() ]));
            }

            $data = $this
                ->input
                ->post();
            $dtl = $this
                ->session
                ->userdata(SUPPLIER_SESS);
            $ex_day = @$data['expiry_date'];
            $pd = @$data['promt_date'];
            $dispatch_date = date('Y-m-d H:i:s', strtotime($ex_day . ' +' . $pd . ' day'));
            if (!empty($dtl))
            {
                $formData = ['sheet_no' => @$data['sheet_no'], 'sheet_name' => @$data['sheet_name'], 'expiry_date' => @$data['expiry_date'], 'location' => @$data['location'], 'payment_type' => @$data['payment_type'], 'contract' => @$data['contract_type'], 'promt_day' => @$data['promt_date'], 'note' => @$data['note'], 'dispatch_date' => date('Y-m-d H:i:s') , 'cash_discount' => @$data['cash_discount'], 'buyer_can_see' => @$data['buyer_can_see'], 'bidding_gap' => @$data['bidding_gap'], 'division' => @$data['division'], 'created_by' => @$dtl['id'], 'created_date' => date('Y-m-d H:i:s') ,

                ];
                $this
                    ->session
                    ->set_userdata('sheet_details', $formData);
                /*$this->db->insert(OFFER_SHEET,$formData);
                    $sheet_id=$this->db->insert_id();
                
                    $garden=$data['garden'];
                    $invoice=$data['invoice'];
                    $grade=$data['grade'];
                    $pkgs=$data['pkgs'];
                    $kgs=$data['kgs'];
                    $price=$data['price'];
                
                    for($i=0;$i<count($garden) && $i<count($invoice) && $i<count($grade) && $i<count($pkgs) && $i<count($kgs) && $i<count($price);$i++)
                    {
                            $data_invoice=array(
                
                                        'sheet_id'=>$sheet_id,
                                        'garden'=>$garden[$i],
                                        'invoice'=>$invoice[$i],
                                        'grade'=>$grade[$i],
                                        'pkgs_no'=>$pkgs[$i],
                                        'total_kgs'=>$kgs[$i],
                                        'price_idea'=>$price[$i],
                
                            );
                            $this->db->insert(OFFER_INVOICE,$data_invoice);
                    }*/

                $new_buyers = @$data['new_mail'];
                $buyers = @$data['buyer'];

                if (!empty($new_buyers))
                {
                    $this
                        ->db
                        ->select('*');
                    $this
                        ->db
                        ->from(USERS);
                    $this
                        ->db
                        ->where_in('email', $new_buyers);
                    $qry = $this
                        ->db
                        ->get();
                    $chkUser = $qry->result();

                }

                if (!empty($chkUser))
                {
                    die(json_encode(['status' => 0, 'msg' => "New Email Already Registered", 'sheet_id' => ""]));
                }
                else
                {
                    $this
                        ->session
                        ->set_userdata('new_buyer', $new_buyers);
                    $this
                        ->session
                        ->set_userdata('assigned_buyer', $buyers);

                }

              

                die(json_encode(['status' => 1, 'msg' => "successfully created ", 'sheet_id' => ""]));
            }

            else
            {
                die(json_encode(['status' => 0, 'msg' => "Invalid request", 'sheet_id' => ""]));
            }

        }
        die(json_encode(['status' => 0, 'msg' => "Invalid request", 'sheet_id' => ""]));
    }

    function generate_offer_invoice()
    {
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this
            ->layout
            ->view('generate-offer-invoice', $this->data);

    }

    function seller_bulk_invoice()
    {
        $firebase = $this->firebase->init();
        $database = $firebase->getDatabase();


        $file_tmp = $_FILES['exfile']['tmp_name'];
        $file_name = $_FILES['exfile']['name'];
        $extension = substr(strrchr($file_name, '.') , 1);

        if ($extension == "xls" || $extension == "xlsx" || $extension == "csv")
        {
            move_uploaded_file($file_tmp, "public/uploads/" . $file_name);
            //$objReader =PHPExcel_IOFactory::createReader('Excel5');     //For excel 2003
            $objReader = PHPExcel_IOFactory::createReader('Excel2007'); // For excel 2007
            //Set to read only
            $objReader->setReadDataOnly(true);
            //Load excel file
            $objPHPExcel = $objReader->load('public/uploads/' . $file_name);
            $totalrows = $objPHPExcel->setActiveSheetIndex(0)
                ->getHighestRow(); //Count Numbe of rows avalable in excel
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

            $data_shet = $this
                ->session
                ->userdata('sheet_details');
            $new_buyers = $this
                ->session
                ->userdata('new_buyer');
            $buyers = $this
                ->session
                ->userdata('assigned_buyer');

            //print_r($new_buyers);  echo count($new_buyers);exit;
            $dtl = $this
                ->session
                ->userdata(SUPPLIER_SESS);
            $ex_day = @$data_shet['expiry_date'];
            $pd = $pd = @$data_shet['promt_day'];
            $dispatch_date = date('Y-m-d H:i:s', strtotime($ex_day . ' +' . $pd . ' day'));

            if (!empty($data_shet))
            {
                 @$newexpiry_date=date("d-m-Y H:i:s", strtotime($data_shet['expiry_date']));
                 @$newEXPDate=@strtotime(@$newexpiry_date);
                $formData = ['complete_close'=>'N','sheet_no' => @$data_shet['sheet_no'], 'sheet_name' => @$data_shet['sheet_name'], 'expiry_date' => @$data_shet['expiry_date'], 'location' => @$data_shet['location'], 'payment_type' => @$data_shet['payment_type'], 'contract' => @$data_shet['contract'], 'promt_day' => @$pd, 'note' => @$data_shet['note'], 'dispatch_date' => date('Y-m-d H:i:s') , 'cash_discount' => @$data_shet['cash_discount'], 'buyer_can_see' => @$data_shet['buyer_can_see'], 'bidding_gap' => @$data_shet['bidding_gap'], 'division' => @$data_shet['division'],'expiry_timestamp'=>$newEXPDate, 'created_by' => @$dtl['id'], 'created_date' => date('Y-m-d H:i:s') ,

                ];

                $this
                    ->db
                    ->insert(OFFER_SHEET, $formData);
                $sheet_id = $this
                    ->db
                    ->insert_id();

                if (!empty(@$new_buyers))
                {
                  
                    for ($i = 0;$i < count($new_buyers);$i++)
                    {
                        $data_new_buyers = array(
                            'emailid' => @$new_buyers[$i],
                            'sheet_id' => $sheet_id,

                        );
                        $this
                            ->db
                            ->insert(BUYER_SHEET_ASSIGNED, $data_new_buyers);
                        $to = @$new_buyers[$i];
                        $from = $this
                            ->config
                            ->item('smtp_user');
                        $email_data['mail_data'] = array(

                            'usheetname' => $data_shet['sheet_name'],
                            'usheetno' => @$data_shet['sheet_no'],
                            'uemail' => encrypt($new_buyers[$i])

                        );

                        $this
                            ->email
                            ->set_mailtype("html");

                        $html_email_user = $this
                            ->load
                            ->view('signupregistration_mail_view', $email_data, true);

                        $this
                            ->email
                            ->from($from, 'Tea Inntech');
                        $this
                            ->email
                            ->to($to);
                        $this
                            ->email
                            ->subject('TEA INNTECH - Invitation email');
                        $this
                            ->email
                            ->message($html_email_user);
                        @$reponse = $this
                            ->email
                            ->send();
                    }

                }

                if (!empty($buyers))
                {
                    for ($i = 0;$i < count($buyers);$i++)
                    {
                        $data_buyer = array(
                            'buyer_id' => @$buyers[$i],
                            'sheet_id' => $sheet_id,

                        );
                        $this
                            ->db
                            ->insert(BUYER_SHEET_ASSIGNED, $data_buyer);
                    }
                }
            }
            $key=0;
            $arrayFirebase=array();
            for ($i = 2;$i <= $totalrows;$i++)
            {
                $serial_no = $objWorksheet->getCellByColumnAndRow(0, $i)->getValue();
                $garden = $objWorksheet->getCellByColumnAndRow(1, $i)->getValue();
                $invoice = $objWorksheet->getCellByColumnAndRow(2, $i)->getValue();
                $grade = $objWorksheet->getCellByColumnAndRow(3, $i)->getValue();
                $pkgs = $objWorksheet->getCellByColumnAndRow(4, $i)->getValue();
                $kgs = $objWorksheet->getCellByColumnAndRow(5, $i)->getValue();
                $price = $objWorksheet->getCellByColumnAndRow(6, $i)->getValue();
                $comment = $objWorksheet->getCellByColumnAndRow(7, $i)->getValue();

                if ($price != "" && $serial_no != "" && $garden != "" && $invoice != "" && $grade != "" && $pkgs != "" && $kgs != "")
                {
                    if ($price != 0 || $price != '0')
                    {

                        if($pkgs > @$data_shet['division'])
                            {
                                $flag="yes";
                            }
                            else
                            {
                                $flag="no";
                            }


                      


                      

                        //$postRef=$database->getReference('bids/'.$sheet_id)->push($fields); 
                        //$firebase_key = $postRef->getKey();

                        $data_invoice = array(
                            'sheet_id' => $sheet_id,
                            'garden' => $garden,
                            'invoice' => $invoice,
                            'grade' => $grade,
                            'pkgs_no' => $pkgs,
                            'total_kgs' => $kgs,
                            'price_idea' => $price,
                            'serial_no' => $serial_no,
                            'comment' => $comment,
                        );

                        $this
                            ->db
                            ->insert(OFFER_INVOICE, $data_invoice);

                        $lastId=$this->db->insert_id();

                        $arrayFirebase[$lastId] = array(
                                            "key" => $key,
                                            "inv_status" => "I",
                                            "invoice_id" => $lastId,
                                            "bid" => 0,
                                            "buyer" => "",
                                            "sheet_id" => $sheet_id,
                                            "seller_final_lock" => "N",
                                            "buyerId" => "",
                                            "price_idea" => $price,
                                            "pkgs_no" => $pkgs,
                                            "division" => @$data_shet['division'],
                                            "seller_comment" => $comment,
                                            "division_check_accept" => 0,
                                            "division_check_buyer" => 0,
                                            "bidMaxbuyerId" => "",
                                            "buyer_can_see" => @$data_shet['buyer_can_see'],
                                            "garden" => $garden,
                                            "invoice" => $invoice,
                                            "grade" => $grade,
                                            "total_kgs" => $kgs,
                                            "flag" => $flag,
                                            "serial_no" => $serial_no,
                                            "firebase_key" => $lastId,
                                            "sheet_no" => $data_shet['sheet_no'],
                                            "seller_id"=>@$dtl['id'],
                                            "sheet_name" => @$data_shet['sheet_name']
                                        );
                        
                                //$mainArryEncode=json_encode($mainArray);

                        //$fieldsInv=array('invoice_id'=>$lastId,'firebase_key'=>$firebase_key);
                        //$database->getReference('bids/'.$sheet_id.'/'.$firebase_key)->update($fieldsInv);
   
                    }
                }
                $key++;
            }
           
            $mainArryEncode=json_encode($arrayFirebase);
            $file_to_save = 'public/uploads/' . $sheet_id . '.json';
            file_put_contents($file_to_save, $mainArryEncode);


            $dataFullEntry = array('sheet_id'=>$sheet_id);
            $this->db->insert('tt_sheet_entry',$dataFullEntry);




            $KEY =FIREBASEKEY;
            $headers = array('Content-Type:application/json', 'Authorization:key='.$KEY);

            $fileName = $sheet_id.'.json';
            $url = FIREBASEURL.$fileName;
            //echo $url;die;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // <--- This line important
            curl_setopt($ch, CURLOPT_POSTFIELDS, $mainArryEncode);    // <--- This line to
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if(curl_errno($ch)){
                return false;
            }

            curl_close($ch);
          // $mainArryEncode=json_encode($arrayFirebase);
          // $postRef=$database->getReference('bids/'.$sheet_id)->push($mainArryEncode); 
                        //$firebase_key = $postRef->getKey();
            @unlink('public/uploads/' . $file_name); //File Deleted After uploading in database
            $this
                ->session
                ->set_flashdata('succmsg', 'Invoice successfully uploaded');
            redirect(BASE_URL . 'generate-offer-sheet-next/' . encrypt($sheet_id));

        }

        else
        {
            $this
                ->session
                ->set_flashdata('fail', 'Failed To Upload.');
            redirect(BASE_URL . 'generate-offer-invoice/');

        }
    }

    function chk_new_mail_exist()
    {
        $new_mail = $this
            ->input
            ->post('new_mail');
        $UserMail = $this
            ->User
            ->findBy("email", $new_mail);

        if (!empty($UserMail))
        {
            $result = 1;
        }
        else
        {
            $result = 0;
        }
        echo json_encode($result);
    }

    function seller_offergenerate_action_final()
    {

        $data = $this
            ->input
            ->post();
        $data_shet = $this
            ->session
            ->userdata('sheet_details');
        $new_buyers = $this
            ->session
            ->userdata('new_buyer');
        $buyers = $this
            ->session
            ->userdata('assigned_buyer');

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        $ex_day = @$data_shet['expiry_date'];
        $pd = @$data_shet['promt_day'];

        $dispatch_date = date('Y-m-d H:i:s', strtotime($ex_day . ' +' . $pd . ' day'));
        if (!empty($data_shet))
        {

            @$newexpiry_date=date("d-m-Y H:i:s", strtotime($data_shet['expiry_date']));
            @$newEXPDate=@strtotime(@$newexpiry_date);

            $formData = ['complete_close'=>'N','sheet_no' => @$data_shet['sheet_no'], 'sheet_name' => @$data_shet['sheet_name'], 'expiry_date' => @$data_shet['expiry_date'], 'location' => @$data_shet['location'], 'payment_type' => @$data_shet['payment_type'], 'contract' => @$data_shet['contract'], 'note' => @$data_shet['note'], 'promt_day' => @$pd, 'dispatch_date' => date('Y-m-d H:i:s') , 'cash_discount' => @$data_shet['cash_discount'], 'buyer_can_see' => @$data_shet['buyer_can_see'], 'bidding_gap' => @$data_shet['bidding_gap'], 'division' => @$data_shet['division'],'expiry_timestamp'=>$newEXPDate, 'created_by' => @$dtl['id'], 'created_date' => date('Y-m-d H:i:s') ,

            ];

            $this
                ->db
                ->insert(OFFER_SHEET, $formData);
            $sheet_id = $this
                ->db
                ->insert_id();

            $garden = $data['garden'];
            $invoice = $data['invoice'];
            $grade = $data['grade'];
            $pkgs = $data['pkgs'];
            $kgs = $data['kgs'];
            $price = $data['price'];
            $serial_no = $data['serial_no'];
            $comment = $data['comment'];
            $key=0;
            $arrayFirebase=array();
            for ($i = 0;$i < count($serial_no) && $i < count($garden) && $i < count($invoice) && $i < count($grade) && $i < count($pkgs) && $i < count($kgs) && $i < count($price);$i++)
            {
                if ($serial_no[$i] != "" && $garden[$i] != "" && $invoice[$i] != "" && $grade[$i] != "" && $pkgs[$i] != "" && $kgs[$i] != "" && $price[$i] != "" && $price[$i] != "0")
                {
                    if(@$pkgs[$i] > @$data_shet['division'])
                            {
                                $flag="yes";
                            }
                            else
                            {
                                $flag="no";
                            }
                   /* $fields=array('key'=>$key,'inv_status'=>"I",'invoice_id'=>"",'bid'=>0,'buyer'=>"",'sheet_id'=>@$sheet_id,'seller_final_lock'=>"N",'buyerId'=>"",'price_idea'=>@$price[$i],'pkgs_no'=>@$pkgs[$i],'division'=>@$data_shet['division'],'seller_comment'=>@$comment[$i],'buyerfull'=>"",'division_check_accept'=>0,"division_check_buyer"=>0,"bidMaxbuyerId"=>"","buyer_can_see"=>@$data_shet['buyer_can_see'],"garden"=>@$grade[$i],'invoice'=>@$invoice[$i],'grade'=>@$grade[$i],"total_kgs"=>@$kgs[$i],'flag'=>$flag,'serial_no'=>$serial_no[$i],'firebase_key'=>"",'sheet_no'=>@$data_shet['sheet_no'],'sheet_name'=>@$data_shet['sheet_name']);


                    $postRef=$database->getReference('bids/'.$sheet_id)->push($fields); 
                    $firebase_key = $postRef->getKey();*/
                    $data_invoice = array(

                        'sheet_id' => $sheet_id,
                        'serial_no' => $serial_no[$i],
                        'garden' => $garden[$i],
                        'invoice' => $invoice[$i],
                        'grade' => $grade[$i],
                        'pkgs_no' => $pkgs[$i],
                        'total_kgs' => $kgs[$i],
                        'price_idea' => $price[$i],
                        'comment' => $comment[$i]

                    );
                    $this
                        ->db
                        ->insert(OFFER_INVOICE, $data_invoice);

                    $lastId=$this->db->insert_id();

                      $arrayFirebase[$lastId] = array(
                                "key" => $key,
                                "inv_status" => "I",
                                "invoice_id" => $lastId,
                                "bid" => 0,
                                "buyer" => "",
                                "sheet_id" => $sheet_id,
                                "seller_final_lock" => "N",
                                "buyerId" => "",
                                "price_idea" => $price[$i],
                                "pkgs_no" => $pkgs[$i],
                                "division" => @$data_shet['division'],
                                "seller_comment" => $comment,
                                "division_check_accept" => 0,
                                "division_check_buyer" => 0,
                                "bidMaxbuyerId" => "",
                                "buyer_can_see" => @$data_shet['buyer_can_see'],
                                "garden" => $garden[$i],
                                "invoice" => $invoice[$i],
                                "grade" => $grade[$i],
                                "total_kgs" => $kgs[$i],
                                "flag" => $flag,
                                "serial_no" => $serial_no[$i],
                                "firebase_key" => $lastId,
                                "sheet_no" => $data_shet['sheet_no'],
                                "seller_id"=>@$dtl['id'],
                                "sheet_name" => @$data_shet['sheet_name']
                            );

                   /* $fieldsInv=array('invoice_id'=>$lastId,'firebase_key'=>$firebase_key);
                    $database->getReference('bids/'.$sheet_id.'/'.$firebase_key)->update($fieldsInv);*/
                }
                $key++;
            }

            $mainArryEncode=json_encode($arrayFirebase);
            ///////////////////////////////////////////
            $file_to_save = 'public/uploads/' . $sheet_id . '.json';
            file_put_contents($file_to_save, $mainArryEncode);

            $dataFullEntry = array('sheet_id'=>$sheet_id);
            $this->db->insert('tt_sheet_entry',$dataFullEntry);
            ////////////////////////////////////////////////////
            $KEY =FIREBASEKEY;
            $headers = array('Content-Type:application/json', 'Authorization:key='.$KEY);

            $fileName = $sheet_id.'.json';
            $url = FIREBASEURL.$fileName;
            //echo $url;die;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // <--- This line important
            curl_setopt($ch, CURLOPT_POSTFIELDS, $mainArryEncode);    // <--- This line to
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if(curl_errno($ch)){
                return false;
            }

            curl_close($ch);

            if (!empty(@$new_buyers))
            {
               
                for ($i = 0;$i < count($new_buyers);$i++)
                {
                    $data_new_buyers = array(
                        'emailid' => @$new_buyers[$i],
                        'sheet_id' => $sheet_id,

                    );
                    $this
                        ->db
                        ->insert(BUYER_SHEET_ASSIGNED, $data_new_buyers);
                    $to = @$new_buyers[$i];
                    $from = $this
                        ->config
                        ->item('smtp_user');
                    $email_data['mail_data'] = array(

                        'usheetname' => $data_shet['sheet_name'],
                        'usheetno' => @$data_shet['sheet_no'],
                        'uemail' => encrypt($new_buyers[$i])

                    );

                    $this
                        ->email
                        ->set_mailtype("html");

                    $html_email_user = $this
                        ->load
                        ->view('signupregistration_mail_view', $email_data, true);

                    $this
                        ->email
                        ->from($from, 'Tea Inntech');
                    $this
                        ->email
                        ->to($to);
                    $this
                        ->email
                        ->subject('Tea Inntech | Sign Up');
                    $this
                        ->email
                        ->message($html_email_user);
                    @$reponse = $this
                        ->email
                        ->send();
                }

            }

            if (!empty($buyers))
            {
                for ($i = 0;$i < count($buyers);$i++)
                {
                    $data_buyer = array(
                        'buyer_id' => @$buyers[$i],
                        'sheet_id' => $sheet_id,

                    );
                    $this
                        ->db
                        ->insert(BUYER_SHEET_ASSIGNED, $data_buyer);
                }
            }

            die(json_encode(['status' => 1, 'msg' => "successfully created ", 'sheet_id' => encrypt($sheet_id) ]));
        }

        die(json_encode(['status' => 0, 'msg' => "Invalid Request", 'sheet_id' => ""]));

    }

 

    function mail_after_expired()
    {   
       
        $this->mail_division_buyer();
        $invoice_dtl_group = $this
            ->Common
            ->find(
                [
                        'table' => BUYER_DIVISION . ' bd', 'select' => "us.id,bd.buyer_request_from,os.sheet_id,bd.sheet_id,oi.invoice_id,bd.inv_id,bd.approve,oi.inv_status,bd.email,os.expire,us.email,us.company_name", 
                        'join' => [
                                    [USERS, 'us', 'INNER', "us.id = bd.buyer_request_from"],

                                    [OFFER_SHEET, 'os', 'INNER', "os.sheet_id = bd.sheet_id"],

                                    [OFFER_INVOICE, 'oi', 'INNER', "oi.invoice_id = bd.inv_id"],

                                  ], 
                        'where' => "bd.approve = 'A' AND oi.inv_status ='A' AND bd.email ='N' AND os.expire = 'Y'",

                        'group' => 'bd.buyer_request_from'

                ]);

        //echo "<pre>";print_r($invoice_dtl_group);exit;
        

        $all_inv_data = array();
        foreach ($invoice_dtl_group as $val)
        {
            array_push($all_inv_data, $val['inv_id']);

        }

        //$totalDivMail=array_merge($invoice_dtl_group,$invoice_dtl_group);
        // echo "<pre>";print_r($totalDivMail);exit;
        // echo @$totalDivMail[0]['email'];;exit;
        

        if (!empty($invoice_dtl_group))
        {

            $count = 0;

            for ($i = 0;$i < count($invoice_dtl_group);$i++)
            {

                $sheet_id = @$invoice_dtl_group[$i]['sheet_id'];

                $objPHPExcel = new PHPExcel();

                $filename = 'invoice-' . @$invoice_dtl_group[$i]['id'] . $sheet_id . '.xls';
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=" . $filename);
                header("Pragma: no-cache"); //Prevent Caching
                header("Expires: 0");

                $objPHPExcel->setActiveSheetIndex(0);

                $objPHPExcel->getActiveSheet()
                    ->setTitle('Tea Inntech');

                $objPHPExcel->getActiveSheet()
                    ->setCellValue('A1', 'Serial no');
                $objPHPExcel->getActiveSheet()
                    ->setCellValue('B1', 'Garden');
                $objPHPExcel->getActiveSheet()
                    ->setCellValue('C1', 'Invoice');
                $objPHPExcel->getActiveSheet()
                    ->setCellValue('D1', 'Grade');
                $objPHPExcel->getActiveSheet()
                    ->setCellValue('E1', 'PKGS');
                $objPHPExcel->getActiveSheet()
                    ->setCellValue('F1', 'KGS');
                $objPHPExcel->getActiveSheet()
                    ->setCellValue('G1', 'Price');

                $objPHPExcel->getActiveSheet()
                    ->getStyle('A1', 'Serial no')
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()
                    ->getStyle('B1', 'Garden')
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()
                    ->getStyle('C1', 'Invoice')
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()
                    ->getStyle('D1', 'Grade')
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()
                    ->getStyle('E1', 'PKGS')
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()
                    ->getStyle('F1', 'KGS')
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()
                    ->getStyle('G1', 'Price')
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                //retrive contries table data
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension('A')
                    ->setWidth(25);
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension('B')
                    ->setWidth(25);
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension('C')
                    ->setWidth(25);
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension('D')
                    ->setWidth(25);
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension('E')
                    ->setWidth(25);
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension('F')
                    ->setWidth(25);
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension('G')
                    ->setWidth(25);

                $this
                    ->db
                    ->select('invoice_id,inv_status,total_kgs,pkgs_no,serial_no,garden,invoice,grade');
                $this
                    ->db
                    ->from(OFFER_INVOICE);
                $this
                    ->db
                    ->where('invoice_id', @$invoice_dtl_group[$i]['inv_id']);
                //$this->db->where('sheet_id',$sheet_id);
                $this
                    ->db
                    ->where('inv_status', 'A');
                $qry = $this
                    ->db
                    ->get();
                $inv_detl = $qry->result();

                $row = 2;

                      $this->db->select('toi.invoice_id,tbd.inv_id,tbd.approve,tbd.sheet_id,toi.inv_status,tbd.buyer_request_from');
                      $this->db->from('tt_buyer_division tbd');
                      $this->db->join('tt_offer_invoice toi','toi.invoice_id=tbd.inv_id');
                      $this->db->where('tbd.approve','A');
                      $this->db->where('tbd.sheet_id', $sheet_id);
                      $this->db->where('toi.inv_status','A');
                      $this->db->where('tbd.buyer_request_from', @$invoice_dtl_group[$i]['id']);
                     $qry1 = $this
                    ->db
                    ->get();
                $mail_division = $qry1->result();

                if (!empty($mail_division))
                {

                    foreach ($mail_division as $value)
                    {

                        $this
                            ->db
                            ->select('invoice_id,inv_status,total_kgs,pkgs_no,serial_no,garden,invoice,grade');
                        $this
                            ->db
                            ->from(OFFER_INVOICE);
                        $this
                            ->db
                            ->where('invoice_id', $value->inv_id);

                        $this->db
                            ->where('inv_status', 'A');
                        $qry = $this
                            ->db
                            ->get();
                            
                        $inv_detl_div = $qry->result();

                        $this
                            ->db
                            ->select('MAX(buyer_price) as maxprice');
                        $this
                            ->db
                            ->from(BID_DETAILS);
                        $this
                            ->db
                            ->where('invoice_id', $value->inv_id);
                        $qry = $this
                            ->db
                            ->get();
                        $dbuyer_price = $qry->result();

                        $dPkgs = @$inv_detl_div[0]->pkgs_no - (round(@$inv_detl_div[0]->pkgs_no / 2));
                        $dTkg = round(@$inv_detl_div[0]->total_kgs / @$inv_detl_div[0]->pkgs_no) * (@$inv_detl_div[0]->pkgs_no - (round(@$inv_detl_div[0]->pkgs_no / 2)));

                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('A' . $row, @$inv_detl_div[0]->serial_no);
                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('B' . $row, @$inv_detl_div[0]->garden);
                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('C' . $row, @$inv_detl_div[0]->invoice);
                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('D' . $row, @$inv_detl_div[0]->grade);
                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('E' . $row, $dPkgs);
                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('F' . $row, $dTkg);
                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('G' . $row, @$dbuyer_price[0]->maxprice);

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('A' . $row, @$inv_detl_div[0]->serial_no)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('B' . $row, @$inv_detl_div[0]->garden)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('C' . $row, @$inv_detl_div[0]->invoice)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('D' . $row, @$inv_detl_div[0]->grade)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('E' . $row, $dPkgs)->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('F' . $row, $dTkg)->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('G' . $row, @$buyer_price[0]->buyer_price)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $row++;

                    }

                }

                //$row1=count(@$mail_division);
                foreach ($inv_detl as $value)
                {
                    $this
                        ->db
                        ->select('buyer_id,sheet_id,invoice_id,buyer_price');
                    $this
                        ->db
                        ->from(BID_DETAILS);
                    $this
                        ->db
                        ->where('buyer_id', @$invoice_dtl_group[$i]['id']);
                    $this
                        ->db
                        ->where('sheet_id', $sheet_id);
                    $this
                        ->db
                        ->where('invoice_id', $value->invoice_id);
                    $qry = $this
                        ->db
                        ->get();
                    $buyer_price = $qry->result();

                    $this
                        ->db
                        ->select('approve,sheet_id,inv_id,buyer_request_to');
                    $this
                        ->db
                        ->from('tt_buyer_division');
                    $this
                        ->db
                        ->where('approve', 'A');
                    $this
                        ->db
                        ->where('sheet_id', $sheet_id);
                    $this
                        ->db
                        ->where('inv_id', $value->invoice_id);
                    $qry1 = $this
                        ->db
                        ->get();
                    $chk_division = $qry1->result();

                    if (@$chk_division[0]->buyer_request_to == @$invoice_dtl_group[$i]['id'])
                    {

                        $dTkg1 = (round($value->total_kgs / $value->pkgs_no) * (round($value->pkgs_no / 2)));

                        $dPkgs1 = round($value->pkgs_no / 2);
                    }

                    else
                    {
                        $dTkg1 = $value->total_kgs;
                        $dPkgs1 = $value->pkgs_no;
                    }

                    $objPHPExcel->getActiveSheet()
                        ->setCellValue('A' . $row, $value->serial_no);
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue('B' . $row, $value->garden);
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue('C' . $row, $value->invoice);
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue('D' . $row, $value->grade);
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue('E' . $row, $dPkgs1);
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue('F' . $row, $dTkg1);
                    $objPHPExcel->getActiveSheet()
                        ->setCellValue('G' . $row, @$buyer_price[0]->buyer_price);

                    $objPHPExcel->getActiveSheet()
                        ->getStyle('A' . $row, $value->serial_no)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()
                        ->getStyle('B' . $row, $value->garden)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()
                        ->getStyle('C' . $row, $value->invoice)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()
                        ->getStyle('D' . $row, $value->grade)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()
                        ->getStyle('E' . $row, $dPkgs1)->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()
                        ->getStyle('F' . $row, $dTkg1)->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()
                        ->getStyle('G' . $row, @$buyer_price[0]->buyer_price)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $row++;
                }

                $count++;
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

                $path = "public/default/";
                $objWriter->save($path . $filename, 'w');

            }

            for ($i = 0;$i < count($invoice_dtl_group);$i++)
            {

                $div_invoice_id = @$invoice_dtl_group[$i]['inv_id'];

                $this
                    ->db
                    ->select('offi.invoice_id');
                $this
                    ->db
                    ->from('tt_offer_invoice' . ' offi');
                $this
                    ->db
                    ->where('offi.invoice_id', $div_invoice_id);
                $qry = $this
                    ->db
                    ->get();
                $chk_div_wheathr_active = $qry->result();

              

                $auto_sht_id = @$invoice_dtl_group[$i]['sheet_id'];

                $this
                    ->db
                    ->select('us.id,os.created_by,os.sheet_id,us.email,us.company_name,os.sheet_name,os.sheet_no');
                $this
                    ->db
                    ->from(OFFER_SHEET . ' os');
                $this
                    ->db
                    ->join(USERS . ' us', 'us.id=os.created_by');
                $this
                    ->db
                    ->where('os.sheet_id', $auto_sht_id);
                $qry = $this
                    ->db
                    ->get();

                $seller = $qry->result();


                $seller_mail = @$seller[0]->email;
                $seller_name = @$seller[0]->company_name;
                $sheet_name = @$seller[0]->sheet_name;
                $sheet_no = @$seller[0]->sheet_no;

               

                /*  $data_arry=array('mail'=>'Y');
                                                                    $this->db->where('sheet_id',$auto_sht_id);
                                                                    $this->db->where('inv_status','A');
                                                                    $this->db->update('tt_offer_invoice',$data_arry);*/

               

                $to = $invoice_dtl_group[$i]['email'];
                $from = $this
                    ->config
                    ->item('smtp_user');
                $email_data['mail_data'] = array(

                    'sheet_id' => $auto_sht_id,
                    'seller_name' => $seller_name,
                    'buyer_name' => @$invoice_dtl_group[$i]['company_name'],
                    'buyer_id' => @$invoice_dtl_group[$i]['id'],

                );

                $this
                    ->email
                    ->set_mailtype("html");

                $html_email_user_div = $this
                    ->load
                    ->view('final_mail_template_invoice', $email_data, true);

                // print_r($html_email_user1);exit;
                $this
                    ->email
                    ->from($from, 'Tea Inntech');
                $this
                    ->email
                    ->to($to);

               
                $this
                    ->email
                    ->subject('Tea Inntech | PRIVATE SALE CONFIRMATION');
                $this
                    ->email
                    ->message(@$html_email_user_div);
                $this
                    ->email
                    ->cc(@$seller_mail);
                $this->email->bcc("teainntech001@gmail.com");
                $this
                    ->email
                    ->attach('public/default/' . 'invoice-' . @$invoice_dtl_group[$i]['id'] . $auto_sht_id . '.xls');

                @unlink('public/default/' . 'invoice-' . @$invoice_dtl_group[$i]['id'] . $auto_sht_id . '.xls');

                //  echo  @$reponse;exit;
                @$reponse = $this
                    ->email
                    ->send();

                if (@$reponse)
                {

                    $this
                        ->email
                        ->clear(true);

                $data_arry = array(
                    'email' => 'Y'
                );
                $this
                    ->db
                    ->where('approve', 'A');
                $this
                    ->db
                    ->where('buyer_request_from', @$invoice_dtl_group[$i]['buyer_request_from']);
                $this
                    ->db
                    ->update('tt_buyer_division', $data_arry);

                    $res = 1;
                }
                else
                {
                    $res = 2;

                }

            

            }

        }

    }

    
    function mail_division_buyer()
    {
        
       
        $bid_details_all = $this
            ->Common
            ->find
            (
                [
                    'table' => BID_DETAILS . ' bd', 'select' => "os.sheet_id,bd.sheet_id,os.expiry_date,os.expire,os.complete_close",  
                    'join'  => 
                        [
                            [OFFER_SHEET, 'os', 'INNER', "os.sheet_id = bd.sheet_id"],
                        ],
                    'where' => "os.complete_close = 'Y'",
                    'group' => 'bd.sheet_id'
                ]
            );

            //pre($this->db->last_query());exit;
        if (!empty($bid_details_all))

        {
            $now = date('d-m-Y H:i:s');
            $currentDateTime = strtotime(@$now);
            //$counttest=0;
            foreach ($bid_details_all as $row)
            {
               
               


                @$expiry_date = date("d-m-Y H:i:s", strtotime($row['expiry_date']));
                @$newEXPDate = @strtotime(@$expiry_date);

                if (@$newEXPDate < $currentDateTime || $row['expire'] == 'Y')
                {

                    $sheet_id = $row['sheet_id'];

                    $this
                        ->db
                        ->select('us.id,os.created_by,os.sheet_id,us.email,us.company_name,os.sheet_name,os.sheet_no');
                    $this
                        ->db
                        ->from(OFFER_SHEET . ' os');
                    $this
                        ->db
                        ->join(USERS . ' us', 'us.id=os.created_by');
                    $this
                        ->db
                        ->where('os.sheet_id', $sheet_id);
                    $qry = $this
                        ->db
                        ->get();

                    $seller = $qry->result();

                    $seller_mail = @$seller[0]->email;
                    $seller_name = @$seller[0]->company_name;
                    $sheet_name = @$seller[0]->sheet_name;
                    $sheet_no = @$seller[0]->sheet_no;

                   
                    $this
                        ->load
                        ->library('dompdf_gen');

                    $data_expire_sheet = array(
                        'expire' => 'Y'
                    );
                    $this
                        ->db
                        ->where('sheet_id', $sheet_id);
                    $this
                        ->db
                        ->update(OFFER_SHEET, $data_expire_sheet);

                    $invoice_dtl_group = $this
                        ->Common
                        ->find(['table' => OFFER_INVOICE . ' os', 'select' => "os.sold_by,us.id,os.mail,os.inv_status,os.sheet_id,us.email,us.company_name", 'join' => [[USERS, 'us', 'INNER', "us.id = os.sold_by"],

                    ], 'where' => "os.sheet_id = {$sheet_id} && os.inv_status='A' && os.mail='N'", 'group' => "os.sold_by"

                    ]);

                    $count = 0;

                    for ($i = 0;$i < count($invoice_dtl_group);$i++){
/*
                        $to = 'subhadeep.lahiri@ivanwebsolutions.com';
       
                        $from = $this->config->item('smtp_user');
                        $this->email->from($from, 'Tea Inntech');
                        $this->email->to($to);
                        $this->email->subject('welcome email'.$counttest." ".count($invoice_dtl_group));
                        $this->email->message(json_encode($invoice_dtl_group));
                        @$reponse=$this->email->send();
                        $counttest++;*/

                        $objPHPExcel = new PHPExcel();

                        $filename = 'invoice-' . @$invoice_dtl_group[$i]['id'] . $sheet_id . '.xls';
                        header("Content-type: application/octet-stream");
                        header("Content-Disposition: attachment; filename=" . $filename);
                        header("Pragma: no-cache"); //Prevent Caching
                        header("Expires: 0");

                        $objPHPExcel->setActiveSheetIndex(0);

                        $objPHPExcel->getActiveSheet()
                            ->setTitle('Tea Inntech');

                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('A1', 'Serial no');
                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('B1', 'Garden');
                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('C1', 'Invoice');
                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('D1', 'Grade');
                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('E1', 'PKGS');
                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('F1', 'KGS');
                        $objPHPExcel->getActiveSheet()
                            ->setCellValue('G1', 'Price');

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('A1', 'Serial no')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('B1', 'Garden')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('C1', 'Invoice')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('D1', 'Grade')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('E1', 'PKGS')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('F1', 'KGS')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()
                            ->getStyle('G1', 'Price')
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                      
                        $objPHPExcel->getActiveSheet()
                            ->getColumnDimension('A')
                            ->setWidth(25);
                        $objPHPExcel->getActiveSheet()
                            ->getColumnDimension('B')
                            ->setWidth(25);
                        $objPHPExcel->getActiveSheet()
                            ->getColumnDimension('C')
                            ->setWidth(25);
                        $objPHPExcel->getActiveSheet()
                            ->getColumnDimension('D')
                            ->setWidth(25);
                        $objPHPExcel->getActiveSheet()
                            ->getColumnDimension('E')
                            ->setWidth(25);
                        $objPHPExcel->getActiveSheet()
                            ->getColumnDimension('F')
                            ->setWidth(25);
                        $objPHPExcel->getActiveSheet()
                            ->getColumnDimension('G')
                            ->setWidth(25);

                        $this
                            ->db
                            ->select('sold_by,sheet_id,inv_status,invoice_id,total_kgs,pkgs_no,serial_no,garden,invoice,grade');
                        $this
                            ->db
                            ->from(OFFER_INVOICE);
                        $this
                            ->db
                            ->where('sold_by', @$invoice_dtl_group[$i]['id']);
                        $this
                            ->db
                            ->where('sheet_id', $sheet_id);
                        $this
                            ->db
                            ->where('inv_status', 'A');
                        $qry = $this
                            ->db
                            ->get();
                        $inv_detl = $qry->result();

                        $row = 2;

                       

                      $this->db->select('toi.invoice_id,tbd.inv_id,tbd.approve,tbd.sheet_id,toi.inv_status,tbd.buyer_request_from');
                      $this->db->from('tt_buyer_division tbd');
                      $this->db->join('tt_offer_invoice toi','toi.invoice_id=tbd.inv_id');
                      $this->db->where('tbd.approve','A');
                      $this->db->where('tbd.sheet_id', $sheet_id);
                      $this->db->where('toi.inv_status','A');
                      $this->db->where('tbd.buyer_request_from', @$invoice_dtl_group[$i]['id']);
                     $qry1 = $this
                    ->db
                    ->get();
                $mail_division = $qry1->result();

                        if (!empty($mail_division))
                        {

                            foreach ($mail_division as $value)
                            {

                                $this
                                    ->db
                                    ->select('invoice_id,pkgs_no,total_kgs,serial_no,garden,invoice,grade');
                                $this
                                    ->db
                                    ->from(OFFER_INVOICE);
                                $this
                                    ->db
                                    ->where('invoice_id', $value->inv_id);
                                $qry = $this
                                    ->db
                                    ->get();
                                $inv_detl_div = $qry->result();

                                $this
                                    ->db
                                    ->select('MAX(buyer_price) as maxprice');
                                $this
                                    ->db
                                    ->from(BID_DETAILS);
                                $this
                                    ->db
                                    ->where('invoice_id', $value->inv_id);
                                $qry = $this
                                    ->db
                                    ->get();
                                $dbuyer_price = $qry->result();

                                $dPkgs = @$inv_detl_div[0]->pkgs_no - (round(@$inv_detl_div[0]->pkgs_no / 2));
                                $dTkg = round(@$inv_detl_div[0]->total_kgs / @$inv_detl_div[0]->pkgs_no) * (@$inv_detl_div[0]->pkgs_no - (round(@$inv_detl_div[0]->pkgs_no / 2)));

                                $objPHPExcel->getActiveSheet()
                                    ->setCellValue('A' . $row, @$inv_detl_div[0]->serial_no);
                                $objPHPExcel->getActiveSheet()
                                    ->setCellValue('B' . $row, @$inv_detl_div[0]->garden);
                                $objPHPExcel->getActiveSheet()
                                    ->setCellValue('C' . $row, @$inv_detl_div[0]->invoice);
                                $objPHPExcel->getActiveSheet()
                                    ->setCellValue('D' . $row, @$inv_detl_div[0]->grade);
                                $objPHPExcel->getActiveSheet()
                                    ->setCellValue('E' . $row, $dPkgs);
                                $objPHPExcel->getActiveSheet()
                                    ->setCellValue('F' . $row, $dTkg);
                                $objPHPExcel->getActiveSheet()
                                    ->setCellValue('G' . $row, @$dbuyer_price[0]->maxprice);

                                $objPHPExcel->getActiveSheet()
                                    ->getStyle('A' . $row, @$inv_detl_div[0]->serial_no)
                                    ->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                                $objPHPExcel->getActiveSheet()
                                    ->getStyle('B' . $row, @$inv_detl_div[0]->garden)
                                    ->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                                $objPHPExcel->getActiveSheet()
                                    ->getStyle('C' . $row, @$inv_detl_div[0]->invoice)
                                    ->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                                $objPHPExcel->getActiveSheet()
                                    ->getStyle('D' . $row, @$inv_detl_div[0]->grade)
                                    ->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                                $objPHPExcel->getActiveSheet()
                                    ->getStyle('E' . $row, $dPkgs)->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                                $objPHPExcel->getActiveSheet()
                                    ->getStyle('F' . $row, $dTkg)->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                                $objPHPExcel->getActiveSheet()
                                    ->getStyle('G' . $row, @$buyer_price[0]->buyer_price)
                                    ->getAlignment()
                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                                $row++;

                            }

                        }

                     
                        foreach ($inv_detl as $value)
                        {
                            $this
                                ->db
                                ->select('buyer_id,sheet_id,invoice_id,buyer_price');
                            $this
                                ->db
                                ->from(BID_DETAILS);
                            $this
                                ->db
                                ->where('buyer_id', @$invoice_dtl_group[$i]['id']);
                            $this
                                ->db
                                ->where('sheet_id', $sheet_id);
                            $this
                                ->db
                                ->where('invoice_id', $value->invoice_id);
                            $qry = $this
                                ->db
                                ->get();
                            $buyer_price = $qry->result();

                            $this
                                ->db
                                ->select('approve,sheet_id,inv_id,buyer_request_to');
                            $this
                                ->db
                                ->from('tt_buyer_division');
                            $this
                                ->db
                                ->where('approve', 'A');
                            $this
                                ->db
                                ->where('sheet_id', $sheet_id);
                            $this
                                ->db
                                ->where('inv_id', $value->invoice_id);
                            $qry1 = $this
                                ->db
                                ->get();
                            $chk_division = $qry1->result();

                            if (@$chk_division[0]->buyer_request_to == @$invoice_dtl_group[$i]['id'])
                            {

                                $dTkg1 = (round($value->total_kgs / $value->pkgs_no) * (round($value->pkgs_no / 2)));

                                $dPkgs1 = round($value->pkgs_no / 2);
                            }

                            else
                            {
                                $dTkg1 = $value->total_kgs;
                                $dPkgs1 = $value->pkgs_no;
                            }

                          

                            $objPHPExcel->getActiveSheet()
                                ->setCellValue('A' . $row, $value->serial_no);
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue('B' . $row, $value->garden);
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue('C' . $row, $value->invoice);
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue('D' . $row, $value->grade);
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue('E' . $row, $dPkgs1);
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue('F' . $row, $dTkg1);
                            $objPHPExcel->getActiveSheet()
                                ->setCellValue('G' . $row, @$buyer_price[0]->buyer_price);

                            $objPHPExcel->getActiveSheet()
                                ->getStyle('A' . $row, $value->serial_no)
                                ->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objPHPExcel->getActiveSheet()
                                ->getStyle('B' . $row, $value->garden)
                                ->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objPHPExcel->getActiveSheet()
                                ->getStyle('C' . $row, $value->invoice)
                                ->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objPHPExcel->getActiveSheet()
                                ->getStyle('D' . $row, $value->grade)
                                ->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objPHPExcel->getActiveSheet()
                                ->getStyle('E' . $row, $dPkgs1)->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objPHPExcel->getActiveSheet()
                                ->getStyle('F' . $row, $dTkg1)->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $objPHPExcel->getActiveSheet()
                                ->getStyle('G' . $row, @$buyer_price[0]->buyer_price)
                                ->getAlignment()
                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            $row++;
                        }
                        $count++;

                        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

                        $path = "public/default/";
                        $objWriter->save($path . $filename, 'w');

                    }

                   
                    if (!empty($invoice_dtl_group))
                    {

                        for ($i = 0;$i < count($invoice_dtl_group);$i++)
                        {

                            $auto_sht_id = @$invoice_dtl_group[$i]['sheet_id'];

                            $data_arry = array(
                                'mail' => 'Y'
                            );
                            $this
                                ->db
                                ->where('sheet_id', $auto_sht_id);
                            $this
                                ->db
                                ->where('inv_status', 'A');
                            $this
                                ->db
                                ->update('tt_offer_invoice', $data_arry);

                            $data_arry = array(
                                'email' => 'Y'
                            );
                            $this
                                ->db
                                ->where('approve', 'A');
                            $this
                                ->db
                                ->where('buyer_request_from', @$invoice_dtl_group[$i]['id']);
                            $this
                                ->db
                                ->update('tt_buyer_division', $data_arry);

                            $to = @$invoice_dtl_group[$i]['email'];
                            $from = $this
                                ->config
                                ->item('smtp_user');
                            $email_data['mail_data'] = array(

                                'sheet_id' => $sheet_id,
                                'seller_name' => $seller_name,
                                'buyer_name' => @$invoice_dtl_group[$i]['company_name'],
                                'buyer_id' => @$invoice_dtl_group[$i]['id'],

                            );

                            $this
                                ->email
                                ->set_mailtype("html");

                            $html_email_user1 = $this
                                ->load
                                ->view('final_mail_template_invoice', $email_data, true);

                            $this
                                ->email
                                ->from($from, 'Tea Inntech');
                            $this
                                ->email
                                ->to($to);
                            $this
                                ->email
                                ->subject('Tea Inntech | PRIVATE SALE CONFIRMATION');
                            $this
                                ->email
                                ->message(@$html_email_user1);
                            $this
                                ->email
                                ->cc(@$seller_mail);
                            $this
                                ->email
                                ->bcc("teainntech001@gmail.com");

                            $this
                                ->email
                                ->attach('public/default/' . 'invoice-' . @$invoice_dtl_group[$i]['id'] . $sheet_id . '.xls');
                            @unlink('public/default/' . 'invoice-' . @$invoice_dtl_group[$i]['id'] . $sheet_id . '.xls');

                            @$reponse = $this
                                ->email
                                ->send();
                             $this
                                ->email
                                ->clear(true);

                            if (@$reponse)
                            {

                                

                                $res = 1;
                            }
                            else
                            {
                                $res = 2;

                            }

                        }

                    }

                }
            }
        }

      
        

        
    }

    public function seller_offer_sheet_next()
    {
        $sheet_id = $this
            ->uri
            ->segment(2);
        $sheet_id = decrypt($sheet_id);

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['offer_sheet'] = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $this->data['invoice_dtl'] = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

        $this->data['offer_sheet_buyer'] = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],

        ], 'where' => "bsa.sheet_id = {$sheet_id}",

        ]);

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$this->data['payment_type'] = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

        $this
            ->layout
            ->view('generate-offer-sheet-next', $this->data);

    }
    public function buyer_search_offer_sheet()
    {
        
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }
        $this->data['buyer_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);
        $this
            ->layout
            ->view('buyer-search-offer-sheet', $this->data);
    }

    function search_offer_sheet_action()
    {
        //pre($this->input->post(),1);

        if (!empty($this->input->post())){
            $this->form_validation->set_rules('sheet_no', 'sheet no', 'trim|required');

            if ($this->form_validation->run() == false){
                die(json_encode(['status' => 0, 'msg' => validation_errors() ]));
            }

            $data = $this->input->post();

            $this->data['sheet_exist'] = $this->Common->findBy(OFFER_SHEET, 'sheet_no', @$data['sheet_no']);
            //pre($this->data['sheet_exist'],1);
            @$expiry_date = @$this->data['sheet_exist']['expiry_date'];
            $newDate = date("d-m-Y H:i:s", strtotime(@$expiry_date));
            @$newEXPDate = @strtotime(@$newDate);
            @$now = date('d-m-Y H:i:s');
            $currentDateTime = strtotime(@$now);

            if (!empty($this->data['sheet_exist']) && ($newEXPDate > @$currentDateTime) && @$this->data['sheet_exist']['dispatch'] == 'Y'){
                $dtl = $this->session->userdata(CUSTOMER_SESS);
                $this->data['sheetChk'] = $this->Common->find(['table' => BUYER_SHEET_ASSIGNED, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} && sheet_id={$this->data['sheet_exist']['sheet_id']}", 'query' => 'first']);
                if (empty($this->data['sheetChk'])){
                    $data_insert_offer = array(
                        'buyer_id' => $dtl['id'],
                        'sheet_id' => $this->data['sheet_exist']['sheet_id']
                    );
                    $this->db->insert(BUYER_SHEET_ASSIGNED, $data_insert_offer);
                    die(json_encode(['status' => 2, 'msg' => "", 'sheet_id' => encrypt(@$this->data['sheet_exist']['sheet_id']) ]));
                }
                else{
                    die(json_encode(['status' => 2, 'msg' => "", 'sheet_id' => encrypt(@$this->data['sheet_exist']['sheet_id']) ]));
                }

            }
            else
            {
                die(json_encode(['status' => 0, 'msg' => 'Invalid Sheet Number', 'sheet_id' => ""]));
            }

        }

        die(json_encode(['status' => 0, 'msg' => "Invalid request", 'sheet_id' => ""]));

    }

    function status_change_reject_sheet()
    {
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $sheet_id = decrypt($this
            ->uri
            ->segment(4));

        $data = array(
            'is_recv' => 'Reject'
        );
        $this
            ->db
            ->where('sheet_id', $sheet_id);
        $this
            ->db
            ->where('buyer_id', $dtl['id']);
        $this
            ->db
            ->update(BUYER_SHEET_ASSIGNED, $data);
        $res = 1;
        echo json_encode($res);
    }

    function status_change_recive_sheet()
    {
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $sheet_id = decrypt($this
            ->uri
            ->segment(4));

        $data = array(
            'is_recv' => 'Recieve'
        );
        $this
            ->db
            ->where('sheet_id', $sheet_id);
        $this
            ->db
            ->where('buyer_id', $dtl['id']);
        $this
            ->db
            ->update(BUYER_SHEET_ASSIGNED, $data);
        $res = 1;
        echo json_encode($res);

    }
    function buyer_search_offer_sheet_next()
    {
        //echo 1;die;
        $sheet_id = $this
            ->uri
            ->segment(2);
        $sheet_id = decrypt($sheet_id);

        //echo $sheet_id;
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['buyer_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['offer_sheet'] = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $created_by = @$this->data['offer_sheet']['created_by'];

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = $created_by", 'query' => 'first']);

       



   

       

        $this->db->select('*');
        $this->db->from(OFFER_INVOICE);
        $this->db->where('sheet_id',$sheet_id);
        $this->db->limit(100,0);
        $qry=$this->db->get();
        $this->data['invoice_dtl']=$qry->result();

            //pre($this->data['invoice_dtl'],1);

        $this->data['data']= $this->data['invoice_dtl'];

        $this->data['offer_sheet_buyer'] = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*",
        /*'join'      => [
                                [USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],
                              
                            ],*/
        'where' => "bsa.sheet_id = {$sheet_id} AND bsa.buyer_id = {$dtl['id']}", 'query' => 'first',

        ]);

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$this->data['payment_type'] = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

         $this
            ->layout
            ->view('buyer-search-offer-sheet-next', $this->data);

       

    }

    function getPagdata()
   {

    $rec_per_page = 50;
    $start = $rec_per_page * @$this->input->post('page');
    $shtId=@$this->input->post('shtId');
    $this->db->select('*');
    $this->db->from(OFFER_INVOICE);
    $this->db->where('sheet_id',$shtId);
    $this->db->limit($rec_per_page,$start);
    $qry=$this->db->get();
    $this->data['invoice_dtl']=$qry->result();



      $this->load->view("search-ajax-sheet-load", $this->data);

   }

    function dispatch_offer_sheet()
    {

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['offer_sheet'] = $this
            ->Common
            ->find(['table' => OFFER_SHEET, 'select' => "*", 'where' => "created_by = {$dtl['id']} && dispatch='Y'",

        ]);

        $this
            ->layout
            ->view('dispatch-offer-sheet', $this->data);

    }

    function status_change_dispatch()
    {
        $sheet_id = decrypt($this
            ->input
            ->post('sheet_id'));
        $data = array(
            'dispatch' => 'Y'
        );
        $this
            ->db
            ->where('sheet_id', $sheet_id);
        $this
            ->db
            ->update(OFFER_SHEET, $data);
        $res = 1;
        echo json_encode($res);
        //redirect(BASE_URL.'dispatch-offer-sheet');
        
    }

    function dispatch_offer_sheet_next()
    {
        $sheet_id = $this
            ->uri
            ->segment(2);
        $sheet_id = decrypt($sheet_id);

        if ($sheet_id == "")
        {
            redirect(BASE_URL . 'dispatch-offer-sheet');

        }

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['offer_sheet'] = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $this->data['invoice_dtl'] = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

        $this->data['offer_sheet_buyer'] = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],

        ], 'where' => "bsa.sheet_id = {$sheet_id}",

        ]);

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$this->data['payment_type'] = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

        $this
            ->layout
            ->view('dispatch-offer-sheet-next', $this->data);
    }

    function buyer_recieve_offer_sheet()
    {
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['buyer_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['offer_sheet_buyer'] = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[OFFER_SHEET, 'ofs', 'INNER', "ofs.sheet_id = bsa.sheet_id"], [USERS, 'us', 'INNER', "us.id = ofs.created_by"],

        ], 'where' => "bsa.buyer_id = {$dtl['id']} AND bsa.is_recv='Recieve'",

        ]);

        $this
            ->layout
            ->view('buyer-recieve-offer-sheet', $this->data);
    }

    function buyer_recieve_offer_sheet_next()
    {

        $sheet_id = $this
            ->uri
            ->segment(2);
        $sheet_id = decrypt($sheet_id);
        if ($sheet_id == "")
        {
            redirect(BASE_URL . 'buyer-recieve-offer-sheet');

        }
        //echo $sheet_id;
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

         

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from('tt_buyer_recieve_comment');
        $this
            ->db
            ->where('sheet_id', $sheet_id);
        $this
            ->db
            ->where('user_id', $dtl['id']);
        $qry = $this
            ->db
            ->get();
        $this->data['chkComment'] = $qry->result();

        $this->data['buyer_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['offer_sheet'] = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $created_by = @$this->data['offer_sheet']['created_by'];

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = $created_by", 'query' => 'first']);

        $this->data['invoice_dtl'] = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

        $this->data['offer_sheet_buyer'] = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],

        ], 'where' => "bsa.sheet_id = {$sheet_id}",

        ]);



        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$this->data['payment_type'] = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

        $this->data['bid_details'] = $this
            ->Common
            ->find(['table' => BID_DETAILS, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} AND sheet_id = {$sheet_id}",

        ]);

        $this
            ->layout
            ->view('buyer-recieve-offer-sheet-next', $this->data);

    }

    function status_change()
    {
        $sheet_id = $this
            ->input
            ->post('sheet_id');
        $status = $this
            ->input
            ->post('status');
        $this
            ->form_validation
            ->set_rules('sheet_id', 'sheet id', 'trim|required');
        $this
            ->form_validation
            ->set_rules('status', 'status', 'trim|required');

        if ($this
            ->form_validation
            ->run() == false)
        {
            die(json_encode(['status' => 0, 'msg' => validation_errors() ]));
        }
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $data_array = array(
            'is_recv' => $status
        );

        $this
            ->db
            ->where('sheet_id', $sheet_id);
        $this
            ->db
            ->where('buyer_id', @$dtl['id']);
        $this
            ->db
            ->update(BUYER_SHEET_ASSIGNED, $data_array);
        $result = 1;
        echo json_encode($result);

    }

    function setAbptemporaryprice()
    {

         $dtl = $this
                ->session
                ->userdata(CUSTOMER_SESS);

        $buyer_id = @$dtl['id'];

        $my_price = $this
            ->input
            ->post('my_price');

        $sheet_id = $this
            ->input
            ->post('sheet_id');

        $invoice_id = $this
            ->input
            ->post('invoice_id');

        $key = $this
            ->input
            ->post('key');

        $price_idea = $this
            ->input
            ->post('price_idea');

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from('tt_abp_receive');
        $this
            ->db
            ->where('sheet_id', $sheet_id);

        $this
            ->db
            ->where('invoice_id', $invoice_id);
        $this
            ->db
            ->where('buyer_id', $buyer_id);

        $qry = $this
            ->db
            ->get();
        $duplicateCheck = $qry->result();

        if(count($duplicateCheck)==0)
        {
            $dataArray=array(

                        'sheet_id'=>$sheet_id,
                        'buyer_id'=>$buyer_id,
                        'invoice_id'=>$invoice_id,
                        'price'=>$my_price,
                        'seller_price'=>$price_idea,
                        'abp_key'=>$key,
                        'abp_time'=>date('Y-m-d H:i:s')
            );
            if(intval($my_price) !="" || intval($my_price)!=0)
            {
                $this->db->insert('tt_abp_receive',$dataArray);
            }
            
        }
        else
        {
            $data=array(
 
                        'price'=>$my_price,   
                        'abp_time'=>date('Y-m-d H:i:s')           
            );

            $this
                ->db
                ->where('sheet_id', $sheet_id);

            $this
                ->db
                ->where('invoice_id', $invoice_id);
            $this
                ->db
                ->where('buyer_id', $buyer_id);
            if(intval($my_price) !="" || intval($my_price)!=0)
            {
             $this->db->update('tt_abp_receive',$data);
            }
        }

    }

    function setAbptemporary()
    {
        $dtl = $this
                ->session
                ->userdata(CUSTOMER_SESS);

        $buyer_id = @$dtl['id'];

        $abp = $this
            ->input
            ->post('abp');

        $sheet_id = $this
            ->input
            ->post('sheet_id');

        $invoice_id = $this
            ->input
            ->post('invoice_id');

        $key = $this
            ->input
            ->post('key');

        $price_idea = $this
            ->input
            ->post('price_idea');

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from('tt_abp_receive');
        $this
            ->db
            ->where('sheet_id', $sheet_id);

        $this
            ->db
            ->where('invoice_id', $invoice_id);
        $this
            ->db
            ->where('buyer_id', $buyer_id);

        $qry = $this
            ->db
            ->get();
        $duplicateCheck = $qry->result();

        if(intval($abp) =="" || intval($abp)==0)
            {
            $this
            ->db
            ->where('invoice_id', $invoice_id);
            $this
                ->db
                ->where('buyer_id', $buyer_id);
            $this->db->delete('tt_abp_receive');
            }

        if(count($duplicateCheck)==0 && intval($abp) > 0)
        {
            $dataArray=array(

                        'sheet_id'=>$sheet_id,
                        'buyer_id'=>$buyer_id,
                        'invoice_id'=>$invoice_id,
                        'abp'=>$abp,
                        'seller_price'=>$price_idea,
                        'abp_key'=>$key,
                        'abp_time'=>date('Y-m-d H:i:s')
            );

           if(intval($abp) !="" || intval($abp)!=0)
            {

            $this->db->insert('tt_abp_receive',$dataArray);
            }
        }
        else
        {
            $data=array(
 
                        'abp'=>$abp,   
                        'abp_time'=>date('Y-m-d H:i:s')           
            );

            $this
                ->db
                ->where('sheet_id', $sheet_id);

            $this
                ->db
                ->where('invoice_id', $invoice_id);
            $this
                ->db
                ->where('buyer_id', $buyer_id);
            if(intval($abp) !="" || intval($abp)!=0)
            {
             $this->db->update('tt_abp_receive',$data);
            }
        }

            
    }

    function updateSelleractivecomment()
    {

        $firebase = $this->firebase->init();
        $database = $firebase->getDatabase();
        $firebaseKey=trim($this
            ->input
            ->post('firebaseKey'));
        $comment = trim($this
            ->input
            ->post('comment'));
        $invoice_id = $this
            ->input
            ->post('invoice_id');

        $sheet_id = $this
            ->input
            ->post('sheet_id');
        $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);

            
        if ($comment != "")
        {
            $fields1=array('seller_comment'=>$comment);
         
            $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);
            $data_update = array(
                'comment' => $comment
            );

            $decodeJson[$invoice_id]['seller_comment']=$comment;
            $mainArryEncode=json_encode($decodeJson);
            $file_to_save = 'public/uploads/' . $sheet_id . '.json';
            file_put_contents($file_to_save, $mainArryEncode);
          /*  $this
                ->db
                ->where('invoice_id', $invoice_id);
            $this
                ->db
                ->update(OFFER_INVOICE, $data_update);*/
            $result = 1;
        }
        else
        {
            $result = 0;
        }
        echo json_encode($result);

    }

 function buyer_place_activate_bid()
    {

     
        if (!empty($this
            ->input
            ->post()))
        {
            $data = $this
                ->input
                ->post();
            $this
                ->form_validation
                ->set_rules('sheet_id', 'sheet_id', 'trim|required');

            if ($this
                ->form_validation
                ->run() == false)
            {
                redirect(BASE_URL . 'buyer-recieve-offer-sheet-next/' . encrypt($data['sheet_id']));
            }

            //$firebase = $this->firebase->init();
            //$database = $firebase->getDatabase();

            $sheet_id = $data['sheet_id'];
            $price = $data['my_bid'];

          
            $comment = $data['comment'];



            $cr_by = $data['seller_id'];

            $dtl = $this
                ->session
                ->userdata(CUSTOMER_SESS);

            $usr_id = $dtl['id'];

            $buyer_id=$dtl['id'];

            $sheet_details = $this
                ->Common
                ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

            ]);

                $ChkSheet = $this
            ->Common
            ->findBy(OFFER_SHEET, 'sheet_id', $sheet_id);

            $increaseRate=$ChkSheet['bidding_gap'];

            $selectedInvoiceid=array();
            $selectedKey=array();

            $chkPrice = array();

             $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
            $decodeJson= json_decode($jsonFile,true);

            for ($i = 0;$i < count($price);$i++)
            {
                array_push($chkPrice, $price[$i]);

                $data_array = array(
                    'price' => $price[$i],
                    'buyer_id' => $dtl['id'],
                    'sheet_id' => $sheet_id,
                    'seller_price' => $sheet_details[$i]['price_idea'],
                    'invoice_id' => $sheet_details[$i]['invoice_id'],
                    'abp_key'=>$i,
                    'abp_time' => date('Y-m-d H:i:s')
                );

                $this
                    ->db
                    ->select('*');
                $this
                    ->db
                    ->from('tt_abp_receive');
                $this
                    ->db
                    ->where('sheet_id', @$sheet_id);

                $this
                    ->db
                    ->where('invoice_id', @$sheet_details[$i]['invoice_id']);
                $this
                    ->db
                    ->where('buyer_id', @$dtl['id']);

                $qry = $this
                    ->db
                    ->get();
                $duplicateCheck = $qry->result();

                
               if($price[$i] !="" || $price[$i] > 0)
                {
                    if(count($duplicateCheck)==0)
                    {
                        $this->db->insert('tt_abp_receive',$data_array);

                    }
                    else
                    {

                        $dataUp = array(
                        'price' => $price[$i],
                      
                        'abp_time' => date('Y-m-d H:i:s')
                        );

                    $this
                        ->db
                        ->where('sheet_id', @$sheet_id);

                    $this
                        ->db
                        ->where('invoice_id', @$sheet_details[$i]['invoice_id']);
                    $this
                        ->db
                        ->where('buyer_id', @$dtl['id']);

                    $this->db->update('tt_abp_receive',$dataUp);

                    }

                }
            }

            if (!array_filter($chkPrice))
            {
                $this
                    ->session
                    ->set_flashdata('errmsg', 'Please Bid For Atleast One Invoice.');
                // echo '<script>alert("Please Bid For Atleast One Invoice");window.close()</script>';
                //redirect(BASE_URL . 'buyer-recieve-offer-sheet-next/' . encrypt($sheet_id) , 'refresh');
                    $selectedInvoiceid=array();
                    $selectedKey=array();

                    $res=2;
                    $fields1 = array();

            }
            else
            {

                $this
                    ->db
                    ->select('*');
                $this
                    ->db
                    ->from('tt_offer_sheets');
                $this
                    ->db
                    ->where('sheet_id', $sheet_id);
                $qry = $this
                    ->db
                    ->get();
                $sheet_details_msg = $qry->result();

                 

            $msg_sheet_name = @$sheet_details_msg[0]->sheet_name;
            $msg_sheet_no = @$sheet_details_msg[0]->sheet_no;
            $msg_sheet_created = @$sheet_details_msg[0]->created_by;

                $this
                    ->db
                    ->select('*');
                $this
                    ->db
                    ->from('tt_users');
                $this
                    ->db
                    ->where('id', $msg_sheet_created);
                $qry = $this
                    ->db
                    ->get();
                $sheet_details_created = $qry->result();


                $this
                    ->db
                    ->select('*');
                $this
                    ->db
                    ->from('tt_users');
                $this
                    ->db
                    ->where('id', $usr_id);
                $qry = $this
                    ->db
                    ->get();
                $sheet_details_place = $qry->result();

          
            $msg_sheet_created_number = trim(@$sheet_details_created[0]->phone);
            $msg_buyer_name = @$sheet_details_place[0]->company_name;

           

            /////////////////////////////////////MESSAGE//////////////////////////////////////////
            // /qq|22|sht|
           // $body = $msg_buyer_name . " has placed a bid and activated offer sheet " . $msg_sheet_no . "-" . $msg_sheet_name;
            $messageId='111592';
            $to = $msg_sheet_created_number;
            $variables=$msg_buyer_name.'|'.$msg_sheet_no.'|'.$msg_sheet_name;
            send_sms($to, $messageId, $variables);

            $my_hbp=$data['my_hbp'];


            $this
                ->db
                ->select('*');
            $this
                ->db
                ->from('tt_abp_receive');
            $this
                ->db
                ->where('sheet_id', $sheet_id);

            $this
                ->db
                ->where('buyer_id', $usr_id);
            $qry = $this
                ->db
                ->get();
            $getAllvalue = $qry->result();

           //echo "<pre>";print_r($getAllvalue);exit;

            for ($i = 0;$i < count($getAllvalue);$i++)
            {

                $this->db->select('MAX(buyer_price) as maxprice');
                $this->db->from(BID_DETAILS);
                $this->db->where('invoice_id',@$getAllvalue[$i]->invoice_id);
                $bid_max=$this->db->get();
                $getMaxprice2=$bid_max->result();
                $chkgetMaxprice2=@$decodeJson[@$getAllvalue[$i]->invoice_id]['bidMaxPrice'];

            /*    $this->db->select('bd.invoice_id,bd.buyer_hbp,bd.buyer_id,bd.buyer_price,tus.id,tus.company_name');
                $this->db->from(BID_DETAILS.' bd');
                $this->db->join('tt_users tus','tus.id=bd.buyer_id','left');
                $this->db->where('bd.invoice_id',@$getAllvalue[$i]->invoice_id);
                $this->db->where('bd.buyer_price',$chkgetMaxprice2);
                $bid_max_hbp_buyer_id8=$this->db->get();
                $get_bid_max_hbp_buyer_id8 = $bid_max_hbp_buyer_id8->result();
                $get_bid_max_hbp_buyer_id_final8=@$get_bid_max_hbp_buyer_id8[0]->company_name;*/


                $this->db->select('company_name,id');
                $this->db->from(USERS);
                $this->db->where('id',@$decodeJson[@$getAllvalue[$i]->invoice_id]['bidMaxbuyerId']);
                $name=$this->db->get();
                $company=$name->result();
                $get_bid_max_hbp_buyer_id_final8=@$company[0]->company_name;


                $this
                    ->db
                    ->select('invoice_id,firebase_key');
                $this
                    ->db
                    ->from('tt_offer_invoice');
                $this
                    ->db
                    ->where('invoice_id', @$getAllvalue[$i]->invoice_id);
                $this
                    ->db
                    ->where('sheet_id', $sheet_id);
                $qry1 = $this
                    ->db
                    ->get();
                $getfirebasekey = $qry1->result();



                $this
                    ->db
                    ->select('*');
                $this
                    ->db
                    ->from('tt_buyer_recieve_comment');
                $this
                    ->db
                    ->where('invoice_id', @$getAllvalue[$i]->invoice_id);
                $this
                    ->db
                    ->where('user_id', $dtl['id']);
                $qry = $this
                    ->db
                    ->get();
                $chkComment = $qry->result();

                if(@$getAllvalue[$i]->price == "" || @$getAllvalue[$i]->price == 0)
                {
                    if($get_bid_max_hbp_buyer_id_final8 != "" && intval($chkgetMaxprice2) > 0 )
                    {
                        //$company_name=$get_bid_max_hbp_buyer_id_final8;
                        $bidprice=@$decodeJson[@$getAllvalue[$i]->invoice_id]['bidMaxPrice'];
                    }
                    else
                    {
                        $company_name="";
                        $bidprice=0;
                    }
       
                }
                else
                {
                     //$company_name = @$dtl['company'];
                     $bidprice=@$getAllvalue[$i]->price;
                }
               
          

                $firebaseKey = @$getfirebasekey[0]->invoice_id;
                

               // $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey.'/buyer_hbp/'.@$dtl['id'])->set(@$getAllvalue[$i]->abp);
                
              //  @$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey.'/buyer_bid/'.@$dtl['id'])->set(trim($bidprice));




                

                $data_array = array(
                    'buyer_price' => @$getAllvalue[$i]->price,
                    'buyer_id' => $dtl['id'],
                    'seller_id' => $data['seller_id'],
                    'sheet_id' => $sheet_id,
                    'seller_price' => @$getAllvalue[$i]->seller_price,
                    'invoice_id' => @$getAllvalue[$i]->invoice_id,
                    'comment_buyer' => @$chkComment[0]->comment,
                    'bid_time' => date('Y-m-d H:i:s'),
                    'buyer_hbp'=>@$getAllvalue[$i]->abp,
                    'hbp_key'=>@$getAllvalue[$i]->abp_key,
                    'hbp_time'=>@$getAllvalue[$i]->abp_time,
                    'push_hbp'=>'N',
                    'firebase_key'=>$firebaseKey
                );
                

                    $data_log = array(

                    'buyer_id' => $dtl['id'],
                    'seller_id' => $data['seller_id'],
                    'invoice_id' => @$getAllvalue[$i]->invoice_id,
                    'sheet_id' => $sheet_id,
                    'buyer_price' => @$getAllvalue[$i]->price,
                    'seller_price' => @$getAllvalue[$i]->seller_price,

                    'bid_on' => date('Y-m-d H:i:s'),
                );

                   


                $this
                    ->db
                    ->insert(BID_LOG, $data_log);

                    $decodeJson[@$getAllvalue[$i]->invoice_id]['bidMaxPrice']=trim(@$getAllvalue[$i]->price);
                    $decodeJson[@$getAllvalue[$i]->invoice_id]['bidMaxbuyerId']=$dtl['id'];

                    $decodeJson[@$getAllvalue[$i]->invoice_id]['buyer_bid'][$dtl['id']] =array(
                                            'bid'         => @$getAllvalue[$i]->price,
                                            'bid_time'    => date('Y-m-d H:i:s'),
                                            'buyer_id'    =>$dtl['id'],
                                            
                                        );

                    $decodeJson[@$getAllvalue[$i]->invoice_id]['buyer_bid'][$dtl['id']]['comment_buyer'] = @$chkComment[0]->comment;

                     $mainArryEncode=json_encode($decodeJson);
                    $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                    file_put_contents($file_to_save, $mainArryEncode);


                    /* $this
                    ->db
                    ->insert(BID_DETAILS, $data_array);*/

                     $s_inv=@$getAllvalue[$i]->invoice_id;

                    $this->db->select('invoice_id,buyer_price');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('invoice_id',$s_inv);
                    $this->db->where('buyer_price >','0');
                    $division_check=$this->db->get();
                    $division_check_buyer=$division_check->result();

                    if($decodeJson[$s_inv]['bidMaxPrice'] > 0){
                        $division_check_buyer = 1;
                    }else{
                        $division_check_buyer = 0;
                    }

                    $this->db->select('inv_id,approve'); 
                    $this->db->from('tt_buyer_division');
                    $this->db->where('inv_id',$s_inv);
                    $this->db->where('approve','A');
                    $division_check=$this->db->get();
                    $division_check_accept=$division_check->result();

                $chk_div_byr = $this
                    ->Common
                    ->find(['table' => 'tt_buyer_division', 'select' => "*", 'where' => "sheet_id = {$sheet_id} AND inv_id={$getAllvalue[$i]->invoice_id}", 'query' => 'first'

                ]);

                if (!empty(@$chk_div_byr) && (@$getAllvalue[$i]->price > @$chk_div_byr['price']) && @$getAllvalue[$i]->price != "")
                {
                    $this
                        ->db
                        ->where('inv_id', $chk_div_byr['inv_id']);
                    $this
                        ->db
                        ->delete('tt_buyer_division');


                        $this->db->select('invoice_id,buyer_price');
                        $this->db->from(BID_DETAILS);
                        $this->db->where('invoice_id',$chk_div_byr['inv_id']);
                        $this->db->where('buyer_price >','0');
                        $division_check=$this->db->get();
                        $division_check_buyer=$division_check->result();

                        if($decodeJson[$s_inv]['bidMaxPrice'] > 0){
                                $division_check_buyer = 1;
                            }else{
                                $division_check_buyer = 0;
                            }

                        $this->db->select('inv_id,approve'); 
                        $this->db->from('tt_buyer_division');
                        $this->db->where('inv_id',$chk_div_byr['inv_id']);
                        $this->db->where('approve','A');
                        $division_check=$this->db->get();
                        $division_check_accept=$division_check->result();

                }

                   // $fields11=array('division_check_accept'=>count($division_check_accept),'division_check_buyer'=>count($division_check_buyer));
                     
                   // $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields11);

                    $this->db->select('MAX(buyer_price) as maxbprice');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('invoice_id',$s_inv);
                    $bid_max1=$this->db->get();
                    $getMaxhbpmaxbprice=$bid_max1->result();
                    $chkgetMaxhbpmaxbprice=$decodeJson[$s_inv]['bidMaxPrice'];

                    $this->db->select('MAX(buyer_hbp) as maxhbp1');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('invoice_id',$s_inv);
                    $bid_max2=$this->db->get();
                    $getMaxhbp=$bid_max2->result();
                    $chkgetMaxhbp=$decodeJson[$s_inv]['abpone'];

                    
                    $this->db->select('bd.invoice_id,bd.buyer_hbp,bd.buyer_id');
                    $this->db->from(BID_DETAILS.' bd');
                    $this->db->where('bd.invoice_id',$s_inv);
                    $this->db->where('bd.buyer_hbp',@$chkgetMaxhbp);
                    $bid_max_hbp_buyer_id=$this->db->get();
                    $get_bid_max_hbp_buyer_id = $bid_max_hbp_buyer_id->result();
                    $get_bid_max_hbp_buyer_id_final=$decodeJson[$s_inv]['buyerone'];

                   

                    if($get_bid_max_hbp_buyer_id_final !="" && intval($chkgetMaxhbp) > intval(@$getAllvalue[$i]->price))
                    {
                        $get_bid_max_hbp_buyer_id_final = $get_bid_max_hbp_buyer_id_final;
                    }
                    else if($get_bid_max_hbp_buyer_id_final8 != "" && intval($chkgetMaxprice2) > 0 && intval($chkgetMaxprice2) > intval(@$getAllvalue[$i]->price))
                    {
                        $get_bid_max_hbp_buyer_id_final = @$decodeJson[@$getAllvalue[$i]->invoice_id]['bidMaxbuyerId'];
                        
                    }
                    else
                    {
                        $get_bid_max_hbp_buyer_id_final = @$dtl['id'];
                    }


                    $this->db->select('id,company_name');
                    $this->db->from('tt_users');
                    $this->db->where('id',$get_bid_max_hbp_buyer_id_final);
                    $qry=$this->db->get();
                    $getCom=$qry->result();

                    $fields1=array('inv_status'=>"I",'bid'=>@$bidprice,'buyer'=>substr(@$getCom[0]->company_name,0,10),'bidMaxbuyerId'=>@$get_bid_max_hbp_buyer_id_final,'buyerId'=>@$get_bid_max_hbp_buyer_id_final,"buyerfull"=>@$getCom[0]->company_name,'buyer_can_see'=>@$sheet_details_msg[0]->buyer_can_see,'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer,'firebaseKey'=>$firebaseKey);

                  if(intval($chkgetMaxhbp)== "" || intval($chkgetMaxhbp) == 0)
                  {
                    $chkgetMaxhbp=0;
                   // $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);
                  }

                 
            
                    $sql="SELECT MAX(`buyer_hbp`) as sec FROM `tt_bid_details` WHERE `invoice_id` = $s_inv AND `buyer_hbp` < $chkgetMaxhbp";
                    $qry=$this->db->query($sql);
                    $secondHighestparice=$qry->result();
                    $getsecondHighestparice=$decodeJson[$s_inv]['abptwo'];

                    if (@$getAllvalue[$i]->price >= @$getAllvalue[$i]->seller_price && @$getAllvalue[$i]->price != "")
                        {


                            $fields1=array('inv_status'=>"A",'bid'=>@$getAllvalue[$i]->price,'buyer'=>substr(@$getCom[0]->company_name,0,10),'bidMaxbuyerId'=>@$get_bid_max_hbp_buyer_id_final,'buyerId'=>@$get_bid_max_hbp_buyer_id_final,"buyerfull"=>@$getCom[0]->company_name,'buyer_can_see'=>@$sheet_details_msg[0]->buyer_can_see,'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer,'firebaseKey'=>$firebaseKey);
                 
                            //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields8);
                            $data_sold = array(
                                'inv_status' => 'A',
                                'sold_by' => $get_bid_max_hbp_buyer_id_final,
                                'sold_on' => date('Y-m-d')
                            );
                            $decodeJson[$s_inv]['bidMaxPrice']=trim(@$getAllvalue[$i]->price);
                            $decodeJson[$s_inv]['bidMaxbuyerId']=$get_bid_max_hbp_buyer_id_final;

                            $decodeJson[$s_inv]['inv_status'] ='A';
                            $decodeJson[$s_inv]['sold_by'] =$get_bid_max_hbp_buyer_id_final;
                            $decodeJson[$s_inv]['sold_on'] =date('Y-m-d');

                           
                            $decodeJson[@$getAllvalue[$i]->invoice_id]['buyer_bid'][$get_bid_max_hbp_buyer_id_final] =array(
                                            'bid'         => @$getAllvalue[$i]->price,
                                            'bid_time'    => date('Y-m-d H:i:s'),
                                            'buyer_id'    =>$get_bid_max_hbp_buyer_id_final,
                                            
                                        );

                            $mainArryEncode=json_encode($decodeJson);
                            $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                            file_put_contents($file_to_save, $mainArryEncode);

                          /*  $this
                                ->db
                                ->where('invoice_id', @$getAllvalue[$i]->invoice_id);
                            $this
                                ->db
                                ->update(OFFER_INVOICE, $data_sold);*/
                        }

                        else
                        {


                    if(intval(@$getAllvalue[$i]->abp)!="" && $chkgetMaxhbp !=0 && $getsecondHighestparice !=0 && intval($getsecondHighestparice)!=intval($chkgetMaxhbp) && intval(@$getAllvalue[$i]->abp) > intval($chkgetMaxhbpmaxbprice))
                        {
                            
                                $maxHBPupdateBuyerwise=array('buyer_price'=>$getsecondHighestparice+$increaseRate);


                                $fields2=array('bid'=>$getsecondHighestparice+$increaseRate,'buyer'=>substr(@$getCom[0]->company_name,0,10),'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name);

                                  $decodeJson[$s_inv]['bidMaxPrice']=trim($getsecondHighestparice+$increaseRate);
                                $decodeJson[$s_inv]['bidMaxbuyerId']=$get_bid_max_hbp_buyer_id_final;

                                 $decodeJson[$s_inv]['buyer_bid'][$get_bid_max_hbp_buyer_id_final] =array(
                                            'bid'         => $getsecondHighestparice+$increaseRate,
                                            'bid_time'    => date('Y-m-d H:i:s'),
                                            'buyer_id'    =>$get_bid_max_hbp_buyer_id_final,
                                        );

                                        $mainArryEncode=json_encode($decodeJson);
                                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                        file_put_contents($file_to_save, $mainArryEncode);


         
                               // $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields2);
                               /* $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                $this
                                ->db
                                ->where('invoice_id', @$s_inv);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);*/

                                 $data_log = array(

                                'buyer_id' => @$get_bid_max_hbp_buyer_id_final,
                                'seller_id' => @$data['seller_id'],
                                'invoice_id' => @$s_inv,
                                'sheet_id' => @$sheet_id,
                                'buyer_price' => @$getsecondHighestparice+$increaseRate,
                                'seller_price' => @$getAllvalue[$i]->seller_price,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );

                            $this
                            ->db
                            ->insert(BID_LOG, $data_log);


                            $this->db->select('buyer_id,invoice_id,buyer_price');
                            $this->db->from(BID_DETAILS);
                            $this->db->where('buyer_id',$get_bid_max_hbp_buyer_id_final);
                            $this->db->where('invoice_id',$s_inv);
                            $QrySold=$this->db->get();
                            $invSold=$QrySold->result();
                            $invSoldprice=$decodeJson[$s_inv]['buyer_bid'][$get_bid_max_hbp_buyer_id_final]['bid'];

                            if(intval($invSoldprice) >= intval(@$getAllvalue[$i]->seller_price))
                                {

                                  $this->db->where('inv_id', @$s_inv);
                                  $this->db->delete('tt_buyer_division');

                                    $this->db->select('invoice_id,buyer_price');
                                    $this->db->from(BID_DETAILS);
                                    $this->db->where('invoice_id',@$s_inv);
                                    $this->db->where('buyer_price >','0');
                                    $division_check=$this->db->get();
                                    $division_check_buyer=$division_check->result();

                                     if($decodeJson[$s_inv]['bidMaxPrice'] > 0){
                                        $division_check_buyer = 1;
                                    }else{
                                            $division_check_buyer = 0;
                                        }

                                    $this->db->select('inv_id,approve'); 
                                    $this->db->from('tt_buyer_division');
                                    $this->db->where('inv_id',@$s_inv);
                                    $this->db->where('approve','A');
                                    $division_check=$this->db->get();
                                    $division_check_accept=$division_check->result();


                                   $fields4=array('inv_status'=>"A",'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);
         
                                   $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields4);
                               


                                $data_sold = array(
                                    'inv_status' => 'A',
                                    'sold_by' => $get_bid_max_hbp_buyer_id_final,
                                    'sold_on' => date('Y-m-d')
                                );
                                 $decodeJson[$s_inv]['inv_status'] ='A';
                                 $decodeJson[$s_inv]['sold_by'] =$get_bid_max_hbp_buyer_id_final;
                                  $decodeJson[$s_inv]['sold_on'] =date('Y-m-d');

                                   $mainArryEncode=json_encode($decodeJson);
                                    $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                    file_put_contents($file_to_save, $mainArryEncode);

                                }
                                
                          
                        }

                        else
                        {
                            if(intval(@$getAllvalue[$i]->price) != "" && @$get_bid_max_hbp_buyer_id_final != $usr_id)
                            {

                                     if(intval($chkgetMaxhbp) > intval(@$getAllvalue[$i]->price))
                                {
                                    $maxHBPupdateBuyerwise=array('buyer_price'=>@$getAllvalue[$i]->price+$increaseRate);

                                        $myTestprice = @$getAllvalue[$i]->price+$increaseRate;



                                        if(intval($myTestprice) >= intval(@$getAllvalue[$i]->seller_price)){
                                                $invStatus = "A";

                                                $data_sold = array(
                                                    'inv_status' =>$invStatus,
                                                    'sold_by' => $get_bid_max_hbp_buyer_id_final,
                                                    'sold_on' => date('Y-m-d')
                                                );
                                              /*  $this
                                                    ->db
                                                    ->where('invoice_id', @$getAllvalue[$i]->invoice_id);
                                                $this
                                                    ->db
                                                    ->update(OFFER_INVOICE, $data_sold);*/

                                                $decodeJson[$s_inv]['inv_status'] ='A';
                                                $decodeJson[$s_inv]['sold_by'] =$get_bid_max_hbp_buyer_id_final;
                                                $decodeJson[$s_inv]['sold_on'] =date('Y-m-d');
                                                $mainArryEncode=json_encode($decodeJson);
                                                $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                                file_put_contents($file_to_save, $mainArryEncode);
                                            }else{
                                                $invStatus = "I";
                                            }
                                             $decodeJson[$s_inv]['bidMaxPrice']=trim($myTestprice);
                                            $decodeJson[$s_inv]['bidMaxbuyerId']=$get_bid_max_hbp_buyer_id_final;
                                        $decodeJson[$s_inv]['buyer_bid'][$get_bid_max_hbp_buyer_id_final] =array(
                                            'bid'         => $myTestprice,
                                            'bid_time'    => date('Y-m-d H:i:s'),
                                            'buyer_id'    =>$get_bid_max_hbp_buyer_id_final,
                                        );

                                       

                                        $mainArryEncode=json_encode($decodeJson);
                                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                        file_put_contents($file_to_save, $mainArryEncode);

                                   // $fields5=array('bid'=>@$getAllvalue[$i]->price+$increaseRate,'buyer'=>substr(@$getCom[0]->company_name,0,10),'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name);

                                    $fields1=array('inv_status'=>$invStatus,'bid'=>@$getAllvalue[$i]->price+$increaseRate,'buyer'=>substr(@$getCom[0]->company_name,0,10),'bidMaxbuyerId'=>@$get_bid_max_hbp_buyer_id_final,'buyerId'=>@$buyer_id,"buyerfull"=>@$getCom[0]->company_name,'buyer_can_see'=>@$sheet_details_msg[0]->buyer_can_see,'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer,'firebaseKey'=>$firebaseKey);
         
                                    //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields5);
                                 /*   $this
                                    ->db
                                    ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                    $this
                                    ->db
                                    ->where('invoice_id', $s_inv);
                                    $this
                                    ->db
                                    ->update(BID_DETAILS, $maxHBPupdateBuyerwise);*/

                                $data_log = array(

                                'buyer_id' => @$get_bid_max_hbp_buyer_id_final,
                                'seller_id' => @$data['seller_id'],
                                'invoice_id' => @$s_inv,
                                'sheet_id' => @$sheet_id,
                                'buyer_price' => $getAllvalue[$i]->price+$increaseRate,
                                'seller_price' => @$getAllvalue[$i]->seller_price,

                                'bid_on' => date('Y-m-d H:i:s'),
                               );

                                $this
                                ->db
                                ->insert(BID_LOG, $data_log);
          

                                }
                                elseif(intval($chkgetMaxhbp) == intval(@$getAllvalue[$i]->price))
                                {

                                       $maxHBPupdateBuyerwise=array('buyer_price'=>@$getAllvalue[$i]->price);

                                        $myTestprice = @$getAllvalue[$i]->price;

                                        if(intval($myTestprice) >= intval(@$getAllvalue[$i]->seller_price)){
                                                $invStatus = "A";

                                                $data_sold = array(
                                                    'inv_status' =>$invStatus,
                                                    'sold_by' => $get_bid_max_hbp_buyer_id_final,
                                                    'sold_on' => date('Y-m-d')
                                                );
                                               /* $this
                                                    ->db
                                                    ->where('invoice_id', @$getAllvalue[$i]->invoice_id);
                                                $this
                                                    ->db
                                                    ->update(OFFER_INVOICE, $data_sold);*/
                                                $decodeJson[$s_inv]['inv_status'] ='A';
                                                $decodeJson[$s_inv]['sold_by'] =$get_bid_max_hbp_buyer_id_final;
                                                $decodeJson[$s_inv]['sold_on'] =date('Y-m-d');
                                                $mainArryEncode=json_encode($decodeJson);
                                                $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                                file_put_contents($file_to_save, $mainArryEncode);
                                            }else{
                                                $invStatus = "I";
                                            }
                                            $decodeJson[$s_inv]['bidMaxPrice']=trim($myTestprice);
                                            $decodeJson[$s_inv]['bidMaxbuyerId']=$get_bid_max_hbp_buyer_id_final;
                                        $decodeJson[$s_inv]['buyer_bid'][$get_bid_max_hbp_buyer_id_final] =array(
                                            'bid'         => $myTestprice,
                                            'bid_time'    => date('Y-m-d H:i:s'),
                                            'buyer_id'    =>$get_bid_max_hbp_buyer_id_final,
                                        );

                                       

                                        $mainArryEncode=json_encode($decodeJson);
                                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                        file_put_contents($file_to_save, $mainArryEncode);

                                       // $fields6=array('bid'=>@$getAllvalue[$i]->price,'buyer'=>substr(@$getCom[0]->company_name,0,10),'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name);


                                          $fields1=array('inv_status'=>$invStatus,'bid'=>@$getAllvalue[$i]->price,'buyer'=>substr(@$getCom[0]->company_name,0,10),'bidMaxbuyerId'=>@$get_bid_max_hbp_buyer_id_final,'buyerId'=>@$buyer_id,"buyerfull"=>@$getCom[0]->company_name,'buyer_can_see'=>@$sheet_details_msg[0]->buyer_can_see,'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer,'firebaseKey'=>$firebaseKey);
         
                                       //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields6);
                                      /*  $this
                                        ->db
                                        ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                        $this
                                        ->db
                                        ->where('invoice_id', $s_inv);
                                        $this
                                        ->db
                                        ->update(BID_DETAILS, $maxHBPupdateBuyerwise);
*/
                                $data_log = array(

                                'buyer_id' => @$get_bid_max_hbp_buyer_id_final,
                                'seller_id' => @$data['seller_id'],
                                'invoice_id' => @$s_inv,
                                'sheet_id' => @$sheet_id,
                                'buyer_price' => @$getAllvalue[$i]->price,
                                'seller_price' => @$getAllvalue[$i]->seller_price,

                                'bid_on' => date('Y-m-d H:i:s'),
                               );

                                $this
                                ->db
                                ->insert(BID_LOG, $data_log);
                                }

                                else
                                {
                                     $fields1=array('inv_status'=>"I",'bid'=>@$bidprice,'buyer'=>substr(@$getCom[0]->company_name,0,10),'bidMaxbuyerId'=>@$get_bid_max_hbp_buyer_id_final,'buyerId'=>@$get_bid_max_hbp_buyer_id_final,"buyerfull"=>@$getCom[0]->company_name,'buyer_can_see'=>@$sheet_details_msg[0]->buyer_can_see,'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer,'firebaseKey'=>$firebaseKey);

                                       $decodeJson[$s_inv]['bidMaxPrice']=trim($bidprice);
                                            $decodeJson[$s_inv]['bidMaxbuyerId']=$get_bid_max_hbp_buyer_id_final;
                                       $decodeJson[$s_inv]['buyer_bid'][$get_bid_max_hbp_buyer_id_final] =array(
                                            'bid'         => $bidprice,
                                            'bid_time'    => date('Y-m-d H:i:s'),
                                            'buyer_id'    =>$get_bid_max_hbp_buyer_id_final,
                                        );

                                       

                                        $mainArryEncode=json_encode($decodeJson);
                                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                        file_put_contents($file_to_save, $mainArryEncode);
                                    //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);
                                }

                            }
                            else
                            {

                                if(intval($chkgetMaxhbp) !="" && intval($chkgetMaxhbp) > 0 && intval($chkgetMaxhbp) > intval($chkgetMaxprice2) && intval($chkgetMaxprice2) > 0 && intval($chkgetMaxprice2) != "" && intval($chkgetMaxhbp) > intval(@$getAllvalue[$i]->price))
                                {

                                $maxHBPupdateBuyerwise1=array('buyer_price'=>@$chkgetMaxprice2+$increaseRate);

                                $myTestprice = @$chkgetMaxprice2+$increaseRate;

                                if(intval($myTestprice) >= intval(@$getAllvalue[$i]->seller_price)){
                                        $invStatus = "A";

                                        $data_sold = array(
                                            'inv_status' =>$invStatus,
                                            'sold_by' => $get_bid_max_hbp_buyer_id_final,
                                            'sold_on' => date('Y-m-d')
                                        );
                                      /*  $this
                                            ->db
                                            ->where('invoice_id', @$getAllvalue[$i]->invoice_id);
                                        $this
                                            ->db
                                            ->update(OFFER_INVOICE, $data_sold);*/

                                             $decodeJson[$s_inv]['inv_status'] ='A';
                                                $decodeJson[$s_inv]['sold_by'] =$get_bid_max_hbp_buyer_id_final;
                                                $decodeJson[$s_inv]['sold_on'] =date('Y-m-d');
                                                $mainArryEncode=json_encode($decodeJson);
                                                $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                                file_put_contents($file_to_save, $mainArryEncode);
                                    }else{
                                        $invStatus = "I";
                                    }
                                    $decodeJson[$s_inv]['bidMaxPrice']=trim($myTestprice);
                                    $decodeJson[$s_inv]['bidMaxbuyerId']=$get_bid_max_hbp_buyer_id_final;
                                     $decodeJson[$s_inv]['buyer_bid'][$get_bid_max_hbp_buyer_id_final] =array(
                                            'bid'         => $myTestprice,
                                            'bid_time'    => date('Y-m-d H:i:s'),
                                            'buyer_id'    =>$get_bid_max_hbp_buyer_id_final,
                                        );

                                       

                                        $mainArryEncode=json_encode($decodeJson);
                                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                        file_put_contents($file_to_save, $mainArryEncode);

                              //  $fields9=array('bid'=>$chkgetMaxprice2+$increaseRate,'buyer'=>substr(@$getCom[0]->company_name,0,10),'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name);

                                 $fields1=array('inv_status'=>$invStatus,'bid'=>$chkgetMaxprice2+$increaseRate,'buyer'=>substr(@$getCom[0]->company_name,0,10),'bidMaxbuyerId'=>@$get_bid_max_hbp_buyer_id_final,'buyerId'=>@$buyer_id,"buyerfull"=>@$getCom[0]->company_name,'buyer_can_see'=>@$sheet_details_msg[0]->buyer_can_see,'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer,'firebaseKey'=>$firebaseKey);
         
                                //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields9);
                            /*    $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                $this
                                ->db
                                ->where('invoice_id', $s_inv);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise1);*/

                                $data_log = array(

                                'buyer_id' => @$get_bid_max_hbp_buyer_id_final,
                                'seller_id' => @$data['seller_id'],
                                'invoice_id' => @$s_inv,
                                'sheet_id' => @$sheet_id,
                                'buyer_price' => @$chkgetMaxprice2+@$increaseRate,
                                'seller_price' => @$getAllvalue[$i]->seller_price,

                                'bid_on' => date('Y-m-d H:i:s'),
                               );

                                $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                                }

                               elseif(intval($chkgetMaxhbp) !="" && intval($chkgetMaxhbp) > 0 && intval($chkgetMaxhbp) > intval($chkgetMaxprice2) && intval($chkgetMaxprice2) > 0 && intval($chkgetMaxprice2) != "" && intval(@$getAllvalue[$i]->price) > intval($chkgetMaxhbp))
                               {

                                 $maxHBPupdateBuyerwise1=array('buyer_price'=>@$getAllvalue[$i]->price);

                                //$fields25=array('bid'=>@$getAllvalue[$i]->price,'buyer'=>substr(@$getCom[0]->company_name,0,10),'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name);

                                 $fields1=array('inv_status'=>"I",'bid'=>@$getAllvalue[$i]->price,'buyer'=>substr(@$getCom[0]->company_name,0,10),'bidMaxbuyerId'=>@$get_bid_max_hbp_buyer_id_final,'buyerId'=>@$buyer_id,"buyerfull"=>@$getCom[0]->company_name,'buyer_can_see'=>@$sheet_details_msg[0]->buyer_can_see,'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer,'firebaseKey'=>$firebaseKey);
         
                               // $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields25);
                              /*  $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                $this
                                ->db
                                ->where('invoice_id', $s_inv);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise1);*/
                                  $decodeJson[$s_inv]['bidMaxPrice']=trim(@$getAllvalue[$i]->price);
                                    $decodeJson[$s_inv]['bidMaxbuyerId']=$get_bid_max_hbp_buyer_id_final;
                                 $decodeJson[$s_inv]['buyer_bid'][$get_bid_max_hbp_buyer_id_final] =array(
                                            'bid'         => @$getAllvalue[$i]->price,
                                            'bid_time'    => date('Y-m-d H:i:s'),
                                            'buyer_id'    =>$get_bid_max_hbp_buyer_id_final,
                                        );

                                       

                                        $mainArryEncode=json_encode($decodeJson);
                                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                        file_put_contents($file_to_save, $mainArryEncode);

                                $data_log = array(

                                'buyer_id' => @$get_bid_max_hbp_buyer_id_final,
                                'seller_id' => @$data['seller_id'],
                                'invoice_id' => @$s_inv,
                                'sheet_id' => @$sheet_id,
                                'buyer_price' => @$getAllvalue[$i]->price,
                                'seller_price' => @$getAllvalue[$i]->seller_price,

                                'bid_on' => date('Y-m-d H:i:s'),
                               );

                                $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                               }
                               else
                               {
                                 $fields1=array('inv_status'=>"I",'bid'=>@$bidprice,'buyer'=>substr(@$getCom[0]->company_name,0,10),'bidMaxbuyerId'=>@$get_bid_max_hbp_buyer_id_final,'buyerId'=>@$get_bid_max_hbp_buyer_id_final,"buyerfull"=>@$getCom[0]->company_name,'buyer_can_see'=>@$sheet_details_msg[0]->buyer_can_see,'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer,'firebaseKey'=>$firebaseKey);

                                  $decodeJson[$s_inv]['bidMaxPrice']=trim(@$bidprice);
                                    $decodeJson[$s_inv]['bidMaxbuyerId']=$get_bid_max_hbp_buyer_id_final;

                                  $decodeJson[$s_inv]['buyer_bid'][$get_bid_max_hbp_buyer_id_final] =array(
                                            'bid'         => @$bidprice,
                                            'bid_time'    => date('Y-m-d H:i:s'),
                                            'buyer_id'    =>$get_bid_max_hbp_buyer_id_final,
                                        );

                                       

                                        $mainArryEncode=json_encode($decodeJson);
                                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                        file_put_contents($file_to_save, $mainArryEncode);
                                 //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);
                               }

                            }
                        
                        }


                    }


             $this->db->where('buyer_id',$usr_id);
             $this->db->where('sheet_id',$sheet_id);
             $this->db->delete('tt_abp_receive');
            
            }


            
            $res=1;
             

               

            }


             echo json_encode(array('res'=>$res,'mainarray'=>@$fields1));
          
            

            //redirect(BASE_URL . 'buyer-active-offer-sheet');

        }

         
    }

    function accept_division_buyer()
    {
         $firebase = $this->firebase->init();
        $database = $firebase->getDatabase();
        $division_id = $this
            ->input
            ->post('division_id');

        $firebaseKey = $this
            ->input
            ->post('firebaseKey');


        $sheet_id = $this
            ->input
            ->post('sheet_id');
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $request_to = @$dtl['id'];
        $data = array(
            'approve' => 'A',
            'div_time'=>date('Y-m-d H:i:s')
        );
        $this
            ->db
            ->where('division_id', $division_id);
        // $this->db->where('buyer_request_to',$request_to);
        $this
            ->db
            ->update('tt_buyer_division', $data);

        $this->db->select('inv_id,approve'); 
        $this->db->from('tt_buyer_division');
        $this->db->where('division_id',$division_id);
        $this->db->where('approve','A');
        $division_check=$this->db->get();
        $division_check_accept=$division_check->result();
         $fields8=array('division_check_accept'=>count($division_check_accept));
         
        $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields8);
        $res = 1;
        echo json_encode($res);

    }

    function reject_division_buyer()
    {
        $division_id = $this
            ->input
            ->post('division_id');
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $request_to = @$dtl['id'];
        $data = array(
            'approve' => 'R'
        );
        $this
            ->db
            ->where('division_id', $division_id);
        //$this->db->where('buyer_request_to',$request_to);
        $this
            ->db
            ->update('tt_buyer_division', $data);
        $res = 1;
        echo json_encode($res);

    }

    function check_notification_division()
    {
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        @$chk_division = $this
            ->Common
            ->find(

                ['table' => 'tt_buyer_division' . ' tbd', 'select' => "*", 


                'join' => [

                    [OFFER_INVOICE, 'oi', 'INNER', "tbd.inv_id = oi.invoice_id"], [OFFER_SHEET, 'os', 'INNER', "os.sheet_id = tbd.sheet_id"], 

                          [USERS, 'us', 'INNER', "us.id = tbd.buyer_request_from"],

        ], 

        'where' => "tbd.buyer_request_to = {$dtl['id']} AND tbd.approve = 'P'",

        'query'=>'first'

        ]);

        if (!empty(@$chk_division))
        {

            echo json_encode(@$chk_division);

        }
        else
        {
            $res = array();
            echo json_encode(@$res);
        }

    }

    function send_request_to_buyer()
    {
         $firebase = $this->firebase->init();
        $database = $firebase->getDatabase();
        $inv_id = $this
            ->input
            ->post('inv_id');
        $sheet_id = $this
            ->input
            ->post('sheet_id');
        $request_to = $this
            ->input
            ->post('request_to');
        $price = $this
            ->input
            ->post('price');
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $request_from = @$dtl['id'];

        $firebaseKey = $this
            ->input
            ->post('firebaseKey');

        @$chk_division = $this
            ->Common
            ->find(['table' => 'tt_buyer_division', 'select' => "*", 'where' => "buyer_request_from = {$dtl['id']} AND inv_id = {$inv_id} AND approve ='P'", 'query' => 'first']);

        $chk_division_approve = $this
            ->Common
            ->find(['table' => 'tt_buyer_division', 'select' => "*", 'where' => "inv_id = {$inv_id} AND approve ='A'", 'query' => 'first']);

        if ($request_from == $request_to)
        {
            $res = 3;
        }
        elseif (!empty(@$chk_division_approve))
        {
            $res = 4;
        }

        elseif (empty(@$chk_division))
        {

            $data_send = array(

                'inv_id' => $inv_id,
                'buyer_request_from' => $request_from,
                'buyer_request_to' => $request_to,
                'sheet_id' => $sheet_id,
                'price' => $price

            );

            $this
                ->db
                ->insert('tt_buyer_division', $data_send);
            $res = 1;

        }
        elseif (@$price > @$chk_division['price'])
        {

            $data_send = array(

                'inv_id' => $inv_id,
                'buyer_request_from' => $request_from,
                'buyer_request_to' => $request_to,
                'sheet_id' => $sheet_id,
                'price' => $price

            );

            $this
                ->db
                ->insert('tt_buyer_division', $data_send);
            $res = 1;

        }
        else
        {
            $res = 2;

        }

        echo json_encode($res);

    }

    function place_bid_action()
    {
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];
        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $price_idea = $this
            ->input
            ->post('price_idea');
        $my_price = $this
            ->input
            ->post('my_price');
        $max_price = $this
            ->input
            ->post('max_price');
        $sheet_id = $this
            ->input
            ->post('sheet_id');
        $final_pi_id = $this
            ->input
            ->post('final_pi_id');

        $this->db->select('*');
        $this->db->from('tt_offer_invoice');
        $this->db->where('invoice_id',$invoice_id);
        $getvl=$this->db->get();
        $getprcid=$getvl->result();
        $price_idea=@$getprcid[0]->price_idea;

        $ChkSheet = $this
            ->Common
            ->findBy(OFFER_SHEET, 'sheet_id', $sheet_id);
        $now = date('d-m-Y H:i:s');
        $currentDateTime = strtotime(@$now);
        @$expiry_date = date("d-m-Y H:i:s", strtotime($ChkSheet['expiry_date']));
        @$newEXPDate = @strtotime(@$expiry_date);

        $this->db->select('MAX(buyer_price) as maxprice');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$invoice_id);
        $bid_max=$this->db->get();
        $getMaxprice=$bid_max->result();
        $chkgetMaxprice=@$getMaxprice[0]->maxprice;

        $this->db->select('MAX(buyer_hbp) as maxhbp');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$invoice_id);
        $bid_max=$this->db->get();
        $getMaxhbp=$bid_max->result();
        $chkgetMaxhbp=@$getMaxhbp[0]->maxhbp;

        $this->db->select('bd.invoice_id,bd.buyer_hbp,bd.buyer_id');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->where('bd.invoice_id',$invoice_id);
        $this->db->where('bd.buyer_hbp',$chkgetMaxhbp);
        $bid_max_hbp_buyer_id=$this->db->get();
        $get_bid_max_hbp_buyer_id = $bid_max_hbp_buyer_id->result();
        $get_bid_max_hbp_buyer_id_final=@$get_bid_max_hbp_buyer_id[0]->buyer_id;


        $this->db->select('buyer_id,invoice_id,buyer_hbp');
        $this->db->from(BID_DETAILS);
        $this->db->where('buyer_id',$buyer_id);
        $this->db->where('invoice_id',$invoice_id);
        $Qry=$this->db->get();
        $chkMyhbp=$Qry->result();

        if(intval($my_price) > intval(@$chkMyhbp[0]->buyer_hbp))
        {

            $data_HBP = array(
            'buyer_hbp' => 0,
           
        );
        $this
            ->db
            ->where('invoice_id', $invoice_id);
        $this
        ->db
        ->where('buyer_id', $buyer_id);
        $this
            ->db
            ->update(BID_DETAILS, $data_HBP);

        }

      
        $increaseRate=$ChkSheet['bidding_gap'];
        if (@$newEXPDate > $currentDateTime)
        {
            $result = 0;
        }

        if ($my_price != "")
        {
            if (intval($max_price) >= intval($my_price) && intval($chkgetMaxprice) >= intval($my_price))
            {
                $result = 0;

            }
            else
            {
               if(intval($my_price) > intval($max_price) && intval($my_price) > intval($chkgetMaxprice)){
                if (intval($my_price) >= intval($price_idea) && $final_pi_id == 'N')
                {

                    $data_update = array(
                        'buyer_price' => $my_price,
                        'message' => 'N',
                        'message_30' => 'N',
                        'bid_time' => date('Y-m-d H:i:s')
                    );


                    $data_update1 = array(
                        'buyer_price' => $my_price,
                        'message' => 'N',
                        'message_30' => 'N',
                        'bid_time' => date('Y-m-d H:i:s'),
                        'buyer_id'=>$buyer_id,
                        'sheet_id'=>$sheet_id,
                        'invoice_id'=>$invoice_id,
                        'seller_id'=>@$ChkSheet['created_by'],
                        'seller_price'=>$price_idea,
                    );

                    $this->db->select('*');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('buyer_id',$buyer_id);
                    $this->db->where('invoice_id',$invoice_id);
                    $Qry=$this->db->get();
                    $chkMyinv=$Qry->result();


                    if($chkgetMaxhbp ==0)
                    {

                        if(empty($chkMyinv))
                        {
                            $this->db->insert(BID_DETAILS,$data_update1);
                        }
                        else
                        {
                            $this
                            ->db
                            ->where('buyer_id', $buyer_id);
                            $this
                            ->db
                            ->where('invoice_id', $invoice_id);
                            $this
                            ->db
                            ->update(BID_DETAILS, $data_update);

                        }

                         $data_log = array(

                            'buyer_id' => $buyer_id,
                            'seller_id' => @$ChkSheet['created_by'],
                            'invoice_id' => $invoice_id,
                            'sheet_id' => $sheet_id,
                            'buyer_price' => $my_price,
                            'seller_price' => $price_idea,

                            'bid_on' => date('Y-m-d H:i:s') ,
                        );
                        $this
                            ->db
                            ->insert(BID_LOG, $data_log);

                    }



                    if($chkgetMaxhbp !=0)
                    {
                        if(intval($chkgetMaxhbp) > intval($my_price) && @$get_bid_max_hbp_buyer_id_final != $buyer_id)
                        {
                               $updatedBid=$my_price+$increaseRate;
                                if(intval($updatedBid) > intval($chkgetMaxhbp))
                                {
                                    $updatedBid=$chkgetMaxhbp;
                                }
                                else
                                {
                                    $updatedBid=$my_price+$increaseRate;

                                }
                                $maxHBPupdateBuyerwise=array('buyer_price'=>$updatedBid);
                                $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);


                                 $data_log = array(

                                'buyer_id' => $get_bid_max_hbp_buyer_id_final,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $updatedBid,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                        }
                        elseif(intval($chkgetMaxhbp) == intval($my_price))
                        {

                            $this->db->select('bd.invoice_id,bd.buyer_hbp,bd.buyer_id');
                            $this->db->from(BID_DETAILS.' bd');
                            $this->db->where('bd.invoice_id',$invoice_id);
                            $this->db->where('bd.buyer_hbp',$chkgetMaxhbp);
                            $bid_max_hbp_buyer_id1=$this->db->get();
                            $get_bid_max_hbp_buyer_id1 = $bid_max_hbp_buyer_id1->result();
                            $get_bid_max_hbp_buyer_id_final1=@$get_bid_max_hbp_buyer_id1[0]->buyer_id;

                               $maxHBPupdateBuyerwise=array('buyer_price'=>$my_price);
                                $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final1);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);

                                 $data_log = array(

                                'buyer_id' => $get_bid_max_hbp_buyer_id_final1,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);
                        }

                        else
                        {

                            if(empty($chkMyinv))
                            {
                                $this->db->insert(BID_DETAILS,$data_update1);
                            }
                            else
                            {
                                $this
                                ->db
                                ->where('buyer_id', $buyer_id);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $data_update);

                            }

                         $data_log = array(

                            'buyer_id' => $buyer_id,
                            'seller_id' => @$ChkSheet['created_by'],
                            'invoice_id' => $invoice_id,
                            'sheet_id' => $sheet_id,
                            'buyer_price' => $my_price,
                            'seller_price' => $max_price,

                            'bid_on' => date('Y-m-d H:i:s') ,
                        );
                        $this
                            ->db
                            ->insert(BID_LOG, $data_log);
                        }
                    }

                 

                  
                    $this
                        ->db
                        ->where('inv_id', @$invoice_id);
                    //$this->db->where('approve','P');
                    $this
                        ->db
                        ->delete('tt_buyer_division');

                    $this->db->select('MAX(buyer_price) as maxprice1');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('invoice_id',$invoice_id);
                    $bid_max=$this->db->get();
                    $getMaxprice=$bid_max->result();
                    $chkgetMaxprice=@$getMaxprice[0]->maxprice1;

                  $this->db->select('*');
                  $this->db->from(BID_DETAILS.' bd');
                  $this->db->where('bd.invoice_id',$invoice_id);
                  $this->db->where('bd.buyer_price',$chkgetMaxprice);
                  $bid_max_buyer_id=$this->db->get();
                  $get_bid_max_buyer_id = $bid_max_buyer_id->result();

                

                  if(intval($chkgetMaxhbp) == intval($my_price))
                  {
                    $getMaxbuyerId=@$get_bid_max_hbp_buyer_id_final;
                  }
                  else
                  {
                    $getMaxbuyerId=@$get_bid_max_buyer_id[0]->buyer_id;
                  }

                    $data_sold = array(
                        'inv_status' => 'A',
                        'sold_by' => $getMaxbuyerId,
                        'sold_on' => date('Y-m-d')
                    );
                    $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(OFFER_INVOICE, $data_sold);

             
                    //send_sms($to,$body);
                    $result = 1;

                }
                else
                {

                    $data_update = array(
                        'buyer_price' => $my_price,
                        'message' => 'N',
                        'message_30' => 'N',
                        'bid_time' => date('Y-m-d H:i:s')
                    );
                    $data_update1 = array(
                        'buyer_price' => $my_price,
                        'message' => 'N',
                        'message_30' => 'N',
                        'bid_time' => date('Y-m-d H:i:s'),
                        'buyer_id'=>$buyer_id,
                        'sheet_id'=>$sheet_id,
                        'invoice_id'=>$invoice_id,
                        'seller_id'=>@$ChkSheet['created_by'],
                        'seller_price'=>$price_idea,
                    );

                    $this->db->select('*');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('buyer_id',$buyer_id);
                    $this->db->where('invoice_id',$invoice_id);
                    $Qry=$this->db->get();
                    $chkMyinv=$Qry->result();

                    if($chkgetMaxhbp ==0)
                    {

                             if(empty($chkMyinv))
                            {
                                $this->db->insert(BID_DETAILS,$data_update1);
                            }
                            else
                            {
                                 $this
                                ->db
                                ->where('buyer_id', $buyer_id);
                              $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                              $this
                                ->db
                                ->update(BID_DETAILS, $data_update);

                            }


                            $data_log = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $max_price,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                    }


                    if($chkgetMaxhbp !=0)
                    {
                        if(intval($chkgetMaxhbp) > intval($my_price) && @$get_bid_max_hbp_buyer_id_final != $buyer_id)
                        {
                               $updatedBid=$my_price+$increaseRate;
                                if(intval($updatedBid) > intval($chkgetMaxhbp))
                                {
                                    $updatedBid=$chkgetMaxhbp;
                                }
                                else
                                {
                                    $updatedBid=$my_price+$increaseRate;

                                }
                                $maxHBPupdateBuyerwise=array('buyer_price'=>$updatedBid);
                                $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);

                                 $data_log = array(

                                'buyer_id' => $get_bid_max_hbp_buyer_id_final,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $updatedBid,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                            if(intval($updatedBid) >= intval($price_idea))
                            {
                                $data_sold = array(
                                    'inv_status' => 'A',
                                    'sold_by' => $get_bid_max_hbp_buyer_id_final,
                                    'sold_on' => date('Y-m-d')
                                );
                                $this
                                    ->db
                                    ->where('invoice_id', $invoice_id);
                                $this
                                    ->db
                                    ->update(OFFER_INVOICE, $data_sold);

                            }

                        }
                        elseif(intval($chkgetMaxhbp) == intval($my_price))
                        {
                            $this->db->select('bd.invoice_id,bd.buyer_hbp,bd.buyer_id');
                            $this->db->from(BID_DETAILS.' bd');
                            $this->db->where('bd.invoice_id',$invoice_id);
                            $this->db->where('bd.buyer_hbp',$chkgetMaxhbp);
                            $bid_max_hbp_buyer_id1=$this->db->get();
                            $get_bid_max_hbp_buyer_id1 = $bid_max_hbp_buyer_id1->result();
                            $get_bid_max_hbp_buyer_id_final1=@$get_bid_max_hbp_buyer_id1[0]->buyer_id;

                               $maxHBPupdateBuyerwise=array('buyer_price'=>$my_price);
                                $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final1);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);

                                 $data_log = array(

                                'buyer_id' => $get_bid_max_hbp_buyer_id_final1,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                            if(intval($my_price) >= intval($price_idea))
                            {
                                $data_sold = array(
                                    'inv_status' => 'A',
                                    'sold_by' => $get_bid_max_hbp_buyer_id_final1,
                                    'sold_on' => date('Y-m-d')
                                );
                                $this
                                    ->db
                                    ->where('invoice_id', $invoice_id);
                                $this
                                    ->db
                                    ->update(OFFER_INVOICE, $data_sold);

                            }

                        }

                        else
                        {
                            if(empty($chkMyinv))
                            {
                                $this->db->insert(BID_DETAILS,$data_update1);
                            }
                            else
                            {
                                 $this
                                ->db
                                ->where('buyer_id', $buyer_id);
                              $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                              $this
                                ->db
                                ->update(BID_DETAILS, $data_update);

                            }


                            $data_log = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);
                        }
                    }
        
                    $this
                        ->db
                        ->where('inv_id', @$invoice_id);
                    //$this->db->where('approve','P');
                    $this
                        ->db
                        ->delete('tt_buyer_division');
                    //send_sms($to,$body);

                    $result = 1;

                }
            }
            }
        }
        else
        {
            $result = 0;

        }
        echo json_encode($result);
    }

    function pushHBPcron(){
         $date = date('Y-m-d H:i:s');
        $this->db->select('os.sheet_id,os.expiry_date,os.expire,toe.sheet_id,toe.full_entry');
        $this->db->from(OFFER_SHEET.' os');
        $this->db->join('tt_sheet_entry toe','toe.sheet_id=os.sheet_id');
        $this->db->where('os.expiry_date <',$date);
        $this->db->where('toe.full_entry','N');
        $chk=$this->db->get();
        $chkDuplicate =  $chk->result();

        $this->db->select('os.sheet_id,os.expiry_date,os.expire,toe.sheet_id,toe.full_entry,os.expire');
        $this->db->from(OFFER_SHEET.' os');
        $this->db->join('tt_sheet_entry toe','toe.sheet_id=os.sheet_id');
        $this->db->where('os.expiry_date >',$date);
        $this->db->where('toe.full_entry','N');
        $this->db->where('os.expire','Y');
        $chk1=$this->db->get();
        $chkDuplicateExpire =  $chk1->result();

      

        if(!empty($chkDuplicate)){
            foreach($chkDuplicate as $row2){
                $jsonFile = file_get_contents('public/uploads/'.$row2->sheet_id.'.json');
                $decodeJson= json_decode($jsonFile,true);

           

            foreach($decodeJson as $row){
               $countHbp = count($row['buyer_hbp']);
               $countBid = count($row['buyer_bid']);

               if($countHbp > $countBid){
                $counter  = 0;
                foreach($row['buyer_hbp'] as $key=>$row1){
             
                    $dataInsert = array(
                                    'invoice_id'=>$row['invoice_id'],
                                    'firebase_key'=>$row['firebase_key'],
                                    'buyer_id'=>$row1['buyer_id'],
                                    'hbp_key'=>$row1['hbp_key'],
                                    'seller_id'=>$row['seller_id'],
                                    'buyer_hbp'=>$row1['abp'],
                                    'sheet_id'=>$row['sheet_id'],
                                    'seller_price'=>$row['price_idea'],
                                    'hbp_time'=>$row1['hbp_time'],
                                    'buyer_price'=>$row['buyer_bid'][$row1['buyer_id']]['bid'],
                                    'bid_time'=>$row['buyer_bid'][$row1['buyer_id']]['bid_time']
                                );

                        $this->db->select('invoice_id,buyer_id');
                        $this->db->from(BID_DETAILS);
                        $this->db->where('invoice_id',$row['invoice_id']);
                        $this->db->where('buyer_id',$row1['buyer_id']);
                        $qry = $this->db->get();
                        $chkDuplicateBidValue = $qry->result();

                       

                        if(empty($chkDuplicateBidValue)){
                          $this->db->insert(BID_DETAILS,$dataInsert);
                        }
                
                    if($row['inv_status']=='A' && $row['sold_by'] != ""){
                        $bidActiveUpdate = array(

                                        'inv_status'=>$row['inv_status'],
                                        'sold_by'=>$row['sold_by'],
                                        'sold_on'=>$row['sold_on'],
                                    );

                      
                        $this->db->where('invoice_id',$row['invoice_id']);
                        $this->db->update(OFFER_INVOICE,$bidActiveUpdate);
                    }

                    $this->db->select('sheet_id,status');
                    $this->db->from('tt_switch_on');
                    $this->db->where('sheet_id',$row['sheet_id']);
                    $this->db->where('buyer_id',$row1['buyer_id']);
                    $getval=$this->db->get();
                    $stat= $getval->result();
                    $hbpType=@$stat[0]->status;

                    if(($hbpType!='semi_aumatic' || $hbpType=="")){

                         if($row['inv_status']!='A' && $row1['abp'] >= $row['price_idea']){
                            $dataBiddetails = array(
                                'buyer_price' => $row['price_idea'],
                                'bid_time' => date('Y-m-d H:i:s'),
                                'push_hbp'=>'Y'
                            );

                            $this->db->where('invoice_id',$row['invoice_id']);
                            $this->db->where('buyer_id',$row1['buyer_id']);
                            $this->db->update(BID_DETAILS,$dataBiddetails);

                            $this->db->where('inv_id', $row['invoice_id']);
                            $this->db->delete('tt_buyer_division');
                     
                            $data_sold = array(
                            'inv_status' => 'A',
                            'hbp_key' => $row1['hbp_key'],
                            'sold_by' => $row1['buyer_id'],
                            'sold_on' => date('Y-m-d')
                             );
                            $this->db->where('invoice_id', $row['invoice_id']);
                            $this->db->update(OFFER_INVOICE, $data_sold);

                             
                        }
                    }
                } 
               }else{

                    foreach($row['buyer_bid'] as $key=>$row1){
                       
                         $bidInsert = array(

                                        'buyer_price'=>$row1['bid'],
                                        'bid_time'=>$row1['bid_time'],
                                        'invoice_id'=>$row['invoice_id'],
                                        'firebase_key'=>$row['firebase_key'],
                                        'buyer_id'=>$row1['buyer_id'],
                                        'seller_id'=>$row['seller_id'],
                                        'seller_price'=>$row['price_idea'],
                                        'sheet_id'=>$row['sheet_id'],
                                        'hbp_key'=>$row['buyer_hbp'][$row1['buyer_id']]['hbp_key'],
                                       'buyer_hbp'=>$row['buyer_hbp'][$row1['buyer_id']]['abp'],
                                       'hbp_time'=>$row['buyer_hbp'][$row1['buyer_id']]['hbp_time']
                                    );

                        $this->db->select('invoice_id,buyer_id');
                        $this->db->from(BID_DETAILS);
                        $this->db->where('invoice_id',$row['invoice_id']);
                        $this->db->where('buyer_id',$row1['buyer_id']);
                        $qry = $this->db->get();
                        $chkDuplicateBidValue = $qry->result();
                      

                        if(empty($chkDuplicateBidValue)){
                          $this->db->insert(BID_DETAILS,$bidInsert);
                        }

                        if($row['inv_status']=='A' && $row['sold_by'] != ""){
                            $bidActiveUpdate = array(

                                            'inv_status'=>$row['inv_status'],
                                            'sold_by'=>$row['sold_by'],
                                            'sold_on'=>$row['sold_on'],
                                        );
                            $this->db->where('invoice_id',$row['invoice_id']);
                            $this->db->update(OFFER_INVOICE,$bidActiveUpdate);

                          
                        }

                            $this->db->select('sheet_id,status');
                            $this->db->from('tt_switch_on');
                            $this->db->where('sheet_id',$row['sheet_id']);
                            $this->db->where('buyer_id',$row1['buyer_id']);
                            $getval=$this->db->get();
                            $stat= $getval->result();
                            $hbpType=@$stat[0]->status;

                            if(($hbpType!='semi_aumatic' || $hbpType=="")){

                                 if($row['inv_status']!='A' && $row['buyer_hbp'][$row1['buyer_id']]['abp'] >= $row['price_idea']){
                                    $dataBiddetails = array(
                                        'buyer_price' => $row['price_idea'],
                                        'bid_time' => date('Y-m-d H:i:s'),
                                        'push_hbp'=>'Y'
                                    );
                                  
                                    $this->db->where('invoice_id',$row['invoice_id']);
                                    $this->db->where('buyer_id',$row1['buyer_id']);
                                    $this->db->update(BID_DETAILS,$dataBiddetails);

                                    $this->db->where('inv_id', $row['invoice_id']);
                                    $this->db->delete('tt_buyer_division');

                                    $data_sold = array(
                                    'inv_status' => 'A',
                                    'hbp_key' => $row['buyer_hbp'][$row1['buyer_id']]['hbp_key'],
                                    'sold_by' => $row1['buyer_id'],
                                    'sold_on' => date('Y-m-d')
                                     );
                                    $this->db->where('invoice_id', $row['invoice_id']);
                                    $this->db->update(OFFER_INVOICE, $data_sold);

                                    
                                }
                            }

                    } 

               }
            }


                $completeClose = array('complete_close'=>'Y');
                $this->db->where('sheet_id',$row2->sheet_id);
                $this->db->update('tt_offer_sheets',$completeClose);

                $completeClose1 = array('full_entry'=>'Y');
                $this->db->where('sheet_id',$row2->sheet_id);
                $this->db->update('tt_sheet_entry',$completeClose1);
            }
        }

        if(!empty($chkDuplicateExpire)){
            foreach($chkDuplicateExpire as $row2){
                $jsonFile = file_get_contents('public/uploads/'.$row2->sheet_id.'.json');
                $decodeJson= json_decode($jsonFile,true);

           

            foreach($decodeJson as $row){
               $countHbp = count($row['buyer_hbp']);
               $countBid = count($row['buyer_bid']);

               if($countHbp > $countBid){
                $counter  = 0;
                foreach($row['buyer_hbp'] as $key=>$row1){
             
                    $dataInsert = array(
                                    'invoice_id'=>$row['invoice_id'],
                                    'firebase_key'=>$row['firebase_key'],
                                    'buyer_id'=>$row1['buyer_id'],
                                    'hbp_key'=>$row1['hbp_key'],
                                    'seller_id'=>$row['seller_id'],
                                    'buyer_hbp'=>$row1['abp'],
                                    'sheet_id'=>$row['sheet_id'],
                                    'seller_price'=>$row['price_idea'],
                                    'hbp_time'=>$row1['hbp_time'],
                                    'buyer_price'=>$row['buyer_bid'][$row1['buyer_id']]['bid'],
                                    'bid_time'=>$row['buyer_bid'][$row1['buyer_id']]['bid_time']
                                );

                        $this->db->select('invoice_id,buyer_id');
                        $this->db->from(BID_DETAILS);
                        $this->db->where('invoice_id',$row['invoice_id']);
                        $this->db->where('buyer_id',$row1['buyer_id']);
                        $qry = $this->db->get();
                        $chkDuplicateBidValue = $qry->result();

                       

                        if(empty($chkDuplicateBidValue)){
                          $this->db->insert(BID_DETAILS,$dataInsert);
                        }
                
                    if($row['inv_status']=='A' && $row['sold_by'] != ""){
                        $bidActiveUpdate = array(

                                        'inv_status'=>$row['inv_status'],
                                        'sold_by'=>$row['sold_by'],
                                        'sold_on'=>$row['sold_on'],
                                    );

                      
                        $this->db->where('invoice_id',$row['invoice_id']);
                        $this->db->update(OFFER_INVOICE,$bidActiveUpdate);
                    }

                    $this->db->select('sheet_id,status');
                    $this->db->from('tt_switch_on');
                    $this->db->where('sheet_id',$row['sheet_id']);
                    $this->db->where('buyer_id',$row1['buyer_id']);
                    $getval=$this->db->get();
                    $stat= $getval->result();
                    $hbpType=@$stat[0]->status;

                    if(($hbpType!='semi_aumatic' || $hbpType=="")){

                         if($row['inv_status']!='A' && $row1['abp'] >= $row['price_idea']){
                            $dataBiddetails = array(
                                'buyer_price' => $row['price_idea'],
                                'bid_time' => date('Y-m-d H:i:s'),
                                'push_hbp'=>'Y'
                            );

                            $this->db->where('invoice_id',$row['invoice_id']);
                            $this->db->where('buyer_id',$row1['buyer_id']);
                            $this->db->update(BID_DETAILS,$dataBiddetails);

                            $this->db->where('inv_id', $row['invoice_id']);
                            $this->db->delete('tt_buyer_division');
                     
                            $data_sold = array(
                            'inv_status' => 'A',
                            'hbp_key' => $row1['hbp_key'],
                            'sold_by' => $row1['buyer_id'],
                            'sold_on' => date('Y-m-d')
                             );
                            $this->db->where('invoice_id', $row['invoice_id']);
                            $this->db->update(OFFER_INVOICE, $data_sold);

                             
                        }
                    }
                } 
               }else{

                    foreach($row['buyer_bid'] as $key=>$row1){
                       
                         $bidInsert = array(

                                        'buyer_price'=>$row1['bid'],
                                        'bid_time'=>$row1['bid_time'],
                                        'invoice_id'=>$row['invoice_id'],
                                        'firebase_key'=>$row['firebase_key'],
                                        'buyer_id'=>$row1['buyer_id'],
                                        'seller_id'=>$row['seller_id'],
                                        'seller_price'=>$row['price_idea'],
                                        'sheet_id'=>$row['sheet_id'],
                                        'hbp_key'=>$row['buyer_hbp'][$row1['buyer_id']]['hbp_key'],
                                       'buyer_hbp'=>$row['buyer_hbp'][$row1['buyer_id']]['abp'],
                                       'hbp_time'=>$row['buyer_hbp'][$row1['buyer_id']]['hbp_time']
                                    );

                        $this->db->select('invoice_id,buyer_id');
                        $this->db->from(BID_DETAILS);
                        $this->db->where('invoice_id',$row['invoice_id']);
                        $this->db->where('buyer_id',$row1['buyer_id']);
                        $qry = $this->db->get();
                        $chkDuplicateBidValue = $qry->result();
                      

                        if(empty($chkDuplicateBidValue)){
                          $this->db->insert(BID_DETAILS,$bidInsert);
                        }

                        if($row['inv_status']=='A' && $row['sold_by'] != ""){
                            $bidActiveUpdate = array(

                                            'inv_status'=>$row['inv_status'],
                                            'sold_by'=>$row['sold_by'],
                                            'sold_on'=>$row['sold_on'],
                                        );
                            $this->db->where('invoice_id',$row['invoice_id']);
                            $this->db->update(OFFER_INVOICE,$bidActiveUpdate);

                          
                        }

                            $this->db->select('sheet_id,status');
                            $this->db->from('tt_switch_on');
                            $this->db->where('sheet_id',$row['sheet_id']);
                            $this->db->where('buyer_id',$row1['buyer_id']);
                            $getval=$this->db->get();
                            $stat= $getval->result();
                            $hbpType=@$stat[0]->status;

                            if(($hbpType!='semi_aumatic' || $hbpType=="")){

                                 if($row['inv_status']!='A' && $row['buyer_hbp'][$row1['buyer_id']]['abp'] >= $row['price_idea']){
                                    $dataBiddetails = array(
                                        'buyer_price' => $row['price_idea'],
                                        'bid_time' => date('Y-m-d H:i:s'),
                                        'push_hbp'=>'Y'
                                    );
                                  
                                    $this->db->where('invoice_id',$row['invoice_id']);
                                    $this->db->where('buyer_id',$row1['buyer_id']);
                                    $this->db->update(BID_DETAILS,$dataBiddetails);

                                    $this->db->where('inv_id', $row['invoice_id']);
                                    $this->db->delete('tt_buyer_division');

                                    $data_sold = array(
                                    'inv_status' => 'A',
                                    'hbp_key' => $row['buyer_hbp'][$row1['buyer_id']]['hbp_key'],
                                    'sold_by' => $row1['buyer_id'],
                                    'sold_on' => date('Y-m-d')
                                     );
                                    $this->db->where('invoice_id', $row['invoice_id']);
                                    $this->db->update(OFFER_INVOICE, $data_sold);

                                    
                                }
                            }

                    } 

               }
            }


                $completeClose = array('complete_close'=>'Y');
                $this->db->where('sheet_id',$row2->sheet_id);
                $this->db->update('tt_offer_sheets',$completeClose);

                $completeClose1 = array('full_entry'=>'Y');
                $this->db->where('sheet_id',$row2->sheet_id);
                $this->db->update('tt_sheet_entry',$completeClose1);
            }
        }
    }

    function pushHBPcronbkk(){
       /* $date = date('Y-m-d H:i:s');
        $this->db->select('os.sheet_id,os.expiry_date,os.expire,toe.sheet_id,toe.full_entry');
        $this->db->from(OFFER_SHEET.' os');
        $this->db->join('tt_sheet_entry toe','toe.sheet_id=os.sheet_id');
        $this->db->where('os.expiry_date <',$date);
        $this->db->where('toe.full_entry','N');
        $chk=$this->db->get();
        $chkDuplicate =  $chk->result();

        if(!empty($chkDuplicate)){
            foreach($chkDuplicate as $row2){
                $jsonFile = file_get_contents('public/uploads/'.$row2->sheet_id.'.json');
                $decodeJson= json_decode($jsonFile,true);

           

            foreach($decodeJson as $row){
               $countHbp = count($row['buyer_hbp']);
               $countBid = count($row['buyer_bid']);

               if($countHbp > $countBid){
               
                foreach($row['buyer_hbp'] as $key=>$row1){

                    $dataInsert = array(
                                    'invoice_id'=>$row['invoice_id'],
                                    'firebase_key'=>$row['firebase_key'],
                                    'buyer_id'=>$row1['buyer_id'],
                                    'hbp_key'=>$row1['hbp_key'],
                                    'seller_id'=>$row['seller_id'],
                                    'buyer_hbp'=>$row1['abp'],
                                    'sheet_id'=>$row['sheet_id'],
                                    'seller_price'=>$row['price_idea'],
                                    'hbp_time'=>$row1['hbp_time'],
                                );

                        $this->db->select('invoice_id,buyer_id');
                        $this->db->from(BID_DETAILS);
                        $this->db->where('invoice_id',$row['invoice_id']);
                        $this->db->where('buyer_id',$row1['buyer_id']);
                        $qry = $this->db->get();
                        $chkDuplicateBidValue = $qry->result();

                        if(empty($chkDuplicateBidValue)){
                          $this->db->insert(BID_DETAILS,$dataInsert);
                        }
                   

                    $bidUpdate = array(

                                        'buyer_price'=>$row['buyer_bid'][$row1['buyer_id']]['bid'],
                                        'bid_time'=>$row['buyer_bid'][$row1['buyer_id']]['bid_time'],
                                    );

                    $this->db->where('invoice_id',$row['invoice_id']);
                    $this->db->where('buyer_id',$row1['buyer_id']);
                    $this->db->update(BID_DETAILS,$bidUpdate);

                    if($row['inv_status']=='A' && $row['sold_by'] != ""){
                        $bidActiveUpdate = array(

                                        'inv_status'=>$row['inv_status'],
                                        'sold_by'=>$row['sold_by'],
                                        'sold_on'=>$row['sold_on'],
                                    );
                        $this->db->where('invoice_id',$row['invoice_id']);
                        $this->db->update(OFFER_INVOICE,$bidActiveUpdate);
                    }

                    $this->db->select('sheet_id,status');
                    $this->db->from('tt_switch_on');
                    $this->db->where('sheet_id',$row['sheet_id']);
                    $this->db->where('buyer_id',$row1['buyer_id']);
                    $getval=$this->db->get();
                    $stat= $getval->result();
                    $hbpType=@$stat[0]->status;

                    if(($hbpType!='semi_aumatic' || $hbpType=="")){

                         if($row['inv_status']!='A' && $row1['abp'] >= $row['price_idea']){
                            $dataBiddetails = array(
                                'buyer_price' => $row['price_idea'],
                                'bid_time' => date('Y-m-d H:i:s'),
                                'push_hbp'=>'Y'
                            );

                            $this->db->where('invoice_id',$row['invoice_id']);
                            $this->db->where('buyer_id',$row1['buyer_id']);
                            $this->db->update(BID_DETAILS,$dataBiddetails);

                            $this->db->where('inv_id', $row['invoice_id']);
                            $this->db->delete('tt_buyer_division');

                            $data_sold = array(
                            'inv_status' => 'A',
                            'hbp_key' => $row1['hbp_key'],
                            'sold_by' => $row1['buyer_id'],
                            'sold_on' => date('Y-m-d')
                             );
                            $this->db->where('invoice_id', $row['invoice_id']);
                            $this->db->update(OFFER_INVOICE, $data_sold);
                        }
                    }
                }
               }else{

                    foreach($row['buyer_bid'] as $key=>$row1){
                         $bidInsert = array(

                                        'buyer_price'=>$row1['bid'],
                                        'bid_time'=>$row1['bid_time'],
                                        'invoice_id'=>$row['invoice_id'],
                                        'firebase_key'=>$row['firebase_key'],
                                        'buyer_id'=>$row1['buyer_id'],
                                        'seller_id'=>$row['seller_id'],
                                        'seller_price'=>$row['price_idea'],
                                        'sheet_id'=>$row['sheet_id'],
                                    );

                        $this->db->select('invoice_id,buyer_id');
                        $this->db->from(BID_DETAILS);
                        $this->db->where('invoice_id',$row['invoice_id']);
                        $this->db->where('buyer_id',$row1['buyer_id']);
                        $qry = $this->db->get();
                        $chkDuplicateBidValue = $qry->result();

                        if(empty($chkDuplicateBidValue)){
                          $this->db->insert(BID_DETAILS,$bidInsert);
                        }


                        

                        $dataUpdate = array(
                                    'hbp_key'=>$row['buyer_hbp'][$row1['buyer_id']]['hbp_key'],
                                    'buyer_hbp'=>$row['buyer_hbp'][$row1['buyer_id']]['abp'],
                                    'hbp_time'=>$row['buyer_hbp'][$row1['buyer_id']]['hbp_time'],
                                );

                        $this->db->where('invoice_id',$row['invoice_id']);
                        $this->db->where('buyer_id',$row1['buyer_id']);
                        $this->db->update(BID_DETAILS,$dataUpdate);

                        if($row['inv_status']=='A' && $row['sold_by'] != ""){
                            $bidActiveUpdate = array(

                                            'inv_status'=>$row['inv_status'],
                                            'sold_by'=>$row['sold_by'],
                                            'sold_on'=>$row['sold_on'],
                                        );
                            $this->db->where('invoice_id',$row['invoice_id']);
                            $this->db->update(OFFER_INVOICE,$bidActiveUpdate);
                        }

                            $this->db->select('sheet_id,status');
                            $this->db->from('tt_switch_on');
                            $this->db->where('sheet_id',$row['sheet_id']);
                            $this->db->where('buyer_id',$row1['buyer_id']);
                            $getval=$this->db->get();
                            $stat= $getval->result();
                            $hbpType=@$stat[0]->status;

                            if(($hbpType!='semi_aumatic' || $hbpType=="")){

                                 if($row['inv_status']!='A' && $row['buyer_hbp'][$row1['buyer_id']]['abp'] >= $row['price_idea']){
                                    $dataBiddetails = array(
                                        'buyer_price' => $row['price_idea'],
                                        'bid_time' => date('Y-m-d H:i:s'),
                                        'push_hbp'=>'Y'
                                    );

                                    $this->db->where('invoice_id',$row['invoice_id']);
                                    $this->db->where('buyer_id',$row1['buyer_id']);
                                    $this->db->update(BID_DETAILS,$dataBiddetails);

                                    $this->db->where('inv_id', $row['invoice_id']);
                                    $this->db->delete('tt_buyer_division');

                                    $data_sold = array(
                                    'inv_status' => 'A',
                                    'hbp_key' => $row['buyer_hbp'][$row1['buyer_id']]['hbp_key'],
                                    'sold_by' => $row1['buyer_id'],
                                    'sold_on' => date('Y-m-d')
                                     );
                                    $this->db->where('invoice_id', $row['invoice_id']);
                                    $this->db->update(OFFER_INVOICE, $data_sold);
                                }
                            }

                    }

               }
            }


                $completeClose = array('complete_close'=>'Y');
                $this->db->where('sheet_id',$row2->sheet_id);
                $this->db->update('tt_offer_sheets',$completeClose);

                $completeClose1 = array('full_entry'=>'Y');
                $this->db->where('sheet_id',$row2->sheet_id);
                $this->db->update('tt_sheet_entry',$completeClose1);
            }
        }*/
    }

  /* function pushHBPcronbkkk()
    {



        $date = date('Y-m-d H:i:s');

        $this->db->select('tbd.sheet_id,tos.sheet_id,tbd.push_hbp,tbd.hbp_time,tbd.buyer_id,tbd.invoice_id,tos.expiry_date,tbd.buyer_hbp,tbd.seller_id,tbd.hbp_key,tos.complete_close');
        $this->db->from('tt_bid_details tbd');
        $this->db->join('tt_offer_sheets tos','tbd.sheet_id=tos.sheet_id');
        $this->db->where('tbd.push_hbp','N');
        $this->db->where('tos.expire','N');
        $this->db->where('tos.expiry_date <',$date);
        $this->db->order_by('tbd.hbp_time',"ASC");
        $qry=$this->db->get();
        $bid_details_all= $qry->result();
       // pre(last_query());
        //pre($bid_details_all,1);

   




        $completeSheetIds =array();
        $selectedInvoiceid=array();
        $selectedKey=array();
        if (!empty($bid_details_all)){
            foreach ($bid_details_all as $row){   
                
                if(!in_array($row->sheet_id, $completeSheetIds))
                {
                    array_push($completeSheetIds,$row->sheet_id);
                }
                $this->db->select('sheet_id,status');
                $this->db->from('tt_switch_on');
                $this->db->where('sheet_id',$row->sheet_id);
                $this->db->where('buyer_id',$row->buyer_id);
                $getval=$this->db->get();
                $stat= $getval->result();

                $hbpType=@$stat[0]->status;

                $this->db->select('invoice_id,inv_status,price_idea');
                $this->db->from(OFFER_INVOICE);
                $this->db->where('invoice_id',$row->invoice_id);
                $getValinv=$this->db->get();
                $getHBPdatainv= $getValinv->result();

                $this->db->select('buyer_id,sheet_id,bid_max_qty');
                $this->db->from('tt_buyer_sheet_assigned');
                $this->db->where('buyer_id',$row->buyer_id);
                $this->db->where('sheet_id',$row->sheet_id);
                $getval2=$this->db->get();
                $bidmaxqty= $getval2->result();

                if(@$bidmaxqty[0]->bid_max_qty== "" || @$bidmaxqty[0]->bid_max_qty==0){
                    $getbidmaxqty=9999;
                }
                else{
                    $getbidmaxqty=@$bidmaxqty[0]->bid_max_qty;

                }
                $this->db->select('buyer_id,sheet_id,push_hbp');
                $this->db->from(BID_DETAILS);
                $this->db->where('buyer_id',$row->buyer_id);
                $this->db->where('sheet_id',$row->sheet_id);
                $this->db->where('push_hbp','Y');
                $countgetVal=$this->db->get();
                $countgetHBPdata= $countgetVal->result();

                $expDatetime=$row->expiry_date;
                $timeFirst  = strtotime(date('Y-m-d H:i:s'));
                $timeSecond = strtotime($expDatetime);
                $differenceInSeconds = $timeSecond - $timeFirst;

                

                if(($hbpType!='semi_aumatic' || $hbpType=="")){
                   
                    if($differenceInSeconds < 1 && @$getHBPdatainv[0]->inv_status!='A' && intval($row->buyer_hbp) >=intval(@$getHBPdatainv[0]->price_idea) && count($countgetHBPdata) < intval($getbidmaxqty)){
                            
                            $dataBiddetails = array(
                            'buyer_price' => @$getHBPdatainv[0]->price_idea,
                            'bid_time' => date('Y-m-d H:i:s'),
                            'push_hbp'=>'Y'
                         );

                        $data_log = array(

                        'buyer_id' => $row->buyer_id,
                        'seller_id' => $row->seller_id,
                        'invoice_id' => $row->invoice_id,
                        'sheet_id' => $row->sheet_id,
                        'buyer_price' => @$getHBPdatainv[0]->price_idea,
                        'seller_price' => @$getHBPdatainv[0]->price_idea,
                         'bid_on' => date('Y-m-d H:i:s') ,
                        );
                        $this
                            ->db
                            ->insert(BID_LOG, $data_log);

                            $this
                            ->db
                            ->where('buyer_id', $row->buyer_id);
                            $this
                                ->db
                                ->where('invoice_id', @$row->invoice_id);
                            $this
                            ->db
                            ->update(BID_DETAILS, $dataBiddetails);

                            $this
                            ->db
                            ->where('inv_id', @$row->invoice_id);
                           
                            $this
                                ->db
                                ->delete('tt_buyer_division');

                            $data_sold = array(
                            'inv_status' => 'A',
                            'hbp_key' => @$row->hbp_key,
                            'sold_by' => @$row->buyer_id,
                            'sold_on' => date('Y-m-d')
                             );
                            $this
                                ->db
                                ->where('invoice_id', @$row->invoice_id);
                            $this
                                ->db
                                ->update(OFFER_INVOICE, $data_sold);

                        
                        }

                        elseif($differenceInSeconds < 1 && @$getHBPdatainv[0]->inv_status=='A'){

                                      ////////////////////////////////////////////////////////
                            $allHBPY = array(
                                        'push_hbp'=>'Y'
                                    );
                            $this->db->where('buyer_id', $row->buyer_id);
                            $this->db->where('invoice_id', @$row->invoice_id);
                            $this->db ->update(BID_DETAILS, $allHBPY);
                            /////////////////////////////////////////////////////// 

                        }
                }  

               

            }

            if(!empty($completeSheetIds)){
                foreach($completeSheetIds as $data){
                    $completeClose = array('complete_close'=>'Y');
                    $this->db->where('sheet_id',$data);
                    $this->db->update('tt_offer_sheets',$completeClose);
                }

            }
        }  

    }*/
    function pushHBPdata()
    {

         $sheet_id = $this
            ->input
            ->post('sheet_id');

        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);

        $buyer_id = @$dtl['id'];
        $this->db->select('*');
        $this->db->from('tt_offer_invoice');
        $this->db->where('sheet_id',$sheet_id);
        $this->db->where('inv_status',"A");
        $qry=$this->db->get();
        $invoice_dtl =$qry->result();

       /* $selectedInvoiceid=array();
        $selectedKey=array();

        if(!empty($invoice_dtl)){

                foreach($invoice_dtl as $key=>$row){ 

                $inv_id=$row->invoice_id;
                array_push($selectedInvoiceid, @$row->invoice_id);
                array_push($selectedKey, @$row->hbp_key);

           }

        }*/

        echo json_encode($invoice_dtl);

    }

  

    function setBidqty()
    {
        $dtl = $this
        ->session
        ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];

        $sheet_id=$this
            ->input
            ->post('sheet_id');

        $qty=$this
        ->input
        ->post('qty');

        $this->db->select('*');
        $this->db->from('tt_buyer_sheet_assigned');
        $this->db->where('buyer_id',$buyer_id);
        $this->db->where('sheet_id',$sheet_id);
        $getval=$this->db->get();
        $duplicateCheck= $getval->result();

        if(count($duplicateCheck) > 0)
        {

        $data = array(
                    'bid_max_qty' => $qty,
                    
                     );
        $this
            ->db
            ->where('buyer_id', @$buyer_id);
        $this
        ->db
        ->where('sheet_id', @$sheet_id);
        $this
            ->db
            ->update('tt_buyer_sheet_assigned', $data);

        }

        else
        {

            $data = array(
                    'bid_max_qty' => $qty,
                    
                     );

            $this
            ->db
            ->insert('tt_buyer_sheet_assigned', $data);

        }

    }

    function setHBPtype()
    {
        $val = $this
            ->input
            ->post('val');
        $dtl = $this
        ->session
        ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];

        $sheet_id=$this
            ->input
            ->post('sheet_id');

         if($val=='true')
         {

            $hbpType='fully_aumatic';
            

         }
         else
         {
            $hbpType='semi_aumatic';
            
         }

        $this->db->select('*');
        $this->db->from('tt_switch_on');
        $this->db->where('buyer_id',$buyer_id);
        $this->db->where('sheet_id',$sheet_id);
        $getval=$this->db->get();
        $duplicateCheck= $getval->result();

        if(count($duplicateCheck) > 0)
        {

        $data = array(
                    'status' => $hbpType,
                    'modified_on' => date('Y-m-d H:i:s')
                     );
        $this
            ->db
            ->where('buyer_id', @$buyer_id);
        $this
        ->db
        ->where('sheet_id', @$sheet_id);
        $this
            ->db
            ->update('tt_switch_on', $data);

        }
        else
        {

        $data = array(
                    'status' => $hbpType,
                    'created_on' => date('Y-m-d H:i:s'),
                    'sheet_id' => $sheet_id,
                    'buyer_id' => $buyer_id
                     );
        
        $this
            ->db
            ->insert('tt_switch_on', $data);

        }

        
         echo json_encode($hbpType);
    }

    function chk_hbp()
    {
        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $price = $this
            ->input
            ->post('price');
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];

        $sheet_id=$this
            ->input
            ->post('sheet_id');

        $final_pi_id=$this
            ->input
            ->post('final_pi_id');

        $ChkSheet = $this
            ->Common
            ->findBy(OFFER_SHEET, 'sheet_id', $sheet_id);

        $price_idea=$this
        ->input
        ->post('price_idea');

        $max_price=$this
        ->input
        ->post('max_price');

        $key=$this
        ->input
        ->post('key');

        $this->db->select('*');
        $this->db->from(BID_DETAILS);
        $this->db->where('buyer_id',$buyer_id);
        $this->db->where('invoice_id',$invoice_id);
        $Qry=$this->db->get();
        $chkMyinv=$Qry->result();

        $this->db->select('buyer_id,invoice_id,buyer_hbp');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$invoice_id);
        $this->db->where('buyer_hbp',intval($price));
        $Qry=$this->db->get();
        $chkHBP=$Qry->result();

        $prevPrice=@$chkMyinv[0]->buyer_hbp;


        if(intval($max_price)==intval($price))
        {
            $result=3;
        }
        elseif(!empty($chkHBP))
        {
            $result = 0;
        }
      

        echo json_encode(array('result'=>$result,'hbp'=>@$prevPrice));

    }

    function bkkkkkplace_hbp()
    {

        $firebase = $this->firebase->init();
        $database = $firebase->getDatabase();

        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $price = $this
            ->input
            ->post('price');
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];

        $sheet_id=$this
            ->input
            ->post('sheet_id');

        $final_pi_id=$this
            ->input
            ->post('final_pi_id');

        $firebaseKey=$this
            ->input
            ->post('firebaseKey');

            

        $ChkSheet = $this
            ->Common
            ->findBy(OFFER_SHEET, 'sheet_id', $sheet_id);

        $price_idea=$this
        ->input
        ->post('price_idea');

        $max_price=$this
        ->input
        ->post('max_price');

        $key=$this
        ->input
        ->post('key');

        $this->db->select('buyer_id,invoice_id,buyer_hbp');
        $this->db->from(BID_DETAILS);
        $this->db->where('buyer_id',$buyer_id);
        $this->db->where('invoice_id',$invoice_id);
        $Qry=$this->db->get();
        $chkMyinv=$Qry->result();

        if(intval($price) == 0)
        {
           @$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey.'/buyer_hbp/'.$buyer_id)->set($price);
            
        }

       
        if(intval(trim($price)) > 0 && intval(trim($price)) !="")
        {
            $this->db->select('buyer_id,invoice_id,buyer_hbp');
            $this->db->from(BID_DETAILS);
            $this->db->where('invoice_id',$invoice_id);
            $this->db->where('buyer_hbp',trim($price));
            $Qry=$this->db->get();
            $chkHBP=$Qry->result();
        }
        else
        {
            $chkHBP=array();
        }

        $prevPrice=@$chkMyinv[0]->buyer_hbp;

        if(intval($max_price)==intval($price) && intval($max_price)!=0 && intval($price) !=0 && intval($max_price)!="" && intval($price) !="")
        {
            $result=3;
        }
        else
        {



            $data=array('buyer_hbp'=>trim($price),'hbp_key'=>$key,'hbp_time'=>date('Y-m-d H:i:s'),'push_hbp'=>'N','firebase_key'=>$firebaseKey);

            $data_update1 = array(
                            'buyer_price' => 0,
                            'bid_time' => date('Y-m-d H:i:s'),
                            'buyer_id'=>$buyer_id,
                            'sheet_id'=>$sheet_id,
                            'invoice_id'=>$invoice_id,
                            'seller_id'=>@$ChkSheet['created_by'],
                            'seller_price'=>$price_idea,
                            'buyer_hbp'=>trim($price),
                            'hbp_key'=>$key,
                            'hbp_time'=>date('Y-m-d H:i:s'),
                            'push_hbp'=>'N',
                            'firebase_key'=>$firebaseKey
                        );
            $increaseRate=@$ChkSheet['bidding_gap'];



            if(!empty($chkHBP) > 0)
            {
                $result = 0;
            }
            else
            {
                if($final_pi_id=='N')
                {

                  
                if(intval($price) > 0)
                {
                   @$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey.'/buyer_hbp/'.$buyer_id)->set($price);
                    
                }
                    
               
                  

                    if(empty($chkMyinv))
                    {
                        $this->db->insert(BID_DETAILS,$data_update1);
                    }
                    else
                    {
                        $this->db->where('buyer_id',$buyer_id);
                        $this->db->where('invoice_id',$invoice_id);
                        $this->db->update(BID_DETAILS,$data);
                    }


                    $this->db->select('MAX(buyer_price) as maxprice');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('invoice_id',$invoice_id);
                    $bid_max_price=$this->db->get();
                    $getMaxprice=$bid_max_price->result();
                    $chkgetMaxprice=@$getMaxprice[0]->maxprice;

                    if(intval($chkgetMaxprice) > 0)
                    {
                        $this->db->select('bd.invoice_id,bd.buyer_price,bd.buyer_id');
                        $this->db->from(BID_DETAILS.' bd');
                        $this->db->where('bd.invoice_id',$invoice_id);
                        $this->db->where('bd.buyer_price',$chkgetMaxprice);
                        $bid_max_price_buyer_id=$this->db->get();
                        $get_bid_max_price_buyer_id = $bid_max_price_buyer_id->result();
                        $get_bid_max_price_buyer_id_final=@$get_bid_max_price_buyer_id[0]->buyer_id;
                    }
                    else
                    {
                        $get_bid_max_price_buyer_id_final=0;
                    }

                 
               

            

                    $this->db->select('MAX(buyer_hbp) as maxhbp1');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('invoice_id',$invoice_id);
                    $bid_max=$this->db->get();
                    $getMaxhbp=$bid_max->result();
                    $chkgetMaxhbp=@$getMaxhbp[0]->maxhbp1;

                    $this->db->select('bd.invoice_id,bd.buyer_hbp,bd.buyer_id');
                    $this->db->from(BID_DETAILS.' bd');
                    $this->db->where('bd.invoice_id',$invoice_id);
                    $this->db->where('bd.buyer_hbp',$chkgetMaxhbp);
                    $bid_max_hbp_buyer_id=$this->db->get();
                    $get_bid_max_hbp_buyer_id = $bid_max_hbp_buyer_id->result();
                    $get_bid_max_hbp_buyer_id_final=@$get_bid_max_hbp_buyer_id[0]->buyer_id;

                    $sql="SELECT MAX(`buyer_hbp`) as sec FROM `tt_bid_details` WHERE `invoice_id` = $invoice_id AND `buyer_hbp` < $chkgetMaxhbp";
                    $qry=$this->db->query($sql);
                    $secondHighestparice=$qry->result();
                    $getsecondHighestparice=@$secondHighestparice[0]->sec;

                    $this->db->select('id,company_name');
                    $this->db->from('tt_users');
                    $this->db->where('id',$get_bid_max_hbp_buyer_id_final);
                    $qry=$this->db->get();
                    $getCom=$qry->result();

                    if(intval($getsecondHighestparice) < intval($chkgetMaxhbp) && intval($getsecondHighestparice) > 0 && $get_bid_max_hbp_buyer_id_final != $buyer_id)
                    {

                        $data_log7 = array(

                                    'buyer_id' => $buyer_id,
                                    'seller_id' => @$ChkSheet['created_by'],
                                    'invoice_id' => $invoice_id,
                                    'sheet_id' => $sheet_id,
                                    'buyer_price' => $price,
                                    'seller_price' => $price_idea,
                                     'bid_on' => date('Y-m-d H:i:s') ,
                                    );
                                    $this
                                        ->db
                                        ->insert(BID_LOG, $data_log7);

                    }


                    if($chkgetMaxhbp !=0 && intval($getsecondHighestparice)!=intval($chkgetMaxhbp) && intval($price) > intval($max_price))
                        {

                        	
                            
                             $updatedBid=$getsecondHighestparice+$increaseRate;
                              if(intval($updatedBid) > intval($chkgetMaxhbp))
                                {
                                    $updatedBid=$chkgetMaxhbp;
                                }
                                else
                                {
                                    $updatedBid=$getsecondHighestparice+$increaseRate;

                                }
                            if(intval($getsecondHighestparice) !=0 && intval($get_bid_max_price_buyer_id_final) !=$buyer_id)
                            {

                                if(intval($updatedBid) < intval($chkgetMaxprice))
                                {
                                    $updatedBid7=$chkgetMaxprice+$increaseRate;

                                    if(intval($updatedBid7) > intval($chkgetMaxhbp))
                                        {
                                            $updatedBid7=$chkgetMaxhbp;
                                        }
                                        else
                                        {
                                            $updatedBid7=$chkgetMaxprice+$increaseRate;

                                        }

                                       

                                    $fields1=array('bid'=>$updatedBid7,'buyer'=>substr(@$getCom[0]->company_name,0,10),'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name);
         
                                    $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);

                                    $maxHBPupdateBuyerwise=array('buyer_price'=>$updatedBid7);
                                    $this
                                    ->db
                                    ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                    $this
                                    ->db
                                    ->where('invoice_id', $invoice_id);
                                    $this
                                    ->db
                                    ->update(BID_DETAILS, $maxHBPupdateBuyerwise);



                                }
                                else
                                {
                                    

                                      $maxHBPupdateBuyerwise=array('buyer_price'=>$updatedBid);

                                       $fields1=array('bid'=>$updatedBid,'buyer'=>substr(@$getCom[0]->company_name,0,10),'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name);
         
                                      $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);
                                      $this
                                        ->db
                                        ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                        $this
                                        ->db
                                        ->where('invoice_id', $invoice_id);
                                        $this
                                        ->db
                                        ->update(BID_DETAILS, $maxHBPupdateBuyerwise);


                                }
                              
                            }

                            elseif(intval($getsecondHighestparice ==0) && intval($get_bid_max_price_buyer_id_final) !=$buyer_id && intval($max_price) > 0)
                            { 


                                
                                 $updatedBid1=$max_price+$increaseRate;
                                  if(intval($updatedBid1) > intval($chkgetMaxhbp))
                                    {
                                        $updatedBid1=$chkgetMaxhbp;
                                    }
                                    else
                                    {
                                        $updatedBid1=$max_price+$increaseRate;

                                    }

                                $fields1=array('bid'=>$updatedBid1,'buyer'=>substr(@$getCom[0]->company_name,0,10),'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name);
     
                                $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);

                                $maxHBPupdateBuyerwise=array('buyer_price'=>$updatedBid1);
                                $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);

                            }

                          
                            
                        }

                    $this->db->select('buyer_id,invoice_id,buyer_price');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('buyer_id',$get_bid_max_hbp_buyer_id_final);
                    $this->db->where('invoice_id',$invoice_id);
                    $QrySold=$this->db->get();
                    $invSold=$QrySold->result();
                    $invSoldprice=@$invSold[0]->buyer_price;

                     $this->db->select('MAX(buyer_price) as maxprice1');
	                $this->db->from(BID_DETAILS);
	                $this->db->where('invoice_id',$invoice_id);
	                $bid_max=$this->db->get();
	                $getMaxprice=$bid_max->result();
	                $maxinvSoldprice=@$getMaxprice[0]->maxprice1;

	               $this->db->select('bd.invoice_id,bd.buyer_price,bd.buyer_id');
	               $this->db->from(BID_DETAILS.' bd');
	               $this->db->where('bd.invoice_id',$invoice_id);
	               $this->db->where('bd.buyer_price',$maxinvSoldprice);
	               $bid_max_buyer_id=$this->db->get();
	               $get_bid_max_buyer_id = $bid_max_buyer_id->result();
	               $maxinvSoldpricebuyerid=@$get_bid_max_buyer_id[0]->buyer_id;


                    if(intval($maxinvSoldprice) >= intval($price_idea))
                        {

                        $fields2=array('bid'=>$maxinvSoldprice,'inv_status'=>"A",'bidMaxbuyerId'=>$maxinvSoldpricebuyerid);
 
                        $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields2);

                          $this->db->where('inv_id', @$invoice_id);
                          $this->db->delete('tt_buyer_division');

                
                        $data_log = array(

                        'buyer_id' => $maxinvSoldpricebuyerid,
                        'seller_id' => @$ChkSheet['created_by'],
                        'invoice_id' => $invoice_id,
                        'sheet_id' => $sheet_id,
                        'buyer_price' => $maxinvSoldprice,
                        'seller_price' => $price_idea,
                         'bid_on' => date('Y-m-d H:i:s') ,
                        );
                        $this
                            ->db
                            ->insert(BID_LOG, $data_log);


                        $data_sold = array(
                            'inv_status' => 'A',
                            'sold_by' => $maxinvSoldpricebuyerid,
                            'sold_on' => date('Y-m-d')
                        );
                        $this
                            ->db
                            ->where('invoice_id', $invoice_id);
                        $this
                            ->db
                            ->update(OFFER_INVOICE, $data_sold); 

                        }
                        elseif(intval($invSoldprice) > intval($max_price))
                        {
                            $fields2=array('bid'=>$invSoldprice,'inv_status'=>"I",'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final);
 
                        $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields2);
                              $data_log = array(

                            'buyer_id' => $get_bid_max_hbp_buyer_id_final,
                            'seller_id' => @$ChkSheet['created_by'],
                            'invoice_id' => $invoice_id,
                            'sheet_id' => $sheet_id,
                            'buyer_price' => $invSoldprice,
                            'seller_price' => $price_idea,
                             'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                            $this->db->where('inv_id', @$invoice_id);
                            $this->db->delete('tt_buyer_division');

                        }

                     $result = 1;

                }
                else
                {
                    $result = 7;
                }
                
            }

        }

        $this->db->select('invoice_id,buyer_price');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$invoice_id);
        $this->db->where('buyer_price >','0');
        $division_check=$this->db->get();
        $division_check_buyer=$division_check->result();

        $this->db->select('inv_id,approve'); 
        $this->db->from('tt_buyer_division');
        $this->db->where('inv_id',$invoice_id);
        $this->db->where('approve','A');
        $division_check=$this->db->get();
        $division_check_accept=$division_check->result();

        $fields8=array('division_check_accept'=>count($division_check_accept),'division_check_buyer'=>count($division_check_buyer));
         
        $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields8);


        echo json_encode(array('result'=>$result,'hbp'=>@$prevPrice));
    }


     function place_hbp()
    {

        //$firebase = $this->firebase->init();
        //$database = $firebase->getDatabase();

        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $price = $this
            ->input
            ->post('price');
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];

        $sheet_id=$this
            ->input
            ->post('sheet_id');

        $final_pi_id=$this
            ->input
            ->post('final_pi_id');

        $firebaseKey=$this
            ->input
            ->post('firebaseKey');

            

        $ChkSheet = $this
            ->Common
            ->findBy(OFFER_SHEET, 'sheet_id', $sheet_id);

        $price_idea=$this
        ->input
        ->post('price_idea');

        $max_price=$this
        ->input
        ->post('max_price');

        $key=$this
        ->input
        ->post('key');

        $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);


       /* $this->db->select('buyer_id,invoice_id,buyer_hbp');
        $this->db->from(BID_DETAILS);
        $this->db->where('buyer_id',$buyer_id);
        $this->db->where('invoice_id',$invoice_id);
        $Qry=$this->db->get();
        $chkMyinv=$Qry->result();*/

        if(intval($price) == 0)
        {
          // @$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey.'/buyer_hbp/'.$buyer_id)->set($price);
            
        }

       
        /*if(intval(trim($price)) > 0 && intval(trim($price)) !="")
        {
            $this->db->select('buyer_id,invoice_id,buyer_hbp');
            $this->db->from(BID_DETAILS);
            $this->db->where('invoice_id',$invoice_id);
            $this->db->where('buyer_hbp',trim($price));
            $Qry=$this->db->get();
            $chkHBP=$Qry->result();
        }
        else
        {
            $chkHBP=array();
        }
*/
        $prevPrice=$decodeJson[$invoice_id]['buyer_hbp'][$buyer_id]['abp'];

        if(intval($max_price)==intval($price) && intval($max_price)!=0 && intval($price) !=0 && intval($max_price)!="" && intval($price) !="")
        {
            $result=3;
        }
        else
        {



            $data=array('buyer_hbp'=>trim($price),'hbp_key'=>$key,'hbp_time'=>date('Y-m-d H:i:s'),'push_hbp'=>'N','firebase_key'=>$firebaseKey);

            $data_update1 = array(
                            'buyer_price' => 0,
                            'bid_time' => date('Y-m-d H:i:s'),
                            'buyer_id'=>$buyer_id,
                            'sheet_id'=>$sheet_id,
                            'invoice_id'=>$invoice_id,
                            'seller_id'=>@$ChkSheet['created_by'],
                            'seller_price'=>$price_idea,
                            'buyer_hbp'=>trim($price),
                            'hbp_key'=>$key,
                            'hbp_time'=>date('Y-m-d H:i:s'),
                            'push_hbp'=>'N',
                            'firebase_key'=>$firebaseKey
                        );
            $increaseRate=@$ChkSheet['bidding_gap'];



            if(trim($price) > 0 && ($decodeJson[$invoice_id]['abpone'] == trim($price) || $decodeJson[$invoice_id]['abptwo'] == trim($price)))
            {
                $result = 0;
            }
            else
            {
                if($final_pi_id=='N')
                {       
                if(intval($price) > 0)
                {
                    
                }
                

                if($decodeJson[$invoice_id]['abpone'] != "" && intval($price) < $decodeJson[$invoice_id]['abpone'] && $decodeJson[$invoice_id]['buyerone'] != $buyer_id){

                    $decodeJson[$invoice_id]['abptwo'] = trim($price);
                    $decodeJson[$invoice_id]['buyertwo'] = $buyer_id;
                }elseif($decodeJson[$invoice_id]['abpone'] != "" && intval($price) > $decodeJson[$invoice_id]['abpone'] && $decodeJson[$invoice_id]['buyerone'] != $buyer_id){
                    $decodeJson[$invoice_id]['abptwo'] = $decodeJson[$invoice_id]['abpone'];
                    $decodeJson[$invoice_id]['buyertwo'] = $decodeJson[$invoice_id]['buyerone'];
                    $decodeJson[$invoice_id]['abpone'] = trim($price);
                    $decodeJson[$invoice_id]['buyerone'] = $buyer_id;
                }else{
                    $decodeJson[$invoice_id]['abpone'] = trim($price);
                    $decodeJson[$invoice_id]['buyerone'] = $buyer_id;
                }
              
                    
                 /*if(empty($chkMyinv))
                    {
                        $this->db->insert(BID_DETAILS,$data_update1);
                    }
                    else
                    {
                        $this->db->where('buyer_id',$buyer_id);
                        $this->db->where('invoice_id',$invoice_id);
                        $this->db->update(BID_DETAILS,$data);
                    }*/
                    $decodeJson[$invoice_id]['buyer_hbp'][$buyer_id] =array(
                                            'abp'           => trim($price),
                                            'hbp_key'       => $key,
                                            'hbp_time'      => date('Y-m-d H:i:s'),
                                            'push_hbp'      =>'N',
                                            'firebase_key'  => $firebaseKey,
                                            'buyer_id'      =>$buyer_id,
                                        );

                    $mainArryEncode=json_encode($decodeJson);
                    $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                    file_put_contents($file_to_save, $mainArryEncode);

                 /*   $this->db->select('MAX(buyer_price) as maxprice');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('invoice_id',$invoice_id);
                    $bid_max_price=$this->db->get();
                    $getMaxprice=$bid_max_price->result();*/
                    $chkgetMaxprice=@$decodeJson[$invoice_id]['bidMaxPrice'];

                    if(intval($chkgetMaxprice) > 0)
                    {
                       /* $this->db->select('bd.invoice_id,bd.buyer_price,bd.buyer_id');
                        $this->db->from(BID_DETAILS.' bd');
                        $this->db->where('bd.invoice_id',$invoice_id);
                        $this->db->where('bd.buyer_price',$chkgetMaxprice);
                        $bid_max_price_buyer_id=$this->db->get();
                        $get_bid_max_price_buyer_id = $bid_max_price_buyer_id->result();*/
                        $get_bid_max_price_buyer_id_final=@$decodeJson[$invoice_id]['bidMaxbuyerId'];
                    }
                    else
                    {
                        $get_bid_max_price_buyer_id_final=0;
                    }

                /*    $this->db->select('MAX(buyer_hbp) as maxhbp1');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('invoice_id',$invoice_id);
                    $bid_max=$this->db->get();
                    $getMaxhbp=$bid_max->result();*/
                    $chkgetMaxhbp=@$decodeJson[$invoice_id]['abpone'];

                  /*  $this->db->select('bd.invoice_id,bd.buyer_hbp,bd.buyer_id');
                    $this->db->from(BID_DETAILS.' bd');
                    $this->db->where('bd.invoice_id',$invoice_id);
                    $this->db->where('bd.buyer_hbp',$chkgetMaxhbp);
                    $bid_max_hbp_buyer_id=$this->db->get();
                    $get_bid_max_hbp_buyer_id = $bid_max_hbp_buyer_id->result();*/
                    $get_bid_max_hbp_buyer_id_final=@$decodeJson[$invoice_id]['buyerone'];

                   /* $sql="SELECT MAX(`buyer_hbp`) as sec FROM `tt_bid_details` WHERE `invoice_id` = $invoice_id AND `buyer_hbp` < $chkgetMaxhbp";
                    $qry=$this->db->query($sql);
                    $secondHighestparice=$qry->result();*/
                    $getsecondHighestparice=@$decodeJson[$invoice_id]['abptwo'];

                    $this->db->select('id,company_name');
                    $this->db->from('tt_users');
                    $this->db->where('id',$get_bid_max_hbp_buyer_id_final);
                    $qry=$this->db->get();
                    $getCom=$qry->result();

                    if(intval($getsecondHighestparice) < intval($chkgetMaxhbp) && intval($getsecondHighestparice) > 0 && $get_bid_max_hbp_buyer_id_final != $buyer_id && intval($price) > intval($max_price))
                    {

                        $data_log7 = array(

                                    'buyer_id' => $buyer_id,
                                    'seller_id' => @$ChkSheet['created_by'],
                                    'invoice_id' => $invoice_id,
                                    'sheet_id' => $sheet_id,
                                    'buyer_price' => $price,
                                    'seller_price' => $price_idea,
                                     'bid_on' => date('Y-m-d H:i:s') ,
                                    );
                                    $this
                                        ->db
                                        ->insert(BID_LOG, $data_log7);

                    }


                    if($chkgetMaxhbp !=0 && intval($getsecondHighestparice)!=intval($chkgetMaxhbp) && intval($price) > intval($max_price))
                        {

                            
                            
                             $updatedBid=$getsecondHighestparice+$increaseRate;
                              if(intval($updatedBid) > intval($chkgetMaxhbp))
                                {
                                    $updatedBid=$chkgetMaxhbp;
                                }
                                else
                                {
                                    $updatedBid=$getsecondHighestparice+$increaseRate;

                                }
                            if(intval($getsecondHighestparice) !=0 && intval($get_bid_max_price_buyer_id_final) !=$buyer_id)
                            {
                                
                                if(intval($updatedBid) < intval($chkgetMaxprice))
                                {
                                    $updatedBid7=$chkgetMaxprice+$increaseRate;

                                    if(intval($updatedBid7) > intval($chkgetMaxhbp))
                                        {
                                            $updatedBid7=$chkgetMaxhbp;
                                        }
                                        else
                                        {
                                            $updatedBid7=$chkgetMaxprice+$increaseRate;

                                        }
                 

                                    $maxHBPupdateBuyerwise=array('buyer_price'=>$updatedBid7);

                                    $decodeJson[$invoice_id]['bidMaxPrice']=trim($updatedBid7);
                                    $decodeJson[$invoice_id]['bidMaxbuyerId']=$get_bid_max_hbp_buyer_id_final;

                                    $decodeJson[$invoice_id]['buyer_bid'][$get_bid_max_hbp_buyer_id_final] =array(
                                            'bid'         => trim($updatedBid7),
                                            'bid_time'    => date('Y-m-d H:i:s'),
                                            'buyer_id'    =>$get_bid_max_hbp_buyer_id_final,
                                            
                                        );

                                        $mainArryEncode=json_encode($decodeJson);
                                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                        file_put_contents($file_to_save, $mainArryEncode);
                                  /*  $this
                                    ->db
                                    ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                    $this
                                    ->db
                                    ->where('invoice_id', $invoice_id);
                                    $this
                                    ->db
                                    ->update(BID_DETAILS, $maxHBPupdateBuyerwise);*/

                                    $this->db->select('invoice_id,buyer_price');
                                    $this->db->from(BID_DETAILS);
                                    $this->db->where('invoice_id',$invoice_id);
                                    $this->db->where('buyer_price >','0');
                                    $division_check=$this->db->get();
                                    $division_check_buyer=$division_check->result();

                                      if($decodeJson[$invoice_id]['bidMaxPrice'] > 0){
                                            $division_check_buyer = 1;
                                        }else{
                                            $division_check_buyer = 0;
                                        }


                                    $this->db->select('inv_id,approve'); 
                                    $this->db->from('tt_buyer_division');
                                    $this->db->where('inv_id',$invoice_id);
                                    $this->db->where('approve','A');
                                    $division_check=$this->db->get();
                                    $division_check_accept=$division_check->result();

                                    $fields1=array('bid'=>$updatedBid7,'buyer'=>substr(@$getCom[0]->company_name,0,10),'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name,'inv_status'=>"I",'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);



                                }
                                else
                                {
                                    

                                      $maxHBPupdateBuyerwise=array('buyer_price'=>$updatedBid);
                                      $decodeJson[$invoice_id]['bidMaxPrice']=trim($updatedBid);
                                      $decodeJson[$invoice_id]['bidMaxbuyerId']=$get_bid_max_hbp_buyer_id_final;


                                        $decodeJson[$invoice_id]['buyer_bid'][$get_bid_max_hbp_buyer_id_final] =array(
                                            'bid'         => trim($updatedBid),
                                            'bid_time'    => date('Y-m-d H:i:s'),
                                            'buyer_id'    =>$get_bid_max_hbp_buyer_id_final,
                                        );

                                        $mainArryEncode=json_encode($decodeJson);
                                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                        file_put_contents($file_to_save, $mainArryEncode);
                                  /*  $this
                                
                                    /*  $this
                                        ->db
                                        ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                        $this
                                        ->db
                                        ->where('invoice_id', $invoice_id);
                                        $this
                                        ->db
                                        ->update(BID_DETAILS, $maxHBPupdateBuyerwise);*/

                                    $this->db->select('invoice_id,buyer_price');
                                    $this->db->from(BID_DETAILS);
                                    $this->db->where('invoice_id',$invoice_id);
                                    $this->db->where('buyer_price >','0');
                                    $division_check=$this->db->get();
                                    $division_check_buyer=$division_check->result();

                                    if($decodeJson[$invoice_id]['bidMaxPrice'] > 0){
                                        $division_check_buyer = 1;
                                    }else{
                                        $division_check_buyer = 0;
                                    }

                                    $this->db->select('inv_id,approve'); 
                                    $this->db->from('tt_buyer_division');
                                    $this->db->where('inv_id',$invoice_id);
                                    $this->db->where('approve','A');
                                    $division_check=$this->db->get();
                                    $division_check_accept=$division_check->result();

                                    $fields1=array('bid'=>$updatedBid,'buyer'=>substr(@$getCom[0]->company_name,0,10),'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name,'inv_status'=>"I",'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);


                                }
                              
                            }

                            elseif(intval($getsecondHighestparice)==0 && intval($get_bid_max_price_buyer_id_final) !=$buyer_id && intval($max_price) > 0)
                            { 

                              
                                 $updatedBid1=$max_price+$increaseRate;
                                  if(intval($updatedBid1) > intval($chkgetMaxhbp))
                                    {
                                        $updatedBid1=$chkgetMaxhbp;
                                    }
                                    else
                                    {
                                        $updatedBid1=$max_price+$increaseRate;

                                    }

                                $maxHBPupdateBuyerwise=array('buyer_price'=>$updatedBid1);
                                     $decodeJson[$invoice_id]['bidMaxPrice']=trim($updatedBid1);
                                      $decodeJson[$invoice_id]['bidMaxbuyerId']=$get_bid_max_hbp_buyer_id_final;
                                  $decodeJson[$invoice_id]['buyer_bid'][$get_bid_max_hbp_buyer_id_final] =array(
                                            'bid'         => trim($updatedBid1),
                                            'bid_time'    => date('Y-m-d H:i:s'),
                                            'buyer_id'    =>$get_bid_max_hbp_buyer_id_final,
                                        );

                                        $mainArryEncode=json_encode($decodeJson);
                                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                        file_put_contents($file_to_save, $mainArryEncode);
                              /*  $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);
*/

                                $this->db->select('invoice_id,buyer_price');
                                $this->db->from(BID_DETAILS);
                                $this->db->where('invoice_id',$invoice_id);
                                $this->db->where('buyer_price >','0');
                                $division_check=$this->db->get();
                                $division_check_buyer=$division_check->result();

                                  if($decodeJson[$invoice_id]['bidMaxPrice'] > 0){
                                        $division_check_buyer = 1;
                                    }else{
                                        $division_check_buyer = 0;
                                    }


                                $this->db->select('inv_id,approve'); 
                                $this->db->from('tt_buyer_division');
                                $this->db->where('inv_id',$invoice_id);
                                $this->db->where('approve','A');
                                $division_check=$this->db->get();
                                $division_check_accept=$division_check->result();

                                $fields1=array('bid'=>$updatedBid1,'buyer'=>substr(@$getCom[0]->company_name,0,10),'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name,'inv_status'=>"I",'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);

                            }

                          
                            
                        }

                  /*  $this->db->select('buyer_id,invoice_id,buyer_price');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('buyer_id',$get_bid_max_hbp_buyer_id_final);
                    $this->db->where('invoice_id',$invoice_id);
                    $QrySold=$this->db->get();
                    $invSold=$QrySold->result();*/
                    $invSoldprice=$decodeJson[$invoice_id]['bidMaxPrice'];
/*
                     $this->db->select('MAX(buyer_price) as maxprice1');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('invoice_id',$invoice_id);
                    $bid_max=$this->db->get();
                    $getMaxprice=$bid_max->result();*/
                    $maxinvSoldprice=$decodeJson[$invoice_id]['bidMaxPrice'];

                 /*  $this->db->select('bd.invoice_id,bd.buyer_price,bd.buyer_id');
                   $this->db->from(BID_DETAILS.' bd');
                   $this->db->where('bd.invoice_id',$invoice_id);
                   $this->db->where('bd.buyer_price',$maxinvSoldprice);
                   $bid_max_buyer_id=$this->db->get();
                   $get_bid_max_buyer_id = $bid_max_buyer_id->result();*/
                   $maxinvSoldpricebuyerid=$decodeJson[$invoice_id]['bidMaxbuyerId'];


                    if(intval($maxinvSoldprice) >= intval($price_idea))
                        {

                            $this->db->select('id,company_name');
                            $this->db->from('tt_users');
                            $this->db->where('id',$maxinvSoldpricebuyerid);
                            $qry1=$this->db->get();
                            $getCom1=$qry1->result();

                          $this->db->where('inv_id', @$invoice_id);
                          $this->db->delete('tt_buyer_division');

                
                        $data_log = array(

                        'buyer_id' => $maxinvSoldpricebuyerid,
                        'seller_id' => @$ChkSheet['created_by'],
                        'invoice_id' => $invoice_id,
                        'sheet_id' => $sheet_id,
                        'buyer_price' => $maxinvSoldprice,
                        'seller_price' => $price_idea,
                         'bid_on' => date('Y-m-d H:i:s') ,
                        );
                        $this
                            ->db
                            ->insert(BID_LOG, $data_log);


                        $data_sold = array(
                            'inv_status' => 'A',
                            'sold_by' => $maxinvSoldpricebuyerid,
                            'sold_on' => date('Y-m-d')
                        );

                        $decodeJson[$invoice_id]['inv_status'] ='A';
                        $decodeJson[$invoice_id]['sold_by'] =$maxinvSoldpricebuyerid;
                        $decodeJson[$invoice_id]['sold_on'] =date('Y-m-d');

                  

                        $mainArryEncode=json_encode($decodeJson);
                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                        file_put_contents($file_to_save, $mainArryEncode);
                      /*  $this
                            ->db
                            ->where('invoice_id', $invoice_id);
                        $this
                            ->db
                            ->update(OFFER_INVOICE, $data_sold); */

                            $this->db->select('invoice_id,buyer_price');
                            $this->db->from(BID_DETAILS);
                            $this->db->where('invoice_id',$invoice_id);
                            $this->db->where('buyer_price >','0');
                            $division_check=$this->db->get();
                            $division_check_buyer=$division_check->result();
                           if($decodeJson[$invoice_id]['bidMaxPrice'] > 0){
                                $division_check_buyer = 1;
                            }else{
                                $division_check_buyer = 0;
                            }


                            $this->db->select('inv_id,approve'); 
                            $this->db->from('tt_buyer_division');
                            $this->db->where('inv_id',$invoice_id);
                            $this->db->where('approve','A');
                            $division_check=$this->db->get();
                            $division_check_accept=$division_check->result();

                           $fields1=array('bid'=>$maxinvSoldprice,'inv_status'=>"A",'bidMaxbuyerId'=>$maxinvSoldpricebuyerid,'price_idea'=>$price_idea,'buyerId'=>$maxinvSoldpricebuyerid,'buyer'=>substr(@$getCom1[0]->company_name,0,10),"buyerfull"=>@$getCom1[0]->company_name,'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);

                        }
                        elseif(intval($invSoldprice) > intval($max_price))
                        {
                           

                            $this->db->select('id,company_name');
                            $this->db->from('tt_users');
                            $this->db->where('id',$get_bid_max_hbp_buyer_id_final);
                            $qry3=$this->db->get();
                            $getCom3=$qry3->result();


                            $this->db->select('invoice_id,buyer_price');
                            $this->db->from(BID_DETAILS);
                            $this->db->where('invoice_id',$invoice_id);
                            $this->db->where('buyer_price >','0');
                            $division_check=$this->db->get();
                            $division_check_buyer=$division_check->result();

                            if($decodeJson[$invoice_id]['bidMaxPrice'] > 0){
                                $division_check_buyer = 1;
                            }else{
                                $division_check_buyer = 0;
                            }

                            $this->db->select('inv_id,approve'); 
                            $this->db->from('tt_buyer_division');
                            $this->db->where('inv_id',$invoice_id);
                            $this->db->where('approve','A');
                            $division_check=$this->db->get();
                            $division_check_accept=$division_check->result();

                           $fields1=array('bid'=>$invSoldprice,'inv_status'=>"I",'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'price_idea'=>$price_idea,'buyerId'=>$get_bid_max_hbp_buyer_id_final,'buyer'=>substr(@$getCom3[0]->company_name,0,10),"buyerfull"=>@$getCom3[0]->company_name,'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);


                              $data_log = array(

                            'buyer_id' => $get_bid_max_hbp_buyer_id_final,
                            'seller_id' => @$ChkSheet['created_by'],
                            'invoice_id' => $invoice_id,
                            'sheet_id' => $sheet_id,
                            'buyer_price' => $invSoldprice,
                            'seller_price' => $price_idea,
                             'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                            $this->db->where('inv_id', @$invoice_id);
                            $this->db->delete('tt_buyer_division');

                        }

                     $result = 1;

                }
                else
                {
                    $result = 7;
                }
                
            }

        }

       
        echo json_encode(array('result'=>$result,'hbp'=>@$prevPrice,'mainarray'=>@$fields1));
    }



    function place_final_lock_buyer()
    {
        //$firebase = $this->firebase->init();
        //$database = $firebase->getDatabase();
        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $max_price = $this
            ->input
            ->post('max_price');
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];

        $sheet_id = $this
            ->input
            ->post('sheet_id');

        $firebaseKey = $this
        ->input
        ->post('firebaseKey');

        $jsonFile   = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson = json_decode($jsonFile,true);
     
        $data_final_pi_msg_buyer = array(
            'final_pi_msg_buyer' => 'Y'
        );
        $decodeJson[$invoice_id]['buyer_bid'][$buyer_id]['final_pi_msg_buyer'] = 'Y';
       
        

       /* $this
            ->db
            ->where('buyer_id', $buyer_id);
        $this
            ->db
            ->update('tt_bid_details', $data_final_pi_msg_buyer);*/

        if ($max_price == '0')
        {
            $result = 2;
        }
        else
        {

           // @$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey.'/buyer_lock/'.$buyer_id)->set('Y');
            $data_update_buyer = array(
                'buyer_lock' => 'Y'
            );
            $decodeJson[$invoice_id]['buyer_bid'][$buyer_id]['buyer_lock'] = 'Y';
            $mainArryEncode1=json_encode($decodeJson);
            $file_to_save1 = 'public/uploads/' . $sheet_id . '.json';
            file_put_contents($file_to_save1, $mainArryEncode1);

          /*  $this
                ->db
                ->where('invoice_id', $invoice_id);
            $this
                ->db
                ->where('buyer_id', $buyer_id);
            $this
                ->db
                ->update(BID_DETAILS, $data_update_buyer);*/
            $result = 1;
        }

        echo json_encode($result);
    }
    function place_final_pi_seller()
    {
        // $firebase = $this->firebase->init();
       // $database = $firebase->getDatabase();
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        $seller_id = @$dtl['id'];
        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $sold_by = $this
            ->input
            ->post('sold_by');
        $sheet_id = $this
            ->input
            ->post('sheet_id');
        $firebaseKey = $this
            ->input
            ->post('firebaseKey');
        $fields1=array('seller_final_lock'=>'Y');

        $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);
         
        //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);
        $data_active = array(
            'seller_final_lock' => 'Y'
        );

        $decodeJson[$invoice_id]['seller_final_lock']= 'Y';
        $decodeJson[$invoice_id]['seller_lock_time']= date('Y-m-d H:i:s');

        $mainArryEncode=json_encode($decodeJson);
        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
        file_put_contents($file_to_save, $mainArryEncode);
     /*   $this
            ->db
            ->where('invoice_id', $invoice_id);
        $this
            ->db
            ->update(OFFER_INVOICE, $data_active);
*/
        $data_update_seller = array(
            'seller_lock' => 'Y',
            'seller_lock_time'=>date('Y-m-d H:i:s')
        );
      /*  $this
            ->db
            ->where('seller_id', $seller_id);
        $this
            ->db
            ->where('invoice_id', $invoice_id);
        $this
            ->db
            ->update(BID_DETAILS, $data_update_seller);
*/
        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from('tt_offer_sheets tos');
        $this
            ->db
            ->join('tt_users tu', 'tos.created_by = tu.id');
        $this
            ->db
            ->where('tos.sheet_id', $sheet_id);
        $supp_name_all = $this
            ->db
            ->get();
        $supp_name = $supp_name_all->result();

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from('tt_bid_details tbd');
        $this
            ->db
            ->join('tt_offer_sheets tos', 'tos.sheet_id = tbd.sheet_id');
        $this
            ->db
            ->join('tt_users tu', 'tu.id = tbd.buyer_id');
        $this
            ->db
            ->where('tbd.sheet_id', $sheet_id);
        $this
            ->db
            ->where('tbd.final_pi_msg', 'N');
        $this
            ->db
            ->group_by('tbd.buyer_id');
        $sheet = $this
            ->db
            ->get();
        $sheetList = $sheet->result();
        if (!empty($sheetList))
        {
            foreach ($sheetList as $msg)
            {

                $to = $msg->phone;

                $body = @$supp_name[0]->company_name . " has given final price on offer sheet " . $msg->sheet_no . "-" . $msg->sheet_name;

                //send_sms($to,$body);
                $data_active_main = array(
                    'final_pi_msg' => 'Y'
                );
             /*   $this
                    ->db
                    ->where('buyer_id', $msg->buyer_id);
                $this
                    ->db
                    ->update('tt_bid_details', $data_active_main);*/

            }

        }

        $result = 1;
        echo json_encode($result);
    }

     function place_price_idea_action()
    {
        //$firebase = $this->firebase->init();
       // $database = $firebase->getDatabase();
        
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        $seller_id = @$dtl['id'];
        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $my_price_idea = trim($this
            ->input
            ->post('my_price_idea'));
        $max_price = $this
            ->input
            ->post('max_price');
        $btnvalue = $this
            ->input
            ->post('btnvalue');
        $final_pi_id = $this
            ->input
            ->post('final_pi_id');
        $price_idea = trim($this
            ->input
            ->post('price_idea'));
        $sold_by = $this
            ->input
            ->post('sold_by');

        $sheet_id = trim($this
            ->input
            ->post('sheet_id'));

         $firebaseKey = trim($this
            ->input
            ->post('firebaseKey'));

        $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);

        $this->db->select('id,company_name');
        $this->db->from('tt_users');
        $this->db->where('id',$sold_by);
        $qry=$this->db->get();
        $getCom=$qry->result();


        $data_log = array(

            'seller_id' => $dtl['id'],

            'invoice_id' => $invoice_id,
            'sheet_id' => $sheet_id,
            'buyer_price' => $max_price,
            'seller_price' => $my_price_idea,

            'bid_on' => date('Y-m-d H:i:s') ,
        );
        $this
            ->db
            ->insert('tt_bid_log_seller', $data_log);

       /* $this->db->select('MAX(buyer_price) as maxprice');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$invoice_id);
        $bid_max=$this->db->get();
        $getMaxprice=$bid_max->result();*/
        $max_price= $decodeJson[$invoice_id]['bidMaxPrice'];

        if(intval($max_price) > 0)
        {
            $max_price = $max_price;
        }
        else
        {
            $max_price = 0;
        }
        //echo 'mp-'.$my_price_idea; echo  'btn-'.$btnvalue;  echo  'pi-'.$final_pi_id; echo  'pric-'.$price_idea;
        

        if ($my_price_idea != "" && $btnvalue != "A" && (intval($price_idea) > intval($my_price_idea) || $my_price_idea == intval(9999)))
        {

            if ($final_pi_id == 'N')
            {

                if (intval($max_price) >= intval($my_price_idea))
                {

               
     
                // $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);

                    $data_active = array(
                        'inv_status' => 'A',
                        'sold_by' => $sold_by,
                        'sold_on' => date('Y-m-d')
                    );
                   /* $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(OFFER_INVOICE, $data_active);*/

                    $decodeJson[$invoice_id]['inv_status']='A';
                    $decodeJson[$invoice_id]['sold_by']=$sold_by;
                    $decodeJson[$invoice_id]['sold_on']= date('Y-m-d');

                    $data_update = array(
                        'price_idea' => $my_price_idea
                    );
                    /*$this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(OFFER_INVOICE, $data_update);
*/


                    $decodeJson[$invoice_id]['price_idea']=$my_price_idea;
                    $decodeJson[$invoice_id]['seller_update']='Y';
                   
                    $data_update_seller = array(
                        'seller_price' => $my_price_idea,
                        'seller_update' => 'Y',
                         'bid_time'=>date('Y-m-d H:i:s')
                    );
                   /* $this
                        ->db
                        ->where('seller_id', $seller_id);
                    $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(BID_DETAILS, $data_update_seller);*/

                    if ($my_price_idea == intval(9999))
                    {
                      $fields2=array('seller_final_lock' => 'Y');

                      $decodeJson[$invoice_id]['seller_final_lock']='Y';
     
                       //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields2);

                        $data_lock = array(
                            'seller_final_lock' => 'Y'
                        );
                      /*  $this
                            ->db
                            ->where('invoice_id', $invoice_id);
                        $this
                            ->db
                            ->update(OFFER_INVOICE, $data_lock);*/

                    }

                    $mainArryEncode1=json_encode($decodeJson);
                    $file_to_save1 = 'public/uploads/' . $sheet_id . '.json';
                    file_put_contents($file_to_save1, $mainArryEncode1);

                    $this->db->select('invoice_id,seller_final_lock');
                    $this->db->from(OFFER_INVOICE);
                    $this->db->where('invoice_id',$invoice_id);
                    $qry1=$this->db->get();
                    $getSlock=$qry1->result();

                    $fields1=array('inv_status' => 'A','bid'=>$max_price,'buyer'=>substr(@$getCom[0]->company_name,0,10),'price_idea'=>$my_price_idea,'bidMaxbuyerId'=>$sold_by,'buyerId'=>$sold_by,"buyerfull"=>@$getCom[0]->company_name,'seller_final_lock'=>@$getSlock[0]->seller_final_lock);


                    $result = 1;

                   
                }
                else
                {
                   

                   
     
                   //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields3);
                    $data_update = array(
                        'price_idea' => $my_price_idea
                    );
                   /* $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(OFFER_INVOICE, $data_update);*/

                    $decodeJson[$invoice_id]['price_idea']=$my_price_idea;
                    $decodeJson[$invoice_id]['seller_update']='Y';

                    $data_update_seller = array(
                        'seller_price' => $my_price_idea,
                        'seller_update' => 'Y',
                        'bid_time'=>date('Y-m-d H:i:s')
                    );
                   /* $this
                        ->db
                        ->where('seller_id', $seller_id);
                    $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(BID_DETAILS, $data_update_seller);*/
                    if ($my_price_idea == intval(9999))
                    {
                    $fields4=array('seller_final_lock'=>'Y');
     
                      // $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields4);
                        $data_lock = array(
                            'seller_final_lock' => 'Y'
                        );
                        $decodeJson[$invoice_id]['seller_final_lock']='Y';
                       /* $this
                            ->db
                            ->where('invoice_id', $invoice_id);
                        $this
                            ->db
                            ->update(OFFER_INVOICE, $data_lock);*/

                    }

                     $mainArryEncode1=json_encode($decodeJson);
                    $file_to_save1 = 'public/uploads/' . $sheet_id . '.json';
                    file_put_contents($file_to_save1, $mainArryEncode1);

                    $this->db->select('invoice_id,seller_final_lock');
                    $this->db->from(OFFER_INVOICE);
                    $this->db->where('invoice_id',$invoice_id);
                    $qry1=$this->db->get();
                    $getSlock=$qry1->result();


                    $fields1=array('inv_status' => 'I','bid'=>$max_price,'buyer'=>substr(@$getCom[0]->company_name,0,10),'price_idea'=>$my_price_idea,'bidMaxbuyerId'=>$sold_by,'buyerId'=>$sold_by,"buyerfull"=>@$getCom[0]->company_name,'seller_final_lock'=>@$getSlock[0]->seller_final_lock);

                    $result = 1;

                }
            }
        }

        else
        {

            $result = 0;
        }
        echo json_encode(array('res'=>$result,'mainarray'=>$fields1));
    }

    function bkkkkplace_price_idea_action()
    {
        $firebase = $this->firebase->init();
        $database = $firebase->getDatabase();
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        $seller_id = @$dtl['id'];
        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $my_price_idea = trim($this
            ->input
            ->post('my_price_idea'));
        $max_price = $this
            ->input
            ->post('max_price');
        $btnvalue = $this
            ->input
            ->post('btnvalue');
        $final_pi_id = $this
            ->input
            ->post('final_pi_id');
        $price_idea = trim($this
            ->input
            ->post('price_idea'));
        $sold_by = $this
            ->input
            ->post('sold_by');

        $sheet_id = trim($this
            ->input
            ->post('sheet_id'));

         $firebaseKey = trim($this
            ->input
            ->post('firebaseKey'));

        $this->db->select('id,company_name');
        $this->db->from('tt_users');
        $this->db->where('id',$sold_by);
        $qry=$this->db->get();
        $getCom=$qry->result();


        $data_log = array(

            'seller_id' => $dtl['id'],

            'invoice_id' => $invoice_id,
            'sheet_id' => $sheet_id,
            'buyer_price' => $max_price,
            'seller_price' => $my_price_idea,

            'bid_on' => date('Y-m-d H:i:s') ,
        );
        $this
            ->db
            ->insert('tt_bid_log_seller', $data_log);

        $this->db->select('MAX(buyer_price) as maxprice');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$invoice_id);
        $bid_max=$this->db->get();
        $getMaxprice=$bid_max->result();
        $max_price=@$getMaxprice[0]->maxprice;
        //echo 'mp-'.$my_price_idea; echo  'btn-'.$btnvalue;  echo  'pi-'.$final_pi_id; echo  'pric-'.$price_idea;
        

        if ($my_price_idea != "" && $btnvalue != "A" && (intval($price_idea) > intval($my_price_idea) || $my_price_idea == intval(9999)))
        {

            if ($final_pi_id == 'N')
            {

                if (intval($max_price) >= intval($my_price_idea))
                {

                $fields1=array('inv_status' => 'A','bid'=>$max_price,'buyer'=>substr(@$getCom[0]->company_name,0,10),'price_idea'=>$my_price_idea,'bidMaxbuyerId'=>$sold_by,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name);
     
                 $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);

                    $data_active = array(
                        'inv_status' => 'A',
                        'sold_by' => $sold_by,
                        'sold_on' => date('Y-m-d')
                    );
                    $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(OFFER_INVOICE, $data_active);

                    $data_update = array(
                        'price_idea' => $my_price_idea
                    );
                    $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(OFFER_INVOICE, $data_update);

                    $data_update_seller = array(
                        'seller_price' => $my_price_idea,
                        'seller_update' => 'Y',
                         'bid_time'=>date('Y-m-d H:i:s')
                    );
                    $this
                        ->db
                        ->where('seller_id', $seller_id);
                    $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(BID_DETAILS, $data_update_seller);

                    if ($my_price_idea == intval(9999))
                    {
                      $fields2=array('seller_final_lock' => 'Y');
     
                       $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields2);

                        $data_lock = array(
                            'seller_final_lock' => 'Y'
                        );
                        $this
                            ->db
                            ->where('invoice_id', $invoice_id);
                        $this
                            ->db
                            ->update(OFFER_INVOICE, $data_lock);

                    }

                    $result = 1;

                }
                else
                {
                    $fields3=array('price_idea'=>$my_price_idea);
     
                   $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields3);
                    $data_update = array(
                        'price_idea' => $my_price_idea
                    );
                    $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(OFFER_INVOICE, $data_update);

                    $data_update_seller = array(
                        'seller_price' => $my_price_idea,
                        'seller_update' => 'Y',
                        'bid_time'=>date('Y-m-d H:i:s')
                    );
                    $this
                        ->db
                        ->where('seller_id', $seller_id);
                    $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(BID_DETAILS, $data_update_seller);
                    if ($my_price_idea == intval(9999))
                    {
                    $fields4=array('seller_final_lock'=>'Y');
     
                       $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields4);
                        $data_lock = array(
                            'seller_final_lock' => 'Y'
                        );
                        $this
                            ->db
                            ->where('invoice_id', $invoice_id);
                        $this
                            ->db
                            ->update(OFFER_INVOICE, $data_lock);

                    }

                    $result = 1;

                }
            }
        }

        else
        {

            $result = 0;
        }
        echo json_encode($result);
    }

    function place_comment_buyer_recieve()
    {
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];
        $comment = $this
            ->input
            ->post('comment');
        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $sheet_id = $this
            ->input
            ->post('sheet_id');

        /*$this->db->where('sheet_id',$sheet_id);
        $this->db->where('user_id',$buyer_id);
        $this->db->delete('tt_buyer_recieve_comment');*/

        for ($i = 0;$i < count($comment);$i++)
        {
            $this
                ->db
                ->select('*');
            $this
                ->db
                ->from('tt_buyer_recieve_comment');
            $this
                ->db
                ->where('invoice_id', $invoice_id[$i]);
            $this
                ->db
                ->where('user_id', $buyer_id[$i]);
            $qry = $this
                ->db
                ->get();
            $chkComment = $qry->result();

            if (empty($chkComment))
            {

                $data = array(
                    'comment' => $comment[$i],
                    'invoice_id' => $invoice_id[$i],
                    'user_id' => $buyer_id,
                    'sheet_id' => $sheet_id
                );

                $this
                    ->db
                    ->insert('tt_buyer_recieve_comment', $data);

            }

            else
            {

            }

        }
        $res = 1;
        echo json_encode($res);

    }

    function place_comment_buyer_individual()
    {
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $firebase = $this->firebase->init();
         $database = $firebase->getDatabase();
        $buyer_id = @$dtl['id'];
        $comment = trim($this
            ->input
            ->post('comment'));
        $invoice_id = $this
            ->input
            ->post('invoice_id');

        $sheet_id = $this
            ->input
            ->post('sheet_id');
        $firebaseKey=$this
            ->input
            ->post('firebaseKey');

        @$chk_comment = $this
            ->Common
            ->find(['table' => 'tt_buyer_recieve_comment', 'select' => "*", 'where' => "invoice_id = {$invoice_id} AND user_id = {$buyer_id}", 'query' => 'first']);

        $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey.'/buyer_comment/'.@$dtl['id'])->set(@$comment);
                   

        if (!empty(@$chk_comment))
        {
            $data_update = array(
                'comment' => $comment
            );

            
            $this
                ->db
                ->where('invoice_id', $invoice_id);
            $this
                ->db
                ->where('user_id', $buyer_id);
            $this
                ->db
                ->update('tt_buyer_recieve_comment', $data_update);
            $result = 1;
        }
        else
        {
            $data_insert = array(
                'comment' => $comment,
                'invoice_id' => $invoice_id,
                'user_id' => $buyer_id
            );

            if ($comment != "")
            {

                $this
                    ->db
                    ->insert('tt_buyer_recieve_comment', $data_insert);
                $result = 1;

            }
            else
            {
                $result = 0;

            }

        }
        echo json_encode($result);

    }

    function place_comment_buyer()
    {
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];
        $comment = trim($this
            ->input
            ->post('comment'));
        $invoice_id = $this
            ->input
            ->post('invoice_id');
        if ($comment != "")
        {
            $data_update = array(
                'comment_buyer' => $comment
            );
            $this
                ->db
                ->where('invoice_id', $invoice_id);
            $this
                ->db
                ->where('buyer_id', $buyer_id);
            $this
                ->db
                ->update(BID_DETAILS, $data_update);
            $result = 1;
        }
        else
        {
            $result = 0;
        }
        echo json_encode($result);

    }

    function place_comment_edit_action()
    {
        //$firebase = $this->firebase->init();
       // $database = $firebase->getDatabase();
        $firebaseKey=trim($this
            ->input
            ->post('firebaseKey'));
        $comment = trim($this
            ->input
            ->post('comment'));
        $invoice_id = $this
            ->input
            ->post('invoice_id');

        $sheet_id = $this
            ->input
            ->post('sheet_id');
        $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);
            
        if ($comment != "")
        {
           // $fields1=array('seller_comment'=>$comment);
         
            //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);
            $data_update = array(
                'comment' => $comment
            );

            $decodeJson[$invoice_id]['seller_comment']=$comment;
            $mainArryEncode1=json_encode($decodeJson);
            $file_to_save1 = 'public/uploads/' . $sheet_id . '.json';
            file_put_contents($file_to_save1, $mainArryEncode1);
           /* $this
                ->db
                ->where('invoice_id', $invoice_id);
            $this
                ->db
                ->update(OFFER_INVOICE, $data_update);*/
            $result = 1;
        }
        else
        {
            $result = 0;
        }
        echo json_encode($result);
    }

    function place_price_idea_edit_action()
    {
        //$firebase = $this->firebase->init();
        //$database = $firebase->getDatabase();

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        $seller_id = @$dtl['id'];
        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $my_price_idea = trim($this
            ->input
            ->post('my_price_idea'));
        $max_price = $this
            ->input
            ->post('max_price');
        $btnvalue = $this
            ->input
            ->post('btnvalue');
        $final_pi_id = $this
            ->input
            ->post('final_pi_id');
        $price_idea = trim($this
            ->input
            ->post('price_idea'));
        $sheet_id = trim($this
            ->input
            ->post('sheet_id'));

         $firebaseKey = trim($this
            ->input
            ->post('firebaseKey'));

        $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);

        
        $data_log = array(

            'seller_id' => $dtl['id'],

            'invoice_id' => $invoice_id,
            'sheet_id' => $sheet_id,
            'buyer_price' => $max_price,
            'seller_price' => $my_price_idea,

            'bid_on' => date('Y-m-d H:i:s') ,
        );
        $this
            ->db
            ->insert('tt_bid_log_seller', $data_log);

        //echo 'mp-'.$my_price_idea; echo  'btn-'.$btnvalue;  echo  'pi-'.$final_pi_id; echo  'pric-'.$price_idea;
        if ($my_price_idea != "" && (intval($price_idea) >= intval($my_price_idea) || $my_price_idea == intval(9999)))
        {

            if (intval($max_price) >= intval($my_price_idea))
            {
               
         
                //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);

                $data_active = array(
                    'inv_status' => 'A'
                );

                    $decodeJson[$invoice_id]['inv_status']='A';
                    $decodeJson[$invoice_id]['sold_by']=$decodeJson[$invoice_id]['bidMaxbuyerId'];
                    $decodeJson[$invoice_id]['sold_on']= date('Y-m-d');

             /*   $this
                    ->db
                    ->where('invoice_id', $invoice_id);
                $this
                    ->db
                    ->update(OFFER_INVOICE, $data_active);*/

                $data_update = array(
                    'price_idea' => $my_price_idea
                );
               /* $this
                    ->db
                    ->where('invoice_id', $invoice_id);
                $this
                    ->db
                    ->update(OFFER_INVOICE, $data_update);*/

                $data_update_seller = array(
                    'seller_price' => $my_price_idea
                );

                $decodeJson[$invoice_id]['price_idea']=$my_price_idea;
                   
              /*  $this
                    ->db
                    ->where('seller_id', $seller_id);
                $this
                    ->db
                    ->where('invoice_id', $invoice_id);
                $this
                    ->db
                    ->update(BID_DETAILS, $data_update_seller);*/

                if ($my_price_idea == intval(9999))
                {
                    $fields2=array('seller_final_lock'=>"Y");
         
                   //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields2);
                    $data_lock = array(
                        'seller_final_lock' => 'Y'
                    );
                    $decodeJson[$invoice_id]['seller_final_lock']='Y';
                  /*  $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(OFFER_INVOICE, $data_lock);*/

                }

                $mainArryEncode1=json_encode($decodeJson);
                $file_to_save1 = 'public/uploads/' . $sheet_id . '.json';
                 file_put_contents($file_to_save1, $mainArryEncode1);

                $this->db->select('invoice_id,seller_final_lock');
                $this->db->from(OFFER_INVOICE);
                $this->db->where('invoice_id',$invoice_id);
                $qry1=$this->db->get();
                $getSlock=$qry1->result();

                $fields1=array('price_idea'=>$my_price_idea,'inv_status'=>"A",'seller_final_lock'=>@$getSlock[0]->seller_final_lock);

                $result = 1;

            }
            else
            {
                //$fields3=array('price_idea'=>$my_price_idea);
         
               // $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields3);
                $data_update = array(
                    'price_idea' => $my_price_idea
                );

                $decodeJson[$invoice_id]['price_idea']=$my_price_idea;
               /* $this
                    ->db
                    ->where('invoice_id', $invoice_id);
                $this
                    ->db
                    ->update(OFFER_INVOICE, $data_update);*/

                $data_update_seller = array(
                    'seller_price' => $my_price_idea
                );
               /* $this
                    ->db
                    ->where('seller_id', $seller_id);
                $this
                    ->db
                    ->where('invoice_id', $invoice_id);
                $this
                    ->db
                    ->update(BID_DETAILS, $data_update_seller);
*/
                if ($my_price_idea == intval(9999))
                {
                   $fields4=array('seller_final_lock'=>"Y");

                    $decodeJson[$invoice_id]['seller_final_lock']='Y';
         
                   //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields4);
                    $data_lock = array(
                        'seller_final_lock' => 'Y'
                    );
                    /*$this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(OFFER_INVOICE, $data_lock);*/

                }

                 $mainArryEncode1=json_encode($decodeJson);
                $file_to_save1 = 'public/uploads/' . $sheet_id . '.json';
                 file_put_contents($file_to_save1, $mainArryEncode1);

                $this->db->select('invoice_id,seller_final_lock');
                $this->db->from(OFFER_INVOICE);
                $this->db->where('invoice_id',$invoice_id);
                $qry1=$this->db->get();
                $getSlock=$qry1->result();

                $fields1=array('price_idea'=>$my_price_idea,'inv_status'=>"I",'seller_final_lock'=>@$getSlock[0]->seller_final_lock);
                $result = 1;
            }
        }

        else
        {

            $result = 0;
        }
        echo json_encode(array('res'=>$result,'mainarray'=>$fields1));
    }

    function bkkkkkplace_price_idea_edit_action()
    {
        $firebase = $this->firebase->init();
        $database = $firebase->getDatabase();

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        $seller_id = @$dtl['id'];
        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $my_price_idea = trim($this
            ->input
            ->post('my_price_idea'));
        $max_price = $this
            ->input
            ->post('max_price');
        $btnvalue = $this
            ->input
            ->post('btnvalue');
        $final_pi_id = $this
            ->input
            ->post('final_pi_id');
        $price_idea = trim($this
            ->input
            ->post('price_idea'));
        $sheet_id = trim($this
            ->input
            ->post('sheet_id'));

         $firebaseKey = trim($this
            ->input
            ->post('firebaseKey'));

        
        $data_log = array(

            'seller_id' => $dtl['id'],

            'invoice_id' => $invoice_id,
            'sheet_id' => $sheet_id,
            'buyer_price' => $max_price,
            'seller_price' => $my_price_idea,

            'bid_on' => date('Y-m-d H:i:s') ,
        );
        $this
            ->db
            ->insert('tt_bid_log_seller', $data_log);

        //echo 'mp-'.$my_price_idea; echo  'btn-'.$btnvalue;  echo  'pi-'.$final_pi_id; echo  'pric-'.$price_idea;
        if ($my_price_idea != "" && (intval($price_idea) >= intval($my_price_idea) || $my_price_idea == intval(9999)))
        {

            if (intval($max_price) >= intval($my_price_idea))
            {
                $fields1=array('price_idea'=>$my_price_idea,'inv_status'=>"A");
         
                $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);

                $data_active = array(
                    'inv_status' => 'A'
                );
                $this
                    ->db
                    ->where('invoice_id', $invoice_id);
                $this
                    ->db
                    ->update(OFFER_INVOICE, $data_active);

                $data_update = array(
                    'price_idea' => $my_price_idea
                );
                $this
                    ->db
                    ->where('invoice_id', $invoice_id);
                $this
                    ->db
                    ->update(OFFER_INVOICE, $data_update);

                $data_update_seller = array(
                    'seller_price' => $my_price_idea
                );
                $this
                    ->db
                    ->where('seller_id', $seller_id);
                $this
                    ->db
                    ->where('invoice_id', $invoice_id);
                $this
                    ->db
                    ->update(BID_DETAILS, $data_update_seller);

                if ($my_price_idea == intval(9999))
                {
                    $fields2=array('seller_final_lock'=>"Y");
         
                   $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields2);
                    $data_lock = array(
                        'seller_final_lock' => 'Y'
                    );
                    $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(OFFER_INVOICE, $data_lock);

                }

                $result = 1;

            }
            else
            {
                $fields3=array('price_idea'=>$my_price_idea);
         
                $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields3);
                $data_update = array(
                    'price_idea' => $my_price_idea
                );
                $this
                    ->db
                    ->where('invoice_id', $invoice_id);
                $this
                    ->db
                    ->update(OFFER_INVOICE, $data_update);

                $data_update_seller = array(
                    'seller_price' => $my_price_idea
                );
                $this
                    ->db
                    ->where('seller_id', $seller_id);
                $this
                    ->db
                    ->where('invoice_id', $invoice_id);
                $this
                    ->db
                    ->update(BID_DETAILS, $data_update_seller);

                if ($my_price_idea == intval(9999))
                {
                   $fields4=array('seller_final_lock'=>"Y");
         
                   $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields4);
                    $data_lock = array(
                        'seller_final_lock' => 'Y'
                    );
                    $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(OFFER_INVOICE, $data_lock);

                }
                $result = 1;
            }
        }

        else
        {

            $result = 0;
        }
        echo json_encode($result);
    }

    function check_same_invoice()
    {
        $new_grade = $this
            ->input
            ->post('new_grade');
        $new_garden = $this
            ->input
            ->post('new_garden');
        $new_invoice = $this
            ->input
            ->post('new_invoice');
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);

        $buyer_id = @$dtl['id'];

        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $price_idea = $this
            ->input
            ->post('price_idea');
        $key = $this
            ->input
            ->post('key');
             $my_price = $this
            ->input
            ->post('my_price');
        $max_price = $this
            ->input
            ->post('max_price');
        $sheet_id = $this
            ->input
            ->post('sheet_id');
        $final_pi_id = $this
            ->input
            ->post('final_pi_id');

        $firebaseKey = $this
            ->input
            ->post('firebaseKey');




        $date = date("Y-m-d H:i:s");
        $six_month = date('Y-m-d H:i:s', strtotime($date . ' - 180 days'));
       /* $this
            ->db
            ->select('*');
        $this
            ->db
            ->from('tt_bid_details tbd');
        $this
            ->db
            ->join('tt_offer_invoice toi', 'toi.sheet_id=tbd.sheet_id');
        $this
            ->db
            ->join('tt_offer_sheets tos', 'tos.sheet_id=tbd.sheet_id');
        $this
            ->db
            ->like('toi.garden', $new_garden);
        $this
            ->db
            ->like('toi.grade', $new_grade);
        $this
            ->db
            ->like('toi.invoice', $new_invoice);
        $this
            ->db
            ->where('tos.expire', 'Y');
        //$this->db->where('tos.expiry_date <',$date);
        //$this->db->where('tos.expiry_date >',$six_month);
        $this
            ->db
            ->where('tbd.buyer_id', @$dtl['id']);
        $qry = $this
            ->db
            ->get();
        $chk_inv = $qry->result();*/

        $chk_inv=array(); ////////////////SET FOR  1 WEEK

        if (!empty($chk_inv))
        {
            $res = 1;
        }
        else
        {
           $this->place_my_bid_now($key,$buyer_id,$invoice_id,$price_idea,$my_price,$max_price,$sheet_id,$final_pi_id,$firebaseKey);
            $res = 2;

        }

        echo json_encode(array(
            'res' => $res,
            'inv_no' => @$chk_inv[0]->sheet_no,
            'sheet_id' => encrypt(@$chk_inv[0]->sheet_id)
        ));
    }

/*    function BKKplace_my_bid_now($key,$buyer_id,$invoice_id,$price_idea,$my_price,$max_price,$sheet_id,$final_pi_id,$firebaseKey)
    {
        $firebase = $this->firebase->init();
        $database = $firebase->getDatabase();

        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);

        $buyer_id = @$dtl['id'];
        $company_name = @$dtl['company'];

        $this->db->select('bd.user_id,us.id,bd.invoice_id,bd.comment');
        $this->db->from("tt_buyer_recieve_comment".' bd');
        $this->db->join(USERS.' us','us.id = bd.user_id');
        $this->db->where('bd.invoice_id',$invoice_id);
        $this->db->where('bd.user_id',$buyer_id);
        $bid_comment=$this->db->get();
        $bid_comment_buyer=$bid_comment->result();

        $this->db->select('MAX(buyer_hbp) as maxhbp');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$invoice_id);
        $bid_max=$this->db->get();
        $getMaxhbp=$bid_max->result();
        $chkgetMaxhbp=@$getMaxhbp[0]->maxhbp;

       

        $this->db->select('seller_final_lock,invoice_id,price_idea');
        $this->db->from('tt_offer_invoice');
        $this->db->where('invoice_id',$invoice_id);
        $getvl=$this->db->get();
        $getprcid=$getvl->result();
        $price_idea=@$getprcid[0]->price_idea;


        $ChkSheet = $this
            ->Common
            ->findBy(OFFER_SHEET, 'sheet_id', $sheet_id);
        $now = date('d-m-Y H:i:s');
        $currentDateTime = strtotime(@$now);
        @$expiry_date = date("d-m-Y H:i:s", strtotime($ChkSheet['expiry_date']));
        @$newEXPDate = @strtotime(@$expiry_date);

        $increaseRate=@$ChkSheet['bidding_gap'];
        $this->db->select('MAX(buyer_price) as maxprice');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$invoice_id);
        $bid_max=$this->db->get();
        $getMaxprice=$bid_max->result();
        $chkgetMaxprice=@$getMaxprice[0]->maxprice;

       

       

        $this->db->select('bd.invoice_id,bd.buyer_hbp,bd.buyer_id');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->where('bd.invoice_id',$invoice_id);
        $this->db->where('bd.buyer_hbp',$chkgetMaxhbp);
        $bid_max_hbp_buyer_id=$this->db->get();
        $get_bid_max_hbp_buyer_id = $bid_max_hbp_buyer_id->result();
        $get_bid_max_hbp_buyer_id_final=@$get_bid_max_hbp_buyer_id[0]->buyer_id;

        $this->db->select('id,company_name');
        $this->db->from('tt_users');
        $this->db->where('id',$get_bid_max_hbp_buyer_id_final);
        $qry=$this->db->get();
        $getCom=$qry->result();


        $this->db->select('buyer_id,invoice_id,buyer_hbp');
        $this->db->from(BID_DETAILS);
        $this->db->where('buyer_id',$buyer_id);
        $this->db->where('invoice_id',$invoice_id);
        $Qry=$this->db->get();
        $chkMyhbp=$Qry->result();

        


     

       if(intval($my_price) > intval(@$chkMyhbp[0]->buyer_hbp))
        {

            $data_HBP = array(
            'buyer_hbp' => 0,
           
        );
        $this
            ->db
            ->where('invoice_id', $invoice_id);
        $this
        ->db
        ->where('buyer_id', $buyer_id);
        $this
            ->db
            ->update(BID_DETAILS, $data_HBP);

        }


        if(intval($my_price) > intval($max_price) && intval($my_price) > intval($chkgetMaxprice)){
        
           if (intval($my_price) >= intval($price_idea) && $final_pi_id == 'N')
                {
                    
                    @$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey.'/buyer_bid/'.$buyer_id)->set(trim($my_price));

                     $data_update = array(
                        'buyer_price' => $my_price,
                        'message' => 'N',
                        'message_30' => 'N',
                        'bid_time' => date('Y-m-d H:i:s'),
                        
                    );

                    $data_update1 = array(
                        'buyer_price' => $my_price,
                        'message' => 'N',
                        'message_30' => 'N',
                        'bid_time' => date('Y-m-d H:i:s'),
                        'buyer_id'=>$buyer_id,
                        'sheet_id'=>$sheet_id,
                        'invoice_id'=>$invoice_id,
                        'seller_id'=>@$ChkSheet['created_by'],
                        'seller_price'=>$price_idea,
                       
                    );

                    $this->db->select('*');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('buyer_id',$buyer_id);
                    $this->db->where('invoice_id',$invoice_id);
                    $Qry=$this->db->get();
                    $chkMyinv=$Qry->result();

                    if($chkgetMaxhbp ==0)
                    {


                        $fields=array('inv_status'=>'A','bid'=>$my_price,'buyer'=>substr($company_name,0,10),'buyer_lock'=>$final_pi_id,'price_idea'=>$price_idea,'bidMaxbuyerId'=>$buyer_id,'buyerId'=>$buyer_id,"buyerfull"=>$company_name,'seller_final_lock'=>@$getprcid[0]->seller_final_lock);
       
          
                        $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields);

                        if(empty($chkMyinv))
                        {
                            $this->db->insert(BID_DETAILS,$data_update1);
                        }
                        else
                        {
                            $this
                            ->db
                            ->where('buyer_id', $buyer_id);
                            $this
                            ->db
                            ->where('invoice_id', $invoice_id);
                            $this
                            ->db
                            ->update(BID_DETAILS, $data_update);

                        }

                         $data_log = array(

                            'buyer_id' => $buyer_id,
                            'seller_id' => @$ChkSheet['created_by'],
                            'invoice_id' => $invoice_id,
                            'sheet_id' => $sheet_id,
                            'buyer_price' => $my_price,
                            'seller_price' => $price_idea,

                            'bid_on' => date('Y-m-d H:i:s') ,
                        );
                        $this
                            ->db
                            ->insert(BID_LOG, $data_log);

                    }

                    if($chkgetMaxhbp !=0)
                    {
                       
                        
                        if(intval($chkgetMaxhbp) > intval($my_price) && @$get_bid_max_hbp_buyer_id_final != $buyer_id)
                        {
                                $updatedBid=$my_price+$increaseRate;
                                if(intval($updatedBid) > intval($chkgetMaxhbp))
                                {
                                    $updatedBid=$chkgetMaxhbp;
                                }
                                else
                                {
                                    $updatedBid=$my_price+$increaseRate;

                                }

                                $fields1=array('bid'=>$updatedBid,'buyer'=>substr(@$getCom[0]->company_name,0,10),'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name);
         
                                $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);
                                $maxHBPupdateBuyerwise=array('buyer_price'=>$updatedBid);
                                $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);


                                 $data_log7 = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,
                                'bid_on' => date('Y-m-d H:i:s') ,
                                );
                                  $this->db->insert(BID_LOG, $data_log7);



                                $data_log = array(

                                'buyer_id' => $get_bid_max_hbp_buyer_id_final,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $updatedBid,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                        }
                        elseif(intval($chkgetMaxhbp) == intval($my_price))
                        {
                            $this->db->select('bd.invoice_id,bd.buyer_hbp,bd.buyer_id,tus.id,tus.company_name');
                            $this->db->from(BID_DETAILS.' bd');
                            $this->db->join('tt_users tus','tus.id=bd.buyer_id');
                            $this->db->where('bd.invoice_id',$invoice_id);
                            $this->db->where('bd.buyer_hbp',$chkgetMaxhbp);
                            $bid_max_hbp_buyer_id1=$this->db->get();
                            $get_bid_max_hbp_buyer_id1 = $bid_max_hbp_buyer_id1->result();
                            $get_bid_max_hbp_buyer_id_final1=@$get_bid_max_hbp_buyer_id1[0]->buyer_id;

                            $this->db->select('id,company_name');
                            $this->db->from('tt_users');
                            $this->db->where('id',$get_bid_max_hbp_buyer_id_final1);
                            $qry=$this->db->get();
                            $getCom1=$qry->result();

                            $fields1=array('bid'=>$my_price,'buyer'=>substr(@$getCom1[0]->company_name,0,10),'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final1,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom1[0]->company_name);
         
                            $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);

                               $maxHBPupdateBuyerwise=array('buyer_price'=>$my_price);
                                $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final1);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);

                                 $data_log7 = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,
                                'bid_on' => date('Y-m-d H:i:s') ,
                                );
                                  $this->db->insert(BID_LOG, $data_log7);

                                 $data_log = array(

                                'buyer_id' => $get_bid_max_hbp_buyer_id_final1,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);
                        }

                        else
                        {

                             $fields=array('inv_status'=>'A','bid'=>$my_price,'buyer'=>substr($company_name,0,10),'buyer_lock'=>$final_pi_id,'price_idea'=>$price_idea,'bidMaxbuyerId'=>$buyer_id,'buyerId'=>$buyer_id,"buyerfull"=>$company_name,'seller_final_lock'=>@$getprcid[0]->seller_final_lock);
       
          
                            $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields);
                            if(empty($chkMyinv))
                            {
                                $this->db->insert(BID_DETAILS,$data_update1);
                            }
                            else
                            {
                                $this
                                ->db
                                ->where('buyer_id', $buyer_id);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $data_update);

                            }

                         $data_log = array(

                            'buyer_id' => $buyer_id,
                            'seller_id' => @$ChkSheet['created_by'],
                            'invoice_id' => $invoice_id,
                            'sheet_id' => $sheet_id,
                            'buyer_price' => $my_price,
                            'seller_price' => $price_idea,

                            'bid_on' => date('Y-m-d H:i:s') ,
                        );
                        $this
                            ->db
                            ->insert(BID_LOG, $data_log);
                        }
                    }
            
                   

                    $this
                        ->db
                        ->where('inv_id', @$invoice_id);
                    //$this->db->where('approve','P');
                    $this
                        ->db
                        ->delete('tt_buyer_division');


                $this->db->select('MAX(buyer_price) as maxprice1');
                $this->db->from(BID_DETAILS);
                $this->db->where('invoice_id',$invoice_id);
                $bid_max=$this->db->get();
                $getMaxprice=$bid_max->result();
                $chkgetMaxprice=@$getMaxprice[0]->maxprice1;

               $this->db->select('bd.invoice_id,bd.buyer_price,bd.buyer_id');
               $this->db->from(BID_DETAILS.' bd');
               $this->db->where('bd.invoice_id',$invoice_id);
               $this->db->where('bd.buyer_price',$chkgetMaxprice);
               $bid_max_buyer_id=$this->db->get();
               $get_bid_max_buyer_id = $bid_max_buyer_id->result();

                if(intval($chkgetMaxhbp) == intval($my_price))
                  {
                    $getMaxbuyerId=@$get_bid_max_hbp_buyer_id_final;
                  }
                  else
                  {
                    $getMaxbuyerId=@$get_bid_max_buyer_id[0]->buyer_id;
                  }

                   $fields15=array('inv_status'=>'A','bid'=>$chkgetMaxprice,'bidMaxbuyerId'=>$getMaxbuyerId,'buyerId'=>$buyer_id);
       
          
                   $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields15);

                    $data_sold = array(
                        'inv_status' => 'A',
                        'sold_by' => $getMaxbuyerId,
                        'sold_on' => date('Y-m-d')
                    );
                    $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(OFFER_INVOICE, $data_sold);


                   

                }
                else
                {


                    @$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey.'/buyer_bid/'.$buyer_id)->set(trim($my_price));
                    $data_update = array(
                        'buyer_price' => $my_price,
                        'message' => 'N',
                        'message_30' => 'N',
                        'bid_time' => date('Y-m-d H:i:s'),
                       
                    );

                    $this->db->select('buyer_id,invoice_id');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('buyer_id',$buyer_id);
                    $this->db->where('invoice_id',$invoice_id);
                    $Qry=$this->db->get();
                    $chkMyinv=$Qry->result();

                    $data_update1 = array(
                        'buyer_price' => $my_price,
                        'message' => 'N',
                        'message_30' => 'N',
                        'bid_time' => date('Y-m-d H:i:s'),
                        'buyer_id'=>$buyer_id,
                        'sheet_id'=>$sheet_id,
                        'invoice_id'=>$invoice_id,
                        'seller_id'=>@$ChkSheet['created_by'],
                        'seller_price'=>$price_idea,
                       
                    );

                    if($chkgetMaxhbp ==0)
                    {
                        $fields15=array('inv_status'=>'I','bid'=>$my_price,'buyer'=>substr($company_name,0,10),'buyer_lock'=>$final_pi_id,'price_idea'=>$price_idea,'bidMaxbuyerId'=>$buyer_id,'buyerId'=>$buyer_id,"buyerfull"=>$company_name,'seller_final_lock'=>@$getprcid[0]->seller_final_lock);
       
          
                        $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields15);

                             if(empty($chkMyinv))
                            {
                                $this->db->insert(BID_DETAILS,$data_update1);
                            }
                            else
                            {
                                 $this
                                ->db
                                ->where('buyer_id', $buyer_id);
                              $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                              $this
                                ->db
                                ->update(BID_DETAILS, $data_update);

                            }


                            $data_log = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                    }

               


                   
                    if($chkgetMaxhbp !=0)
                    {


                         
                        if(intval($chkgetMaxhbp) > intval($my_price) && @$get_bid_max_hbp_buyer_id_final != $buyer_id)
                        {

                                $updatedBid=$my_price+$increaseRate;
                                if(intval($updatedBid) > intval($chkgetMaxhbp))
                                {
                                    $updatedBid=$chkgetMaxhbp;
                                }
                                else
                                {
                                    $updatedBid=$my_price+$increaseRate;

                                }
                                $fields5=array('bid'=>$updatedBid,'buyer'=>substr(@$getCom[0]->company_name,0,10),'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name);
         
                                $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields5);

                                $maxHBPupdateBuyerwise=array('buyer_price'=>$updatedBid);
                                $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);

                                 $data_log7 = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,
                                'bid_on' => date('Y-m-d H:i:s') ,
                                );

                                 $this->db->insert(BID_LOG, $data_log7);

                                $data_log = array(

                                'buyer_id' => $get_bid_max_hbp_buyer_id_final,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $updatedBid,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                            if(intval($updatedBid) >= intval($price_idea))
                            {
                                $fields6=array("inv_status"=>"A");
         
                                $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields6);
                                $data_sold = array(
                                    'inv_status' => 'A',
                                    'sold_by' => $get_bid_max_hbp_buyer_id_final,
                                    'sold_on' => date('Y-m-d')
                                );
                                $this
                                    ->db
                                    ->where('invoice_id', $invoice_id);
                                $this
                                    ->db
                                    ->update(OFFER_INVOICE, $data_sold);

                            }

                        }
                        elseif(intval($chkgetMaxhbp) == intval($my_price))
                        {

                            $this->db->select('bd.invoice_id,bd.buyer_hbp,bd.buyer_id,tus.id,tus.company_name');
                            $this->db->from(BID_DETAILS.' bd');
                            $this->db->join("tt_users tus",'tus.id=bd.buyer_id');
                            $this->db->where('bd.invoice_id',$invoice_id);
                            $this->db->where('bd.buyer_hbp',$chkgetMaxhbp);
                            $bid_max_hbp_buyer_id1=$this->db->get();
                            $get_bid_max_hbp_buyer_id1 = $bid_max_hbp_buyer_id1->result();
                            $get_bid_max_hbp_buyer_id_final1=@$get_bid_max_hbp_buyer_id1[0]->buyer_id;

                            $this->db->select('id,company_name');
                            $this->db->from('tt_users');
                            $this->db->where('id',$get_bid_max_hbp_buyer_id_final1);
                            $qry=$this->db->get();
                            $getCom1=$qry->result();

                             $fields1=array('bid'=>$my_price,'buyer'=>substr(@$getCom1[0]->company_name,0,10),'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final1,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom1[0]->company_name);
         
                                $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields1);

                               $maxHBPupdateBuyerwise=array('buyer_price'=>$my_price);
                                $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final1);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);

                                $data_log7 = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,
                                'bid_on' => date('Y-m-d H:i:s') ,
                                );

                                 $this->db->insert(BID_LOG, $data_log7);

                                $data_log = array(

                                'buyer_id' => $get_bid_max_hbp_buyer_id_final1,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                            if(intval($my_price) >= intval($price_idea))
                            {
                                $fields3=array('inv_status'=>"A");
         
                                $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields3);
                                $data_sold = array(
                                    'inv_status' => 'A',
                                    'sold_by' => $get_bid_max_hbp_buyer_id_final1,
                                    'sold_on' => date('Y-m-d')
                                );
                                $this
                                    ->db
                                    ->where('invoice_id', $invoice_id);
                                $this
                                    ->db
                                    ->update(OFFER_INVOICE, $data_sold);

                            }
                        }
                        else
                        {

                            $fields17=array('inv_status'=>'I','bid'=>$my_price,'buyer'=>substr($company_name,0,10),'buyer_lock'=>$final_pi_id,'price_idea'=>$price_idea,'bidMaxbuyerId'=>$buyer_id,'buyerId'=>$buyer_id,"buyerfull"=>$company_name,'seller_final_lock'=>@$getprcid[0]->seller_final_lock);
       
          
                            $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields17);

                            if(empty($chkMyinv))
                            {
                                $this->db->insert(BID_DETAILS,$data_update1);
                            }
                            else
                            {
                                 $this
                                ->db
                                ->where('buyer_id', $buyer_id);
                              $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                              $this
                                ->db
                                ->update(BID_DETAILS, $data_update);

                            }

                           


                            $data_log = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                        }
                    }

                    $this
                        ->db
                        ->where('inv_id', @$invoice_id);
                    //$this->db->where('approve','P');
                    $this
                        ->db
                        ->delete('tt_buyer_division');
                }
            }



        

        $this->db->select('invoice_id,buyer_price');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$invoice_id);
        $this->db->where('buyer_price >','0');
        $division_check=$this->db->get();
        $division_check_buyer=$division_check->result();

        $this->db->select('inv_id,approve'); 
        $this->db->from('tt_buyer_division');
        $this->db->where('inv_id',$invoice_id);
        $this->db->where('approve','A');
        $division_check=$this->db->get();
        $division_check_accept=$division_check->result();

        $fields8=array('division_check_accept'=>count($division_check_accept),'division_check_buyer'=>count($division_check_buyer));
         
        $database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields8); 
    }
*/

function place_my_bid_now()
    {
        $new_grade = $this
            ->input
            ->post('new_grade');
        $new_garden = $this
            ->input
            ->post('new_garden');
        $new_invoice = $this
            ->input
            ->post('new_invoice');
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);

        $buyer_id = @$dtl['id'];

        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $price_idea = $this
            ->input
            ->post('price_idea');
        $key = $this
            ->input
            ->post('key');
             $my_price = $this
            ->input
            ->post('my_price');
        $max_price = $this
            ->input
            ->post('max_price');
        $sheet_id = $this
            ->input
            ->post('sheet_id');
        $final_pi_id = $this
            ->input
            ->post('final_pi_id');

        $firebaseKey = $this
            ->input
            ->post('firebaseKey');

        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);

        $buyer_id = @$dtl['id'];
        $company_name = @$dtl['company'];

        $jsonFile   = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson = json_decode($jsonFile,true);

        $this->db->select('bd.user_id,us.id,bd.invoice_id,bd.comment');
        $this->db->from("tt_buyer_recieve_comment".' bd');
        $this->db->join(USERS.' us','us.id = bd.user_id');
        $this->db->where('bd.invoice_id',$invoice_id);
        $this->db->where('bd.user_id',$buyer_id);
        $bid_comment=$this->db->get();
        $bid_comment_buyer=$bid_comment->result();

      /*  $this->db->select('MAX(buyer_hbp) as maxhbp');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$invoice_id);
        $bid_max=$this->db->get();
        $getMaxhbp=$bid_max->result();*/
        $chkgetMaxhbp=$decodeJson[$invoice_id]['abpone'];

       

      /*  $this->db->select('seller_final_lock,invoice_id,price_idea');
        $this->db->from('tt_offer_invoice');
        $this->db->where('invoice_id',$invoice_id);
        $getvl=$this->db->get();
        $getprcid=$getvl->result();*/
        $price_idea=$decodeJson[$invoice_id]['price_idea'];

 
        $ChkSheet = $this
            ->Common
            ->findBy(OFFER_SHEET, 'sheet_id', $sheet_id);
        $now = date('d-m-Y H:i:s');
        $currentDateTime = strtotime(@$now);
        @$expiry_date = date("d-m-Y H:i:s", strtotime($ChkSheet['expiry_date']));
        @$newEXPDate = @strtotime(@$expiry_date);

        $increaseRate=@$ChkSheet['bidding_gap'];
       /* $this->db->select('MAX(buyer_price) as maxprice');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$invoice_id);
        $bid_max=$this->db->get();
        $getMaxprice=$bid_max->result();*/
        $chkgetMaxprice=$decodeJson[$invoice_id]['bidMaxPrice'];
       

       /* $this->db->select('bd.invoice_id,bd.buyer_hbp,bd.buyer_id');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->where('bd.invoice_id',$invoice_id);
        $this->db->where('bd.buyer_hbp',$chkgetMaxhbp);
        $bid_max_hbp_buyer_id=$this->db->get();
        $get_bid_max_hbp_buyer_id = $bid_max_hbp_buyer_id->result();*/
        $get_bid_max_hbp_buyer_id_final=$decodeJson[$invoice_id]['buyerone'];

        $this->db->select('id,company_name');
        $this->db->from('tt_users');
        $this->db->where('id',$get_bid_max_hbp_buyer_id_final);
        $qry=$this->db->get();
        $getCom=$qry->result();


        $this->db->select('buyer_id,invoice_id,buyer_hbp');
        $this->db->from(BID_DETAILS);
        $this->db->where('buyer_id',$buyer_id);
        $this->db->where('invoice_id',$invoice_id);
        $Qry=$this->db->get();
        $chkMyhbp=$Qry->result();

       if(intval($my_price) > @$decodeJson[$invoice_id]['buyer_hbp'][$buyer_id]['abp'] && @$decodeJson[$invoice_id]['buyer_hbp'][$buyer_id]['abp'] != ""){
            $data_HBP = array(
            'buyer_hbp' => 0,
        );
        @$decodeJson[$invoice_id]['buyer_hbp'][$buyer_id]['abp'] = 0;
        $mainArryEncode1=json_encode($decodeJson);
        $file_to_save1 = 'public/uploads/' . $sheet_id . '.json';
        file_put_contents($file_to_save1, $mainArryEncode1);
                    
      /*  $this
            ->db
            ->where('invoice_id', $invoice_id);
        $this
        ->db
        ->where('buyer_id', $buyer_id);
        $this
            ->db
            ->update(BID_DETAILS, $data_HBP);*/

        }

        if(intval($my_price) > intval($max_price) && intval($my_price) > intval($chkgetMaxprice)){
        
           if (intval($my_price) >= intval($price_idea) && $final_pi_id == 'N'){

                    $data_update = array(
                        'buyer_price' => $my_price,
                        'message' => 'N',
                        'message_30' => 'N',
                        'bid_time' => date('Y-m-d H:i:s'),                   
                    );

                    $data_update1 = array(
                        'buyer_price' => $my_price,
                        'message' => 'N',
                        'message_30' => 'N',
                        'bid_time' => date('Y-m-d H:i:s'),
                        'buyer_id'=>$buyer_id,
                        'sheet_id'=>$sheet_id,
                        'invoice_id'=>$invoice_id,
                        'seller_id'=>@$ChkSheet['created_by'],
                        'seller_price'=>$price_idea,
                       
                    );
                    $this->db->select('buyer_id,invoice_id');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('buyer_id',$buyer_id);
                    $this->db->where('invoice_id',$invoice_id);
                    $Qry=$this->db->get();
                    $chkMyinv=$Qry->result();

                    if($chkgetMaxhbp ==0){
                        $decodeJson[$invoice_id]['bidMaxbuyerId'] = $buyer_id;
                        $decodeJson[$invoice_id]['bidMaxPrice'] = $my_price;
                        $decodeJson[$invoice_id]['buyer_bid'][$buyer_id] = array(
                                            'bid'      => $my_price,
                                            'buyer_id' => $buyer_id,
                                            'bid_time' => date('Y-m-d H:i:s'),
                                        );

                     
                       
                        $mainArryEncode=json_encode($decodeJson);
                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                        file_put_contents($file_to_save, $mainArryEncode);

                        if(empty($chkMyinv)){
                            //$this->db->insert(BID_DETAILS,$data_update1);
                        }
                        else{
                           /* $this
                            ->db
                            ->where('buyer_id', $buyer_id);
                            $this
                            ->db
                            ->where('invoice_id', $invoice_id);
                            $this
                            ->db
                            ->update(BID_DETAILS, $data_update);*/
                        }

                         $data_log = array(

                            'buyer_id' => $buyer_id,
                            'seller_id' => @$ChkSheet['created_by'],
                            'invoice_id' => $invoice_id,
                            'sheet_id' => $sheet_id,
                            'buyer_price' => $my_price,
                            'seller_price' => $price_idea,

                            'bid_on' => date('Y-m-d H:i:s') ,
                        );
                        $this
                            ->db
                            ->insert(BID_LOG, $data_log);


                            $this->db->select('invoice_id,buyer_price');
                            $this->db->from(BID_DETAILS);
                            $this->db->where('invoice_id',$invoice_id);
                            $this->db->where('buyer_price >','0');
                            $division_check=$this->db->get();
                            $division_check_buyer=$division_check->result();

                            if($decodeJson[$invoice_id]['bidMaxPrice'] > 0){
                                $division_check_buyer = 1;
                            }else{
                                $division_check_buyer = 0;
                            }


                            $this->db->select('inv_id,approve'); 
                            $this->db->from('tt_buyer_division');
                            $this->db->where('inv_id',$invoice_id);
                            $this->db->where('approve','A');
                            $division_check=$this->db->get();
                            $division_check_accept=$division_check->result();

                            $fields15=array('inv_status'=>'A','bid'=>$my_price,'buyer'=>substr($company_name,0,10),'buyer_lock'=>$final_pi_id,'price_idea'=>$price_idea,'bidMaxbuyerId'=>$buyer_id,'buyerId'=>$buyer_id,"buyerfull"=>$company_name,'seller_final_lock'=>$decodeJson[$invoice_id]['seller_final_lock'],'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);

                    }

                    if($chkgetMaxhbp !=0){

                        if(intval($chkgetMaxhbp) > intval($my_price) && @$get_bid_max_hbp_buyer_id_final != $buyer_id){

                                $updatedBid=$my_price+$increaseRate;

                                if(intval($updatedBid) > intval($chkgetMaxhbp)){
                                    $updatedBid=$chkgetMaxhbp;
                                }
                                else{
                                    $updatedBid=$my_price+$increaseRate;
                                }

                                $maxHBPupdateBuyerwise=array('buyer_price'=>$updatedBid);
                                $decodeJson[$invoice_id]['bidMaxbuyerId'] = $get_bid_max_hbp_buyer_id_final;
                                $decodeJson[$invoice_id]['bidMaxPrice'] = $updatedBid;
                                $decodeJson[$invoice_id]['buyer_bid'][$get_bid_max_hbp_buyer_id_final] = array(
                                            'bid'      => $updatedBid,
                                            'buyer_id' => $get_bid_max_hbp_buyer_id_final,
                                            'bid_time' => date('Y-m-d H:i:s'),
                                        );

                     
                       
                                $mainArryEncode=json_encode($decodeJson);
                                $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                file_put_contents($file_to_save, $mainArryEncode);
                              /*  $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);*/


                                 $data_log7 = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,
                                'bid_on' => date('Y-m-d H:i:s') ,
                                );
                                  $this->db->insert(BID_LOG, $data_log7);



                                $data_log = array(

                                'buyer_id' => $get_bid_max_hbp_buyer_id_final,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $updatedBid,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);


                            $this->db->select('invoice_id,buyer_price');
                            $this->db->from(BID_DETAILS);
                            $this->db->where('invoice_id',$invoice_id);
                            $this->db->where('buyer_price >','0');
                            $division_check=$this->db->get();
                            $division_check_buyer=$division_check->result();

                            if($decodeJson[$invoice_id]['bidMaxPrice'] > 0){
                                $division_check_buyer = 1;
                            }else{
                                $division_check_buyer = 0;
                            }


                            $this->db->select('inv_id,approve'); 
                            $this->db->from('tt_buyer_division');
                            $this->db->where('inv_id',$invoice_id);
                            $this->db->where('approve','A');
                            $division_check=$this->db->get();
                            $division_check_accept=$division_check->result();

                            $fields15=array('inv_status'=>'A','bid'=>$updatedBid,'buyer'=>substr(@$getCom[0]->company_name,0,10),'buyer_lock'=>$final_pi_id,'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name,'seller_final_lock'=>$decodeJson[$invoice_id]['seller_final_lock'],'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);

                        }
                        elseif(intval($chkgetMaxhbp) == intval($my_price)){

                           /* $this->db->select('bd.invoice_id,bd.buyer_hbp,bd.buyer_id,tus.id,tus.company_name');
                            $this->db->from(BID_DETAILS.' bd');
                            $this->db->join('tt_users tus','tus.id=bd.buyer_id');
                            $this->db->where('bd.invoice_id',$invoice_id);
                            $this->db->where('bd.buyer_hbp',$chkgetMaxhbp);
                            $bid_max_hbp_buyer_id1=$this->db->get();
                            $get_bid_max_hbp_buyer_id1 = $bid_max_hbp_buyer_id1->result();*/
                            $get_bid_max_hbp_buyer_id_final1=$decodeJson[$invoice_id]['buyerone'];

                            $this->db->select('id,company_name');
                            $this->db->from('tt_users');
                            $this->db->where('id',$get_bid_max_hbp_buyer_id_final1);
                            $qry=$this->db->get();
                            $getCom1=$qry->result();

                            $maxHBPupdateBuyerwise=array('buyer_price'=>$my_price);

                             $decodeJson[$invoice_id]['bidMaxbuyerId'] = $get_bid_max_hbp_buyer_id_final1;
                                $decodeJson[$invoice_id]['bidMaxPrice'] = $my_price;

                             $decodeJson[$invoice_id]['buyer_bid'][$get_bid_max_hbp_buyer_id_final1] = array(
                                            'bid'      => $my_price,
                                            'buyer_id' => $get_bid_max_hbp_buyer_id_final1,
                                            'bid_time' => date('Y-m-d H:i:s'),
                                        );

                     
                       
                                $mainArryEncode=json_encode($decodeJson);
                                $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                file_put_contents($file_to_save, $mainArryEncode);
                               /* $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final1);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);*/

                                 $data_log7 = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,
                                'bid_on' => date('Y-m-d H:i:s') ,
                                );
                                  $this->db->insert(BID_LOG, $data_log7);

                                 $data_log = array(

                                'buyer_id' => $get_bid_max_hbp_buyer_id_final1,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                            $this->db->select('invoice_id,buyer_price');
                            $this->db->from(BID_DETAILS);
                            $this->db->where('invoice_id',$invoice_id);
                            $this->db->where('buyer_price >','0');
                            $division_check=$this->db->get();
                            $division_check_buyer=$division_check->result();

                            if($decodeJson[$invoice_id]['bidMaxPrice'] > 0){
                                $division_check_buyer = 1;
                            }else{
                                $division_check_buyer = 0;
                            }


                            $this->db->select('inv_id,approve'); 
                            $this->db->from('tt_buyer_division');
                            $this->db->where('inv_id',$invoice_id);
                            $this->db->where('approve','A');
                            $division_check=$this->db->get();
                            $division_check_accept=$division_check->result();

                            $fields15=array('inv_status'=>'A','bid'=>$my_price,'buyer'=>substr(@$getCom1[0]->company_name,0,10),'buyer_lock'=>$final_pi_id,'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final1,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom1[0]->company_name,'seller_final_lock'=>$decodeJson[$invoice_id]['seller_final_lock'],'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);
                        }

                        else{

                             $decodeJson[$invoice_id]['bidMaxbuyerId'] = $buyer_id;
                                $decodeJson[$invoice_id]['bidMaxPrice'] = $my_price;
                            $decodeJson[$invoice_id]['buyer_bid'][$buyer_id] = array(
                                            'bid'      => $my_price,
                                            'buyer_id' => $buyer_id,
                                            'bid_time' => date('Y-m-d H:i:s'),
                                        );

                     
                       
                        $mainArryEncode=json_encode($decodeJson);
                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                        file_put_contents($file_to_save, $mainArryEncode);
                            if(empty($chkMyinv)){
                                //$this->db->insert(BID_DETAILS,$data_update1);
                            }
                            else{

                              /*  $this
                                ->db
                                ->where('buyer_id', $buyer_id);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $data_update);*/
                            }

                         $data_log = array(

                            'buyer_id' => $buyer_id,
                            'seller_id' => @$ChkSheet['created_by'],
                            'invoice_id' => $invoice_id,
                            'sheet_id' => $sheet_id,
                            'buyer_price' => $my_price,
                            'seller_price' => $price_idea,

                            'bid_on' => date('Y-m-d H:i:s') ,
                        );
                        $this
                            ->db
                            ->insert(BID_LOG, $data_log);

                            $this->db->select('invoice_id,buyer_price');
                            $this->db->from(BID_DETAILS);
                            $this->db->where('invoice_id',$invoice_id);
                            $this->db->where('buyer_price >','0');
                            $division_check=$this->db->get();
                            $division_check_buyer=$division_check->result();

                           if($decodeJson[$invoice_id]['bidMaxPrice'] > 0){
                                $division_check_buyer = 1;
                            }else{
                                $division_check_buyer = 0;
                            }

                            $this->db->select('inv_id,approve'); 
                            $this->db->from('tt_buyer_division');
                            $this->db->where('inv_id',$invoice_id);
                            $this->db->where('approve','A');
                            $division_check=$this->db->get();
                            $division_check_accept=$division_check->result();

                            $fields15=array('inv_status'=>'A','bid'=>$my_price,'buyer'=>substr($company_name,0,10),'buyer_lock'=>$final_pi_id,'price_idea'=>$price_idea,'bidMaxbuyerId'=>$buyer_id,'buyerId'=>$buyer_id,"buyerfull"=>@$company_name,'seller_final_lock'=>$decodeJson[$invoice_id]['seller_final_lock'],'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);
                        }
                    }
                    $this
                        ->db
                        ->where('inv_id', @$invoice_id);
                    $this
                        ->db
                        ->delete('tt_buyer_division');

/*
                $this->db->select('MAX(buyer_price) as maxprice1');
                $this->db->from(BID_DETAILS);
                $this->db->where('invoice_id',$invoice_id);
                $bid_max=$this->db->get();
                $getMaxprice=$bid_max->result();*/
                $chkgetMaxprice=$decodeJson[$invoice_id]['bidMaxPrice'];

              /* $this->db->select('bd.invoice_id,bd.buyer_price,bd.buyer_id');
               $this->db->from(BID_DETAILS.' bd');
               $this->db->where('bd.invoice_id',$invoice_id);
               $this->db->where('bd.buyer_price',$chkgetMaxprice);
               $bid_max_buyer_id=$this->db->get();
               $get_bid_max_buyer_id = $bid_max_buyer_id->result();*/

                if(intval($chkgetMaxhbp) == intval($my_price))
                  {
                    $getMaxbuyerId=@$get_bid_max_hbp_buyer_id_final;
                  }
                  else
                  {
                    $getMaxbuyerId=$decodeJson[$invoice_id]['bidMaxbuyerId'];
                  }

                   //$fields15=array('inv_status'=>'A','bid'=>$chkgetMaxprice,'bidMaxbuyerId'=>$getMaxbuyerId,'buyerId'=>$buyer_id);
       
          
                   //$database->getReference('bids/'.$sheet_id.'/'.$firebaseKey)->update($fields15);

                    $data_sold = array(
                        'inv_status' => 'A',
                        'sold_by' => $getMaxbuyerId,
                        'sold_on' => date('Y-m-d')
                    );

                    $decodeJson[$invoice_id]['inv_status'] ='A';
                    $decodeJson[$invoice_id]['sold_by'] =$getMaxbuyerId;
                    $decodeJson[$invoice_id]['sold_on'] =date('Y-m-d');
                    $mainArryEncode=json_encode($decodeJson);
                   $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                    file_put_contents($file_to_save, $mainArryEncode);
                   /* $this
                        ->db
                        ->where('invoice_id', $invoice_id);
                    $this
                        ->db
                        ->update(OFFER_INVOICE, $data_sold);*/


                   

                }//FOR BELOW BID
                else{
                    $data_update = array(
                        'buyer_price' => $my_price,
                        'message' => 'N',
                        'message_30' => 'N',
                        'bid_time' => date('Y-m-d H:i:s'),
                       
                    );

                /*    $this->db->select('buyer_id,invoice_id');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('buyer_id',$buyer_id);
                    $this->db->where('invoice_id',$invoice_id);
                    $Qry=$this->db->get();
                    $chkMyinv=$Qry->result();*/

                    $data_update1 = array(
                        'buyer_price' => $my_price,
                        'message' => 'N',
                        'message_30' => 'N',
                        'bid_time' => date('Y-m-d H:i:s'),
                        'buyer_id'=>$buyer_id,
                        'sheet_id'=>$sheet_id,
                        'invoice_id'=>$invoice_id,
                        'seller_id'=>@$ChkSheet['created_by'],
                        'seller_price'=>$price_idea,
                       
                    );

                    if($chkgetMaxhbp ==0){
                      
                      $decodeJson[$invoice_id]['bidMaxbuyerId'] = $buyer_id;
                      $decodeJson[$invoice_id]['bidMaxPrice'] = $my_price;
                        $decodeJson[$invoice_id]['buyer_bid'][$buyer_id] = array(
                                            'bid'      => $my_price,
                                            'buyer_id' => $buyer_id,
                                            'bid_time' => date('Y-m-d H:i:s'),
                                        );

                     
                       
                        $mainArryEncode=json_encode($decodeJson);
                        $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                        file_put_contents($file_to_save, $mainArryEncode);

                       
                            /*if(empty($chkMyinv)){
                                $this->db->insert(BID_DETAILS,$data_update1);
                            }
                            else{
                                 $this
                                ->db
                                ->where('buyer_id', $buyer_id);
                              $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                              $this
                                ->db
                                ->update(BID_DETAILS, $data_update);

                            }*/
                            $data_log = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                            $this->db->select('invoice_id,buyer_price');
                            $this->db->from(BID_DETAILS);
                            $this->db->where('invoice_id',$invoice_id);
                            $this->db->where('buyer_price >','0');
                            $division_check=$this->db->get();
                            $division_check_buyer=$division_check->result();

                           if($decodeJson[$invoice_id]['bidMaxPrice'] > 0){
                                $division_check_buyer = 1;
                            }else{
                                $division_check_buyer = 0;
                            }


                            $this->db->select('inv_id,approve'); 
                            $this->db->from('tt_buyer_division');
                            $this->db->where('inv_id',$invoice_id);
                            $this->db->where('approve','A');
                            $division_check=$this->db->get();
                            $division_check_accept=$division_check->result();
                            $fields15=array('inv_status'=>'I','bid'=>$my_price,'buyer'=>substr($company_name,0,10),'buyer_lock'=>$final_pi_id,'price_idea'=>$price_idea,'bidMaxbuyerId'=>$buyer_id,'buyerId'=>$buyer_id,"buyerfull"=>$company_name,'seller_final_lock'=>$decodeJson[$invoice_id]['seller_final_lock'],'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);

                    }
                   
                    if($chkgetMaxhbp !=0){
                         
                        if(intval($chkgetMaxhbp) > intval($my_price) && @$get_bid_max_hbp_buyer_id_final != $buyer_id){

                                $updatedBid=$my_price+$increaseRate;
                                if(intval($updatedBid) > intval($chkgetMaxhbp)){
                                    $updatedBid=$chkgetMaxhbp;
                                }
                                else{
                                    $updatedBid=$my_price+$increaseRate;

                                }
                                $maxHBPupdateBuyerwise=array('buyer_price'=>$updatedBid);
                                $decodeJson[$invoice_id]['bidMaxbuyerId'] = $get_bid_max_hbp_buyer_id_final;
                                $decodeJson[$invoice_id]['bidMaxPrice'] = $updatedBid;
                                $decodeJson[$invoice_id]['buyer_bid'][$get_bid_max_hbp_buyer_id_final] = array(
                                            'bid'      => $updatedBid,
                                            'buyer_id' => $get_bid_max_hbp_buyer_id_final,
                                            'bid_time' => date('Y-m-d H:i:s'),
                                        );

                                $mainArryEncode=json_encode($decodeJson);
                                $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                file_put_contents($file_to_save, $mainArryEncode);
                               /* $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);*/

                                 $data_log7 = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,
                                'bid_on' => date('Y-m-d H:i:s') ,
                                );

                                 $this->db->insert(BID_LOG, $data_log7);

                                $data_log = array(

                                'buyer_id' => $get_bid_max_hbp_buyer_id_final,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $updatedBid,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                            if(intval($updatedBid) >= intval($price_idea)){
                                $fields6=array("inv_status"=>"A");
                                $data_sold = array(
                                    'inv_status' => 'A',
                                    'sold_by' => $get_bid_max_hbp_buyer_id_final,
                                    'sold_on' => date('Y-m-d')
                                );
                                $decodeJson[$invoice_id]['inv_status'] ='A';
                                $decodeJson[$invoice_id]['sold_by'] =$get_bid_max_hbp_buyer_id_final;
                                $decodeJson[$invoice_id]['sold_on'] =date('Y-m-d');
                                      

                                $mainArryEncode=json_encode($decodeJson);
                                $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                file_put_contents($file_to_save, $mainArryEncode);
                               /* $this
                              /*  $this
                                    ->db
                                    ->where('invoice_id', $invoice_id);
                                $this
                                    ->db
                                    ->update(OFFER_INVOICE, $data_sold);*/

                            }
                            $this->db->select('invoice_id,inv_status');
                            $this->db->from(OFFER_INVOICE);
                            $this->db->where('invoice_id',$invoice_id);
                            $invchknow=$this->db->get();
                            $invchknowplace=$invchknow->result();

                            $this->db->select('invoice_id,buyer_price');
                            $this->db->from(BID_DETAILS);
                            $this->db->where('invoice_id',$invoice_id);
                            $this->db->where('buyer_price >','0');
                            $division_check=$this->db->get();
                            $division_check_buyer=$division_check->result();

                             if($decodeJson[$invoice_id]['bidMaxPrice'] > 0){
                                $division_check_buyer = 1;
                            }else{
                                $division_check_buyer = 0;
                            }


                            $this->db->select('inv_id,approve'); 
                            $this->db->from('tt_buyer_division');
                            $this->db->where('inv_id',$invoice_id);
                            $this->db->where('approve','A');
                            $division_check=$this->db->get();
                            $division_check_accept=$division_check->result();

                            $fields15=array('inv_status'=>$decodeJson[$invoice_id]['inv_status'],'bid'=>$updatedBid,'buyer'=>substr(@$getCom[0]->company_name,0,10),'buyer_lock'=>$final_pi_id,'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom[0]->company_name,'seller_final_lock'=>$decodeJson[$invoice_id]['seller_final_lock'],'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);

                        }
                        elseif(intval($chkgetMaxhbp) == intval($my_price)){

                           /* $this->db->select('bd.invoice_id,bd.buyer_hbp,bd.buyer_id,tus.id,tus.company_name');
                            $this->db->from(BID_DETAILS.' bd');
                            $this->db->join("tt_users tus",'tus.id=bd.buyer_id');
                            $this->db->where('bd.invoice_id',$invoice_id);
                            $this->db->where('bd.buyer_hbp',$chkgetMaxhbp);
                            $bid_max_hbp_buyer_id1=$this->db->get();
                            $get_bid_max_hbp_buyer_id1 = $bid_max_hbp_buyer_id1->result();*/
                            $get_bid_max_hbp_buyer_id_final1=$decodeJson[$invoice_id]['buyerone'];

                            $this->db->select('id,company_name');
                            $this->db->from('tt_users');
                            $this->db->where('id',$get_bid_max_hbp_buyer_id_final1);
                            $qry=$this->db->get();
                            $getCom1=$qry->result();


                               $maxHBPupdateBuyerwise=array('buyer_price'=>$my_price);
                               $decodeJson[$invoice_id]['bidMaxbuyerId'] = $get_bid_max_hbp_buyer_id_final1;
                                $decodeJson[$invoice_id]['bidMaxPrice'] = $my_price;
                               $decodeJson[$invoice_id]['buyer_bid'][$get_bid_max_hbp_buyer_id_final1] =array(
                                            'bid'      => $my_price,
                                            'buyer_id' => $get_bid_max_hbp_buyer_id_final1,
                                            'bid_time' => date('Y-m-d H:i:s'),
                                        );

                                $mainArryEncode=json_encode($decodeJson);
                                $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                file_put_contents($file_to_save, $mainArryEncode);


                              /*  $this
                                ->db
                                ->where('buyer_id', $get_bid_max_hbp_buyer_id_final1);
                                $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                                $this
                                ->db
                                ->update(BID_DETAILS, $maxHBPupdateBuyerwise);*/

                                $data_log7 = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,
                                'bid_on' => date('Y-m-d H:i:s') ,
                                );

                                 $this->db->insert(BID_LOG, $data_log7);

                                $data_log = array(

                                'buyer_id' => $get_bid_max_hbp_buyer_id_final1,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                            if(intval($my_price) >= intval($price_idea)){
                                $fields3=array('inv_status'=>"A");
                                $data_sold = array(
                                    'inv_status' => 'A',
                                    'sold_by' => $get_bid_max_hbp_buyer_id_final1,
                                    'sold_on' => date('Y-m-d')
                                );
                               /* $this
                                    ->db
                                    ->where('invoice_id', $invoice_id);
                                $this
                                    ->db
                                    ->update(OFFER_INVOICE, $data_sold);*/
                                $decodeJson[$invoice_id]['inv_status'] ='A';
                                $decodeJson[$invoice_id]['sold_by'] =$get_bid_max_hbp_buyer_id_final1;
                                $decodeJson[$invoice_id]['sold_on'] =date('Y-m-d');

                           

                                $mainArryEncode=json_encode($decodeJson);
                                $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                file_put_contents($file_to_save, $mainArryEncode);


                            }
                            $this->db->select('invoice_id,inv_status');
                            $this->db->from(OFFER_INVOICE);
                            $this->db->where('invoice_id',$invoice_id);
                            $invchknow=$this->db->get();
                            $invchknowplace=$invchknow->result();

                            $this->db->select('invoice_id,buyer_price');
                            $this->db->from(BID_DETAILS);
                            $this->db->where('invoice_id',$invoice_id);
                            $this->db->where('buyer_price >','0');
                            $division_check=$this->db->get();
                            $division_check_buyer=$division_check->result();

                            if($decodeJson[$invoice_id]['bidMaxPrice'] > 0){
                                $division_check_buyer = 1;
                            }else{
                                $division_check_buyer = 0;
                            }


                            $this->db->select('inv_id,approve'); 
                            $this->db->from('tt_buyer_division');
                            $this->db->where('inv_id',$invoice_id);
                            $this->db->where('approve','A');
                            $division_check=$this->db->get();
                            $division_check_accept=$division_check->result();

                            $fields15=array('inv_status'=>$decodeJson[$invoice_id]['inv_status'],'bid'=>$my_price,'buyer'=>substr(@$getCom1[0]->company_name,0,10),'buyer_lock'=>$final_pi_id,'price_idea'=>$price_idea,'bidMaxbuyerId'=>$get_bid_max_hbp_buyer_id_final1,'buyerId'=>$buyer_id,"buyerfull"=>@$getCom1[0]->company_name,'seller_final_lock'=>$decodeJson[$invoice_id]['seller_final_lock'],'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);
                        }
                        else{

                            $this->db->select('invoice_id,buyer_price');
                            $this->db->from(BID_DETAILS);
                            $this->db->where('invoice_id',$invoice_id);
                            $this->db->where('buyer_price >','0');
                            $division_check=$this->db->get();
                            $division_check_buyer=$division_check->result();

                           

                            $this->db->select('inv_id,approve'); 
                            $this->db->from('tt_buyer_division');
                            $this->db->where('inv_id',$invoice_id);
                            $this->db->where('approve','A');
                            $division_check=$this->db->get();
                            $division_check_accept=$division_check->result();

                        
                             $decodeJson[$invoice_id]['bidMaxbuyerId'] = $buyer_id;
                                $decodeJson[$invoice_id]['bidMaxPrice'] = $my_price;

                            $decodeJson[$invoice_id]['buyer_bid'][$buyer_id] =array(
                                            'bid'      => $my_price,
                                            'buyer_id' => $buyer_id,
                                            'bid_time' => date('Y-m-d H:i:s'),
                                        );

                                $mainArryEncode=json_encode($decodeJson);
                                $file_to_save = 'public/uploads/' . $sheet_id . '.json';
                                file_put_contents($file_to_save, $mainArryEncode);

                                 if($decodeJson[$invoice_id]['bidMaxPrice'] > 0){
                                        $division_check_buyer = 1;
                                    }else{
                                        $division_check_buyer = 0;
                                    }

                                        $fields15=array('inv_status'=>"I",'bid'=>$my_price,'buyer'=>substr($company_name,0,10),'buyer_lock'=>$final_pi_id,'price_idea'=>$price_idea,'bidMaxbuyerId'=>$buyer_id,'buyerId'=>$buyer_id,"buyerfull"=>$company_name,'seller_final_lock'=>$decodeJson[$invoice_id]['seller_final_lock'],'division_check_accept'=>count($division_check_accept),'division_check_buyer'=>$division_check_buyer);

    
/*
                            if(empty($chkMyinv)){
                                $this->db->insert(BID_DETAILS,$data_update1);
                            }
                            else{
                                 $this
                                ->db
                                ->where('buyer_id', $buyer_id);
                              $this
                                ->db
                                ->where('invoice_id', $invoice_id);
                              $this
                                ->db
                                ->update(BID_DETAILS, $data_update);

                            }*/
                            $data_log = array(

                                'buyer_id' => $buyer_id,
                                'seller_id' => @$ChkSheet['created_by'],
                                'invoice_id' => $invoice_id,
                                'sheet_id' => $sheet_id,
                                'buyer_price' => $my_price,
                                'seller_price' => $price_idea,

                                'bid_on' => date('Y-m-d H:i:s') ,
                            );
                            $this
                                ->db
                                ->insert(BID_LOG, $data_log);

                        }
                    }

                    $this
                        ->db
                        ->where('inv_id', @$invoice_id);
                    //$this->db->where('approve','P');
                    $this
                        ->db
                        ->delete('tt_buyer_division');
                }
            }
       echo json_encode($fields15);
    }
    function buyer_close_offer_sheet()
    {
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }
        $this->data['buyer_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['bid_details'] = $this
            ->Common
            ->find(
                [
                    'table' => BID_DETAILS . ' bd', 'select' => "*",
                    'join' =>   [
                                   [OFFER_SHEET, 'os', 'INNER', "os.sheet_id = bd.sheet_id"
                                   ],
                                   [USERS, 'usr', 'INNER', "usr.id = os.created_by"
                                   ],
                                ], 
                    "order_by"=>"os.created_date desc",
                    'where' => "bd.buyer_id = {$dtl['id']}", 'group' => 'bd.sheet_id'
                ]);
//echo '<pre>';print_r($this->data['bid_details']);exit;
        $this
            ->layout
            ->view('buyer-close-offer-sheet', $this->data);
    }

    function yearrwiseSheetSeller()
    {
        $sheet_no = $this
            ->input
            ->post('sheet_no');
        $sheet_name = $this
            ->input
            ->post('sheet_name');
        $expiry_date = $this
            ->input
            ->post('expiry_date');
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if ($expiry_date != "")
        {
            $expiry_date1 = date('Y-m-d', strtotime(str_replace('.', '/', $expiry_date)));
        }
        else
        {
            $expiry_date1 = "";
        }

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from(BID_DETAILS . ' bd');
        $this
            ->db
            ->join(OFFER_SHEET . ' os', 'os.sheet_id=bd.sheet_id');
        $this
            ->db
            ->join(USERS . ' usr', 'usr.id = os.created_by');
        $this
            ->db
            ->where('bd.seller_id', $dtl['id']);
        if ($sheet_no != "")
        {
            $this
                ->db
                ->like('os.sheet_no', $sheet_no);

        }
        if ($sheet_name != "")
        {
            $this
                ->db
                ->like('os.sheet_name', $sheet_name);

        }
        if ($expiry_date1 != "")
        {
            $this
                ->db
                ->like('os.expiry_date', $expiry_date1);

        }
        $this
            ->db
            ->group_by('bd.sheet_id');
        $qry = $this
            ->db
            ->get();
        $bid_details = $qry->result();

?>
                                       <?php $i = 0;
        if (!empty($bid_details))

        {
            $now = date('d-m-Y H:i:s');
            $currentDateTime = strtotime(@$now);
            foreach ($bid_details as $row)
            {
                @$expiry_year = date('Y', strtotime($row->expiry_date));
                @$expiry_date = date("d-m-Y H:i:s", strtotime($row->expiry_date));
                @$newEXPDate = @strtotime(@$expiry_date);

                if (@$newEXPDate < $currentDateTime || $row->expire == 'Y')
                {
                    $i++;
                    $this
                        ->db
                        ->select('*');
                    $this
                        ->db
                        ->from(OFFER_INVOICE);
                    $this
                        ->db
                        ->where('sheet_id', $row->sheet_id);
                    $total_inv = $this
                        ->db
                        ->get();
                    $total_inv_count = $total_inv->result();

                    $this
                        ->db
                        ->select('*');
                    $this
                        ->db
                        ->from(OFFER_INVOICE);
                    $this
                        ->db
                        ->where('sheet_id', $row->sheet_id);
                    $this
                        ->db
                        ->where('inv_status', 'A');
                    $total_inv_active = $this
                        ->db
                        ->get();
                    $total_inv_count_active = $total_inv_active->result();

?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><a title="View Details" href="<?=BASE_URL . 'close-offer-sheet-next/' . encrypt($row->sheet_id); ?>"><?=$row->sheet_name; ?></a></td>
                                                <td><a title="View Details" href="<?=BASE_URL . 'close-offer-sheet-next/' . encrypt($row->sheet_id); ?>"><?=$row->sheet_no; ?></a></td>
                                                <td><?php echo date('F d, Y', strtotime(@$row->expiry_date)); ?> | <strong><?php echo date('h:i A', strtotime(@$row->expiry_date)); ?></td>
                                                <td>

                                                 <?php $Totalcount = 0;
                    foreach ($total_inv_count as $row2)
                    {

                        $Totalcount = $Totalcount + $row2->total_kgs;
                    }

                    $TotalSold = 0;
                    if (!empty($total_inv_count_active))
                    {
                        foreach ($total_inv_count_active as $row1)
                        {

                            $TotalSold = $TotalSold + $row1->total_kgs;
                        }
                    }
                    else
                    {
                        $TotalSold = 0;
                    }

                    $t_sold=($TotalSold/$Totalcount)*100; 
                    echo number_format($t_sold, 2, '.', '');
?>


                                                </td>
                                                <!--  <td><a title="View Details" href="<?=BASE_URL . 'seller-close-offer-sheet-next/' . encrypt($row->sheet_id); ?>"><i class="fa fa-eye"></i></a></td> -->
                                            </tr>
                                         <?php
                }
            }
        } ?>
                                          

        <?php
    }

    function yearrwiseSheet()
    {
        $sheet_no = $this
            ->input
            ->post('sheet_no');
        $sheet_name = $this
            ->input
            ->post('sheet_name');
        $expiry_date = $this
            ->input
            ->post('expiry_date');

        if ($expiry_date != "")
        {
            $expiry_date1 = date('Y-m-d', strtotime(str_replace('.', '/', $expiry_date)));
        }
        else
        {
            $expiry_date1 = "";
        }
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from(BID_DETAILS . ' bd');
        $this
            ->db
            ->join(OFFER_SHEET . ' os', 'os.sheet_id=bd.sheet_id');
        $this
            ->db
            ->join(USERS . ' usr', 'usr.id = os.created_by');
        $this
            ->db
            ->where('bd.buyer_id', $dtl['id']);
        if ($sheet_no != "")
        {
            $this
                ->db
                ->like('os.sheet_no', $sheet_no);

        }
        if ($sheet_name != "")
        {
            $this
                ->db
                ->like('os.sheet_name', $sheet_name);

        }
        if ($expiry_date1 != "")
        {
            $this
                ->db
                ->like('os.expiry_date', $expiry_date1);

        }
        $this
            ->db
            ->group_by('bd.sheet_id');
        $qry = $this
            ->db
            ->get();
        $bid_details = $qry->result();

?>

                                                 <?php if (!empty($bid_details))
        {
            $now = date('d-m-Y H:i:s');
            $currentDateTime = strtotime(@$now);
            foreach ($bid_details as $row)
            {
                @$expiry_year = date('Y', strtotime($row->expiry_date));

                @$expiry_date = date("d-m-Y H:i:s", strtotime($row->expiry_date));
                @$newEXPDate = @strtotime(@$expiry_date);
                if (@$newEXPDate < $currentDateTime || $row->expire == 'Y')
                {
?>
                                            <tr>
                                                <td><?php echo $row->name; ?> (<span><?php echo $row->company_name; ?></span>)</td>
                                                <td><a title="View Details" href="<?=BASE_URL . 'buyer-close-offer-sheet-next/' . encrypt($row->sheet_id); ?>"><?php echo $row->sheet_name; ?></a></td>
                                                <td><a title="View Details" href="<?=BASE_URL . 'buyer-close-offer-sheet-next/' . encrypt($row->sheet_id); ?>"><?php echo $row->sheet_no; ?></a></td>
                                                <td style="color: #ec4b4b;"><?php echo date('F d, Y', strtotime($row->expiry_date)); ?> | <strong><?php echo date('h:i A', strtotime($row->expiry_date)); ?></td>
                                                   
                                            </tr>
                                        <?php
                }
            }
        } ?>

        <?php
    }

    function buyer_active_offer_sheet()
    {
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }
        $this->data['buyer_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

       

        $this->data['bid_details'] = $this
            ->Common
            ->find(
                [
                    'table' => OFFER_SHEET . ' bd', 'select' => "*", 

                ]
         );

        //echo "<pre>"; print_r($this->data['bid_details']);exit;
        

        $this
            ->layout
            ->view('buyer-active-offer-sheet', $this->data);
    }

      function buyer_active_offer_sheet_demo()
    {
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }
        $this->data['buyer_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['bid_details'] = $this
            ->Common
            ->find(['table' => BID_DETAILS . ' bd', 'select' => "*", 'join' => [[OFFER_SHEET, 'os', 'INNER', "os.sheet_id = bd.sheet_id"],
        //[USERS, 'usr', 'INNER', "usr.id = bd.buyer_id"],
        /* [LOCATION, 'loc', 'INNER', "loc.id = os.location"],
         [OFFER_INVOICE, 'oiv', 'INNER', "oiv.sheet_id = bd.sheet_id"],*/

        ], 'where' => "bd.buyer_id = {$dtl['id']}", 'group' => 'bd.sheet_id']);

        //echo "<pre>"; print_r($this->data['bid_details']);exit;
        

        $this
            ->layout
            ->view('buyer-active-offer-sheet-demo', $this->data);
    }
    function buyer_close_offer_sheet_next()
    {
        $sheet_id = $this
            ->uri
            ->segment(2);
        $sheet_id = decrypt($sheet_id);
        if ($sheet_id == "")
        {
            redirect(BASE_URL . 'buyer-close-offer-sheet');

        }

        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['buyer_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['offer_sheet'] = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $created_by = @$this->data['offer_sheet']['created_by'];

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = $created_by", 'query' => 'first']);

        $this->data['invoice_dtl'] = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

        $this->data['offer_sheet_buyer'] = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],

        ], 'where' => "bsa.sheet_id = {$sheet_id}",

        ]);

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$this->data['payment_type'] = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

        $this->data['bid_details'] = $this
            ->Common
            ->find(['table' => BID_DETAILS, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} AND sheet_id = {$sheet_id}",

        ]);

        $this
            ->layout
            ->view('buyer-close-offer-sheet-next', $this->data);

    }

    function buyer_active_offer_sheet_next()
    {
        $sheet_id = $this
            ->uri
            ->segment(2);
        $sheet_id = decrypt($sheet_id);


        if ($sheet_id == "")
        {
            redirect(BASE_URL . 'buyer-active-offer-sheet');

        }
        //echo $sheet_id;
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

         $chkRecievesheet = $this
            ->Common
            ->find(
            [
                    'table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 
                    'where' => "bsa.sheet_id = {$sheet_id} AND bsa.buyer_id= {$dtl['id']} AND is_recv='Recieve'",

            ]);

          

        if(empty($chkRecievesheet))
        {
             redirect(BASE_URL.'buyer-search-offer-sheet');
        }


        $this->data['buyer_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['chk_seller_noti'] = $this
            ->Common
            ->find(['table' => SHEET_CLOSE, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} AND sheet_id = {$sheet_id} AND close_status = 'S'", 'query' => 'first']);

        $this->data['offer_sheet'] = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $created_by = @$this->data['offer_sheet']['created_by'];

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = $created_by", 'query' => 'first']);

        $this->data['invoice_dtl'] = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

        $this->data['offer_sheet_buyer'] = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],

        ], 'where' => "bsa.sheet_id = {$sheet_id}",

        ]);

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$this->data['payment_type'] = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

        $this->data['bid_details'] = $this
            ->Common
            ->find(['table' => BID_DETAILS, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} AND sheet_id = {$sheet_id}",

        ]);

        $this->db->select('*');
        $this->db->from('tt_buyer_sheet_assigned');
        $this->db->where('buyer_id',@$dtl['id']);
        $this->db->where('sheet_id',$sheet_id);
        $getval1=$this->db->get();
        $duplicateCheckbidqty= $getval1->result();

       $this->data['sheet_id']=$sheet_id;

        $this->db->select('*');
        $this->db->from('tt_switch_on');
        $this->db->where('buyer_id',@$dtl['id']);
        $this->db->where('sheet_id',$sheet_id);
        $getval=$this->db->get();
        $hbpVal= $getval->result();

        $this->data['hbpType']=@$hbpVal[0]->status;

        $this->data['bidQty']=@$duplicateCheckbidqty[0]->bid_max_qty;

        $this
            ->db
                ->where('sheet_id', $sheet_id);
        $this
              ->db
                ->where('buyer_id', @$dtl['id']);
        $this->db->delete('tt_abp_receive');


//pre($this->data,1);

        $this
            ->layout
            ->view('buyer-active-offer-sheet-next', $this->data);

    }

       function buyer_active_offer_sheet_next_demo()
    {
        $sheet_id = $this
            ->uri
            ->segment(2);
        $sheet_id = decrypt($sheet_id);
        if ($sheet_id == "")
        {
            redirect(BASE_URL . 'buyer-active-offer-sheet');

        }
        //echo $sheet_id;
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['buyer_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['chk_seller_noti'] = $this
            ->Common
            ->find(['table' => SHEET_CLOSE, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} AND sheet_id = {$sheet_id} AND close_status = 'S'", 'query' => 'first']);

        $this->data['offer_sheet'] = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $created_by = @$this->data['offer_sheet']['created_by'];

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = $created_by", 'query' => 'first']);

        $this->data['invoice_dtl'] = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

        $this->data['offer_sheet_buyer'] = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],

        ], 'where' => "bsa.sheet_id = {$sheet_id}",

        ]);

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$this->data['payment_type'] = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

        $this->data['bid_details'] = $this
            ->Common
            ->find(['table' => BID_DETAILS, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} AND sheet_id = {$sheet_id}",

        ]);

       $this->data['sheet_id']=$sheet_id;

        $this
            ->layout
            ->view('buyer-active-offer-sheet-next-demo', $this->data);

    }

     function getdatabyInvoiceid(){

$invoiceId=$this->input->post('invoiceId');


$dtl = $this
    ->session
    ->userdata(CUSTOMER_SESS);
$buyer_id = @$dtl['id'];

$totalArray=array();



 $this->db->select('toi.sheet_id,tos.sheet_id,toi.price_idea,toi.inv_status,toi.invoice_id,toi.seller_final_lock,tos.buyer_can_see,toi.serial_no,toi.garden,toi.invoice,toi.grade,toi.pkgs_no,toi.total_kgs,tos.division');
 $this->db->from('tt_offer_invoice toi');
 $this->db->join('tt_offer_sheets tos','tos.sheet_id=toi.sheet_id');
 $this->db->where('toi.invoice_id',$invoiceId);
 $qry=$this->db->get();
 $invoice_dtl =$qry->result();

 

$this->db->select('bd.buyer_id,us.id,bd.invoice_id,bd.buyer_lock,bd.buyer_hbp');
$this->db->from(BID_DETAILS.' bd');
$this->db->join(USERS.' us','us.id = bd.buyer_id');
$this->db->where('bd.invoice_id',$invoiceId);
$this->db->where('bd.buyer_id',$buyer_id);
$bid_lock_self=$this->db->get();
$bid_lock_self_buyer = $bid_lock_self->result();

$this->db->select('MAX(buyer_price) as maxprice');
$this->db->from(BID_DETAILS);
$this->db->where('invoice_id',$invoiceId);
$bid_max=$this->db->get();
$bid_max_details =  $bid_max->result();


$max_price=@$bid_max_details[0]->maxprice;

$this->db->select('bd.buyer_id,us.id,bd.buyer_price,bd.invoice_id,us.company_name');
$this->db->from(BID_DETAILS.' bd');
$this->db->join(USERS.' us','us.id = bd.buyer_id');
$this->db->where('bd.invoice_id',$invoiceId);
$this->db->where('bd.buyer_price',$max_price);
$bid_max_buyer=$this->db->get();
$bid_max_details_buyer = $bid_max_buyer->result();

$this->db->select('bd.user_id,us.id,bd.invoice_id,bd.comment');
$this->db->from("tt_buyer_recieve_comment".' bd');
$this->db->join(USERS.' us','us.id = bd.user_id');
$this->db->where('bd.invoice_id',$invoiceId);
 $this->db->where('bd.user_id',$buyer_id);
$bid_comment=$this->db->get();
$bid_comment_buyer=$bid_comment->result();

$this->db->select('invoice_id,buyer_price');
$this->db->from(BID_DETAILS);
$this->db->where('invoice_id',$invoiceId);
$this->db->where('buyer_price !=',0);
$division_check=$this->db->get();
$division_check_buyer=$division_check->result();

$this->db->select('inv_id,approve'); 
$this->db->from('tt_buyer_division');
$this->db->where('inv_id',$invoiceId);
$this->db->where('approve','A');
$division_check=$this->db->get();
$division_check_accept=$division_check->result();



if($max_price =="")
{
    $max_price=0;
}
else
{
    $max_price=$max_price;
}

if(@$bid_lock_self_buyer[0]->buyer_lock =="")
{
    $getValue['buyer_lock']="N";
}
else
{
    $getValue['buyer_lock']=@$bid_lock_self_buyer[0]->buyer_lock;
}

if(@$invoice_dtl[0]->seller_final_lock =="")
{
    $getValue['seller_final_lock']="N";
}
else
{
    $getValue['seller_final_lock']=@$invoice_dtl[0]->seller_final_lock;
}


$getValue['price_idea']=@$invoice_dtl[0]->price_idea;

if(@$bid_lock_self_buyer[0]->buyer_hbp =="")
{
    $getValue['buyer_hbp']=0;
}
else
{
    $getValue['buyer_hbp']=@$bid_lock_self_buyer[0]->buyer_hbp;
}


$getValue['bid']=$max_price;

$getValue['inv_status']=@$invoice_dtl[0]->inv_status;
$getValue['invoice_id']=@$invoice_dtl[0]->invoice_id;

$getValue['pkgs_no']=@$invoice_dtl[0]->pkgs_no;
$getValue['buyer']= substr(@$bid_max_details_buyer[0]->company_name,0,10);
$getValue['buyerfull']= @$bid_max_details_buyer[0]->company_name;

$getValue['comment']=substr(@$bid_comment_buyer[0]->comment,0,10);
$getValue['fullcomment']=@$bid_comment_buyer[0]->comment;

$getValue['seller_final_lock']=@$invoice_dtl[0]->seller_final_lock;

$getValue['division_check_accept']=count(@$division_check_accept);
$getValue['division_check_buyer']=count(@$division_check_buyer);

$getValue['bidMaxbuyerId']= @$bid_max_details_buyer[0]->id;

$getValue['buyer_can_see']= @$invoice_dtl[0]->buyer_can_see;

$getValue['serial_no']=@$invoice_dtl[0]->serial_no;
$getValue['garden']=@$invoice_dtl[0]->garden;
$getValue['invoice']=@$invoice_dtl[0]->invoice;
$getValue['grade']=@$invoice_dtl[0]->grade;
$getValue['pkgs_no']=@$invoice_dtl[0]->pkgs_no;
$getValue['total_kgs']=@$invoice_dtl[0]->total_kgs;

$getValue['division']= @$invoice_dtl[0]->division;
$getValue['buyerId']=@$buyer_id;

if(@$invoice_dtl[0]->pkgs_no > @$invoice_dtl[0]->division)
{
    $getValue['flag']="yes";
}
else
{
    $getValue['flag']="no";
}

    echo json_encode($getValue);
}


    function getdatabyInvoiceidseller()
    {
        $invoiceId=$this->input->post('invoiceId');

        $totalArray=array();



         $this->db->select('*');
         $this->db->from('tt_offer_invoice toi');
         $this->db->join('tt_offer_sheets tos','tos.sheet_id=toi.sheet_id');
         $this->db->where('toi.invoice_id',$invoiceId);
         $qry=$this->db->get();
         $invoice_dtl =$qry->result();

         

        $this->db->select('*');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->join(USERS.' us','us.id = bd.buyer_id');
        $this->db->where('invoice_id',$invoiceId);
       // $this->db->where('bd.buyer_id',$buyer_id);
        $bid_lock_self=$this->db->get();
        $bid_lock_self_buyer = $bid_lock_self->result();

        $this->db->select('MAX(buyer_price) as maxprice');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$invoiceId);
        $bid_max=$this->db->get();
        $bid_max_details =  $bid_max->result();

       
        $max_price=@$bid_max_details[0]->maxprice;

        $this->db->select('*');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->join(USERS.' us','us.id = bd.buyer_id');
        $this->db->where('bd.invoice_id',$invoiceId);
        $this->db->where('bd.buyer_price',$max_price);
        $bid_max_buyer=$this->db->get();
        $bid_max_details_buyer = $bid_max_buyer->result();

      

        $this->db->select('*');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$invoiceId);
        $this->db->where('buyer_price !=',0);
        $division_check=$this->db->get();
        $division_check_buyer=$division_check->result();

        $this->db->select('*'); 
        $this->db->from('tt_buyer_division');
        $this->db->where('inv_id',$invoiceId);
        $this->db->where('approve','A');
        $division_check=$this->db->get();
        $division_check_accept=$division_check->result();

      

        if($max_price =="")
        {
            $max_price=0;
        }
        else
        {
            $max_price=$max_price;
        }

      
      
       
        $getValue['price_idea']=@$invoice_dtl[0]->price_idea;

        $getValue['bid']=$max_price;

        $getValue['inv_status']=@$invoice_dtl[0]->inv_status;
        $getValue['invoice_id']=@$invoice_dtl[0]->invoice_id;
       
        $getValue['pkgs_no']=@$invoice_dtl[0]->pkgs_no;
        $getValue['buyer']= substr(@$bid_max_details_buyer[0]->company_name,0,10);
        $getValue['buyerfull']= @$bid_max_details_buyer[0]->company_name;
        $getValue['buyer_lock']=@$bid_max_details_buyer[0]->buyer_lock;
        $getValue['comment']=substr(@$invoice_dtl[0]->comment,0,10);
        $getValue['fullcomment']=@$invoice_dtl[0]->comment;
      
        $getValue['seller_final_lock']=@$invoice_dtl[0]->seller_final_lock;
       
        $getValue['division_check_accept']=count(@$division_check_accept);
        $getValue['division_check_buyer']=count(@$division_check_buyer);
       
        $getValue['bidMaxbuyerId']= @$bid_max_details_buyer[0]->id;

        $getValue['buyer_can_see']= @$invoice_dtl[0]->buyer_can_see;

        $getValue['serial_no']=@$invoice_dtl[0]->serial_no;
        $getValue['garden']=@$invoice_dtl[0]->garden;
        $getValue['invoice']=@$invoice_dtl[0]->invoice;
        $getValue['grade']=@$invoice_dtl[0]->grade;
        $getValue['pkgs_no']=@$invoice_dtl[0]->pkgs_no;
        $getValue['total_kgs']=@$invoice_dtl[0]->total_kgs;
       
        $getValue['division']= @$invoice_dtl[0]->division;
       
        
       


       
       

        echo json_encode($getValue);
    }

    function getAlldataviewseller(){
        $data = json_decode(file_get_contents("php://input"));

        $row7 = $data->row;
        $rowperpage = $data->rowperpage;
        # code...
        $sheet_id = $this ->uri ->segment(4);
        $selectedInvoiceid=array();
        $selectedKey=array();

          //////////////////////////////////////////////////////////////////////////////////////

        $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);

        $this->db->select('full_entry,sheet_id');
        $this->db->from('tt_sheet_entry');
        $this->db->where('full_entry','N');
        $this->db->where('sheet_id',$sheet_id);
        $chk=$this->db->get();
        $chkDuplicate =  $chk->result();

       if(!empty($chkDuplicate)){
             $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
             $decodeJson= json_decode($jsonFile,true);
             $decodeJson1 = array_slice($decodeJson, $row7, $rowperpage);

            $totalArray=array();
            foreach($decodeJson1 as $key=>$row){

                 $this->db->select('*');
                 $this->db->from('tt_buyer_division tbd');
                 $this->db->join(USERS.' us','us.id=tbd.buyer_request_from','inner');
                 $this->db->where('tbd.inv_id',$row['invoice_id']);
                 $this->db->where('tbd.buyer_request_to',$row['bidMaxbuyerId']);
                 $this->db->where('tbd.approve','A');
                 $division_check=$this->db->get();
                 $chk_division_seller=$division_check->result();
              
                if($row['bidMaxPrice'] > 0){
                        $division_check_buyer =1; 
                    }else{
                        $division_check_buyer =0; 
                    }

                $this->db->select('*'); 
                $this->db->from('tt_buyer_division');
                $this->db->where('inv_id',$row['invoice_id']);
                $this->db->where('approve','A');
                $division_check=$this->db->get();
                $division_check_accept=$division_check->result();

                    $this->db->select('sheet_id,status');
                    $this->db->from('tt_switch_on');
                    $this->db->where('sheet_id',$sheet_id);
                    $this->db->where('buyer_id', $row['buyerone']);
                    $getval=$this->db->get();
                    $stat= $getval->result();
                    $hbpType=@$stat[0]->status;

                if(($hbpType!='semi_aumatic' || $hbpType=="")){
                if($row['inv_status']!='A' && $row['abpone'] >= $row['price_idea']){

                        $soldBy = $row['buyerone'];
                        $max_price = $row['price_idea'];
                        $invStatus = 'A';
                        $row['bidMaxbuyerId'] = $row['buyerone'];

                        $this->db->select('id,company_name');
                        $this->db->from(USERS);
                        $this->db->where('id',$soldBy);
                        $name=$this->db->get();
                        $company= $name->result();
                        $companName=@$company[0]->company_name;
                       
                }elseif($row['inv_status']=='A' && $row['sold_by'] !=""){
                    $soldBy = $row['sold_by'];
                    $invStatus = 'A';
                    $max_price = $row['bidMaxPrice'];
                    $this->db->select('id,company_name');
                    $this->db->from(USERS);
                    $this->db->where('id',$soldBy);
                    $name=$this->db->get();
                    $company= $name->result();
                    $companName=@$company[0]->company_name;
                }elseif($row['inv_status']!='A'){
                    $soldBy = $row['bidMaxbuyerId'];
                    $invStatus = 'I';
                    $max_price = $row['bidMaxPrice'];
                    $this->db->select('id,company_name');
                    $this->db->from(USERS);
                    $this->db->where('id',$soldBy);
                    $name=$this->db->get();
                    $company= $name->result();
                    $companName=@$company[0]->company_name;
                }else{

                }
            }else{

                if($row['inv_status']=='A' && $row['sold_by'] !=""){
                    $soldBy = $row['sold_by'];
                    $invStatus = 'A';
                    $max_price = $row['bidMaxPrice'];
                    $this->db->select('id,company_name');
                    $this->db->from(USERS);
                    $this->db->where('id',$soldBy);
                    $name=$this->db->get();
                    $company= $name->result();
                    $companName=@$company[0]->company_name;
                }
                elseif($row['inv_status']!='A'){
                    $soldBy = $row['bidMaxbuyerId'];
                    $invStatus = 'I';
                    $max_price = $row['bidMaxPrice'];
                    $this->db->select('id,company_name');
                    $this->db->from(USERS);
                    $this->db->where('id',$soldBy);
                    $name=$this->db->get();
                    $company= $name->result();
                    $companName=@$company[0]->company_name;
                }else{
                    
                }
            }
                if($max_price =="")
                            {
                                $max_price=0;
                            }
                            else
                            {
                                $max_price=$max_price;
                            }

                $getValue['chk_division_seller_buyer']=@$chk_division_seller[0]->company_name;
                $getValue['chk_division_seller']=@$chk_division_seller;
                $getValue['sold_by']=$soldBy;
               $getValue['serial_no']=$row['serial_no'];
                    $getValue['garden']=$row['garden'];
                    $getValue['invoice']=$row['invoice'];
                    $getValue['grade']=$row['grade'];
                    $getValue['pkgs_no']=is_numeric($row['pkgs_no']);
                    $getValue['total_kgs']=$row['total_kgs'];
                    $getValue['price_idea']=$row['price_idea'];
                $getValue['bid']=@$max_price;

                $getValue['inv_status']=@$invStatus;
                $getValue['invoice_id']=$row['invoice_id'];
                $getValue['key']=$key;
                 $getValue['pkgs_no']=$row['pkgs_no'];
                    $getValue['buyer']= substr($companName,0,10);
                    $getValue['buyerfull']= $companName;
                
                $getValue['buyer_lock']=$row['invoice_id']['buyer_bid'][$row['bidMaxbuyerId']]['buyer_lock'];
                $getValue['comment']=substr(@$row['seller_comment'],0,10);
                $getValue['fullcomment']=@$row['seller_comment'];
                $getValue['division']= is_numeric(@$row['division']);
                $getValue['seller_final_lock']=@$row['seller_final_lock'];
                    $getValue['buyer_can_see']=@$row['buyer_can_see'];
                    $getValue['sheet_no']=@$row['sheet_no'];
                    $getValue['sheet_id']=@$row['sheet_id'];
                    $getValue['sheet_name']=@$row['sheet_name'];
                $getValue['buyerId']=@$buyer_id;
                $getValue['bidMaxbuyerId']= $row['bidMaxbuyerId'];

                $getValue['division_check_accept']=count(@$division_check_accept);
                $getValue['division_check_buyer']=@$division_check_buyer;


                array_push($totalArray, $getValue);

            }
         }else{
               $this->db->select('invoice_id,sheet_id,sold_by,serial_no,garden,invoice,grade,pkgs_no,total_kgs,price_idea,inv_status,seller_final_lock,comment');
         $this->db->from('tt_offer_invoice');
         $this->db->where('sheet_id',$sheet_id);
         $this->db->limit($rowperpage,$row7);
         $qry=$this->db->get();
         $invoice_dtl1 =$qry->result();

        $offer_sheet = $this
            ->Common
            ->find([

                'table' => OFFER_SHEET . ' os', 

                'select' => "os.division,os.buyer_can_see,os.sheet_no,os.sheet_id,os.sheet_name",

                'where' => "os.sheet_id = '{$sheet_id}'", 

               'query' => 'first'
           ]);


         $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];

         $totalArray=array();

           if(!empty($invoice_dtl1)){
        
        foreach($invoice_dtl1 as $key=>$row){ 

        $inv_id=@$row->invoice_id;

        $this->db->select('*');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->join(USERS.' us','us.id = bd.buyer_id');
        $this->db->where('invoice_id',$inv_id);
        //$this->db->where('bd.buyer_id',$buyer_id);
        $bid_lock_self=$this->db->get();
        $bid_lock_self_buyer = $bid_lock_self->result();

        $this->db->select('MAX(buyer_price) as maxprice');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$inv_id);
        $bid_max=$this->db->get();
        $bid_max_details =  $bid_max->result();

       
        $max_price=@$bid_max_details[0]->maxprice;

        $this->db->select('*');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->join(USERS.' us','us.id = bd.buyer_id');
        $this->db->where('bd.invoice_id',@$inv_id);
        $this->db->where('bd.buyer_price',@$max_price);
        $bid_max_buyer=$this->db->get();
        $bid_max_details_buyer = $bid_max_buyer->result();

          $this->db->select('*');
          $this->db->from('tt_buyer_division tbd');
          $this->db->join(USERS.' us','us.id=tbd.buyer_request_from','inner');
          $this->db->where('tbd.inv_id',$inv_id);
          $this->db->where('tbd.buyer_request_to',@$bid_max_details_buyer[0]->id);
          $this->db->where('tbd.approve','A');

          $division_check=$this->db->get();
          $chk_division_seller=$division_check->result();

    

        $this->db->select('*');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',@$inv_id);
        $this->db->where('buyer_price !=',0);
        $division_check=$this->db->get();
        $division_check_buyer=$division_check->result();

        $this->db->select('*'); 
        $this->db->from('tt_buyer_division');
        $this->db->where('inv_id',$inv_id);
        $this->db->where('approve','A');
        $division_check=$this->db->get();
        $division_check_accept=$division_check->result();

        if($max_price =="")
        {
            @$max_price=0;
        }
        else
        {
            $max_price=@$max_price;
        }

        //$getValue['bid_lock_self_buyer']=$bid_lock_self_buyer;
        //$getValue['bid_max']=$bid_max_details;
        //$getValue['bid_max_buyer']=$bid_max_details_buyer;
        $getValue['chk_division_seller_buyer']=@$chk_division_seller[0]->company_name;
        $getValue['chk_division_seller']=@$chk_division_seller;
        $getValue['sold_by']=@$row->sold_by;
        $getValue['serial_no']=@$row->serial_no;
        $getValue['garden']=@$row->garden;
        $getValue['invoice']=@$row->invoice;
        $getValue['grade']=@$row->grade;
        $getValue['pkgs_no']=is_numeric(@$row->pkgs_no);
        $getValue['total_kgs']=@$row->total_kgs;
        $getValue['price_idea']=@$row->price_idea;

        $getValue['bid']=@$max_price;

        $getValue['inv_status']=@$row->inv_status;
        $getValue['invoice_id']=@$row->invoice_id;
        $getValue['key']=$key;
        $getValue['pkgs_no']=@$row->pkgs_no;
        $getValue['buyer']= substr(@$bid_max_details_buyer[0]->company_name,0,10);
        $getValue['buyerfull']= @$bid_max_details_buyer[0]->company_name;
        $getValue['buyer_lock']=@$bid_max_details_buyer[0]->buyer_lock;
        $getValue['comment']=substr(@$row->comment,0,10);
        $getValue['fullcomment']=@$row->comment;
        $getValue['division']= is_numeric(@$offer_sheet['division']);
        $getValue['seller_final_lock']=@$row->seller_final_lock;
        $getValue['buyer_can_see']=@$offer_sheet['buyer_can_see'];
        $getValue['sheet_no']=@$offer_sheet['sheet_no'];
        $getValue['sheet_id']=@$offer_sheet['sheet_id'];
        $getValue['sheet_name']=@$offer_sheet['sheet_name'];
        $getValue['buyerId']=@$buyer_id;
        $getValue['bidMaxbuyerId']= @$bid_max_details_buyer[0]->id;

        $getValue['division_check_accept']=count(@$division_check_accept);
        $getValue['division_check_buyer']=count(@$division_check_buyer);
        array_push($totalArray, @$getValue);



        }


        }
         }



        ///////////////////////////////////////////////////////////////////////////
        

     

         echo json_encode(array('invoice_dtl'=>$this->utf8ize(@$totalArray)));

    }

    function getAlldataviewsellerbkkkk()
    {
       $data = json_decode(file_get_contents("php://input"));

        $row = $data->row;
        $rowperpage = $data->rowperpage;
        # code...
        $sheet_id = $this ->uri ->segment(4);
        $selectedInvoiceid=array();
        $selectedKey=array();

         $this->db->select('invoice_id,sheet_id,sold_by,serial_no,garden,invoice,grade,pkgs_no,total_kgs,price_idea,inv_status,seller_final_lock,comment');
         $this->db->from('tt_offer_invoice');
         $this->db->where('sheet_id',$sheet_id);
         $this->db->limit($rowperpage,$row);
         $qry=$this->db->get();
         $invoice_dtl =$qry->result();

        foreach($invoice_dtl as $row1){
            array_push($selectedInvoiceid, $row1->invoice_id);
        }

       if(!empty($selectedInvoiceid)){
            $this->db->select('tbd.sheet_id,tos.sheet_id,tbd.push_hbp,tbd.hbp_time,tbd.buyer_id,tbd.invoice_id,tos.expiry_date,tbd.buyer_hbp,tbd.seller_id,tbd.hbp_key,tos.complete_close');
            $this->db->from('tt_bid_details tbd');
            $this->db->join('tt_offer_sheets tos','tbd.sheet_id=tos.sheet_id');
            $this->db->where_in('tbd.invoice_id',$selectedInvoiceid);
            $this->db->where('tbd.sheet_id',$sheet_id);
            $this->db->where('tos.expire','N');
            $this->db->where('tbd.push_hbp','N');
            $this->db->order_by('tbd.hbp_time',"ASC");
            $qry=$this->db->get();
            $bid_details_hbpY= $qry->result();
            $countbiddetailshbpY = count(@$bid_details_hbpY);
              if (!empty($bid_details_hbpY)){
            foreach ($bid_details_hbpY as $row){   
                $this->db->select('sheet_id,status');
                $this->db->from('tt_switch_on');
                $this->db->where('sheet_id',$row->sheet_id);
                $this->db->where('buyer_id',$row->buyer_id);
                $getval=$this->db->get();
                $stat= $getval->result();

                $hbpType=@$stat[0]->status;

                $this->db->select('invoice_id,inv_status,price_idea');
                $this->db->from(OFFER_INVOICE);
                $this->db->where('invoice_id',$row->invoice_id);
                $getValinv=$this->db->get();
                $getHBPdatainv= $getValinv->result();

                $this->db->select('buyer_id,sheet_id,bid_max_qty');
                $this->db->from('tt_buyer_sheet_assigned');
                $this->db->where('buyer_id',$row->buyer_id);
                $this->db->where('sheet_id',$row->sheet_id);
                $getval2=$this->db->get();
                $bidmaxqty= $getval2->result();

                if(@$bidmaxqty[0]->bid_max_qty== "" || @$bidmaxqty[0]->bid_max_qty==0){
                    $getbidmaxqty=9999;
                }
                else{
                    $getbidmaxqty=@$bidmaxqty[0]->bid_max_qty;

                }
                $this->db->select('buyer_id,sheet_id,push_hbp');
                $this->db->from(BID_DETAILS);
                $this->db->where('buyer_id',$row->buyer_id);
                $this->db->where('sheet_id',$row->sheet_id);
                $this->db->where('push_hbp','Y');
                $countgetVal=$this->db->get();
                $countgetHBPdata= $countgetVal->result();

                $expDatetime=$row->expiry_date;
                $timeFirst  = strtotime(date('Y-m-d H:i:s'));
                $timeSecond = strtotime($expDatetime);
                $differenceInSeconds = $timeSecond - $timeFirst;

                 ////////////////////////////////////////////////////////
                    $allHBPY = array(
                                'push_hbp'=>'Y'
                            );
                    $this->db->where('buyer_id', $row->buyer_id);
                    $this->db->where('invoice_id', @$row->invoice_id);
                    $this->db ->update(BID_DETAILS, $allHBPY);
                    /////////////////////////////////////////////////////// 

                if(($hbpType!='semi_aumatic' || $hbpType=="")){
                   
                    if(@$getHBPdatainv[0]->inv_status!='A' && intval($row->buyer_hbp) >=intval(@$getHBPdatainv[0]->price_idea) && count($countgetHBPdata) < intval($getbidmaxqty)){
                            
                            $dataBiddetails = array(
                            'buyer_price' => @$getHBPdatainv[0]->price_idea,
                            'bid_time' => date('Y-m-d H:i:s'),
                            'push_hbp'=>'Y'
                         );

                        $data_log = array(

                        'buyer_id' => $row->buyer_id,
                        'seller_id' => $row->seller_id,
                        'invoice_id' => $row->invoice_id,
                        'sheet_id' => $row->sheet_id,
                        'buyer_price' => @$getHBPdatainv[0]->price_idea,
                        'seller_price' => @$getHBPdatainv[0]->price_idea,
                         'bid_on' => date('Y-m-d H:i:s') ,
                        );
                        $this
                            ->db
                            ->insert(BID_LOG, $data_log);

                            $this
                            ->db
                            ->where('buyer_id', $row->buyer_id);
                            $this
                                ->db
                                ->where('invoice_id', @$row->invoice_id);
                            $this
                            ->db
                            ->update(BID_DETAILS, $dataBiddetails);

                            $this
                            ->db
                            ->where('inv_id', @$row->invoice_id);
                            //$this->db->where('approve','P');
                            $this
                                ->db
                                ->delete('tt_buyer_division');

                            $data_sold = array(
                            'inv_status' => 'A',
                            'hbp_key' => @$row->hbp_key,
                            'sold_by' => @$row->buyer_id,
                            'sold_on' => date('Y-m-d')
                             );
                            $this
                                ->db
                                ->where('invoice_id', @$row->invoice_id);
                            $this
                                ->db
                                ->update(OFFER_INVOICE, $data_sold);

                        
                        }
                }  
            }
        }  
       
       $offer_sheet = $this
            ->Common
            ->find([

                'table' => OFFER_SHEET . ' os', 

                'select' => "os.division,os.buyer_can_see,os.sheet_no,os.sheet_id,os.sheet_name",

                'where' => "os.sheet_id = '{$sheet_id}'", 

               'query' => 'first'
           ]);


         $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];
       


         if(!empty($selectedInvoiceid)){
            $this->db->select('invoice_id,sheet_id,sold_by,serial_no,garden,invoice,grade,pkgs_no,total_kgs,price_idea,inv_status,seller_final_lock,comment');
            $this->db->from('tt_offer_invoice');
            $this->db->where('sheet_id',$sheet_id);
            $this->db->where_in('invoice_id',$selectedInvoiceid);
            $qry1=$this->db->get();
            $invoice_dtl1 =$qry1->result();
        }else{
            $this->db->select('invoice_id,sheet_id,sold_by,serial_no,garden,invoice,grade,pkgs_no,total_kgs,price_idea,inv_status,seller_final_lock,comment');
            $this->db->from('tt_offer_invoice');
            $this->db->where('sheet_id',$sheet_id);
            $qry1=$this->db->get();
            $invoice_dtl1 =$qry1->result();
        }
        $totalArray=array();
       if(!empty($invoice_dtl1)){
        
        foreach($invoice_dtl1 as $key=>$row){ 

        $inv_id=@$row->invoice_id;

        $this->db->select('*');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->join(USERS.' us','us.id = bd.buyer_id');
        $this->db->where('invoice_id',$inv_id);
        //$this->db->where('bd.buyer_id',$buyer_id);
        $bid_lock_self=$this->db->get();
        $bid_lock_self_buyer = $bid_lock_self->result();

        $this->db->select('MAX(buyer_price) as maxprice');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$inv_id);
        $bid_max=$this->db->get();
        $bid_max_details =  $bid_max->result();

       
        $max_price=@$bid_max_details[0]->maxprice;

        $this->db->select('*');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->join(USERS.' us','us.id = bd.buyer_id');
        $this->db->where('bd.invoice_id',@$inv_id);
        $this->db->where('bd.buyer_price',@$max_price);
        $bid_max_buyer=$this->db->get();
        $bid_max_details_buyer = $bid_max_buyer->result();

          $this->db->select('*');
          $this->db->from('tt_buyer_division tbd');
          $this->db->join(USERS.' us','us.id=tbd.buyer_request_from','inner');
          $this->db->where('tbd.inv_id',$inv_id);
          $this->db->where('tbd.buyer_request_to',@$bid_max_details_buyer[0]->id);
          $this->db->where('tbd.approve','A');

          $division_check=$this->db->get();
          $chk_division_seller=$division_check->result();

    

        $this->db->select('*');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',@$inv_id);
        $this->db->where('buyer_price !=',0);
        $division_check=$this->db->get();
        $division_check_buyer=$division_check->result();

        $this->db->select('*'); 
        $this->db->from('tt_buyer_division');
        $this->db->where('inv_id',$inv_id);
        $this->db->where('approve','A');
        $division_check=$this->db->get();
        $division_check_accept=$division_check->result();

        if($max_price =="")
        {
            @$max_price=0;
        }
        else
        {
            $max_price=@$max_price;
        }

        //$getValue['bid_lock_self_buyer']=$bid_lock_self_buyer;
        //$getValue['bid_max']=$bid_max_details;
        //$getValue['bid_max_buyer']=$bid_max_details_buyer;
        $getValue['chk_division_seller_buyer']=@$chk_division_seller[0]->company_name;
        $getValue['chk_division_seller']=@$chk_division_seller;
        $getValue['sold_by']=@$row->sold_by;
        $getValue['serial_no']=@$row->serial_no;
        $getValue['garden']=@$row->garden;
        $getValue['invoice']=@$row->invoice;
        $getValue['grade']=@$row->grade;
        $getValue['pkgs_no']=is_numeric(@$row->pkgs_no);
        $getValue['total_kgs']=@$row->total_kgs;
        $getValue['price_idea']=@$row->price_idea;

        $getValue['bid']=@$max_price;

        $getValue['inv_status']=@$row->inv_status;
        $getValue['invoice_id']=@$row->invoice_id;
        $getValue['key']=$key;
        $getValue['pkgs_no']=@$row->pkgs_no;
        $getValue['buyer']= substr(@$bid_max_details_buyer[0]->company_name,0,10);
        $getValue['buyerfull']= @$bid_max_details_buyer[0]->company_name;
        $getValue['buyer_lock']=@$bid_max_details_buyer[0]->buyer_lock;
        $getValue['comment']=substr(@$row->comment,0,10);
        $getValue['fullcomment']=@$row->comment;
        $getValue['division']= is_numeric(@$offer_sheet['division']);
        $getValue['seller_final_lock']=@$row->seller_final_lock;
        $getValue['buyer_can_see']=@$offer_sheet['buyer_can_see'];
        $getValue['sheet_no']=@$offer_sheet['sheet_no'];
        $getValue['sheet_id']=@$offer_sheet['sheet_id'];
        $getValue['sheet_name']=@$offer_sheet['sheet_name'];
        $getValue['buyerId']=@$buyer_id;
        $getValue['bidMaxbuyerId']= @$bid_max_details_buyer[0]->id;

        $getValue['division_check_accept']=count(@$division_check_accept);
        $getValue['division_check_buyer']=count(@$division_check_buyer);
        array_push($totalArray, @$getValue);



        }


        }

         $this->db->select('tbd.sheet_id,tos.sheet_id,tbd.push_hbp,tbd.hbp_time,tbd.buyer_id,tbd.invoice_id,tos.expiry_date,tbd.buyer_hbp,tbd.seller_id,tbd.hbp_key,tos.complete_close');
        $this->db->from('tt_bid_details tbd');
        $this->db->join('tt_offer_sheets tos','tbd.sheet_id=tos.sheet_id');
        $this->db->where('tbd.sheet_id',$sheet_id);
        $this->db->where('tos.expire','N');
        $this->db->where('tbd.push_hbp','N');
        $this->db->order_by('tbd.hbp_time',"ASC");
        $qry2=$this->db->get();
        $bid_details_hbpY2= $qry2->result();
        $countbiddetailshbpY2 = count(@$bid_details_hbpY2);
        if($countbiddetailshbpY2 == 0){
                    $completeClose = array('complete_close'=>'Y');
                    $this->db->where('sheet_id',$sheet_id);
                    $this->db->update('tt_offer_sheets',$completeClose);
        }
        }else{
            $bid_details_hbpY= array();
            $countbiddetailshbpY = 0;
            $totalArray = array();
        }


       
       
      

        // print_r($totalArray); die;
       
       // echo "<pre>";print_r($totalArray);exit;
        //echo json_encode(['invoice_dtl' => $this->utf8ize($totalArray)]);

        echo json_encode(array('invoice_dtl'=>$this->utf8ize(@$totalArray),'bid_details_hbpY'=>count(@$countbiddetailshbpY2)));


    }

    function utf8ize($d) {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = $this->utf8ize($v);
            }
        } else if (is_string ($d)) {
            return utf8_encode($d);
        }
        return $d;
    }




function getAlldataview(){

$data = json_decode(file_get_contents("php://input"));

$firebase = $this->firebase->init();
$database = $firebase->getDatabase();
$row = $data->row;
$rowperpage = $data->rowperpage;
# code...
$sheet_id = $this
    ->uri
    ->segment(4);

 $this->db->select('*');
 $this->db->from('tt_offer_invoice');
 $this->db->where('sheet_id',$sheet_id);
 $this->db->limit($rowperpage,$row);
 $qry=$this->db->get();
 $invoice_dtl =$qry->result();

$offer_sheet = $this
    ->Common
    ->find([

        'table' => OFFER_SHEET . ' os', 

        'select' => "*", 

       /* 'join' => [

            [LOCATION, 'loc', 'INNER', "loc.id = os.location"],


        ], */

       'where' => "os.sheet_id = '{$sheet_id}'", 

       'query' => 'first'
   ]);


 $dtl = $this
    ->session
    ->userdata(CUSTOMER_SESS);
$buyer_id = @$dtl['id'];

 $totalArray=array();
if(!empty($invoice_dtl)){

foreach($invoice_dtl as $key=>$row){ 

$inv_id=$row->invoice_id;
$firebase_key=$row->firebase_key;
$last_row= $database->getReference('bids/'.$sheet_id.'/'.$firebase_key)->getvalue();

$this->db->select('bd.buyer_id,bd.buyer_lock,us.id,bd.invoice_id,bd.buyer_hbp');
$this->db->from(BID_DETAILS.' bd');
$this->db->join(USERS.' us','us.id = bd.buyer_id');
$this->db->where('bd.invoice_id',$inv_id);
$this->db->where('bd.buyer_id',$buyer_id);
$bid_lock_self=$this->db->get();
$bid_lock_self_buyer = $bid_lock_self->result();

$this->db->select('MAX(buyer_price) as maxprice');
$this->db->from(BID_DETAILS);
$this->db->where('invoice_id',$inv_id);
$bid_max=$this->db->get();
$bid_max_details =  $bid_max->result();


//$max_price=@$bid_max_details[0]->maxprice;
$max_price=@$last_row['bid'];

$this->db->select('bd.buyer_id,us.id,bd.buyer_price,bd.invoice_id,us.company_name');
$this->db->from(BID_DETAILS.' bd');
$this->db->join(USERS.' us','us.id = bd.buyer_id');
$this->db->where('bd.invoice_id',$inv_id);
$this->db->where('bd.buyer_price',$max_price);
$bid_max_buyer=$this->db->get();
$bid_max_details_buyer = $bid_max_buyer->result();

$this->db->select('bd.user_id,bd.invoice_id,us.id,bd.comment');
$this->db->from("tt_buyer_recieve_comment".' bd');
$this->db->join(USERS.' us','us.id = bd.user_id');
$this->db->where('bd.invoice_id',$inv_id);
$this->db->where('bd.user_id',$buyer_id);
$bid_comment=$this->db->get();
$bid_comment_buyer=$bid_comment->result();

$this->db->select('invoice_id,buyer_price');
$this->db->from(BID_DETAILS);
$this->db->where('invoice_id',$inv_id);
$this->db->where('buyer_price !=',0);
$division_check=$this->db->get();
$division_check_buyer=$division_check->result();

$this->db->select('inv_id,approve'); 
$this->db->from('tt_buyer_division');
$this->db->where('inv_id',$inv_id);
$this->db->where('approve','A');
$division_check=$this->db->get();
$division_check_accept=$division_check->result();

if($max_price =="")
{
    $max_price=0;
}
else
{
    $max_price=$max_price;
}

//$getValue['bid_lock_self_buyer']=$bid_lock_self_buyer;
//$getValue['bid_max']=$bid_max_details;
//$getValue['bid_max_buyer']=$bid_max_details_buyer;



if(@$row->seller_final_lock =="")
{
    $getValue['seller_final_lock']="N";
}
else
{
    $getValue['seller_final_lock']=$row->seller_final_lock;
}

if(@$bid_lock_self_buyer[0]->buyer_lock =="")
{
    $getValue['buyer_lock']="N";
}
else
{
    $getValue['buyer_lock']=@$bid_lock_self_buyer[0]->buyer_lock;
}

$getValue['firebase_key']=$row->firebase_key;
$getValue['serial_no']=$row->serial_no;
$getValue['garden']=$row->garden;
$getValue['invoice']=$row->invoice;
$getValue['grade']=$row->grade;
$getValue['pkgs_no']=is_numeric($row->pkgs_no);
$getValue['total_kgs']=$row->total_kgs;
$getValue['price_idea']=$row->price_idea;


if(@$bid_lock_self_buyer[0]->buyer_hbp == "")
{
    $getValue['buyer_hbp']=0;
}
else
{
    $getValue['buyer_hbp']=@$bid_lock_self_buyer[0]->buyer_hbp;
}
$getValue['bid']=$max_price;

$getValue['inv_status']=@$last_row['inv_status'];
$getValue['invoice_id']=$row->invoice_id;
$getValue['key']=$key;
$getValue['pkgs_no']=trim($row->pkgs_no);
$getValue['buyer']= $last_row['buyer'];
$getValue['buyerfull']= $last_row['buyerfull'];

$getValue['comment']=substr(@$bid_comment_buyer[0]->comment,0,10);
$getValue['fullcomment']=@$bid_comment_buyer[0]->comment;
$getValue['division']= trim(@$offer_sheet['division']);

$getValue['buyer_can_see']=@$offer_sheet['buyer_can_see'];
$getValue['sheet_no']=@$offer_sheet['sheet_no'];
$getValue['sheet_id']=@$offer_sheet['sheet_id'];
$getValue['sheet_name']=@$offer_sheet['sheet_name'];
$getValue['buyerId']=@$buyer_id;
$getValue['bidMaxbuyerId']= @$bid_max_details_buyer[0]->id;
if($row->pkgs_no > @$offer_sheet['division'])
{
    $getValue['flag']="yes";
}
else
{
    $getValue['flag']="no";
}


$getValue['division_check_accept']=count(@$division_check_accept);
$getValue['division_check_buyer']=count(@$division_check_buyer);
array_push($totalArray, $getValue);



}


}
echo json_encode(array('invoice_dtl'=>$totalArray));







       
    }

    function getAlldataviewsearch(){


# code...
$sheet_id = $this
    ->uri
    ->segment(4);

 $this->db->select('*');
 $this->db->from('tt_offer_invoice');
 $this->db->where('sheet_id',$sheet_id);
 $this->db->limit(100,0);
 $qry=$this->db->get();
 $invoice_dtl =$qry->result();

$offer_sheet = $this
    ->Common
    ->find([

        'table' => OFFER_SHEET . ' os', 

        'select' => "*", 

       /* 'join' => [

            [LOCATION, 'loc', 'INNER', "loc.id = os.location"],


        ], */

       'where' => "os.sheet_id = '{$sheet_id}'", 

       'query' => 'first'
   ]);


 $dtl = $this
    ->session
    ->userdata(CUSTOMER_SESS);
$buyer_id = @$dtl['id'];

 $totalArray=array();
if(!empty($invoice_dtl)){

foreach($invoice_dtl as $key=>$row){ 

$inv_id=$row->invoice_id;

$this->db->select('bd.buyer_id,bd.buyer_lock,us.id,bd.invoice_id');
$this->db->from(BID_DETAILS.' bd');
$this->db->join(USERS.' us','us.id = bd.buyer_id');
$this->db->where('bd.invoice_id',$inv_id);
$this->db->where('bd.buyer_id',$buyer_id);
$bid_lock_self=$this->db->get();
$bid_lock_self_buyer = $bid_lock_self->result();

$this->db->select('MAX(buyer_price) as maxprice');
$this->db->from(BID_DETAILS);
$this->db->where('invoice_id',$inv_id);
$bid_max=$this->db->get();
$bid_max_details =  $bid_max->result();


$max_price=@$bid_max_details[0]->maxprice;

$this->db->select('bd.buyer_id,us.id,bd.buyer_price,bd.invoice_id,us.company_name');
$this->db->from(BID_DETAILS.' bd');
$this->db->join(USERS.' us','us.id = bd.buyer_id');
$this->db->where('bd.invoice_id',$inv_id);
$this->db->where('bd.buyer_price',$max_price);
$bid_max_buyer=$this->db->get();
$bid_max_details_buyer = $bid_max_buyer->result();

$this->db->select('bd.user_id,bd.invoice_id,us.id,bd.comment');
$this->db->from("tt_buyer_recieve_comment".' bd');
$this->db->join(USERS.' us','us.id = bd.user_id');
$this->db->where('bd.invoice_id',$inv_id);
$this->db->where('bd.user_id',$buyer_id);
$bid_comment=$this->db->get();
$bid_comment_buyer=$bid_comment->result();

$this->db->select('invoice_id,buyer_price');
$this->db->from(BID_DETAILS);
$this->db->where('invoice_id',$inv_id);
$this->db->where('buyer_price !=',0);
$division_check=$this->db->get();
$division_check_buyer=$division_check->result();

$this->db->select('inv_id,approve'); 
$this->db->from('tt_buyer_division');
$this->db->where('inv_id',$inv_id);
$this->db->where('approve','A');
$division_check=$this->db->get();
$division_check_accept=$division_check->result();

if($max_price =="")
{
    $max_price=0;
}
else
{
    $max_price=$max_price;
}

//$getValue['bid_lock_self_buyer']=$bid_lock_self_buyer;
//$getValue['bid_max']=$bid_max_details;
//$getValue['bid_max_buyer']=$bid_max_details_buyer;



if(@$row->seller_final_lock =="")
{
    $getValue['seller_final_lock']="N";
}
else
{
    $getValue['seller_final_lock']=$row->seller_final_lock;
}

if(@$bid_lock_self_buyer[0]->buyer_lock =="")
{
    $getValue['buyer_lock']="N";
}
else
{
    $getValue['buyer_lock']=@$bid_lock_self_buyer[0]->buyer_lock;
}
$getValue['serial_no']=$row->serial_no;
$getValue['garden']=$row->garden;
$getValue['invoice']=$row->invoice;
$getValue['grade']=$row->grade;
$getValue['pkgs_no']=is_numeric($row->pkgs_no);
$getValue['total_kgs']=$row->total_kgs;
$getValue['price_idea']=$row->price_idea;

$getValue['bid']=$max_price;

$getValue['inv_status']=$row->inv_status;
$getValue['invoice_id']=$row->invoice_id;
$getValue['key']=$key;
$getValue['pkgs_no']=trim($row->pkgs_no);
$getValue['buyer']= substr(@$bid_max_details_buyer[0]->company_name,0,10);
$getValue['buyerfull']= @$bid_max_details_buyer[0]->company_name;

$getValue['comment']=substr(@$bid_comment_buyer[0]->comment,0,10);
$getValue['fullcomment']=@$bid_comment_buyer[0]->comment;
$getValue['division']= trim(@$offer_sheet['division']);

$getValue['buyer_can_see']=@$offer_sheet['buyer_can_see'];
$getValue['sheet_no']=@$offer_sheet['sheet_no'];
$getValue['sheet_id']=@$offer_sheet['sheet_id'];
$getValue['sheet_name']=@$offer_sheet['sheet_name'];
$getValue['buyerId']=@$buyer_id;
$getValue['bidMaxbuyerId']= @$bid_max_details_buyer[0]->id;
if($row->pkgs_no > @$offer_sheet['division'])
{
    $getValue['flag']="yes";
}
else
{
    $getValue['flag']="no";
}


$getValue['division_check_accept']=count(@$division_check_accept);
$getValue['division_check_buyer']=count(@$division_check_buyer);
array_push($totalArray, $getValue);



}


}
echo json_encode(array('invoice_dtl'=>$totalArray));







       
    }

    function getAlldataviewclose(){
        $data = json_decode(file_get_contents("php://input"));

        $row7 = $data->row;
        $rowperpage = $data->rowperpage;
        # code...
        $sheet_id = $this ->uri ->segment(4);
        $selectedInvoiceid=array();
        $selectedKey=array();
        $dtl = $this
                    ->session
                    ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];
    
        //////////////////////////////////////////////////////////////////////////////////////

       

        $this->db->select('full_entry,sheet_id');
        $this->db->from('tt_sheet_entry');
        $this->db->where('full_entry','N');
        $this->db->where('sheet_id',$sheet_id);
        $chk=$this->db->get();
        $chkDuplicate =  $chk->result();

      

        if(!empty($chkDuplicate)){
             $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
             $decodeJson= json_decode($jsonFile,true);
             $decodeJson1 = array_slice($decodeJson, $row7, $rowperpage);

            
              $totalArray=array();
             foreach($decodeJson1 as $key=>$row){

                    $this->db->select('sheet_id,status');
                    $this->db->from('tt_switch_on');
                    $this->db->where('sheet_id',$row['sheet_id']);
                    $this->db->where('buyer_id', $row['buyerone']);
                    $getval=$this->db->get();
                    $stat= $getval->result();
                    $hbpType=@$stat[0]->status;

                    $this->db->select('us.id,tbd.buyer_request_from,us.company_name,tbd.inv_id,tbd.approve');
                    $this->db->from('tt_buyer_division tbd');
                    $this->db->join(USERS.' us','us.id=tbd.buyer_request_from','inner');
                    $this->db->where('tbd.inv_id',$row['invoice_id']);
                    $this->db->where('tbd.buyer_request_from',$buyer_id);
                    $this->db->where('tbd.approve','A');
                    $division_check=$this->db->get();
                    $chk_division_buyer_name=$division_check->result();

                    $this->db->select('us.id,tbd.buyer_request_from,tbd.inv_id,tbd.approve,tbd.buyer_request_to');
                    $this->db->from('tt_buyer_division tbd');
                    $this->db->join(USERS.' us','us.id=tbd.buyer_request_from','inner');
                    $this->db->where('tbd.inv_id',$row['invoice_id']);
                    $this->db->where('tbd.approve','A');
                    $division_check=$this->db->get();
                    $chk_division_buyer=$division_check->result();

                    $this->db->select('us.id,bd.user_id,bd.invoice_id,bd.user_id,bd.comment');
                    $this->db->from("tt_buyer_recieve_comment".' bd');
                    $this->db->join(USERS.' us','us.id = bd.user_id');
                    $this->db->where('bd.invoice_id',$row['invoice_id']);
                    $this->db->where('bd.user_id',$buyer_id);
                    $bid_comment=$this->db->get();
                    $bid_comment_buyer=$bid_comment->result();

                    $this->db->select('approve,inv_id'); 
                    $this->db->from('tt_buyer_division');
                    $this->db->where('inv_id',$row['invoice_id']);
                    $this->db->where('approve','A');
                    $division_check=$this->db->get();
                    $division_check_accept=$division_check->result();

                    if($row['bidMaxPrice'] > 0){
                        $division_check_buyer =1; 
                    }else{
                        $division_check_buyer =0; 
                    }

                    

                    if(($hbpType!='semi_aumatic' || $hbpType=="")){

                        if($row['inv_status']!='A' && $row['abpone'] >= $row['price_idea']){

                                $soldBy = $row['buyerone'];
                                $max_price = $row['price_idea'];
                                $invStatus = 'A';
                                $row['bidMaxbuyerId'] = $row['buyerone'];

                                $this->db->select('id,company_name');
                                $this->db->from(USERS);
                                $this->db->where('id',$soldBy);
                                $name=$this->db->get();
                                $company= $name->result();
                                $companName=@$company[0]->company_name;
                               
                        }elseif($row['inv_status']=='A' && $row['sold_by'] !=""){
                            $soldBy = $row['sold_by'];
                            $invStatus = 'A';
                            $max_price = $row['bidMaxPrice'];
                            $this->db->select('id,company_name');
                            $this->db->from(USERS);
                            $this->db->where('id',$soldBy);
                            $name=$this->db->get();
                            $company= $name->result();
                            $companName=@$company[0]->company_name;
                        }elseif($row['inv_status']!='A'){
                            $soldBy = $row['bidMaxbuyerId'];
                            $invStatus = 'I';
                            $max_price = $row['bidMaxPrice'];
                            $this->db->select('id,company_name');
                            $this->db->from(USERS);
                            $this->db->where('id',$soldBy);
                            $name=$this->db->get();
                            $company= $name->result();
                            $companName=@$company[0]->company_name;
                        }else{

                        }
                    }else{
                       if($row['inv_status']=='A'){
                            $soldBy = $row['sold_by'];
                            $invStatus = 'A';
                            $max_price = $row['bidMaxPrice'];
                            $this->db->select('id,company_name');
                            $this->db->from(USERS);
                            $this->db->where('id',$soldBy);
                            $name=$this->db->get();
                            $company= $name->result();
                            $companName=@$company[0]->company_name;
                        }else{
                            $soldBy = $row['bidMaxbuyerId'];
                            $invStatus = 'I';
                            $max_price = $row['bidMaxPrice'];
                            $this->db->select('id,company_name');
                            $this->db->from(USERS);
                            $this->db->where('id',$soldBy);
                            $name=$this->db->get();
                            $company= $name->result();
                            $companName=@$company[0]->company_name;
                        }
                    }

                    if($max_price =="")
                            {
                                $max_price=0;
                            }
                            else
                            {
                                $max_price=$max_price;
                            }

                  
                    $getValue['sold_by']=$soldBy;
                    $getValue['req_to']=@$chk_division_buyer[0]->buyer_request_to;
                    $getValue['chk_division_buyer']=$chk_division_buyer;
                    $getValue['chk_division_buyer_name']=$chk_division_buyer_name;
                    $getValue['serial_no']=$row['serial_no'];
                    $getValue['garden']=$row['garden'];
                    $getValue['invoice']=$row['invoice'];
                    $getValue['grade']=$row['grade'];
                    $getValue['pkgs_no']=is_numeric($row['pkgs_no']);
                    $getValue['total_kgs']=$row['total_kgs'];
                    $getValue['price_idea']=$row['price_idea'];

                    $getValue['bid']=$max_price;

                    $getValue['chk_division_buyer']=$chk_division_buyer;

                    $getValue['chk_name']=@$chk_division_buyer_name[0]->company_name;

                    $getValue['inv_status']=$invStatus;
                    $getValue['invoice_id']=$row['invoice_id'];
                    $getValue['key']=$key;
                    $getValue['pkgs_no']=$row['pkgs_no'];
                    $getValue['buyer']= substr($companName,0,10);
                    $getValue['buyerfull']= $companName;
                    $getValue['buyer_lock']=$row['invoice_id']['buyer_bid'][$buyer_id]['buyer_lock'];
                    $getValue['comment']=substr(@$bid_comment_buyer[0]->comment,0,10);
                    $getValue['fullcomment']=@$bid_comment_buyer[0]->comment;
                    $getValue['division']= is_numeric(@$row['division']);
                    $getValue['seller_final_lock']=@$row['seller_final_lock'];
                    $getValue['buyer_can_see']=@$row['buyer_can_see'];
                    $getValue['sheet_no']=@$row['sheet_no'];
                    $getValue['sheet_id']=@$row['sheet_id'];
                    $getValue['sheet_name']=@$row['sheet_name'];
                    $getValue['buyerId']=@$buyer_id;
                    $getValue['bidMaxbuyerId']= @$row['bidMaxbuyerId'];

                    $getValue['division_check_accept']=count(@$division_check_accept);


                    $getValue['division_check_buyer']=@$division_check_buyer;

                    $getValue['roundpkg']= round(@$row['pkgs_no']/2);

                    array_push($totalArray, $getValue);

             }
        }else{
              $this->db->select('invoice_id,sheet_id,sold_by,serial_no,garden,invoice,grade,pkgs_no,total_kgs,price_idea,inv_status,seller_final_lock');
         $this->db->from('tt_offer_invoice');
         $this->db->where('sheet_id',$sheet_id);
         $this->db->limit($rowperpage,$row7);
         $qry=$this->db->get();
         $invoice_dtl1 =$qry->result();

        $offer_sheet = $this
            ->Common
            ->find([

                'table' => OFFER_SHEET . ' os', 

                'select' => "os.division,os.buyer_can_see,os.sheet_no,os.sheet_id,os.sheet_name",

                'where' => "os.sheet_id = '{$sheet_id}'", 

               'query' => 'first'
           ]);


        
        $totalArray=array();
         if(!empty($invoice_dtl1)){
        
        foreach($invoice_dtl1 as $key=>$row){ 

        $inv_id=$row->invoice_id;

        $this->db->select('us.id,tbd.buyer_request_from,us.company_name,tbd.inv_id,tbd.approve');
        $this->db->from('tt_buyer_division tbd');
        $this->db->join(USERS.' us','us.id=tbd.buyer_request_from','inner');
        $this->db->where('tbd.inv_id',$inv_id);
        $this->db->where('tbd.buyer_request_from',$buyer_id);
        $this->db->where('tbd.approve','A');

        $division_check=$this->db->get();
        $chk_division_buyer_name=$division_check->result();

        $this->db->select('us.id,bd.buyer_id,bd.invoice_id,bd.buyer_lock');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->join(USERS.' us','us.id = bd.buyer_id');
        $this->db->where('bd.invoice_id',$inv_id);
        $this->db->where('bd.buyer_id',$buyer_id);
        $bid_lock_self=$this->db->get();
        $bid_lock_self_buyer = $bid_lock_self->result();

        $this->db->select('MAX(buyer_price) as maxprice');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$inv_id);
        $bid_max=$this->db->get();
        $bid_max_details =  $bid_max->result();

       
        $max_price=@$bid_max_details[0]->maxprice;

        $this->db->select('us.id,bd.buyer_id,bd.invoice_id,bd.buyer_price,us.company_name');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->join(USERS.' us','us.id = bd.buyer_id');
        $this->db->where('bd.invoice_id',$inv_id);
        $this->db->where('bd.buyer_price',$max_price);
        $bid_max_buyer=$this->db->get();
        $bid_max_details_buyer = $bid_max_buyer->result();


        $this->db->select('us.id,tbd.buyer_request_from,tbd.inv_id,tbd.approve,tbd.buyer_request_to');
        $this->db->from('tt_buyer_division tbd');
        $this->db->join(USERS.' us','us.id=tbd.buyer_request_from','inner');
        $this->db->where('tbd.inv_id',$inv_id);
          //$this->db->where('tbd.buyer_request_to',$req_to);
        $this->db->where('tbd.approve','A');
          
        $division_check=$this->db->get();
        $chk_division_buyer=$division_check->result();

        $this->db->select('us.id,bd.user_id,bd.invoice_id,bd.user_id,bd.comment');
        $this->db->from("tt_buyer_recieve_comment".' bd');
        $this->db->join(USERS.' us','us.id = bd.user_id');
        $this->db->where('bd.invoice_id',$inv_id);
        $this->db->where('bd.user_id',$buyer_id);
        $bid_comment=$this->db->get();
        $bid_comment_buyer=$bid_comment->result();

        $this->db->select('invoice_id,buyer_price');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$inv_id);
        $this->db->where('buyer_price !=',0);
        $division_check=$this->db->get();
        $division_check_buyer=$division_check->result();

        $this->db->select('approve,inv_id'); 
        $this->db->from('tt_buyer_division');
        $this->db->where('inv_id',$inv_id);
        $this->db->where('approve','A');
        $division_check=$this->db->get();
        $division_check_accept=$division_check->result();

        if($max_price =="")
        {
            $max_price=0;
        }
        else
        {
            $max_price=$max_price;
        }

        //$getValue['bid_lock_self_buyer']=$bid_lock_self_buyer;
        //$getValue['bid_max']=$bid_max_details;
        //$getValue['bid_max_buyer']=$bid_max_details_buyer;
        $getValue['sold_by']=$row->sold_by;
        $getValue['req_to']=@$chk_division_buyer[0]->buyer_request_to;
        $getValue['chk_division_buyer']=$chk_division_buyer;
        $getValue['chk_division_buyer_name']=$chk_division_buyer_name;
        $getValue['serial_no']=$row->serial_no;
        $getValue['garden']=$row->garden;
        $getValue['invoice']=$row->invoice;
        $getValue['grade']=$row->grade;
        $getValue['pkgs_no']=is_numeric($row->pkgs_no);
        $getValue['total_kgs']=$row->total_kgs;
        $getValue['price_idea']=$row->price_idea;

        $getValue['bid']=$max_price;

        $getValue['chk_division_buyer']=$chk_division_buyer;

        $getValue['chk_name']=@$chk_division_buyer_name[0]->company_name;

        $getValue['inv_status']=$row->inv_status;
        $getValue['invoice_id']=$row->invoice_id;
        $getValue['key']=$key;
        $getValue['pkgs_no']=$row->pkgs_no;
        $getValue['buyer']= substr(@$bid_max_details_buyer[0]->company_name,0,10);
        $getValue['buyerfull']= @$bid_max_details_buyer[0]->company_name;
        $getValue['buyer_lock']=@$bid_lock_self_buyer[0]->buyer_lock;
        $getValue['comment']=substr(@$bid_comment_buyer[0]->comment,0,10);
        $getValue['fullcomment']=@$bid_comment_buyer[0]->comment;
        $getValue['division']= is_numeric(@$offer_sheet['division']);
        $getValue['seller_final_lock']=$row->seller_final_lock;
        $getValue['buyer_can_see']=@$offer_sheet['buyer_can_see'];
        $getValue['sheet_no']=@$offer_sheet['sheet_no'];
        $getValue['sheet_id']=@$offer_sheet['sheet_id'];
        $getValue['sheet_name']=@$offer_sheet['sheet_name'];
        $getValue['buyerId']=@$buyer_id;
        $getValue['bidMaxbuyerId']= @$bid_max_details_buyer[0]->id;

        $getValue['division_check_accept']=count(@$division_check_accept);
        $getValue['division_check_buyer']=count(@$division_check_buyer);

        $getValue['roundpkg']= round($row->pkgs_no/2);


        



        array_push($totalArray, $getValue);



        }


        }
        }
       

        ///////////////////////////////////////////////////////////////////////////

       

         echo json_encode(array('invoice_dtl'=>$totalArray));
    }
 /*function getAlldataviewcloseBKKK()
    {
        $data = json_decode(file_get_contents("php://input"));

        $row = $data->row;
        $rowperpage = $data->rowperpage;
        # code...
        $sheet_id = $this ->uri ->segment(4);
        $selectedInvoiceid=array();
        $selectedKey=array();

         $this->db->select('invoice_id,sheet_id,sold_by,serial_no,garden,invoice,grade,pkgs_no,total_kgs,price_idea,inv_status,seller_final_lock');
         $this->db->from('tt_offer_invoice');
         $this->db->where('sheet_id',$sheet_id);
         $this->db->limit($rowperpage,$row);
         $qry=$this->db->get();
         $invoice_dtl =$qry->result();

        foreach($invoice_dtl as $row1){
            array_push($selectedInvoiceid, $row1->invoice_id);
        }

        if(!empty($selectedInvoiceid)){
            $this->db->select('tbd.sheet_id,tos.sheet_id,tbd.push_hbp,tbd.hbp_time,tbd.buyer_id,tbd.invoice_id,tos.expiry_date,tbd.buyer_hbp,tbd.seller_id,tbd.hbp_key,tos.complete_close');
            $this->db->from('tt_bid_details tbd');
            $this->db->join('tt_offer_sheets tos','tbd.sheet_id=tos.sheet_id');
            $this->db->where_in('tbd.invoice_id',$selectedInvoiceid);
            $this->db->where('tbd.sheet_id',$sheet_id);
            $this->db->where('tos.expire','N');
            $this->db->where('tbd.push_hbp','N');
            $this->db->order_by('tbd.hbp_time',"ASC");
            $qry=$this->db->get();
            $bid_details_hbpY= $qry->result();
            $countbiddetailshbpY = count(@$bid_details_hbpY);



   
       
        if (!empty($bid_details_hbpY)){
            foreach ($bid_details_hbpY as $row){  

          
                $this->db->select('sheet_id,status');
                $this->db->from('tt_switch_on');
                $this->db->where('sheet_id',$row->sheet_id);
                $this->db->where('buyer_id',$row->buyer_id);
                $getval=$this->db->get();
                $stat= $getval->result();

                $hbpType=@$stat[0]->status;

                $this->db->select('invoice_id,inv_status,price_idea');
                $this->db->from(OFFER_INVOICE);
                $this->db->where('invoice_id',$row->invoice_id);
                $getValinv=$this->db->get();
                $getHBPdatainv= $getValinv->result();

                $this->db->select('buyer_id,sheet_id,bid_max_qty');
                $this->db->from('tt_buyer_sheet_assigned');
                $this->db->where('buyer_id',$row->buyer_id);
                $this->db->where('sheet_id',$row->sheet_id);
                $getval2=$this->db->get();
                $bidmaxqty= $getval2->result();

                if(@$bidmaxqty[0]->bid_max_qty== "" || @$bidmaxqty[0]->bid_max_qty==0){
                    $getbidmaxqty=9999;
                }
                else{
                    $getbidmaxqty=@$bidmaxqty[0]->bid_max_qty;

                }
                $this->db->select('buyer_id,sheet_id,push_hbp');
                $this->db->from(BID_DETAILS);
                $this->db->where('buyer_id',$row->buyer_id);
                $this->db->where('sheet_id',$row->sheet_id);
                $this->db->where('push_hbp','Y');
                $countgetVal=$this->db->get();
                $countgetHBPdata= $countgetVal->result();

                $expDatetime=$row->expiry_date;
                $timeFirst  = strtotime(date('Y-m-d H:i:s'));
                $timeSecond = strtotime($expDatetime);
                $differenceInSeconds = $timeSecond - $timeFirst;


                  ////////////////////////////////////////////////////////
                    $allHBPY = array(
                                'push_hbp'=>'Y'
                            );
                    $this->db->where('buyer_id', $row->buyer_id);
                    $this->db->where('invoice_id', @$row->invoice_id);
                    $this->db ->update(BID_DETAILS, $allHBPY);
                    /////////////////////////////////////////////////////// 

                if(($hbpType!='semi_aumatic' || $hbpType=="")){
                   
                    if(@$getHBPdatainv[0]->inv_status!='A' && intval($row->buyer_hbp) >=intval(@$getHBPdatainv[0]->price_idea) && count($countgetHBPdata) < intval($getbidmaxqty)){
                            
                        $dataBiddetails = array(
                            'buyer_price' => @$getHBPdatainv[0]->price_idea,
                            'bid_time' => date('Y-m-d H:i:s'),
                            'push_hbp'=>'Y'
                         );

                        $data_log = array(

                        'buyer_id' => $row->buyer_id,
                        'seller_id' => $row->seller_id,
                        'invoice_id' => $row->invoice_id,
                        'sheet_id' => $row->sheet_id,
                        'buyer_price' => @$getHBPdatainv[0]->price_idea,
                        'seller_price' => @$getHBPdatainv[0]->price_idea,
                         'bid_on' => date('Y-m-d H:i:s') ,
                        );
                        $this
                            ->db
                            ->insert(BID_LOG, $data_log);

                            $this
                            ->db
                            ->where('buyer_id', $row->buyer_id);
                            $this
                                ->db
                                ->where('invoice_id', @$row->invoice_id);
                            $this
                            ->db
                            ->update(BID_DETAILS, $dataBiddetails);

                            $this
                            ->db
                            ->where('inv_id', @$row->invoice_id);
                            //$this->db->where('approve','P');
                            $this
                                ->db
                                ->delete('tt_buyer_division');

                            $data_sold = array(
                            'inv_status' => 'A',
                            'hbp_key' => @$row->hbp_key,
                            'sold_by' => @$row->buyer_id,
                            'sold_on' => date('Y-m-d')
                             );
                            $this
                                ->db
                                ->where('invoice_id', @$row->invoice_id);
                            $this
                                ->db
                                ->update(OFFER_INVOICE, $data_sold);

                        
                        }
                }  
            }
        }  
       

       

       

        $offer_sheet = $this
            ->Common
            ->find([

                'table' => OFFER_SHEET . ' os', 

                'select' => "os.division,os.buyer_can_see,os.sheet_no,os.sheet_id,os.sheet_name",

                'where' => "os.sheet_id = '{$sheet_id}'", 

               'query' => 'first'
           ]);


         $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];
    
        $totalArray=array();

        if(!empty($selectedInvoiceid)){
            $this->db->select('invoice_id,sheet_id,sold_by,serial_no,garden,invoice,grade,pkgs_no,total_kgs,price_idea,inv_status,seller_final_lock');
            $this->db->from('tt_offer_invoice');
            $this->db->where('sheet_id',$sheet_id);
            $this->db->where_in('invoice_id',$selectedInvoiceid);
            $qry1=$this->db->get();
            $invoice_dtl1 =$qry1->result();
        }else{
            $this->db->select('invoice_id,sheet_id,sold_by,serial_no,garden,invoice,grade,pkgs_no,total_kgs,price_idea,inv_status,seller_final_lock');
            $this->db->from('tt_offer_invoice');
            $this->db->where('sheet_id',$sheet_id);
            $qry1=$this->db->get();
            $invoice_dtl1 =$qry1->result();
        }



       if(!empty($invoice_dtl1)){
        
        foreach($invoice_dtl1 as $key=>$row){ 

        $inv_id=$row->invoice_id;

        $this->db->select('us.id,tbd.buyer_request_from,us.company_name,tbd.inv_id,tbd.approve');
        $this->db->from('tt_buyer_division tbd');
        $this->db->join(USERS.' us','us.id=tbd.buyer_request_from','inner');
        $this->db->where('tbd.inv_id',$inv_id);
        $this->db->where('tbd.buyer_request_from',$buyer_id);
        $this->db->where('tbd.approve','A');

        $division_check=$this->db->get();
        $chk_division_buyer_name=$division_check->result();

        $this->db->select('us.id,bd.buyer_id,bd.invoice_id,bd.buyer_lock');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->join(USERS.' us','us.id = bd.buyer_id');
        $this->db->where('bd.invoice_id',$inv_id);
        $this->db->where('bd.buyer_id',$buyer_id);
        $bid_lock_self=$this->db->get();
        $bid_lock_self_buyer = $bid_lock_self->result();

        $this->db->select('MAX(buyer_price) as maxprice');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$inv_id);
        $bid_max=$this->db->get();
        $bid_max_details =  $bid_max->result();

       
        $max_price=@$bid_max_details[0]->maxprice;

        $this->db->select('us.id,bd.buyer_id,bd.invoice_id,bd.buyer_price,us.company_name');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->join(USERS.' us','us.id = bd.buyer_id');
        $this->db->where('bd.invoice_id',$inv_id);
        $this->db->where('bd.buyer_price',$max_price);
        $bid_max_buyer=$this->db->get();
        $bid_max_details_buyer = $bid_max_buyer->result();


        $this->db->select('us.id,tbd.buyer_request_from,tbd.inv_id,tbd.approve,tbd.buyer_request_to');
        $this->db->from('tt_buyer_division tbd');
        $this->db->join(USERS.' us','us.id=tbd.buyer_request_from','inner');
        $this->db->where('tbd.inv_id',$inv_id);
          //$this->db->where('tbd.buyer_request_to',$req_to);
        $this->db->where('tbd.approve','A');
          
        $division_check=$this->db->get();
        $chk_division_buyer=$division_check->result();

        $this->db->select('us.id,bd.user_id,bd.invoice_id,bd.user_id,bd.comment');
        $this->db->from("tt_buyer_recieve_comment".' bd');
        $this->db->join(USERS.' us','us.id = bd.user_id');
        $this->db->where('bd.invoice_id',$inv_id);
        $this->db->where('bd.user_id',$buyer_id);
        $bid_comment=$this->db->get();
        $bid_comment_buyer=$bid_comment->result();

        $this->db->select('invoice_id,buyer_price');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$inv_id);
        $this->db->where('buyer_price !=',0);
        $division_check=$this->db->get();
        $division_check_buyer=$division_check->result();

        $this->db->select('approve,inv_id'); 
        $this->db->from('tt_buyer_division');
        $this->db->where('inv_id',$inv_id);
        $this->db->where('approve','A');
        $division_check=$this->db->get();
        $division_check_accept=$division_check->result();

        if($max_price =="")
        {
            $max_price=0;
        }
        else
        {
            $max_price=$max_price;
        }

        //$getValue['bid_lock_self_buyer']=$bid_lock_self_buyer;
        //$getValue['bid_max']=$bid_max_details;
        //$getValue['bid_max_buyer']=$bid_max_details_buyer;
        $getValue['sold_by']=$row->sold_by;
        $getValue['req_to']=@$chk_division_buyer[0]->buyer_request_to;
        $getValue['chk_division_buyer']=$chk_division_buyer;
        $getValue['chk_division_buyer_name']=$chk_division_buyer_name;
        $getValue['serial_no']=$row->serial_no;
        $getValue['garden']=$row->garden;
        $getValue['invoice']=$row->invoice;
        $getValue['grade']=$row->grade;
        $getValue['pkgs_no']=is_numeric($row->pkgs_no);
        $getValue['total_kgs']=$row->total_kgs;
        $getValue['price_idea']=$row->price_idea;

        $getValue['bid']=$max_price;

        $getValue['chk_division_buyer']=$chk_division_buyer;

        $getValue['chk_name']=@$chk_division_buyer_name[0]->company_name;

        $getValue['inv_status']=$row->inv_status;
        $getValue['invoice_id']=$row->invoice_id;
        $getValue['key']=$key;
        $getValue['pkgs_no']=$row->pkgs_no;
        $getValue['buyer']= substr(@$bid_max_details_buyer[0]->company_name,0,10);
        $getValue['buyerfull']= @$bid_max_details_buyer[0]->company_name;
        $getValue['buyer_lock']=@$bid_lock_self_buyer[0]->buyer_lock;
        $getValue['comment']=substr(@$bid_comment_buyer[0]->comment,0,10);
        $getValue['fullcomment']=@$bid_comment_buyer[0]->comment;
        $getValue['division']= is_numeric(@$offer_sheet['division']);
        $getValue['seller_final_lock']=$row->seller_final_lock;
        $getValue['buyer_can_see']=@$offer_sheet['buyer_can_see'];
        $getValue['sheet_no']=@$offer_sheet['sheet_no'];
        $getValue['sheet_id']=@$offer_sheet['sheet_id'];
        $getValue['sheet_name']=@$offer_sheet['sheet_name'];
        $getValue['buyerId']=@$buyer_id;
        $getValue['bidMaxbuyerId']= @$bid_max_details_buyer[0]->id;

        $getValue['division_check_accept']=count(@$division_check_accept);
        $getValue['division_check_buyer']=count(@$division_check_buyer);

        $getValue['roundpkg']= round($row->pkgs_no/2);


        



        array_push($totalArray, $getValue);



        }


        }

        $this->db->select('tbd.sheet_id,tos.sheet_id,tbd.push_hbp,tbd.hbp_time,tbd.buyer_id,tbd.invoice_id,tos.expiry_date,tbd.buyer_hbp,tbd.seller_id,tbd.hbp_key,tos.complete_close');
        $this->db->from('tt_bid_details tbd');
        $this->db->join('tt_offer_sheets tos','tbd.sheet_id=tos.sheet_id');
        $this->db->where('tbd.sheet_id',$sheet_id);
        $this->db->where('tos.expire','N');
        $this->db->where('tbd.push_hbp','N');
        $this->db->order_by('tbd.hbp_time',"ASC");
        $qry2=$this->db->get();
        $bid_details_hbpY2= $qry2->result();
      
       $countbiddetailshbpY2 = count(@$bid_details_hbpY2);
        if($countbiddetailshbpY2 == 0){
                    $completeClose = array('complete_close'=>'Y');
                    $this->db->where('sheet_id',$sheet_id);
                    $this->db->update('tt_offer_sheets',$completeClose);
        }
        }else{
            $bid_details_hbpY= array();
            $countbiddetailshbpY = 0;
            $totalArray  = array();
        }

    
        echo json_encode(array('invoice_dtl'=>$totalArray,'bid_details_hbpY'=>@$countbiddetailshbpY2));
    }*/
    function seller_active_offer_sheet()
    {

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['bid_details'] = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' bd', 'select' => "*", 'where' => "bd.created_by = {$dtl['id']}"]);

        //echo "<pre>"; print_r($this->data['bid_details']);exit;
        

        $this
            ->layout
            ->view('seller-active-offer-sheet', $this->data);

    }
    function seller_close_offer_sheet()
    {

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['bid_details'] = $this
            ->Common
            ->find([
                    'table' => BID_DETAILS . ' bd', 
                    'select' => "os.sheet_id,bd.sheet_id,bd.seller_id,os.created_date,os.expiry_date,os.expire,os.sheet_name,os.sheet_no,os.created_by", 
                    'join' => [
                        [OFFER_SHEET, 'os', 'INNER', "os.sheet_id = bd.sheet_id"],
                    ], 
                    'where' => "os.created_by = {$dtl['id']}", 
                    'group' => 'bd.sheet_id',
                    "order_by"=>"os.created_date desc",
                    ]);

        //echo "<pre>"; print_r($this->data['bid_details']);exit;
        

        $this
            ->layout
            ->view('seller-close-offer-sheet', $this->data);

    }
    function seller_close_offer_sheet_next()
    {
        $sheet_id = $this
            ->uri
            ->segment(2);
        $sheet_id = decrypt($sheet_id);
        if ($sheet_id == "")
        {
            redirect(BASE_URL . 'close-offer-sheet');

        }
        //echo $sheet_id;
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['offer_sheet'] = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $this->data['invoice_dtl'] = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

        $this->data['offer_sheet_buyer'] = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],

        ], 'where' => "bsa.sheet_id = {$sheet_id}",

        ]);

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$this->data['payment_type'] = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

        $this->data['bid_details'] = $this
            ->Common
            ->find(['table' => BID_DETAILS, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} AND sheet_id = {$sheet_id}",

        ]);

        $this
            ->layout
            ->view('seller-close-offer-sheet-next', $this->data);

    }

    function seller_close_reminder_send()
    {
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        $seller_id = $dtl['id'];
        $sheet_id = $this
            ->input
            ->post('id');

        $bid_details = $this
            ->Common
            ->find(['table' => SHEET_CLOSE . ' sc', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = sc.buyer_id"],

        ], 'where' => "sc.sheet_id = {$sheet_id} AND sc.close_status ='S'",

        ]);

        if (!empty($bid_details))
        {
            for ($i = 0;$i < count($bid_details);$i++)
            {
?>

              <span style="color: red;"><?php echo $bid_details[$i]['company_name']; ?></span><br>
             
             

            <?php
            }

?>
        

        <p>Above buyers yet to accept closure</p>
        <?php
        }

        else
        {
?>
         <p>No buyers yet to accept closure</p>.


        <?php

        }

    }

    function modify_notification_buyer()
    {

        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = $dtl['id'];
        $sheet_id = $this
            ->input
            ->post('shtid');

        $chk_noti = $this
            ->Common
            ->find(['table' => SHEET_CLOSE, 'select' => "*",

        'where' => "sheet_id = {$sheet_id} AND buyer_id = {$buyer_id}", 'query' => 'first'

        ]);

        $count = @$chk_noti['count'];

        $m_count = $count - 1;

        $data = array(
            'count' => $m_count
        );
        $this
            ->db
            ->where('sheet_id', $sheet_id);
        $this
            ->db
            ->where('buyer_id', $buyer_id);
        $this
            ->db
            ->update(SHEET_CLOSE, $data);

        $res = 1;
        echo json_encode($res);

    }

    function accept_notification_buyer()
    {

        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = $dtl['id'];
        $sheet_id = $this
            ->input
            ->post('shtid');
        $data = array(
            'close_status' => 'R'
        );
        $this
            ->db
            ->where('sheet_id', $sheet_id);
        $this
            ->db
            ->where('buyer_id', $buyer_id);
        $this
            ->db
            ->update(SHEET_CLOSE, $data);

        $chk_noti_S = $this
            ->Common
            ->find(['table' => SHEET_CLOSE, 'select' => "*",

        'where' => "sheet_id = {$sheet_id}",

        ]);

        $chk_noti_R = $this
            ->Common
            ->find(['table' => SHEET_CLOSE, 'select' => "*",

        'where' => "sheet_id = {$sheet_id} AND close_status ='R'",

        ]);

        if (count($chk_noti_R) == count($chk_noti_S))
        {
            $data_expire_sheet = array(
                'expire' => 'Y'
            );
            $this
                ->db
                ->where('sheet_id', $sheet_id);
            $this
                ->db
                ->update(OFFER_SHEET, $data_expire_sheet);

        }

        $res = 1;
        echo json_encode($res);

    }

    function check_seller_close_sheet()
    {

        $sheet_id = $this
            ->input
            ->post('shtId');
        $chk_noti_exp = $this
            ->Common
            ->find(['table' => OFFER_SHEET, 'select' => "*",

        'where' => "sheet_id = '{$sheet_id}' AND expire = 'Y'", 'query' => 'count'

        ]);

        if ($chk_noti_exp > 0)
        {
            $res = 1;

        }
        else
        {
            $res = 2;
        }
        echo json_encode($res);

    }

    function check_notification_buyer()
    {

        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = $dtl['id'];
        /* $sheet_id=decrypt($this->input->post('id'));*/

        $chk_noti_S = $this
            ->Common
            ->find(['table' => SHEET_CLOSE . ' sc', 'select' => "*", 'join' => [[OFFER_SHEET, 'os', 'INNER', "os.sheet_id = sc.sheet_id"],

        ],

        'where' => "sc.buyer_id = {$buyer_id} AND sc.close_status ='S' AND sc.count != 0",

        ]);

        
            echo json_encode($chk_noti_S);
        
        

    }

    function seller_close_noti_send_again()
    {
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        $seller_id = $dtl['id'];
        $sheet_id = decrypt($this
            ->input
            ->post('id'));

        $sheet_close = $this
            ->Common
            ->find(['table' => SHEET_CLOSE, 'select' => "*", 'where' => "sheet_id = {$sheet_id} AND close_status = 'S' AND count = 0",

        ]);

        if (!empty($sheet_close))
        {
            for ($i = 0;$i < count($sheet_close);$i++)
            {

                $data_count = array(
                    'count' => '2'
                );

                $this
                    ->db
                    ->where('id', @$sheet_close[$i]['id']);
                $this
                    ->db
                    ->update(SHEET_CLOSE, $data_count);

            }
        }

        $res = 1;
        echo json_encode($res);

    }

    function seller_close_noti_send()
    {

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        $seller_id = $dtl['id'];
        $sheet_id = decrypt($this
            ->input
            ->post('id'));
        $bid_details = $this
            ->Common
            ->find(['table' => 'tt_bid_log', 'select' => "*", 'where' => "sheet_id = {$sheet_id}", 'group' => "buyer_id",

        ]);

        $seller = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$seller_id}", 'query' => 'first'

        ]);

        $seller_name = @$seller['company_name'];

        if (!empty($bid_details))
        {
            for ($i = 0;$i < count($bid_details);$i++)
            {
                $data = array(
                    'sheet_id' => $sheet_id,
                    'seller_id' => $seller_id,
                    'buyer_id' => $bid_details[$i]['buyer_id'],
                    'close_status' => 'S',
                    'close_on' => date('Y-m-d H:i:s') ,
                    'enc_id' => encrypt($sheet_id) ,
                    'count' => '2'

                );

                $this
                    ->db
                    ->insert(SHEET_CLOSE, $data);

                $buyer_id = @$bid_details[$i]['buyer_id'];

                $users = $this
                    ->Common
                    ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$buyer_id}", 'query' => 'first'

                ]);

                $sheet_details = $this
                    ->Common
                    ->find(['table' => OFFER_SHEET, 'select' => "*", 'where' => "sheet_id = {$sheet_id}", 'query' => 'first'

                ]);

                $sheet_name = @$sheet_details['sheet_name'];
                $sheet_no = @$sheet_details['sheet_no'];

                $to = @$users['phone'];

                //$body = $seller_name . " is requesting to close offer sheet " . $sheet_no . '-' . $sheet_name . ' immedietly.';

                $messageId='111596';
                $variables=$seller_name.'|'.$sheet_no.'|'.$sheet_name;
                send_sms($to, $messageId, $variables);

                //send_sms($to, $body);

            }

        }
        $result = 1;
        echo json_encode($result);

    }

    function dispatch_offer_sheet_edit()
    {

        $sheet_id = $this
            ->uri
            ->segment(2);
        $sheet_id = decrypt($sheet_id);
        if ($sheet_id == "")
        {
            redirect(BASE_URL . 'dispatch-offer-sheet');

        }
        //echo $sheet_id;
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['offer_sheet'] = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $this->data['invoice_dtl'] = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

        $this->data['offer_sheet_buyer'] = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],

        ], 'where' => "bsa.sheet_id = {$sheet_id}",

        ]);

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$this->data['payment_type'] = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

        $this->data['bid_details'] = $this
            ->Common
            ->find(['table' => BID_DETAILS, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} AND sheet_id = {$sheet_id}",

        ]);

        $this
            ->layout
            ->view('seller-dispatch-offer-sheet-edit', $this->data);

    }

    function seller_active_offer_sheet_next()
    {
        $sheet_id = $this
            ->uri
            ->segment(2);
        $sheet_id = decrypt($sheet_id);
        if ($sheet_id == "")
        {
            redirect(BASE_URL . 'active-offer-sheet');

        }
        //echo $sheet_id;
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['chk_close_sheet'] = $this
            ->Common
            ->find(['table' => SHEET_CLOSE, 'select' => "*", 'where' => "sheet_id = {$sheet_id} AND seller_id = {$dtl['id']}", 'query' => 'first'

        ]);

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this->data['offer_sheet'] = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $this->data['invoice_dtl'] = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

        $this->data['offer_sheet_buyer'] = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],

        ], 'where' => "bsa.sheet_id = {$sheet_id}",

        ]);

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$this->data['payment_type'] = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

        $this->data['bid_details'] = $this
            ->Common
            ->find(['table' => BID_DETAILS, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} AND sheet_id = {$sheet_id}",

        ]);

        $this
            ->layout
            ->view('seller-active-offer-sheet-next', $this->data);

    }

    function seller_report()
    {
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['seller_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this
            ->db
            ->select('garden');
        $this
            ->db
            ->distinct();
        $query = $this
            ->db
            ->get(OFFER_INVOICE);
        $this->data['garden'] = $query->result();

        $this
            ->db
            ->select('grade');
        $this
            ->db
            ->distinct();
        $query = $this
            ->db
            ->get(OFFER_INVOICE);
        $this->data['grade'] = $query->result();

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from(USERS);
        $this
            ->db
            ->where('role', '2');
        $query = $this
            ->db
            ->get();
        $this->data['buyer'] = $query->result();

        $this
            ->layout
            ->view('seller-report', $this->data);
    }

    function seller_report_action()
    {
        $this
            ->load
            ->helper('download');
        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);

        $type = $this
            ->input
            ->post('type');
        $period = $this
            ->input
            ->post('period');

        $garden = $this
            ->input
            ->post('garden');
        $grade = $this
            ->input
            ->post('grade');
        $buyer = $this
            ->input
            ->post('buyer');

        if ($period == "custom")
        {
            $from_date = $this
                ->input
                ->post('from_date');
            $to_date = $this
                ->input
                ->post('to_date');

            $to_date_all = date('Y-m-d H:i:s', strtotime($to_date));
            $from_date_all = date('Y-m-d H:i:s', strtotime($from_date));




        }
        else
        {
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-d', strtotime($to_date . ' -' . $period . ' day'));


            $to_date_all = date('Y-m-d H:i:s');
            $from_date_all = date('Y-m-d H:i:s', strtotime($to_date . ' -' . $period . ' day'));
        }

        /*$convertForm=date("d-m-Y", strtotime($from_date));
        $convertTo=date("d-m-Y", strtotime($to_date));
        $from_date = strtotime(@$convertForm);
        $to_date = strtotime(@$convertTo);  */
        //echo $from_date;exit;

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=Tea Inntech report.csv");

        if ($type != "summary")
        {

            

            $this
                ->db
                ->select('os.sheet_id,oi.sheet_id,oi.sold_on,oi.garden,oi.grade,oi.sold_by,os.created_date');
            $this
                ->db
                ->from(OFFER_INVOICE . ' oi');
            $this
                ->db
                ->join(OFFER_SHEET . ' os', 'os.sheet_id=oi.sheet_id', 'inner');

            if($type !="all")
            {

            $this
                ->db
                ->where('oi.sold_on >=', $from_date);
            $this
                ->db
                ->where('oi.sold_on <=', $to_date);

            if ($garden != "")
            {
                $this
                    ->db
                    ->where('oi.garden', $garden);
            }
            if ($grade != "")
            {
                $this
                    ->db
                    ->where('oi.grade', $grade);
            }
            if ($buyer != "")
            {
                $this
                    ->db
                    ->where('oi.sold_by', $buyer);
            }

            }

            if($type =="all")
            {

            $this
            ->db
            ->where('os.created_date >=', $from_date_all);
            $this
                ->db
                ->where('os.created_date <=', $to_date_all);
            if ($garden != "")
            {
                $this
                    ->db
                    ->where('oi.garden', $garden);
            }
            if ($grade != "")
            {
                $this
                    ->db
                    ->where('oi.grade', $grade);
            }
            if ($buyer != "")
            {
                $this
                    ->db
                    ->where('oi.sold_by', $buyer);
            }
            }
           
            $this
                ->db
                ->where('os.created_by', $dtl['id']);
            $qry = $this
                ->db
                ->get();
            $bid_details = $qry->result();
            //echo $from_date.'to'.$to_date.'crt'.$dtl['id'];
            //print_r($bid_details);
            $sheet_id_array = array();
            if (!empty($bid_details))
            {
                foreach ($bid_details as $row)
                {

                    array_push($sheet_id_array, $row->sheet_id);

                }
                $this
                    ->db
                    ->select('os.sheet_id,oi.sheet_id,oi.sold_on,oi.garden,oi.grade,oi.sold_by,os.created_date,oi.invoice_id,oi.serial_no,os.sheet_name,os.sheet_no,oi.invoice,oi.pkgs_no,oi.total_kgs');
                $this
                    ->db
                    ->from(OFFER_INVOICE . ' oi');
                $this
                    ->db
                    ->join(OFFER_SHEET . ' os', 'os.sheet_id=oi.sheet_id', 'inner');
                $this
                    ->db
                    ->where_in('oi.sheet_id', $sheet_id_array);
                if ($garden != "")
                {
                    $this
                        ->db
                        ->where('oi.garden', $garden);
                }
                if ($grade != "")
                {
                    $this
                        ->db
                        ->where('oi.grade', $grade);
                }
                if ($buyer != "")
                {
                    $this
                        ->db
                        ->where('oi.sold_by', $buyer);
                }
                $qry = $this
                    ->db
                    ->get();
                $bid_details1 = $qry->result();
            }

            else
            {
                $bid_details1 = array();
            }

            $output = fopen('php://output', 'w');

            fputcsv($output, array(
                'Sl No.',
                'Sheet Name',
                'Sheet Number',
                'Garden',
                'Invoice',
                'Grade',
                'Pkgs',
                'kgs',
                'Buyer',
                'Price',
                'Status'
            ));

            $count = 1;

            foreach ($bid_details1 as $row)
            {

                $this
                    ->db
                    ->select('bd.invoice_id,bd.buyer_id,bd.buyer_price');
                $this
                    ->db
                    ->from(BID_DETAILS . ' bd');
                $this
                    ->db
                    ->where('bd.invoice_id', @$row->invoice_id);
                $this
                    ->db
                    ->where('bd.buyer_id', @$row->sold_by);
                $qry = $this
                    ->db
                    ->get();
                $bid_price = $qry->result();

                $this
                    ->db
                    ->select('MAX(buyer_price) as maxprice');
                $this
                    ->db
                    ->from(BID_DETAILS);
                $this
                    ->db
                    ->where('invoice_id', @$row->invoice_id);
                $qry = $this
                    ->db
                    ->get();
                $bid_max = $qry->result();

                $this
                    ->db
                    ->select('bd.buyer_id,bd.invoice_id,bd.buyer_price,us.id,us.company_name');
                $this
                    ->db
                    ->from(BID_DETAILS . ' bd');
                $this
                    ->db
                    ->join(USERS . ' us', 'us.id=bd.buyer_id', 'inner');
                $this
                    ->db
                    ->where('bd.invoice_id', @$row->invoice_id);
                $this
                    ->db
                    ->where('bd.buyer_price', @$bid_max[0]->maxprice);
                $this
                    ->db
                    ->where('bd.buyer_price !=', 0);
                $qry = $this
                    ->db
                    ->get();
                $unsold_name = $qry->result();

                if ($row->sold_on == '')
                {
                    $Status = "Unsold";
                }
                else
                {
                    $Status = "Sold";
                }

                $this
                    ->db
                    ->select('us.id,us.company_name');
                $this
                    ->db
                    ->from(USERS . ' us');
                $this
                    ->db
                    ->where('us.id', @$row->sold_by);

                $qryy = $this
                    ->db
                    ->get();
                $buyername = $qryy->result();
                if (@$buyername[0]->company_name != "")
                {

                    $b_name = @$buyername[0]->company_name;

                }
                else
                {
                    $b_name = @$unsold_name[0]->company_name;
                }

                if (@$bid_price[0]->buyer_price != "")
                {
                    $bdPrice = @$bid_price[0]->buyer_price;
                }
                else
                {
                    $bdPrice = @$bid_max[0]->maxprice;
                }

                fputcsv($output, array(

                    "Sl No" => $row->serial_no,
                    "Sheet Name" => $row->sheet_name,
                    "Sheet Number" => $row->sheet_no,
                    'Garden' => $row->garden,
                    'Invoice' => $row->invoice,
                    'Grade' => $row->grade,
                    'Pkgs' => $row->pkgs_no,
                    'kgs' => $row->total_kgs,
                    'Buyer' => @$b_name,
                    'Price' => $bdPrice,
                    'Status' => $Status

                ));

                $count++;

            }

        }
        else
        {

            $this
                ->db
                ->select('os.sheet_id,oi.sheet_id,oi.sold_on,os.created_by,oi.inv_status,oi.invoice_id,oi.total_kgs');
            $this
                ->db
                ->from(OFFER_INVOICE . ' oi');
            $this
                ->db
                ->join(OFFER_SHEET . ' os', 'os.sheet_id=oi.sheet_id', 'inner');
            $this
                ->db
                ->where('oi.sold_on >=', $from_date);
            $this
                ->db
                ->where('oi.sold_on <=', $to_date);
            $this
                ->db
                ->where('os.created_by', $dtl['id']);
            $this
                ->db
                ->where('oi.inv_status', 'A');
            $qry = $this
                ->db
                ->get();
            $bid_details_active = $qry->result();


            $this
                ->db
                ->select('os.sheet_id,oi.sheet_id,os.created_date,os.created_by,oi.total_kgs');
            $this
                ->db
                ->from(OFFER_INVOICE . ' oi');
            $this
                ->db
                ->join(OFFER_SHEET . ' os', 'os.sheet_id=oi.sheet_id', 'inner');

             $this
            ->db
            ->where('os.created_date >=', $from_date_all);
            $this
                ->db
                ->where('os.created_date <=', $to_date_all);
  
            $this
                ->db
                ->where('os.created_by', $dtl['id']);
            $qry = $this
                    ->db
                    ->get();
            $bid_details_all = $qry->result();
        

            $output = fopen('php://output', 'w');

            fputcsv($output, array(
                'Quantity Offered',
                'Quantity Sold',
                'Sold %',
                'AVG PRICE'
            ));

            $count = 1;
            $Totalcount = 0;
            $TotalSold = 0;
            $Totalprice = 0;
            $get_offer_id = array();
            if (!empty($bid_details_active))
            {

                foreach ($bid_details_active as $row1)
                {
                    $this->db->select('MAX(buyer_price) as maxprice');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('invoice_id',$row1->invoice_id);
                    $bid_max=$this->db->get();
                    $bid_max_details =  $bid_max->result();

                    $Totalprice = $Totalprice + (@$bid_max_details[0]->maxprice * $row1->total_kgs);
                    $TotalSold = $TotalSold + $row1->total_kgs;
                }
            }
            else
            {
                $TotalSold = 0;
               
            }
           

            if (!empty($bid_details_all))
            {

                foreach ($bid_details_all as $row2)
                {
                    $Totalcount = $Totalcount + $row2->total_kgs;

                   
                }
            }

            if ($TotalSold != "")
            {
                $sold = ($TotalSold / $Totalcount) * 100;
            }
            else
            {
                $sold = "";
            }

            if ($Totalprice != "")
            {
                $avg_price = round($Totalprice / $TotalSold);
            }
            else
            {
                $avg_price = "";
            }

            fputcsv($output, array(

                "Quantity Offered" => $Totalcount,
                "Quantity Sold" => $TotalSold,
                "Sold %" => $sold,
                'AVG PRICE' => $avg_price,

            ));

            $count++;

        }

    }
    function buyer_report()
    {

        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $this->data['buyer_dtl'] = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $this
            ->db
            ->select('garden');
        $this
            ->db
            ->distinct();
        $query = $this
            ->db
            ->get(OFFER_INVOICE);
        $this->data['garden'] = $query->result();

        $this
            ->db
            ->select('grade');
        $this
            ->db
            ->distinct();
        $query = $this
            ->db
            ->get(OFFER_INVOICE);
        $this->data['grade'] = $query->result();

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from(USERS);
        $this
            ->db
            ->where('role', '3');
        $query = $this
            ->db
            ->get();
        $this->data['seller'] = $query->result();

        //print_r($this->data['seller']);exit;
        $this
            ->layout
            ->view('buyer-report', $this->data);
    }

    function make_print_buyer_recieve_sheet()
    {

        $sheet_id = decrypt($this
            ->uri
            ->segment(4));

        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);

        $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $buyer_dtl = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $offer_sheet = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $invoice_dtl = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

     /*   $offer_sheet_buyer = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],

        ], 'where' => "bsa.sheet_id = {$sheet_id}",

        ]);*/

        $created_by = @$offer_sheet['created_by'];

        $seller_dtl = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = $created_by", 'query' => 'first']);

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$payment_type = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

      /*  $bid_details = $this
            ->Common
            ->find(['table' => BID_DETAILS, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} AND sheet_id = {$sheet_id}",

        ]);*/

        $invoice_no = 'TeaInntech-' . date('Y-m-d') . $sheet_id;
        $mail_data = array(
            'seller_dtl' => $seller_dtl,
            'buyer_dtl' => $buyer_dtl,
            'offer_sheet' => $offer_sheet,
            'invoice_dtl' => $decodeJson,
           

        );

        $this
            ->load
            ->view('print_invoice_buyer_recieve', $mail_data);
        $html = $this
            ->output
            ->get_output();

        $dompdf = new DOMPDF();
        $html = preg_replace('/>\s+</', '><', $html);
        $dompdf->load_html($html);
        $dompdf->set_paper('A4', 'potrait');
        $dompdf->render();
        $output = $dompdf->output();

        $i = $invoice_no;
        $file_to_save = 'public/uploads/' . $i . '.pdf';
        file_put_contents($file_to_save, $output);

        $data_file = file_get_contents($file_to_save);

        $name = $i . '.pdf';
        force_download($name, $data_file);

    }

    function make_print_buyer_close_sheet()
    {

        $sheet_id = decrypt($this
            ->uri
            ->segment(4));

        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $buyer_dtl = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $offer_sheet = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $invoice_dtl = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

        $offer_sheet_buyer = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],

        ], 'where' => "bsa.sheet_id = {$sheet_id}",

        ]);

        $created_by = @$offer_sheet['created_by'];

        $seller_dtl = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = $created_by", 'query' => 'first']);

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$payment_type = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

        $bid_details = $this
            ->Common
            ->find(['table' => BID_DETAILS, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} AND sheet_id = {$sheet_id}",

        ]);

        $invoice_no = 'TeaInntech-' . date('Y-m-d') . $sheet_id;
        $mail_data = array(
            'seller_dtl' => $seller_dtl,
            'buyer_dtl' => $buyer_dtl,
            'offer_sheet' => $offer_sheet,
            'invoice_dtl' => $invoice_dtl,
            'offer_sheet_buyer' => $offer_sheet_buyer,
            'bid_details' => $bid_details,

        );

        $this
            ->load
            ->view('print_invoice_buyer_close', $mail_data);
        $html = $this
            ->output
            ->get_output();

        $dompdf = new DOMPDF();
        $html = preg_replace('/>\s+</', '><', $html);
        $dompdf->load_html($html);
        $dompdf->set_paper('A4', 'potrait');
        $dompdf->render();
        $output = $dompdf->output();

        $i = $invoice_no;
        $file_to_save = 'public/uploads/' . $i . '.pdf';
        file_put_contents($file_to_save, $output);

        $data_file = file_get_contents($file_to_save);

        $name = $i . '.pdf';
        force_download($name, $data_file);

    }
   function buyer_report_action()
    {
        $this
            ->load
            ->helper('download');
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);

        $type = $this
            ->input
            ->post('type');
        $period = $this
            ->input
            ->post('period');

        $garden = $this
            ->input
            ->post('garden');
        $grade = $this
            ->input
            ->post('grade');
        $seller = $this
            ->input
            ->post('seller');

        if ($period == "custom")
        {
            $from_date = $this
                ->input
                ->post('from_date');
            $to_date = $this
                ->input
                ->post('to_date');

            $to_date_all = date('Y-m-d H:i:s', strtotime($to_date));
            $from_date_all = date('Y-m-d H:i:s', strtotime($from_date));
        }
        else
        {
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-d', strtotime($to_date . ' -' . $period . ' day'));


             $to_date_all = date('Y-m-d H:i:s');
            $from_date_all = date('Y-m-d H:i:s', strtotime($to_date . ' -' . $period . ' day'));
        }

        /*$convertForm=date("d-m-Y", strtotime($from_date));
        $convertTo=date("d-m-Y", strtotime($to_date));
        $from_date = strtotime(@$convertForm);
        $to_date = strtotime(@$convertTo);  */
        //echo $from_date;exit;
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=Tea Inntech report.csv");

        if ($type != "summary")
        {
            $this
                ->db
                ->select('*');
            $this
                ->db
                ->from(OFFER_INVOICE . ' oi');
            $this
                ->db
                ->join(OFFER_SHEET . ' os', 'os.sheet_id=oi.sheet_id', 'inner');
            $this
                ->db
                ->where('oi.sold_on >=', $from_date);
            $this
                ->db
                ->where('oi.sold_on <=', $to_date);
            if ($garden != "")
            {
                $this
                    ->db
                    ->where('oi.garden', $garden);
            }
            if ($grade != "")
            {
                $this
                    ->db
                    ->where('oi.grade', $grade);
            }
            if ($seller != "")
            {
                $this
                    ->db
                    ->where('os.created_by', $seller);
            }
            $this
                ->db
                ->where('oi.sold_by', $dtl['id']);
            $qry = $this
                ->db
                ->get();
            $bid_details = $qry->result();

            $output = fopen('php://output', 'w');

            fputcsv($output, array(
                'Sl No.',
                'Sheet Name',
                'Sheet Number',
                'Garden',
                'Invoice',
                'Grade',
                'Pkgs',
                'kgs',
                'Sold Price',
                'Status'
            ));

            $count = 1;

            foreach ($bid_details as $row)
            {

                $this
                    ->db
                    ->select('*');
                $this
                    ->db
                    ->from(BID_DETAILS . ' bd');
                $this
                    ->db
                    ->where('bd.invoice_id', @$row->invoice_id);
                $this
                    ->db
                    ->where('bd.buyer_id', @$row->sold_by);
                $qry = $this
                    ->db
                    ->get();
                $bid_price = $qry->result();

                $bdPrice = @$bid_price[0]->buyer_price;
                if ($row->inv_status == 'A')
                {
                    $Status = "Sold";
                }
                else
                {
                    $Status = "Unsold";
                }

                fputcsv($output, array(

                    "Sl No" => $row->serial_no,
                    "Sheet Name" => $row->sheet_name,
                    "Sheet Number" => $row->sheet_no,
                    'Garden' => $row->garden,
                    'Invoice' => $row->invoice,
                    'Grade' => $row->grade,
                    'Pkgs' => $row->pkgs_no,
                    'kgs' => $row->total_kgs,
                    'Sold Price' => $bdPrice,
                    'Status' => $Status

                ));

                $count++;

            }
        }
        else
        {
            $this
                ->db
                ->select('*');
            $this
                ->db
                ->from(OFFER_INVOICE . ' oi');
            $this
                ->db
                ->join(OFFER_SHEET . ' os', 'os.sheet_id=oi.sheet_id', 'inner');
            $this
                ->db
                ->where('oi.sold_on >=', $from_date);
            $this
                ->db
                ->where('oi.sold_on <=', $to_date);
            $this
                ->db
                ->where('oi.inv_status', 'A');
            $this
                ->db
                ->where('oi.sold_by', $dtl['id']);
            $qry = $this
                ->db
                ->get();
            $bid_details_active = $qry->result();


          
             $this
                ->db
                ->select('*');
            $this
                ->db
                ->from(OFFER_INVOICE . ' oi');
            $this
                ->db
                ->join(OFFER_SHEET . ' os', 'os.sheet_id=oi.sheet_id', 'inner');
            $this
                ->db
                ->join('tt_buyer_sheet_assigned tba', 'os.sheet_id=tba.sheet_id', 'inner');
            $this
                ->db
                ->where('os.created_date >=', $from_date_all);
            $this
                ->db
                ->where('os.created_date <=', $to_date_all);
            $this
                ->db
                ->where('tba.buyer_id', $dtl['id']);
            $this
                ->db
                ->where('tba.is_recv', 'Recieve');
            $qry = $this
                ->db
                ->get();
            $bid_details_all = $qry->result();



            

            //print_r($bid_details_active);exit;
            $output = fopen('php://output', 'w');

            fputcsv($output, array(
                'QUANTITY OFFERED',
                'QUANTITY BOUGHT',
                'BOUGHT %',
                'AVG PRICE'
            ));

            $count = 1;

            $Totalcount = 0;
            $TotalSold = 0;
            $Totalprice = 0;
            $get_offer_id = array();
            if (!empty($bid_details_active))
            {

                foreach ($bid_details_active as $row1)
                {
                    $this->db->select('MAX(buyer_price) as maxprice');
                    $this->db->from(BID_DETAILS);
                    $this->db->where('invoice_id',$row1->invoice_id);
                    $bid_max=$this->db->get();
                    $bid_max_details =  $bid_max->result();

                    $Totalprice = $Totalprice + (@$bid_max_details[0]->maxprice * $row1->total_kgs);

                    $TotalSold = $TotalSold +  $row1->total_kgs;
                }
            }
            else
            {
                $TotalSold = 0;
                
            }

            
            if (!empty($bid_details_all))
            {

                foreach ($bid_details_all as $row2)
                {
                    $Totalcount = $Totalcount +  $row2->total_kgs;

                }
            }
            if ($TotalSold != "")
            {
                $bought = ($TotalSold / $Totalcount) * 100;
            }
            else
            {
                $bought = "";
            }
            if ($Totalprice != "")
            {
                $avg_price = round($Totalprice / $TotalSold);
            }
            else
            {
                $avg_price = "";
            }

            fputcsv($output, array(

                "QUANTITY OFFERED" => $Totalcount,
                "QUANTITY BOUGHT" => $TotalSold,
                "BOUGHT %" => $bought,
                'AVG PRICE' => $avg_price,

            ));

            $count++;

        }

    }

    function bid_history()
    {
        $invoice_id = $this
            ->input
            ->post('invoice_id');
        $buyer_can_see = $this
            ->input
            ->post('buyer_can_see');

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from(BID_LOG . ' bl');
        $this
            ->db
            ->join(USERS . ' us', 'us.id = bl.buyer_id');
        $this
            ->db
            ->where('bl.invoice_id', $invoice_id);
        $this
            ->db
            ->where('bl.buyer_price !=', 0);
        $this
            ->db
            ->order_by('bl.bid_id', 'DESC');

        $bd_history = $this
            ->db
            ->get();
        $bid_history = $bd_history->result();
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);

?>


        <?php if (!empty($bid_history))
        {
            $i = 0;
            foreach ($bid_history as $row)
            {
                $i++;
?>

             <tr>
               <td><?php echo date('F d, Y', strtotime(@$row->bid_on)); ?> | <strong><?php echo date('h:i A', strtotime($row->bid_on)); ?></strong></td>
               <td><?php if ($buyer_can_see == 'Yes')
                {
                    echo $row->company_name;
                }
                elseif ($buyer_can_see == 'No' && $row->buyer_id == @$dtl['id'])
                {
                    echo $row->company_name;
                }
                else
                {
                    echo '';
                } ?></td>
               <td><?php echo $row->buyer_price; ?></td>
             
            </tr>

     <?php
            }
        } ?>

     <?php
    }

    function bid_history_seller()
    {
        $invoice_id = $this
            ->input
            ->post('invoice_id');

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from(BID_LOG . ' bl');
        $this
            ->db
            ->join(USERS . ' us', 'us.id = bl.buyer_id');
        $this
            ->db
            ->where('bl.invoice_id', $invoice_id);
        $this
            ->db
            ->where('bl.buyer_price !=', 0);
        $this
            ->db
            ->order_by('bl.bid_id', 'DESC');
        $bd_history = $this
            ->db
            ->get();
        $bid_history = $bd_history->result();

?>


        <?php if (!empty($bid_history))
        {
            $i = 0;
            foreach ($bid_history as $row)
            {
                $i++;
?>

             <tr>
               <td><?php echo date('F d, Y', strtotime(@$row->bid_on)); ?> | <strong><?php echo date('h:i A', strtotime($row->bid_on)); ?></strong></td>
               <td><?php echo $row->company_name; ?></td>
               <td><?php echo $row->buyer_price; ?></td>
             
            </tr>

     <?php
            }
        } ?>

     <?php
    }

    function make_export_seller_close_sheet()
    {
        $sheet_id = $this
            ->uri
            ->segment(4);

        //echo $sheet_id;exit;
        

        $offer_sheet = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $invoice_dtl = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=Tea Inntech close offer sheet.csv");
        $output = fopen('php://output', 'w');

        fputcsv($output, array(
            'Sl No.',
            'Sheet Name',
            'Sheet Number',
            'Garden',
            'Invoice',
            'Grade',
            'Pkgs',
            'kgs',
            'Price',
            'Buyer',
            'Status'
        ));

        $count = 1;
        if (!empty($invoice_dtl))
        {
            $i = 0;
            foreach ($invoice_dtl as $key => $row)
            {
                $i++;
                $this
                    ->db
                    ->select('MAX(buyer_price) as maxprice');
                $this
                    ->db
                    ->from(BID_DETAILS);
                $this
                    ->db
                    ->where('invoice_id', $row['invoice_id']);
                $bid_max = $this
                    ->db
                    ->get();
                $bid_max_details = $bid_max->result();
                //print_r($bid_max_details);
                $this
                    ->db
                    ->select('*');
                $this
                    ->db
                    ->from(BID_DETAILS . ' bd');
                $this
                    ->db
                    ->join(USERS . ' us', 'us.id = bd.buyer_id');
                $this
                    ->db
                    ->where('invoice_id', $row['invoice_id']);
                $this
                    ->db
                    ->where('bd.buyer_price', @$bid_max_details[0]->maxprice);
                $bid_max_buyer = $this
                    ->db
                    ->get();
                $bid_max_details_buyer = $bid_max_buyer->result();

                $chk_division_seller = $this
                    ->Common
                    ->chk_division_seller(@$row['invoice_id'], @$bid_max_details_buyer[0]->id);

                if ($row['inv_status'] == "A")
                {
                    $status = "Sold";
                }
                else
                {
                    $status = 'Unsold';
                }

                if (@$bid_max_details[0]->maxprice != 0)
                {
                    $name = @$bid_max_details_buyer[0]->company_name;

                }
                else
                {
                    $name = '';
                }

                if (empty($chk_division_seller))
                {
                    if (@$bid_max_details_buyer[0]->company_name != "" && $bid_max_details[0]->maxprice != 0)
                    {
                        $name = @$bid_max_details_buyer[0]->company_name;
                    }
                    else
                    {
                        $name = "";
                    }
                }
                else
                {

                    if (@$bid_max_details_buyer[0]->company_name != "" && $bid_max_details[0]->maxprice != 0)
                    {
                        $name = @$bid_max_details_buyer[0]->company_name . ' / ' . @$chk_division_seller[0]->company_name;
                    }
                    else
                    {
                        $name = "";
                    }
                }

                fputcsv($output, array(

                    "Sl No" => $row['serial_no'],
                    "Sheet Name" => $offer_sheet['sheet_name'],
                    "Sheet Number" => $offer_sheet['sheet_no'],
                    'Garden' => $row['garden'],
                    'Invoice' => $row['invoice'],
                    'Grade' => $row['grade'],
                    'Pkgs' => $row['pkgs_no'],
                    'kgs' => $row['total_kgs'],
                    'Price' => @$bid_max_details[0]->maxprice,
                    'Buyer' => @$name,
                    'Status' => $status

                ));

                $count++;

            }
        }

    }

    function make_print_buyer_active_sheet()
    {

        $sheet_id = decrypt($this
            ->uri
            ->segment(4));

        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $buyer_dtl = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $offer_sheet = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

       /* $invoice_dtl = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);
*/
     /*   $offer_sheet_buyer = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],

        ], 'where' => "bsa.sheet_id = {$sheet_id}",

        ]);*/

        $created_by = @$offer_sheet['created_by'];

        $seller_dtl = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = $created_by", 'query' => 'first']);

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$payment_type = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

      /*  $bid_details = $this
            ->Common
            ->find(['table' => BID_DETAILS, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} AND sheet_id = {$sheet_id}",

        ]);
*/
        $invoice_no = 'TeaInntech-' . date('Y-m-d') . $sheet_id;

        $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);

      //  echo "<pre>";print_r($decodeJson);exit;
        $mail_data = array(
            'seller_dtl' => $seller_dtl,
            'buyer_dtl' => $buyer_dtl,
            'offer_sheet' => $offer_sheet,
            'invoice_dtl' => $decodeJson,
            //'offer_sheet_buyer' => $offer_sheet_buyer,
            //'bid_details' => $decodeJson,

        );

        ob_start();
        $this
            ->load
            ->view('print_invoice_buyer_active', $mail_data);
        // $html = $this->output->get_output();
        $html = $this
            ->output
            ->get_output();
        /* $this->load->library('m_pdf');
        $i = $invoice_no;
            $html=$this->load->view('print_invoice_buyer_active',$mail_data, true); 
         
            $pdfFilePath ='public/uploads/'.$i.".pdf";
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output($pdfFilePath, "F");
            $data_file = file_get_contents($pdfFilePath);
        
            $name = $i.'.pdf';
            force_download($name,$data_file);*/
        /*    require_once APPPATH.'third_party/TCPDF/tcpdf.php';
        $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $obj_pdf->SetCreator(PDF_CREATOR);
        $title = "PDF Report";
        $obj_pdf->SetTitle($title);
        $obj_pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $title, PDF_HEADER_STRING);
        $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $obj_pdf->SetDefaultMonospacedFont('helvetica');
        $obj_pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $obj_pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $obj_pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $obj_pdf->SetFont('helvetica', '', 9);
        $obj_pdf->setFontSubsetting(false);
        $obj_pdf->AddPage();
        
        $obj_pdf->writeHTML($html, true, false, true, false, '');
        $obj_pdf->Output('output.pdf', 'I');*/

        $html = ob_get_clean();
        $dompdf = new DOMPDF();
        $html = preg_replace('/>\s+</', '><', $html);
        $dompdf->load_html($html);
        $dompdf->set_paper('A4', 'potrait');
        $dompdf->render();

        $canvas = $dompdf->get_canvas();
        $font = Font_Metrics::get_font("helvetica", "bold");
        $canvas->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}",
                   $font, 6, array(0,0,0));
        $output = $dompdf->output();

        $i = $invoice_no;
        $file_to_save = 'public/uploads/' . $i . '.pdf';
        file_put_contents($file_to_save, $output);

        $data_file = file_get_contents($file_to_save);

        $name = $i . '.pdf';
        force_download($name, $data_file);

    }

    function make_print_seller_close_sheet()
    {

        $sheet_id = decrypt($this
            ->uri
            ->segment(4));

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

        $seller_dtl = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $offer_sheet = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $invoice_dtl = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

        $offer_sheet_buyer = $this
            ->Common
            ->find(['table' => BUYER_SHEET_ASSIGNED . ' bsa', 'select' => "*", 'join' => [[USERS, 'us', 'INNER', "us.id = bsa.buyer_id"],

        ], 'where' => "bsa.sheet_id = {$sheet_id}",

        ]);

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$payment_type = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

        $bid_details = $this
            ->Common
            ->find(['table' => BID_DETAILS, 'select' => "*", 'where' => "buyer_id = {$dtl['id']} AND sheet_id = {$sheet_id}",

        ]);

        $invoice_no = 'TeaInntech-' . date('Y-m-d') . $sheet_id;
        $mail_data = array(
            'seller_dtl' => $seller_dtl,
            'offer_sheet' => $offer_sheet,
            'invoice_dtl' => $invoice_dtl,
            'offer_sheet_buyer' => $offer_sheet_buyer,
            'bid_details' => $bid_details,

        );

        $this
            ->load
            ->view('print_invoice_seller_close', $mail_data);
        $html = $this
            ->output
            ->get_output();

        $dompdf = new DOMPDF();
        $html = preg_replace('/>\s+</', '><', $html);
        $dompdf->load_html($html);
        $dompdf->set_paper('A4', 'potrait');
        $dompdf->render();
        $output = $dompdf->output();

        $i = $invoice_no;
        $file_to_save = 'public/uploads/' . $i . '.pdf';
        file_put_contents($file_to_save, $output);

        $data_file = file_get_contents($file_to_save);

        $name = $i . '.pdf';
        force_download($name, $data_file);

    }

    function make_print_seller_active_sheet()
    {

        $sheet_id = decrypt($this
            ->uri
            ->segment(4));

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }

         $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);

        $seller_dtl = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $offer_sheet = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

      
      

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$payment_type = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }


        $invoice_no = 'TeaInntech-' . date('Y-m-d') . $sheet_id;
        $mail_data = array(
            'seller_dtl' => $seller_dtl,
            'offer_sheet' => $offer_sheet,
            'invoice_dtl' => $decodeJson,
          
          

        );

        $this
            ->load
            ->view('print_invoice_seller_active', $mail_data);
        $html = $this
            ->output
            ->get_output();

        $dompdf = new DOMPDF();
        $html = preg_replace('/>\s+</', '><', $html);
        $dompdf->load_html($html);
        $dompdf->set_paper('A4', 'potrait');
        $dompdf->render();
        $output = $dompdf->output();

        $i = $invoice_no;
        $file_to_save = 'public/uploads/' . $i . '.pdf';
        file_put_contents($file_to_save, $output);

        $data_file = file_get_contents($file_to_save);

        $name = $i . '.pdf';
        force_download($name, $data_file);

    }

    function make_print_seller_dispatch_sheet()
    {

        $sheet_id = decrypt($this
            ->uri
            ->segment(4));

        $dtl = $this
            ->session
            ->userdata(SUPPLIER_SESS);
        if (empty($dtl))
        {
            redirect(BASE_URL);
        }
       $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);
        $seller_dtl = $this
            ->Common
            ->find(['table' => USERS, 'select' => "*", 'where' => "id = {$dtl['id']}", 'query' => 'first']);

        $offer_sheet = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

     

     

        $payment_type = @$this->data['offer_sheet']['payment_type'];
        $contract_type = @$this->data['offer_sheet']['contract'];
        if ($payment_type != "")
        {
            @$payment_type = $this
                ->Common
                ->find(['table' => PAYMENT_TYPE, 'select' => "*", 'where' => "id = {$payment_type}", 'query' => 'first']);
        }

        if ($contract_type != "")
        {

            @$this->data['contract_type'] = $this
                ->Common
                ->find(['table' => CONTRACT_TYPE, 'select' => "*", 'where' => "id = {$contract_type}", 'query' => 'first']);
        }

        $invoice_no = 'TeaInntech-' . date('Y-m-d') . $sheet_id;
        $mail_data = array(
            'seller_dtl' => $seller_dtl,
            'offer_sheet' => $offer_sheet,
            'invoice_dtl' => $decodeJson,
          
          

        );

        $this
            ->load
            ->view('print_invoice_seller_dispatch', $mail_data);

        $html = $this
            ->output
            ->get_output();

        //print_r($html);exit;
        $dompdf = new DOMPDF();
        $html = preg_replace('/>\s+</', '><', $html);
        $dompdf->load_html($html);
        $dompdf->set_paper('A4', 'potrait');
        $dompdf->render();
        $output = $dompdf->output();

        $i = $invoice_no;
        $file_to_save = 'public/uploads/' . $i . '.pdf';
        file_put_contents($file_to_save, $output);

        $data_file = file_get_contents($file_to_save);

        $name = $i . '.pdf';
        force_download($name, $data_file);

    }

    function make_export_seller_dispatch_sheet()
    {

        $sheet_id = $this
            ->uri
            ->segment(4);

        //echo $sheet_id;exit;
         $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);

        $offer_sheet = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $invoice_dtl = $decodeJson;

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=Tea Inntech dispatch offer sheet.csv");
        $output = fopen('php://output', 'w');

        fputcsv($output, array(
            'Sl No.',
            'Sheet Name',
            'Sheet Number',
            'Garden',
            'Invoice',
            'Grade',
            'Pkgs',
            'kgs',
            'Price Idea',
            'Status'
        ));

        $count = 1;
        if (!empty($invoice_dtl))
        {
            $i = 0;
            foreach ($invoice_dtl as $key => $row)
            {
                $i++;
               /* $this
                    ->db
                    ->select('MAX(buyer_price) as maxprice');
                $this
                    ->db
                    ->from(BID_DETAILS);
                $this
                    ->db
                    ->where('invoice_id', $row['invoice_id']);
                $bid_max = $this
                    ->db
                    ->get();
                $bid_max_details = $bid_max->result();
                //print_r($bid_max_details);
                $this
                    ->db
                    ->select('*');
                $this
                    ->db
                    ->from(BID_DETAILS . ' bd');
                $this
                    ->db
                    ->join(USERS . ' us', 'us.id = bd.buyer_id');
                $this
                    ->db
                    ->where('invoice_id', $row['invoice_id']);
                $this
                    ->db
                    ->where('bd.buyer_price', @$bid_max_details[0]->maxprice);
                $bid_max_buyer = $this
                    ->db
                    ->get();
                $bid_max_details_buyer = $bid_max_buyer->result();*/

                if ($row['inv_status'] == "A")
                {
                    $status = "Active";
                }
                else
                {
                    $status = 'Inactive';
                }

                fputcsv($output, array(

                    "Sl No" => $row['serial_no'],
                    "Sheet Name" => $offer_sheet['sheet_name'],
                    "Sheet Number" => $offer_sheet['sheet_no'],
                    'Garden' => $row['garden'],
                    'Invoice' => $row['invoice'],
                    'Grade' => $row['grade'],
                    'Pkgs' => $row['pkgs_no'],
                    'kgs' => $row['total_kgs'],
                    'Price Idea' => $row['price_idea'],
                    'Status' => $status

                ));

                $count++;

            }
        }

    }

    function make_export_buyer_recieve_sheet()
    {

        $sheet_id = $this
            ->uri
            ->segment(4);

        //echo $sheet_id;exit;
        $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);

        $offer_sheet = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $invoice_dtl = $decodeJson;

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=Tea Inntech recieve offer sheet.csv");
        $output = fopen('php://output', 'w');

        fputcsv($output, array(
            'Sl No.',
            'Sheet Name',
            'Sheet Number',
            'Garden',
            'Invoice',
            'Grade',
            'Pkgs',
            'kgs',
            'Price Idea',
            'Bid Price',
            'Buyer',
            'Status'
        ));

        $count = 1;
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        if (!empty($invoice_dtl))
        {
            $i = 0;
            foreach ($invoice_dtl as $key => $row)
            {
                $i++;
              /*  $this
                    ->db
                    ->select('MAX(buyer_price) as maxprice');
                $this
                    ->db
                    ->from(BID_DETAILS);
                $this
                    ->db
                    ->where('invoice_id', $row['invoice_id']);
                $bid_max = $this
                    ->db
                    ->get();
                $bid_max_details = $bid_max->result();
                //print_r($bid_max_details);
                $this
                    ->db
                    ->select('*');
                $this
                    ->db
                    ->from(BID_DETAILS . ' bd');
                $this
                    ->db
                    ->join(USERS . ' us', 'us.id = bd.buyer_id');
                $this
                    ->db
                    ->where('invoice_id', $row['invoice_id']);
                $this
                    ->db
                    ->where('bd.buyer_price', @$bid_max_details[0]->maxprice);
                $bid_max_buyer = $this
                    ->db
                    ->get();
                $bid_max_details_buyer = $bid_max_buyer->result();*/

                $this->db->select('company_name,id');
                $this->db->from(USERS);
                $this->db->where('id',@$row['bidMaxbuyerId']);
                $name=$this->db->get();
                $company=$name->result();

                if ($row['inv_status'] == "A")
                {
                    $status = "Active";
                }
                else
                {
                    $status = 'Inactive';
                }

                if ($row['bidMaxPrice'] != 0 && $row['buyer_can_see'] == 'Yes')
                {
                    $name = @$company[0]->company_name;

                }
                elseif ($row['bidMaxPrice'] != 0 && $row['buyer_can_see'] == 'No' && $row['bidMaxbuyerId'] == @$dtl['id'])
                {
                    $name = @$company[0]->company_name;

                }
                else
                {
                    $name = '';
                }

                fputcsv($output, array(

                    "Sl No" => $row['serial_no'],
                    "Sheet Name" => $row['sheet_name'],
                    "Sheet Number" => $row['sheet_no'],
                    'Garden' => $row['garden'],
                    'Invoice' => $row['invoice'],
                    'Grade' => $row['grade'],
                    'Pkgs' => $row['pkgs_no'],
                    'kgs' => $row['total_kgs'],
                    'Price Idea' => $row['price_idea'],
                    'Bid Price' => @$row['bidMaxPrice'],
                    'Buyer' => @$name,
                    'Status' => $status

                ));

                $count++;

            }
        }

    }

    function make_export_buyer_close_sheet()
    {

        $sheet_id = $this
            ->uri
            ->segment(4);

        //echo $sheet_id;exit;
        

        $offer_sheet = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $invoice_dtl = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=Tea Inntech close offer sheet.csv");
        $output = fopen('php://output', 'w');

        fputcsv($output, array(
            'Sl No.',
            'Sheet Name',
            'Sheet Number',
            'Garden',
            'Invoice',
            'Grade',
            'Pkgs',
            'kgs',
            'Price Idea',
            'Price',
            'Buyer',
            'Status'
        ));

        $count = 1;
        if (!empty($invoice_dtl))
        {
            $i = 0;
            foreach ($invoice_dtl as $key => $row)
            {
                $i++;
                $this
                    ->db
                    ->select('MAX(buyer_price) as maxprice');
                $this
                    ->db
                    ->from(BID_DETAILS);
                $this
                    ->db
                    ->where('invoice_id', $row['invoice_id']);
                $bid_max = $this
                    ->db
                    ->get();
                $bid_max_details = $bid_max->result();
                //print_r($bid_max_details);
                $this
                    ->db
                    ->select('*');
                $this
                    ->db
                    ->from(BID_DETAILS . ' bd');
                $this
                    ->db
                    ->join(USERS . ' us', 'us.id = bd.buyer_id');
                $this
                    ->db
                    ->where('invoice_id', $row['invoice_id']);
                $this
                    ->db
                    ->where('bd.buyer_price', @$bid_max_details[0]->maxprice);
                $bid_max_buyer = $this
                    ->db
                    ->get();
                $bid_max_details_buyer = $bid_max_buyer->result();
                $dtl = $this
                    ->session
                    ->userdata(CUSTOMER_SESS);

                $chk_division_buyer = $this
                    ->Common
                    ->chk_division_buyer(@$row['invoice_id'], @$bid_max_details_buyer[0]->id);
                $chk_division_buyer_name = $this
                    ->Common
                    ->chk_division_buyer_name(@$row['invoice_id'], $dtl['id']);
                if (empty($chk_division_buyer))
                {
                    $pkgs = $row['pkgs_no'];

                }
                else
                {
                    if ($dtl['id'] == @$chk_division_buyer[0]->buyer_request_to)
                    {

                        $pkgs = round($row['pkgs_no'] / 2);

                    }
                    else
                    {
                        $pkgs = $row['pkgs_no'] - (round($row['pkgs_no'] / 2));
                    }
                }

                if (empty($chk_division_buyer))
                {
                    $total_kgs = $row['total_kgs'];

                }
                else
                {
                    if ($dtl['id'] == @$chk_division_buyer[0]->buyer_request_to)
                    {

                        $total_kgs = (round($row['total_kgs'] / $row['pkgs_no']) * (round($row['pkgs_no'] / 2)));

                    }
                    else
                    {
                        $total_kgs = round($row['total_kgs'] / $row['pkgs_no']) * ($row['pkgs_no'] - (round($row['pkgs_no'] / 2)));
                    }
                }

                if ($row['inv_status'] == "A")
                {
                    $status = "Sold";
                }
                else
                {
                    $status = 'Unsold';
                }

                if (empty($chk_division_buyer_name))
                {

                    if (@$bid_max_details[0]->maxprice != 0 && $offer_sheet['buyer_can_see'] == 'Yes')
                    {
                        $name = @$bid_max_details_buyer[0]->company_name;

                    }
                    elseif (@$bid_max_details[0]->maxprice != 0 && $offer_sheet['buyer_can_see'] == 'No' && @$bid_max_details_buyer[0]->id == @$dtl['id'])
                    {
                        $name = @$bid_max_details_buyer[0]->company_name;

                    }
                    else
                    {
                        $name = '';
                    }
                }
                else
                {
                    $name = @$chk_division_buyer_name[0]->company_name;

                }

                fputcsv($output, array(

                    "Sl No" => $row['serial_no'],
                    "Sheet Name" => $offer_sheet['sheet_name'],
                    "Sheet Number" => $offer_sheet['sheet_no'],
                    'Garden' => $row['garden'],
                    'Invoice' => $row['invoice'],
                    'Grade' => $row['grade'],
                    'Pkgs' => $pkgs,
                    'kgs' => $total_kgs,
                    'Price Idea' => $row['price_idea'],
                    'Price' => @$bid_max_details[0]->maxprice,
                    'Buyer' => $name,
                    'Status' => $status

                ));

                $count++;

            }
        }

    }

    function make_export_buyer_active_sheet()
    {

        $sheet_id = $this
            ->uri
            ->segment(4);

        //echo $sheet_id;exit;
        

        $offer_sheet = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

/*        $invoice_dtl = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);*/

        $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $invoice_dtl= json_decode($jsonFile,true);

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=Tea Inntech active offer sheet.csv");
        $output = fopen('php://output', 'w');

        fputcsv($output, array(
            'Sl No.',
            'Sheet Name',
            'Sheet Number',
            'Garden',
            'Invoice',
            'Grade',
            'Pkgs',
            'kgs',
            'Price Idea',
            'Bid Price',
            'Buyer',
            'Status'
        ));
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $count = 1;
        if (!empty($invoice_dtl))
        {
            $i = 0;
            foreach ($invoice_dtl as $key => $row)
            {
                $i++;
               /* $this
                    ->db
                    ->select('MAX(buyer_price) as maxprice');
                $this
                    ->db
                    ->from(BID_DETAILS);
                $this
                    ->db
                    ->where('invoice_id', $row['invoice_id']);
                $bid_max = $this
                    ->db
                    ->get();
                $bid_max_details = $bid_max->result();*/
                //print_r($bid_max_details);
               /* $this
                    ->db
                    ->select('*');
                $this
                    ->db
                    ->from(BID_DETAILS . ' bd');
                $this
                    ->db
                    ->join(USERS . ' us', 'us.id = bd.buyer_id');
                $this
                    ->db
                    ->where('invoice_id', $row['invoice_id']);
                $this
                    ->db
                    ->where('bd.buyer_price', @$bid_max_details[0]->maxprice);
                $bid_max_buyer = $this
                    ->db
                    ->get();
                $bid_max_details_buyer = $bid_max_buyer->result();*/

                $this->db->select('company_name,id');
                $this->db->from(USERS);
                $this->db->where('id',@$row['bidMaxbuyerId']);
                $name=$this->db->get();
                $company=$name->result();

                if ($row['inv_status'] == "A")
                {
                    $status = "Active";
                }
                else
                {
                    $status = 'Inactive';
                }

                if (@$row['bidMaxPrice'] != 0 && @$row['buyer_can_see'] == 'Yes')
                {
                    $name =  @$company[0]->company_name;

                }
                elseif (@$row['bidMaxPrice'] != 0 && @$row['buyer_can_see'] == 'No' && $row['bidMaxbuyerId'] == @$dtl['id'])
                {
                    $name =  @$company[0]->company_name;

                }
                else
                {
                    $name = '';
                }

                fputcsv($output, array(

                    "Sl No" => $row['serial_no'],
                    "Sheet Name" => $offer_sheet['sheet_name'],
                    "Sheet Number" => $offer_sheet['sheet_no'],
                    'Garden' => $row['garden'],
                    'Invoice' => $row['invoice'],
                    'Grade' => $row['grade'],
                    'Pkgs' => $row['pkgs_no'],
                    'kgs' => $row['total_kgs'],
                    'Price Idea' => $row['price_idea'],
                    'Bid Price' => @$row['bidMaxPrice'],
                    'Buyer' => $name,
                    'Status' => $status

                ));

                $count++;

            }
        }

    }

    function make_export_seller_active_sheet()
    {
        $sheet_id = $this
            ->uri
            ->segment(4);

        //echo $sheet_id;exit;
          $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);

        $offer_sheet = $this
            ->Common
            ->find(['table' => OFFER_SHEET . ' os', 'select' => "*", 'join' => [[LOCATION, 'loc', 'INNER', "loc.id = os.location"],

        ], 'where' => "os.sheet_id = {$sheet_id}", 'query' => 'first']);

        $invoice_dtl = $decodeJson;

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=Tea Inntech active offer sheet.csv");
        $output = fopen('php://output', 'w');

        fputcsv($output, array(
            'Sl No.',
            'Sheet Name',
            'Sheet Number',
            'Garden',
            'Invoice',
            'Grade',
            'Pkgs',
            'kgs',
            'Price Idea',
            'Bid Price',
            'Buyer',
            'Status'
        ));

        $count = 1;
        if (!empty($invoice_dtl))
        {
            $i = 0;
            foreach ($invoice_dtl as $key => $row)
            {
                $i++;
               /* $this
                    ->db
                    ->select('MAX(buyer_price) as maxprice');
                $this
                    ->db
                    ->from(BID_DETAILS);
                $this
                    ->db
                    ->where('invoice_id', $row['invoice_id']);
                $bid_max = $this
                    ->db
                    ->get();
                $bid_max_details = $bid_max->result();
                //print_r($bid_max_details);
                $this
                    ->db
                    ->select('*');
                $this
                    ->db
                    ->from(BID_DETAILS . ' bd');
                $this
                    ->db
                    ->join(USERS . ' us', 'us.id = bd.buyer_id');
                $this
                    ->db
                    ->where('invoice_id', $row['invoice_id']);
                $this
                    ->db
                    ->where('bd.buyer_price', @$bid_max_details[0]->maxprice);
                $bid_max_buyer = $this
                    ->db
                    ->get();
                $bid_max_details_buyer = $bid_max_buyer->result();*/
                    $this->db->select('company_name,id');
                    $this->db->from(USERS);
                    $this->db->where('id',@$row['bidMaxbuyerId']);
                    $name=$this->db->get();
                    $company=$name->result();
                if ($row['inv_status'] == "A")
                {
                    $status = "Active";
                }
                else
                {
                    $status = 'Inactive';
                }
                if ($row['bidMaxPrice'] != 0)
                {
                    $name = @$company[0]->company_name;

                }
                else
                {
                    $name = '';
                }

                fputcsv($output, array(

                    "Sl No" => $row['serial_no'],
                    "Sheet Name" => $row['sheet_name'],
                    "Sheet Number" => $row['sheet_no'],
                    'Garden' => $row['garden'],
                    'Invoice' => $row['invoice'],
                    'Grade' => $row['grade'],
                    'Pkgs' => $row['pkgs_no'],
                    'kgs' => $row['total_kgs'],
                    'Price Idea' => $row['price_idea'],
                    'Bid Price' => $row['bidMaxPrice'],
                    'Buyer' => @$name,
                    'Status' => $status

                ));

                $count++;

            }
        }

    }

    //////////////////////////////////////////////////MESSAGE CRON/////////////////////////////////////////////
    //to BUYER
   function seller_price_update_to_buyer_msg()
    {
        $this
            ->db
            ->select('tos.sheet_id,tbd.sheet_id,tu.id,tbd.buyer_id,tbd.seller_update,tbd.buyer_id,tu.phone,tos.sheet_no,tos.sheet_name');
        $this
            ->db
            ->from('tt_bid_details tbd');
        $this
            ->db
            ->join('tt_offer_sheets tos', 'tos.sheet_id = tbd.sheet_id');
        $this
            ->db
            ->join('tt_users tu', 'tu.id = tbd.buyer_id');
        $this
            ->db
            ->where('tbd.seller_update', 'Y');
        $this
            ->db
            ->group_by('tbd.buyer_id');
        $sheet = $this
            ->db
            ->get();
        $sheetList = $sheet->result();

        if (!empty($sheetList))
        {
            foreach ($sheetList as $msg)
            {

                $to = $msg->phone;
                //$body = "There has been an update on offer sheet " . $msg->sheet_no . "-" . $msg->sheet_name . " in the last 30 mins";
                $messageId='111593';
                $variables=$msg->sheet_no.'|'.$msg->sheet_name;
                send_sms($to, $messageId, $variables);
              

                $data_active = array(
                    'seller_update' => 'N'
                );
                $this
                    ->db
                    ->where('buyer_id', $msg->buyer_id);
                $this
                    ->db
                    ->update('tt_bid_details', $data_active);

            }

        }
    }

     function bid_updation_msg()
    {

        $this
            ->db
            ->select('tos.sheet_id,tbl.sheet_id,tu.id,tbl.seller_id,tbl.message,tbl.buyer_price,tbl.sheet_id,tu.phone,tos.sheet_no,tos.sheet_name');
        $this
            ->db
            ->from('tt_bid_log tbl');
        $this
            ->db
            ->join('tt_offer_sheets tos', 'tos.sheet_id = tbl.sheet_id');
        $this
            ->db
            ->join('tt_users tu', 'tu.id = tbl.seller_id');
        $this
            ->db
            ->where('tbl.message', 'N');
        $this
            ->db
            ->where('tbl.buyer_price !=', '0');
        $this
            ->db
            ->group_by('tbl.sheet_id');
        $sheet = $this
            ->db
            ->get();
        $sheetList = $sheet->result();

        foreach ($sheetList as $row)
        {
            $msg_sheet_created_number = $row->phone;
            $to = $msg_sheet_created_number;
            $msg_sheet_no = $row->sheet_no;
            $msg_sheet_name = $row->sheet_name;

            //$body = "There has been an update on offer sheet " . $msg_sheet_no . "-" . $msg_sheet_name . " in the last 30 mins";

            $messageId='111593';
            $variables=$msg_sheet_no.'|'.$msg_sheet_name;
            send_sms($to, $messageId, $variables);

           
            $messageStatus = array(
                'message' => 'Y'
            );
            $this
                ->db
                ->where('sheet_id', $row->sheet_id);
            $this
                ->db
                ->update('tt_bid_log', $messageStatus);

        }

    }
    //to seller
   function expiry_thirty_sixty_min_msg_seller()
    {
        $today = date('Y-m-d');

        $Now=date('Y-m-d H:i:s');

        ////////////////////////////////////////////// 60 Minute /////////////////////////////////////////////////

        $sql="SELECT * FROM (
                                SELECT `sheet_id`,`message_60`,TIMESTAMPDIFF(MINUTE, '$Now', `expiry_date`) as `difference` FROM `tt_offer_sheets`
                            ) tt_offer_sheets
                            WHERE  difference BETWEEN 50 AND 60 AND `message_60` = 'N'
                            ";
        $qry=$this->db->query($sql);
        $res=$qry->result();


        $shtId=array();

        foreach ($res as $key => $value) {
           array_push($shtId, $value->sheet_id);
        }

              ////////////////////////////////////////////// 30 Minute /////////////////////////////////////////////////

        $sql_30="SELECT * FROM (
                                SELECT `sheet_id`,`message_30_seller`,TIMESTAMPDIFF(MINUTE, '$Now', `expiry_date`) as `difference` FROM `tt_offer_sheets`
                            ) tt_offer_sheets
                            WHERE  difference BETWEEN 20 AND 30 AND `message_30_seller` = 'N'
                            ";
        $qry_30=$this->db->query($sql_30);
        $res_30=$qry_30->result();


        $shtId_30=array();

        foreach ($res_30 as $key => $value_30) {
           array_push($shtId_30, $value_30->sheet_id);
        }

        if(!empty($shtId_30))
        {

        $this
            ->db
            ->select('tu.id,tos.created_by,tos.message_30_seller,tos.expiry_date,tos.sheet_id,tu.phone,tos.sheet_no,tos.sheet_name');
        $this
            ->db
            ->from('tt_offer_sheets tos');
        $this
            ->db
            ->join('tt_users tu', 'tu.id = tos.created_by');
        $this
            ->db
            ->where('tos.message_30_seller', 'N');
        $this
            ->db
            ->where('DATE(tos.expiry_date)', $today);
        $this
            ->db
            ->where_in('tos.sheet_id',$shtId_30);
        $sheet_30 = $this
            ->db
            ->get();
        $sheetList_30 = $sheet_30->result();

        }
        else
        {
            $sheetList_30=array();
        }


       

        if(!empty($shtId))
        {
        $this
            ->db
            ->select('tu.id,tos.created_by,tos.expiry_date,tos.sheet_id,tos.message_60,tu.phone,tos.sheet_no,tos.sheet_name');
        $this
            ->db
            ->from('tt_offer_sheets tos');
        $this
            ->db
            ->join('tt_users tu', 'tu.id = tos.created_by');
        $this
            ->db
            ->where('tos.message_60', 'N');
        $this
            ->db
            ->where('DATE(tos.expiry_date)', $today);

        $this
            ->db
            ->where_in('tos.sheet_id',$shtId);
        $sheet_60 = $this
            ->db
            ->get();
        $sheetList_60 = $sheet_60->result();


        }
        else
        {
            $sheetList_60=array();
        }

       
  
        

        if (!empty($sheetList_30))
        {
            foreach ($sheetList_30 as $row)
            {
                $msg_sheet_created_number = $row->phone;
                $to = $msg_sheet_created_number;
                $msg_sheet_no = $row->sheet_no;
                $msg_sheet_name = $row->sheet_name;

             

               
                    //$body = "Offer sheet  " . $msg_sheet_no . "-" . $msg_sheet_name . "  will expire in 30 mins";
                    $messageId='111595';
                    $variables=$msg_sheet_no.'|'.$msg_sheet_name;
                    send_sms($to, $messageId, $variables);

                   

                    $dataMsg = array(
                        'message_30_seller' => 'Y',
                        'message_30_time' => date('Y-m-d H:i:s')
                    );
                    $this
                        ->db
                        ->where('sheet_id', $row->sheet_id);
                    $this
                        ->db
                        ->update('tt_offer_sheets', $dataMsg);

               

            }
        }
        if (!empty($sheetList_60))
        {
            foreach ($sheetList_60 as $row)
            {
                $msg_sheet_created_number = $row->phone;
                $to = $msg_sheet_created_number;
                $msg_sheet_no = $row->sheet_no;
                $msg_sheet_name = $row->sheet_name;

           
               

                    //$body = "Offer sheet  " . $msg_sheet_no . "-" . $msg_sheet_name . "  will expire in 60 mins";
                $messageId='111594';
                $variables=$msg_sheet_no.'|'.$msg_sheet_name;
                send_sms($to, $messageId, $variables);

                    send_sms($to, $body);

                    $dataMsg = array(
                        'message_60' => 'Y',
                        'message_60_time' => date('Y-m-d H:i:s')
                    );
                    $this
                        ->db
                        ->where('sheet_id', $row->sheet_id);
                    $this
                        ->db
                        ->update('tt_offer_sheets', $dataMsg);

              

            }
        }
    }


    //to buyer
    function expiry_thirty_sixty_min_msg_buyer()
    {

        $today = date('Y-m-d');

       

        $Now=date('Y-m-d H:i:s');

        ////////////////////////////////////////////// 60 Minute /////////////////////////////////////////////////

        $sql="SELECT * FROM (
                                SELECT `sheet_id`,TIMESTAMPDIFF(MINUTE, '$Now', `expiry_date`) as `difference` FROM `tt_offer_sheets`
                            ) tt_offer_sheets
                            WHERE difference BETWEEN 50 AND 60
                            ";
        $qry=$this->db->query($sql);
        $res=$qry->result();


        $shtId=array();

        foreach ($res as $key => $value) {
           array_push($shtId, $value->sheet_id);
        }


        ////////////////////////////////////////////// 30 Minute /////////////////////////////////////////////////

        $sql_30="SELECT * FROM (
                                SELECT `sheet_id`,TIMESTAMPDIFF(MINUTE, '$Now', `expiry_date`) as `difference` FROM `tt_offer_sheets`
                            ) tt_offer_sheets
                            WHERE difference BETWEEN 20 AND 30
                            ";
        $qry_30=$this->db->query($sql_30);
        $res_30=$qry_30->result();


        $shtId_30=array();

        foreach ($res_30 as $key => $value_30) {
           array_push($shtId_30, $value_30->sheet_id);
        }



        if(!empty($shtId))
        {
        $this
            ->db
            ->select('tos.sheet_id,tbd.sheet_id,tu.id,tbd.buyer_id,tbd.message,tbd.sheet_id,tbd.buyer_id,tu.phone,tos.sheet_name,tos.sheet_no,tos.expiry_date');
        $this
            ->db
            ->from('tt_bid_details tbd');
        $this
            ->db
            ->join('tt_offer_sheets tos', 'tos.sheet_id = tbd.sheet_id');
        $this
            ->db
            ->join('tt_users tu', 'tu.id = tbd.buyer_id');
        $this
            ->db
            ->where('tbd.message', 'N');
        $this
            ->db
            ->where_in('tbd.sheet_id',$shtId);
     
        $this
            ->db
            ->group_by('tbd.buyer_id');
        $sheet = $this
            ->db
            ->get();
        $sheetList = $sheet->result();

        }
        else
        {
            $sheetList=array();
        }
               

      
        if(!empty($shtId_30))
        {
        $this
            ->db
            ->select('tos.sheet_id,tbd.sheet_id,tu.id,tbd.buyer_id,tbd.message_30,tos.expiry_date,tbd.sheet_id,tbd.buyer_id,tu.phone,tos.sheet_name,tos.sheet_no,tos.expiry_date');
        $this
            ->db
            ->from('tt_bid_details tbd');
        $this
            ->db
            ->join('tt_offer_sheets tos', 'tos.sheet_id = tbd.sheet_id');
        $this
            ->db
            ->join('tt_users tu', 'tu.id = tbd.buyer_id');
        $this
            ->db
            ->where('tbd.message_30', 'N');
        $this
            ->db
            ->where('DATE(tos.expiry_date)', $today);

         $this
            ->db
            ->where_in('tbd.sheet_id',$shtId_30);
        $this
            ->db
            ->group_by('tbd.buyer_id');
        $sheet_30 = $this
            ->db
            ->get();
        $sheetList_30 = $sheet_30->result();

        }
        else
        {
            $sheetList_30=array();
        }
       
       

        if (!empty($sheetList))
        {
            foreach ($sheetList as $row)
            {
                $msg_sheet_created_number = $row->phone;
                $to = $msg_sheet_created_number;
                $msg_sheet_no = $row->sheet_no;
                $msg_sheet_name = $row->sheet_name;

                $date1 = strtotime($row->expiry_date);
                $d = date('Y-m-d H:i:s');
                $date2 = strtotime($d);
                $diff = abs($date2 - $date1);

                $minutes = round($diff / 60);

                

              
                    //$body = "Offer sheet " . $msg_sheet_no . "-" . $msg_sheet_name . "  will expire in 60 mins";

                $messageId='111594';
                $variables=$msg_sheet_no.'|'.$msg_sheet_name;
                send_sms($to, $messageId, $variables);

                   // send_sms($row->phone, $body);

                    $dataMsg = array(
                        'message' => 'Y',
                        'message_time' => date('Y-m-d H:i:s')
                    );
                    $this
                        ->db
                        ->where('sheet_id', $row->sheet_id);
                    $this
                        ->db
                        ->where('buyer_id', $row->buyer_id);
                    $this
                        ->db
                        ->update('tt_bid_details', $dataMsg);
                
               
                
           }

        }
        //echo '<pre>';print_r($sheetList);exit;
        if (!empty($sheetList_30))
        {
            foreach ($sheetList_30 as $row)
            {
                $msg_sheet_created_number = $row->phone;
                $to = $msg_sheet_created_number;
                $msg_sheet_no = $row->sheet_no;
                $msg_sheet_name = $row->sheet_name;

                $date1 = strtotime($row->expiry_date);
                $d = date('Y-m-d H:i:s');
                $date2 = strtotime($d);
                $diff = abs($date2 - $date1);

                $minutes = round($diff / 60);

              

                    //$body = "Offer sheet  " . $msg_sheet_no . "-" . $msg_sheet_name . "  will expire in 30 mins";
                $messageId='111595';
                $variables=$msg_sheet_no.'|'.$msg_sheet_name;
                send_sms($to, $messageId, $variables);

                    //send_sms($row->phone, $body);

                    $dataMsg = array(
                        'message_30' => 'Y',
                        'message_30_time' => date('Y-m-d H:i:s')
                    );
                    $this
                        ->db
                        ->where('sheet_id', $row->sheet_id);
                    $this
                        ->db
                        ->where('buyer_id', $row->buyer_id);
                    $this
                        ->db
                        ->update('tt_bid_details', $dataMsg);

              

            }

        }

        //echo '<pre>';print_r($sheetList);exit;
        

        
    }


        function getdivisiondata()
    {

        $sheet_id=$this->input->post('sheet_id');
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
            $totalArray=array();
        $buyer_id = @$dtl['id'];

        $currentTime=date('Y-m-d H:i:s');

        $timestamp = strtotime($currentTime);

      
        $time = $timestamp - 4;

       
        $datetime = date("Y-m-d H:i:s", $time);

         $this
            ->db
            ->select('tbd.inv_id,tbd.div_time,tbd.sheet_id,tbd.approve,toi.pkgs_no');
        $this
            ->db
            ->from('tt_buyer_division tbd');
         $this
            ->db
            ->join('tt_offer_invoice toi','toi.invoice_id=tbd.inv_id');
        $this
            ->db
            ->where('tbd.div_time >', $datetime);
        $this
            ->db
            ->where('tbd.sheet_id', $sheet_id);
        $this
            ->db
            ->where('tbd.approve', 'A');
        $sheet = $this
            ->db
            ->get();
        $sheetList = $sheet->result();

       

            if(!empty($sheetList)){
        
            foreach($sheetList as $key=>$row){ 

                 $getValue['inv_id']=$row->inv_id;
                 $getValue['pkgs_no']=$row->pkgs_no;

                array_push($totalArray, $getValue);


            }

             echo json_encode($totalArray);

    }
}

 function getmyOfferSheet()
    {
        $sheet_id=$this->input->post('sheet_id');
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];

        $currentTime=date('Y-m-d H:i:s');

        $timestamp = strtotime($currentTime);

      
        $time = $timestamp - 4;

       
        $datetime = date("Y-m-d H:i:s", $time);

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from('tt_bid_details tbd');
        $this
            ->db
            ->where('tbd.bid_time >', $datetime);
        $this
            ->db
            ->where('tbd.sheet_id', $sheet_id);
        $sheet = $this
            ->db
            ->get();
        $sheetList = $sheet->result();

        $invoiceId=array();

        if(!empty($sheetList)){
        foreach($sheetList as $key=>$row)
        {
            array_push($invoiceId, $row->invoice_id);

        }
        }

        /*$invoice_dtl = $this
            ->Common
            ->find(['table' => OFFER_INVOICE, 'select' => "*", 'where' => "sheet_id = {$sheet_id}",

        ]);*/

        $this
            ->db
            ->select('*');
        $this
            ->db
            ->from('tt_offer_invoice');
        $this
            ->db
            ->where('sheet_id', $sheet_id);

        if(!empty($invoiceId))
        {

              $this
            ->db
            ->where_in('invoice_id', $invoiceId);

        }
      
        $invoicedtl = $this
            ->db
            ->get();
        $invoice_dtl = $invoicedtl->result();




        $offer_sheet = $this
            ->Common
            ->find([

                'table' => OFFER_SHEET . ' os', 

                'select' => "*", 

                'join' => [

                    [LOCATION, 'loc', 'INNER', "loc.id = os.location"],


                ], 

               'where' => "os.sheet_id = '{$sheet_id}'", 

               'query' => 'first'
           ]);

        $totalArray=array();




        

        if(!empty($invoice_dtl) && !empty($invoiceId)){
        
        foreach($invoice_dtl as $key=>$row){ 

        $inv_id=$row->invoice_id;

        $this->db->select('*');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->join(USERS.' us','us.id = bd.buyer_id');
        $this->db->where('invoice_id',$inv_id);
        $this->db->where('bd.buyer_id',$buyer_id);
        $bid_lock_self=$this->db->get();
        $bid_lock_self_buyer = $bid_lock_self->result();

        $this->db->select('MAX(buyer_price) as maxprice');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$inv_id);
        $bid_max=$this->db->get();
        $bid_max_details =  $bid_max->result();

       
        $max_price=@$bid_max_details[0]->maxprice;

        $this->db->select('*');
        $this->db->from(BID_DETAILS.' bd');
        $this->db->join(USERS.' us','us.id = bd.buyer_id');
        $this->db->where('invoice_id',$inv_id);
        $this->db->where('bd.buyer_price',$max_price);
        $bid_max_buyer=$this->db->get();
        $bid_max_details_buyer = $bid_max_buyer->result();

        $this->db->select('*');
        $this->db->from("tt_buyer_recieve_comment".' bd');
        $this->db->join(USERS.' us','us.id = bd.user_id');
        $this->db->where('bd.invoice_id',$inv_id);
        $bid_comment=$this->db->get();
        $bid_comment_buyer=$bid_comment->result();

        $this->db->select('*');
        $this->db->from(BID_DETAILS);
        $this->db->where('invoice_id',$inv_id);
        $this->db->where('buyer_price !=',0);
        $division_check=$this->db->get();
        $division_check_buyer=$division_check->result();

        $this->db->select('*'); 
        $this->db->from('tt_buyer_division');
        $this->db->where('inv_id',$inv_id);
        $this->db->where('approve','A');
        $division_check=$this->db->get();
        $division_check_accept=$division_check->result();

        //$getValue['bid_lock_self_buyer']=$bid_lock_self_buyer;
        //$getValue['bid_max']=$bid_max_details;
        //$getValue['bid_max_buyer']=$bid_max_details_buyer;
       
        $getValue['garden']=$row->garden;
        $getValue['invoice']=$row->invoice;
        $getValue['grade']=$row->grade;
        $getValue['pkgs_no']=$row->pkgs_no;
        $getValue['total_kgs']=$row->total_kgs;
        $getValue['price_idea']=$row->price_idea;

        $getValue['bid']=$max_price;

        $getValue['inv_status']=$row->inv_status;
        $getValue['invoice_id']=$row->invoice_id;
        $getValue['key']=$key;
        $getValue['pkgs_no']=$row->pkgs_no;
        $getValue['buyer']= substr(@$bid_max_details_buyer[0]->company_name,0,10);
        $getValue['buyerfull']= @$bid_max_details_buyer[0]->company_name;
        $getValue['buyer_lock']=@$bid_lock_self_buyer[0]->buyer_lock;
        $getValue['comment']=substr(@$bid_comment_buyer[0]->comment,0,10);
        $getValue['fullcomment']=@$bid_comment_buyer[0]->comment;
        $getValue['division']= @$offer_sheet['division'];
        $getValue['seller_final_lock']=$row->seller_final_lock;
        $getValue['buyer_can_see']=@$offer_sheet['buyer_can_see'];
        $getValue['sheet_no']=@$offer_sheet['sheet_no'];
        $getValue['sheet_id']=@$offer_sheet['sheet_id'];
        $getValue['sheet_name']=@$offer_sheet['sheet_name'];
        $getValue['buyerId']=@$buyer_id;
        $getValue['bidMaxbuyerId']= @$bid_max_details_buyer[0]->id;

        $getValue['division_check_accept']=count(@$division_check_accept);
        $getValue['division_check_buyer']=count(@$division_check_buyer);
        array_push($totalArray, $getValue);



        }


        }
        echo json_encode($totalArray);
        //echo "<pre>";print_r($totalArray);

        //echo json_encode($totalArray);

       /*   $this->db->select('MAX(buyer_price) as maxprice');
          $this->db->from(BID_DETAILS);
          $this->db->where('invoice_id',$inv_id);
          $bid_max=$this->db->get();
          return $bid_max->result();

          $this->db->select('*');
          $this->db->from(BID_DETAILS.' bd');
          $this->db->join(USERS.' us','us.id = bd.buyer_id');
          $this->db->where('invoice_id',$inv_id);
          $this->db->where('bd.buyer_price',$max_price);
          $bid_max_buyer=$this->db->get();
          return $bid_max_buyer->result();

          $this->db->select('*');
          $this->db->from(BID_DETAILS.' bd');
          $this->db->join(USERS.' us','us.id = bd.buyer_id');
          $this->db->where('invoice_id',$inv_id);
          $this->db->where('bd.buyer_id',$buyer_id);
          $bid_lock_self=$this->db->get();
          return $bid_lock_self->result();

          $this->db->select('*');
          $this->db->from(BID_DETAILS);
          $this->db->where('invoice_id',$inv_id);
          $this->db->where('buyer_price !=',0);
          $division_check=$this->db->get();
          return $division_check->result();

         $this->db->select('*'); 
         $this->db->from('tt_buyer_division');
         $this->db->where('inv_id',$inv_id);
         $this->db->where('approve','A');
         $division_check=$this->db->get();
         return $division_check->result();*/

      /*  $this
            ->db
            ->select('*');
        $this
            ->db
            ->from('tt_bid_details tbd');
        $this
            ->db
            ->where('tbd.bid_time >', $datetime);
        $this
            ->db
            ->where('tbd.sheet_id', $sheet_id);
        $sheet = $this
            ->db
            ->get();
        $sheetList = $sheet->result();



        $bid_max_details_push=array();
        $bid_max_details_buyer_push=array();
        $invoiceDtl_push=array();
        $seller_price_push=array();
        if(!empty($sheetList)){
        foreach($sheetList as $key=>$row)
        {

       

        $bid_max_details=$this->Common->bid_max_detail($row->invoice_id);

        $bid_max_details_buyer=$this->Common->bid_max_details_buyer(@$row->invoice_id,@$bid_max_details[0]->maxprice);

        array_push($bid_max_details_push, @$bid_max_details[0]->maxprice);
        array_push($bid_max_details_buyer_push, @$bid_max_details_buyer[0]->company_name);
       

       

        }
        }
          

      
 echo json_encode(array('Price'=>$bid_max_details_push,'buyerCompany'=>$bid_max_details_buyer_push,'bid_id'=>$sheetList));*/

        


    }


    function getsellerlockdata()
    {

        $sheet_id=$this->input->post('sheet_id');
        $dtl = $this
            ->session
            ->userdata(CUSTOMER_SESS);
            $totalArray=array();
        $buyer_id = @$dtl['id'];

        $currentTime=date('Y-m-d H:i:s');

        $timestamp = strtotime($currentTime);

      
        $time = $timestamp - 4;

       
        $datetime = date("Y-m-d H:i:s", $time);

         $this
            ->db
            ->select('tbd.seller_lock_time,tbd.sheet_id,tbd.seller_lock,tbd.invoice_id,toi.price_idea');
        $this
            ->db
            ->from('tt_bid_details tbd');
        $this
            ->db
            ->join('tt_offer_invoice toi','toi.invoice_id=tbd.invoice_id');
        $this
            ->db
            ->where('tbd.seller_lock_time >', $datetime);
        $this
            ->db
            ->where('tbd.sheet_id', $sheet_id);
        $this
            ->db
            ->where('tbd.seller_lock ', 'Y');
        $sheet = $this
            ->db
            ->get();
        $sheetList = $sheet->result();

        

            if(!empty($sheetList)){
        
            foreach($sheetList as $key=>$row){ 

                 $getValue['invoice_id']=$row->invoice_id;
                 $getValue['price_idea']=$row->price_idea;

                array_push($totalArray, $getValue);


            }

             echo json_encode($totalArray);

    }
}

function get_sms()
{
     $messageId='111592';
     $to = '9038499732';
     $msg_buyer_name="tehhjjkdggg a";
     $msg_sheet_no="12d";
     $msg_sheet_name="ABCD";

     $variables=$msg_buyer_name.'|'.$msg_sheet_no.'|'.$msg_sheet_name;
     send_sms($to, $messageId, $variables);
}

  function pushHBPdatademo(){
      //  $firebase = $this->firebase->init();
       // $database = $firebase->getDatabase();

        $sheet_id = $this ->input->post('sheet_id');
        $seconds  = $this ->input->post('seconds');
        $dtl = $this ->session->userdata(CUSTOMER_SESS);
        $buyer_id = @$dtl['id'];

        $jsonFile = file_get_contents('public/uploads/'.$sheet_id.'.json');
        $decodeJson= json_decode($jsonFile,true);

        $shtDetails = $this
            ->Common
            ->find(['table' => OFFER_SHEET, 'select' => "expiry_date,sheet_id", 'where' => "sheet_id = {$sheet_id}", 'query' => 'first']);


        $allData = array();

        foreach($decodeJson as $key1=>$row){
            if(!empty($row['buyer_hbp'])){
            foreach($row['buyer_hbp'] as $key=>$row1){
                
                $this->db->select('sheet_id,status');
                $this->db->from('tt_switch_on');
                $this->db->where('sheet_id',$row['sheet_id']);
                $this->db->where('buyer_id',$row1['buyer_id']);
                $getval=$this->db->get();
                $stat= $getval->result();
                $hbpType=@$stat[0]->status;
                if((@$hbpType!='semi_aumatic' || @$hbpType=="")){

                    if(@$row['inv_status']!='A' && @$row1['abp'] >= @$row['price_idea']){
                        $this->db->select('id,company_name');
                        $this->db->from('tt_users');
                        $this->db->where('id',@$row1['buyer_id']);
                        $qry=$this->db->get();
                        $getCom1=$qry->result();

             

                        $firebaseData['bid']                  = @$row['price_idea'];
                        $firebaseData['price_idea']           = @$row['price_idea'];
                        $firebaseData['firebase_key']         = @$row['firebase_key'];
                        $firebaseData['buyer']                = substr(@$getCom1[0]->company_name,0,10);
                        $firebaseData['bidMaxbuyerId']        = @$row1['buyer_id'];
                        $firebaseData['buyerId']              = @$row1['buyer_id'];
                        $firebaseData['buyerfull']            = @$getCom1[0]->company_name;
                        if(count(@$allData) < 100){
                           array_push($allData,$firebaseData);
                        }else{
                            break;
                        }
                        
                    }
                }
            
            }
        }
    }

    echo json_encode($allData);
}

}

