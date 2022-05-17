<html>

<head>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Invoice</title>

    <meta http-equiv="Content-Type" content="text/html;"/>

    <meta charset="UTF-8">

	<style media="all">

		@font-face {

            font-family: 'Roboto';

          

            font-weight: normal;

            font-style: normal;

        }

        *{

            margin: 0;

            padding: 0;

            line-height: 1.3;

            font-family: 'Roboto';

            color: #333542;

        }

		body{

			font-size: .875rem;

		}

		.gry-color *,

		.gry-color{

			color:#878f9c;

		}

		table{

			width: 100%;

		}

		table th{

			font-weight: normal;

		}

		table.padding th{

			padding: .5rem .7rem;

		}

		table.padding td{

			padding: .7rem;

		}

		table.sm-padding td{

			padding: .2rem .7rem;

		}

		.border-bottom td,

		.border-bottom th{

			border-bottom:1px solid #eceff4;

		}

		.text-left{

			text-align:left;

		}

		.text-right{

			text-align:right;

		}

		.small{

			font-size: .85rem;

		}

		.currency{



		}

	</style>

</head>

<body>

	<div>

	

		<div style="background: #eceff4;padding: 1.5rem;">

			<table>

				<tr>

					<td>

					

							<img loading="lazy"  src="<?=FRONT_ASSETS?>images/logo.png" height="40" style="display:inline-block;">

					
					</td>

				</tr>

			</table>

			<table>


				<tr>

					<td style="font-size: 1.2rem;" class="strong"><?=SITE_NAME?></td>

					<td class="text-right"></td>

				</tr>

				<tr>

					<td class="gry-color small"><?=@$setting[0]['address']?></td>

					<td class="text-right"></td>

				</tr>

				<tr>

					<td class="gry-color small">Email: <?=@$setting[0]['email']?></td>
					

					<td class="text-right small"><span class="gry-color small">Order ID:</span> <span class="strong"><?=@$orderno;?></span></td>

				</tr>

				<tr>

					<td class="gry-color small">Phone: <?=@$setting[0]['contact']?></td>

				

				</tr>

			</table>



		</div>



		<div style="padding: 1.5rem;padding-bottom: 0">

			 <table>


				<tr><td class="strong">Billing Address</td></tr>

				<tr><td><?php echo @$getuserdetails[0]['first_name'].' '.@$getuserdetails[0]['last_name']; ?></td></tr>

				<tr><td class="gry-color small"><?php echo @$getuserdetails[0]['address']?>, <?php echo @$getuserdetails[0]['pincode']?></td></tr>

				<tr><td class="gry-color small">Email: <?php echo @$getuserdetails[0]['email']?></td></tr>

				<tr><td class="gry-color small">Phone: <?php echo @$getuserdetails[0]['phone']?></td></tr>

			</table>

            <table>


				<tr><td class="strong">Shipping Address</td></tr>

				<tr><td ><?php echo @$getAddress[0]['user_first_name'].' '.@$getAddress[0]['user_last_name']; ?></td></tr>

				<tr><td class="gry-color small"><?php echo @$getAddress[0]['user_address']?>, <?php echo @$getAddress[0]['user_pincode']?>, <?php echo @$getAddress[0]['user_city']?>, <?php echo @$getAddress[0]['user_state']?>, <?php echo @$getAddress[0]['user_country']?></td></tr>

				<tr><td class="gry-color small">Email: <?php echo @$getuserdetails[0]['email']?></td></tr>

				<tr><td class="gry-color small">Phone: <?php echo @$getAddress[0]['user_phone_no']?></td></tr>

			</table>

		</div>

		



	    <div style="padding: 1.5rem;">

			<table class="padding text-left small border-bottom">

				<thead>

	                <tr class="gry-color" style="background: #eceff4;">

	                    <th width="35%">Product Name</th>

						<th width="15%">MRP (AUD)</th>

	                    <th width="10%">Discount(%)</th>

	                    <th width="15%">Net Pricce (AUD)</th>

	                    <th width="10%">QTY</th>

	                    <th width="15%" class="text-right">Total (AUD)</th>

	                </tr>

				</thead>

				<tbody class="strong">

	               <?php 
					$i=0;
					$subTotal = 0;
					 $order_details=$this
					    ->Common
					    ->find(
					        [
					        'table'  =>ORDER_DETAILS." ORDER_DETAILS", 
					        'select' => "*", 
					        'join'  => [
			                                [PRODUCT, 'Product', 'INNER', "ORDER_DETAILS.order_product_id = Product.id"],
			                               
			                            ],
					        'where'  => "order_id = '{$order_id}'"
					        ]);
					foreach($order_details as $details)
					{
						$subTotal = @$subTotal + (@$details['cart_item_pro_qty'] * @$details['cart_item_net_price']);
						$i++;
						?>

							<tr class="">

								<td><?=@$details['title']?></td>

								<td class="gry-color"><?=@$details['cart_item_price']?></td>

								<td class="gry-color currency"><?=@$details['cart_item_price_disc']?></td>

								<td class="gry-color currency"><?=@$details['cart_item_net_price']?></td>

			                    <td class="text-right currency"><?=@$details['cart_item_pro_qty']?></td>

			                     <td class="text-right currency"><?=(@$details['cart_item_pro_qty'] * @$details['cart_item_net_price'])?></td>

							</tr>

		               <?php  } ?>

	            </tbody>

			</table>

		</div>
	    <div style="padding:0 1.5rem;">

	        <table style="width: 40%;margin-left:auto;" class="text-right sm-padding small strong">

		        <tbody>

			        <tr>

			            <th class="gry-color text-left">Sub Total (AUD)</th>

			            <td class="currency"><?=number_format(@$subTotal,2);?></td>

			        </tr>
			        <?php if(@$shipping_charge > 0) { ?>
			        <tr>

			            <th class="gry-color text-left">Shipping Cost (AUD)</th>

			            <td class="currency"><?=number_format(@$shipping_charge,2)?></td>

			        </tr>
			        <?php } ?>
			        <tr>

			            <th class="text-left strong">Grand Total (AUD)</th>

			            <td class="currency"><?=number_format(@$total_amt,2)?></td>

			        </tr>
		        </tbody>
		    </table>
	    </div>
	</div>
</body>

</html>

