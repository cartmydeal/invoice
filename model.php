<style>
p
{
line-height:15px;
}
.base
{

}
 .print_this{display:none;}
 
    @media print {
     .input{display: none;}
     .orderedetails {
       display: block;
     }
    }
	position: absolute;
left: 20%;
top: 30px;
#base{
margin-left:7%;
margin-top:300px;
width:500px}
.footer
{
width:800px;
}
#product
{
width:800px;
}
.columns
{width:800px;
}
.page

{   
	margin-top:10%;
	line-height:9px;
	position:relative;
	left:170px;
	background-color:#FFFFFF;
	height:900px;
	width: 800px;
}

.container
{
	zoom:1;
	border:300px;
	position:static;
	border-color:#6688FF;
	width:895px;
	height:842px;
	
	margin-left:-10%;
	background-color:#FFFFFF;
	
	
}
.left-column
{
	width:350px;
	height:350px;
	
}
.right-column

{   top:44px;
	right:81px;
    position:absolute;
    float:right;
	width:350px;
	height:350px;
}
</style>
<div class="input">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" />
<input type="text" name="order" />
<input type="submit" name="sub" />
</form>
<a href="javascript:window.print()" style="position: absolute;left: 20%;top: 10px;}"><img src="click-here-to-print.jpg" alt="print this page" id="print-button" /></a>

</div>


<?php
$con= mysqli_connect("103.19.89.246:3306","root","rustx@123","hitech_cartmyd");
if($con)
{echo "";
}
else
{
echo "Connection Failed";
}

$vendor_userid=array();
$user_count=0;

