<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Order E-mail</title>
</head>
<body style="font-family:arial,sans-serif; font-size:13px; color:#222">
<div style=" width: 100%">
  <div><a href="<?php echo base_url(); ?>"/><img src="<?=FRONT_ASSETS?>images/logo.png" alt="Ignitemusic" style="width: 50px;"></a></div>
  <div>
<?php
$dtl = $this->session->userdata(MEMBER_SESS);
$getuserdetails = $this
    ->Common
    ->find([
            'table'  => USERS, 
            'select' => "*", 
            'where'  => "id = '{$dtl['id']}'"
          ]);

$getAddress = $this
  ->Common
  ->find([
          'table'  => "im_order_address", 
          'select' => "*", 
          'where'  => "order_id = '{$mail_data['orderId']}'"
        ]);
?>
  <tr><td class="strong small gry-color"><b>Order Id :</b> <?=@$mail_data['order_id']?></td></tr><br>
  <tr><td class="strong small gry-color"><b>Order Date :</b> <?php echo date('jS M y', strtotime(@$mail_data['orderDate']));?></td></tr>
<table border="1" width="100%">
<tr>
    <td class="strong small gry-color"><b>Billing Address</b><br>
    <?php echo @$getuserdetails[0]['first_name'].' '.@$getuserdetails[0]['last_name']; ?><br>
    <?php echo @$getuserdetails[0]['address']?>, <?php echo @$getuserdetails[0]['pincode']?><br>
    <?php echo @$getuserdetails[0]['email']?><br>
    <?php echo @$getuserdetails[0]['phone']?>
  </td>
</tr>
<tr>
  <td class="strong small gry-color"><b>Shipping Address</b><br>
  <?php echo @$getAddress[0]['first_name'].' '.@$getAddress[0]['last_name']; ?><br>
  <?php echo @$getAddress[0]['address']?>, <?php echo @$getAddress[0]['pincode']?>, <?php echo @$getAddress[0]['city']?>, <?php echo @$getAddress[0]['state']?>, <?php echo @$getAddress[0]['country']?><br>
  <?php echo @$getuserdetails[0]['email']?><br>
  <?php echo @$getAddress[0]['phone']?>
</td>
</tr>
</table>
<table  width="100%">
  <thead>
      <tr style="background: #eceff4;">

          <th width="30%">Product Name</th>

           <th width="15%">MRP (AUD)</th>

          <th width="10%">Discount(%)</th>

          <th width="20%">Net Price (AUD)</th>

          <th width="10%">QTY</th>

          <th width="15%" class="text-right">Total (AUD)</th>
      </tr>
  </thead>

    <tbody>
      <?php 
        $i=0;
        $subTotal = 0;
        $order_details=$this
        ->Common
        ->find([
            'table'  =>ORDER_DETAILS." ORDER_DETAILS", 
            'select' => "*", 
            'join'  =>[
                        [PRODUCT, 'Product', 'INNER', "ORDER_DETAILS.order_product_id = Product.id"],
                               
                      ],
            'where'  => "order_id = '{$mail_data['orderId']}'"
          ]);
          foreach($order_details as $details){
            $subTotal = @$subTotal + (@$details['cart_item_pro_qty'] * @$details['cart_item_net_price']);
            $i++;
            ?>
          <tr>
            <td><?=@$details['title']?></td>
            <td><?=@$details['cart_item_price']?></td>
            <td><?=@$details['cart_item_price_disc']?></td>
            <td><?=@$details['cart_item_net_price']?></td>
            <td><?=@$details['cart_item_pro_qty']?></td>
            <td><?=(@$details['cart_item_pro_qty'] * @$details['cart_item_net_price'])?></td>
          </tr>
          <?php  } ?>
    </tbody>

  
               <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td  width="20%"><b>Sub-Total</b></td>
                  <td  width="20%"><?php echo number_format((float)@$subTotal, 2, '.', '');?> AUD</td>
                  

                 
                </tr>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td  width="20%"><b>Shipping Charge</b> </td>
                  <td  width="20%"> <?=number_format((float)@$mail_data['shipping'], 2, '.', '');?> AUD</td>
                 
                 
                </tr>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td  width="20%"><b>Total</b></td>
                  <td  width="20%"><?=number_format((float)@$mail_data['total_amt'], 2, '.', '');?> AUD</td>
                 
                  
                </tr>
              
</table>
<table border="1">
<tr>
<td colspan="10">THIS IS A COMPUTER GENERATED INVOICE AND DOES NOT REQUIRE SIGNATURE</td>
</tr>
</table>
<h3> </h3>
<h3></h3>
<h3></h3>
<h3></h3>
<br />
</div>
<div class="clear"></div>
</div>
</body>
</html>