if(isset($_POST['sub']))
{
$ord=$_POST['order'];


//vendor details
function vendor_ids($order,$con)
{
$connect=$con;
$order_id=$order;

$vendor_unique="SELECT DISTINCT
`tb_admin_user`.`user_id`
FROM `tb_sales_flat_order_item`,`tb_sales_flat_order_address` ,`tb_admin_user`,
`tb_junaidbhura_jbmarketplace_products` ,  `tb_catalog_product_entity` 
WHERE `tb_sales_flat_order_item`.`order_id` = `tb_sales_flat_order_address`.`parent_id` 
AND  `tb_catalog_product_entity`.`sku` =  `tb_sales_flat_order_item`.`sku` 
AND  `tb_catalog_product_entity`.`entity_id` =  `tb_junaidbhura_jbmarketplace_products`.`product_id` 
AND  `tb_junaidbhura_jbmarketplace_products`.`user_id` =  `tb_admin_user`.`user_id` 
AND `order_id` =$order_id ORDER BY `tb_admin_user`.`user_id` ";


$vendor_query=mysqli_query($connect,$vendor_unique);
$counter=0;
while($userids = mysqli_fetch_array($vendor_query))
{

$counter++;
$vendor_userid[$counter]=$userids['user_id'];

}
foreach ($vendor_userid as $venid)
{
//echo $venid;
head();
vendordetails($order_id,$venid,$con);
buyer($order_id,$con);
orders($order_id,$venid,$con);
footer();
space();

}
}
//buyer
function buyer($order,$con)
{
$connect=$con;
$order_id=$order;
$buyer="SELECT DISTINCT
`tb_sales_flat_order_address`.`firstname`,
`tb_sales_flat_order_address`.`lastname`,
`tb_sales_flat_order_address`.`street`,
`tb_sales_flat_order_address`.`city`,
`tb_sales_flat_order_address`.`postcode`,
`tb_sales_flat_order_address`.`telephone`,
`tb_sales_flat_order_address`.`region`, 
`tb_sales_flat_order_address`.`country_id` 
FROM `tb_sales_flat_order_item`,`tb_sales_flat_order_address`
WHERE `tb_sales_flat_order_item`.`order_id` = `tb_sales_flat_order_address`.`parent_id` 
AND `order_id` =$order_id ";
$buyer_res=mysqli_query($connect,$buyer);
while($buy=mysqli_fetch_array($buyer_res))
{
echo '<div class="right-column">
<p><b>INVOICE DATE__________</b></p>
<p><b>REF. DATE</b>'.date('d-M-y').'</p><hr></hr>
<p><b>Buyer:</b></p>
<p>'.$buy['firstname'].'&nbsp;'.$buy['lastname']. '</p>
<p>'.$buy['street'].'</p>
<p>'.$buy['city'].'</p>
<p>'.$buy['region'].'</p>
<p>'.$buy['country_id'].'-'.$buy['postcode'].'</p>
<p>'.$buy['telephone'].'</p>
<p><b>TRK NO#</b></p><hr>
</div><hr>
</div>';
}
}
//vendor_details
function vendordetails($order,$v_id,$con)
{

$order_id=$order;
$connect=$con;
$vendor_id=$v_id;
$check_sql="SELECT * from `tb_sales_invoice_no` where order_id=$order_id and user_id=$vendor_id";
$res_check=mysqli_query($connect,$check_sql);
$check_count=mysqli_num_rows($res_check);
$vend_sql="SELECT username,address, phoneno,city as admin_city,state,pincode,tinno from `tb_admin_user` where user_id = $vendor_id";
$vend_res=mysqli_query($connect,$vend_sql);
while($vendor = mysqli_fetch_array($vend_res))
{
$vendor_username=$vendor['username'];
$vendor_address=$vendor['address'];
$vendor_phoneno=$vendor['phoneno'];
$vendor_city=$vendor['admin_city'];
$vendor_state=$vendor['state'];
$vendor_pincode=$vendor['pincode'];
$vendor_tinno=$vendor['tinno'];
}
echo '
<div class="left-column">
<p><b>INVOICE NO_________</b></p>
<p><b>REF. NO:</b>';
if($check_count>0)
{
getordernum($vendor_id,$order_id,$connect);
}
else
{
saved($vendor_id,$order_id,$connect);
}
echo '
</p><hr />
<p><b>Seller</b></p>
<p>'.$vendor_username.'</p>
<p>'.$vendor_address.'</p>
<p>'.$vendor_city.'</p>
<p>'.$vendor_state.'</p>
<p>IN-'.$vendor_pincode.'</p>
<p><b>COMPANYS VAT TIN</b>'.$vendor_tinno.'</p><hr>
</div>';
}
//orders
function orders($order,$vendor,$con)
{

$amt=0;
$order_id=$order;
$vendor_id=$vendor;
$connect=$con;
$order_sql="SELECT DISTINCT
`tb_admin_user`.`user_id` ,
`tb_junaidbhura_jbmarketplace_products`.`product_id` ,
`tb_junaidbhura_jbmarketplace_products`.`user_id`,
`tb_catalog_product_entity`.`entity_id`,
`tb_catalog_product_entity`.`sku`,
`tb_sales_flat_order_item`.`sku`,
`tb_sales_flat_order_item`.`tax_amount`,
`tb_sales_flat_order_item`.`row_total`,
`tb_sales_flat_order_item`.`order_id`,
`tb_sales_flat_order_item`.`discount_amount`,
`tb_sales_flat_order_item`.`name`,
`tb_sales_flat_order_item`.`qty_ordered`,
`tb_sales_flat_order_item`.`price_incl_tax`
FROM `tb_sales_flat_order_item`,`tb_sales_flat_order_address` ,`tb_admin_user`,
`tb_junaidbhura_jbmarketplace_products` ,  `tb_catalog_product_entity` 
WHERE `tb_sales_flat_order_item`.`order_id` = `tb_sales_flat_order_address`.`parent_id` 
AND  `tb_catalog_product_entity`.`sku` =  `tb_sales_flat_order_item`.`sku` 
AND  `tb_catalog_product_entity`.`entity_id` =  `tb_junaidbhura_jbmarketplace_products`.`product_id` 
AND  `tb_junaidbhura_jbmarketplace_products`.`user_id` =  `tb_admin_user`.`user_id` 
AND `order_id` =$order_id and `tb_admin_user`.`user_id`=$vendor_id ";


$order_result=mysqli_query($connect,$order_sql);
echo '<div id="product">
<br><table border="1" style="width:800px;" cellpadding="5px">
<tr>
<td>S.No</td>
<td>SKU</td>
<td>Item Description</td>
<td>Price</td>
<td>Quantity</td>
<td>Amount</td>
</tr>';
$count=0;
while($orders=mysqli_fetch_array($order_result))
{ $count++;

echo '<tr>
<td><p>'.$count.'</p></td>
<td><p>'.$orders['sku'].'</p></td>
<td><p>'.$orders['name'].'</p></td>
<td><p>Rs '.$orders['price_incl_tax'].'</p></td>
<td><p>'.$orders['qty_ordered'].'</p></td>
<td><p>Rs '.$orders['price_incl_tax']*$orders['qty_ordered'].'</p></td>
</tr>';
$amt=$amt+($orders['price_incl_tax']*$orders['qty_ordered']);
}
echo '</table>
<p style=" margin-left: -7%;text-align: -webkit-center;">TOTAL : Rs.'.$amt.'(Including All taxes)</p>
<br><hr>
<p><b>AMOUNT IN WORDS: </b>'; echo convert_number_to_words($amt); echo ' Rupees Only</p>
</div>';
}
//no to string
function convert_number_to_words($number) 
{

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'Zero',
        1                   => 'One',
        2                   => 'Two',
        3                   => 'Three',
        4                   => 'Four',
        5                   => 'Five',
        6                   => 'Six',
        7                   => 'Seven',
        8                   => 'Eight',
        9                   => 'Nine',
        10                  => 'Ten',
        11                  => 'Eleven',
        12                  => 'Twelve',
        13                  => 'Thirteen',
        14                  => 'Fourteen',
        15                  => 'Fifteen',
        16                  => 'Sixteen',
        17                  => 'Seventeen',
        18                  => 'Eighteen',
        19                  => 'Nineteen',
        20                  => 'Twenty',
        30                  => 'Thirty',
        40                  => 'Fourty',
        50                  => 'Fifty',
        60                  => 'Sixty',
        70                  => 'Seventy',
        80                  => 'Eighty',
        90                  => 'Ninety',
        100                 => 'Hundred',
        1000                => 'Thousand',
        1000000             => 'Million',
        1000000000          => 'Billion',
        1000000000000       => 'Trillion',
        1000000000000000    => 'Quadrillion',
        1000000000000000000 => 'Quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}
function footer()
{
echo '<div class="footer">
<hr></br>
<p><b>DECLARATION</b></p>
<p>We declare that this invoice shows actual price of the goods described inclusive of taxes and that all particulars are true and correct.</p>
<p>In case you find selling price on this invoice to be more than MRP mentioned on the product, please inform info@cartmydeal.com</p>
</br></br>
<hr><center><b>THIS IS A COMPUTER GENERATED INVOICE AND DOES NOT REQUIRE SIGNATURE</b></center><hr>
<div style="float:right;"><p>Powered By CartMyDeal.com</br></br>
A Unit Of Hi-Tech International</p>
</div>
<img src="http://cartmydeal.com/skin/frontend/default/galabrandstore/images/CMDLogo.png" style="
    width: 90px;
    height: 70px; float:right; margin-right:10px; "/>
</div>

</div>
<div id="base" style="position: absolute;
top: 130%;
left: 20%;">
<hr><center><b>USE THIS COPY FOR SHIPPING TO BUYER</b></center><hr>
</div></div>


</html>';
}
function head()
{
echo '<div class="page">
<div class="container"></br>
<div class="columns">
<hr /><center>RETAIL INVOICE</center><hr />';
}
function space()
{
echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
}
function saved($user_id,$order_id,$con)
{
$date=date('y');
$u_id=$user_id;
$o_id=$order_id;
$connect=$con;
$fetch_previous="SELECT * from `tb_sales_invoice_no` 
WHERE  `tb_sales_invoice_no`.`user_id`=$u_id 
ORDER BY order_id ";
$fetch_previous_res=mysqli_query($connect,$fetch_previous);
$previous_count=mysqli_num_rows($fetch_previous_res);
$k=1;
if($previous_count==0)
{
$order_no=$k;
$save_sql="INSERT INTO `tb_sales_invoice_no` (order_id,user_id,fiscal_start,order_no,created)
values ($o_id,$u_id,$date,$order_no,NOW())";
$res=mysqli_query($connect,$save_sql);
}
else if($previous_count !=0)
{
$order_no=$previous_count + $k;
$save_sql="INSERT INTO `tb_sales_invoice_no` (order_id,user_id,fiscal_start,order_no,created)
values ($o_id,$u_id,$date,$order_no,NOW())";
$res=mysqli_query($connect,$save_sql);
echo "CMD00".$u_id."/".$date."-".$date+1 . "/".$order_no ;
}
}
function getordernum($user_id,$order_id,$con)
{
$end=0;
$u_id=$user_id;
$o_id=$order_id;
$connect=$con;
$fetch_previous="SELECT * from `tb_sales_invoice_no` 
WHERE  `tb_sales_invoice_no`.`user_id`=$u_id and `tb_sales_invoice_no`.`order_id`=$o_id ORDER BY order_id ";
$fetch_previous_res=mysqli_query($connect,$fetch_previous);
while($invoice_num = mysqli_fetch_array($fetch_previous_res))
{ 
$end=$invoice_num['fiscal_start'] + 1;
$invoice_number  =$invoice_num['prefix'];
$invoice_number .=$u_id."/";
$invoice_number .= $invoice_num['fiscal_start']."-" .$end . "/" . $invoice_num['order_no'];
echo $invoice_number;
}
}
//generate
vendor_ids($ord,$con);





}




?>
