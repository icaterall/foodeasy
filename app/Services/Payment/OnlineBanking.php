<?php
namespace App\Services\Payment;


use Redirect;
use Auth;
use Facades\App\Models\Order;
use Facades\App\Models\User;
use Facades\App\Services\Payment\SaveOrderToCart;
use Session;
use Facades\App\Services\Emails\OrderStatusEmail;
class OnlineBanking
{
    private $razer_merchantid;
    private $razer_vkey;
    private $razer_secret;

    public function __construct()
    {
        $this->razer_merchantid = config('services.razer.id');
        $this->razer_vkey = config('services.razer.key');
        $this->razer_secret = config('services.razer.secret');
        $this->razer_paymentUrl = config('services.razer.url');
    }




       public function MolpayOnlineBanking($amount,$bank_code,$user_id)
      {

       if(Session::get('order_id') != null)
         {
            try {
             Order::destroy(Session::get('order_id'));
             }    catch (ModelNotFoundException $e) {
             // Handle the error.
           }
         }
       $order_id = SaveOrderToCart::SaveToOrders(0,$user_id);
       $user = User::find($user_id);
       Session::put(['order_id' => $order_id]);

       $merchantid = $this->razer_merchantid;
       $vkey = $this->razer_vkey;
       $user_name = $user->name;
       $user_mobile = $user->mobile;
       $user_email = $user->email;
       $amount=round($amount,2);

      $vcode = md5($amount.$merchantid.$order_id.$vkey);
      $razer_url = $this->razer_paymentUrl;
      $bank_code = $bank_code.'.php';
      $prm_returnURL    = route('PaymentStatus');
//set POST variables

    $form_pymnt = "<html><head></head>"."\n";
    $form_pymnt = "<body onload=\"document.createElement('form').submit.call(document.getElementById('molpay_form'))\" >"."\n";
    $form_pymnt .= "<form action=' ".$razer_url."/".$bank_code."' method='post' id='molpay_form'  >"."\n";
    $form_pymnt .= "<input type='hidden' name='amount' value='".$amount."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='merchant_id' value='".$merchantid."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='orderid' value='".$order_id."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='bill_name' value='".$user_name."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='bill_email' value='".$user_email."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='bill_mobile' value='".$user_mobile."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='bill_desc' value='Do not close the bank page after paying, you have to return to Spoongate'>"."\n";
    $form_pymnt .= "<input type='hidden' name='country' value='MY'>"."\n";
    $form_pymnt .= "<input type='hidden' name='vcode' value='".$vcode."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='returnurl' value='".$prm_returnURL."'>"."\n"; //use the name from your
  $form_pymnt .= "<input type='hidden' name='submit' id='submit' value='Pay with Molpay'>"."\n";
  $form_pymnt .= "</form>"."\n";
  $form_pymnt .= "<br />"."\n";
  $form_pymnt .= "</body>"."\n";
  $form_pymnt .= "</html>"."\n";
  $paycash = 'n';
  $ispaid = 'y';
echo $form_pymnt;
    }

//---------For App


       public function AppOnlineBanking($amount,$bank_code,$user_id)
      {

       if(Session::get('order_id') != null)
         {
            try {
             Order::destroy(Session::get('order_id'));
             }    catch (ModelNotFoundException $e) {
             // Handle the error.
           }
         }
       $order_id = SaveOrderToCart::SaveToOrders(0,$user_id);
       $user = User::find($user_id);
       Session::put(['order_id' => $order_id]);

       $merchantid = $this->razer_merchantid;
       $vkey = $this->razer_vkey;
       $user_name = $user->name;
       $user_mobile = $user->mobile;
       $user_email = $user->email;
       $amount=round($amount,2);

      $vcode = md5($amount.$merchantid.$order_id.$vkey);
      $razer_url = $this->razer_paymentUrl;
      $bank_code = $bank_code.'.php';
      $prm_returnURL    = route('AppPaymentStatus');
//set POST variables

    $form_pymnt = "<html><head></head>"."\n";
    $form_pymnt = "<body onload=\"document.createElement('form').submit.call(document.getElementById('molpay_form'))\" >"."\n";
    $form_pymnt .= "<form action=' ".$razer_url."/".$bank_code."' method='post' id='molpay_form'  >"."\n";
    $form_pymnt .= "<input type='hidden' name='amount' value='".$amount."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='merchant_id' value='".$merchantid."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='orderid' value='".$order_id."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='bill_name' value='".$user_name."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='bill_email' value='".$user_email."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='bill_mobile' value='".$user_mobile."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='bill_desc' value='Do not close the bank page after paying, you have to return to Spoongate'>"."\n";
    $form_pymnt .= "<input type='hidden' name='country' value='MY'>"."\n";
    $form_pymnt .= "<input type='hidden' name='vcode' value='".$vcode."'>"."\n";
    $form_pymnt .= "<input type='hidden' name='returnurl' value='".$prm_returnURL."'>"."\n"; //use the name from your
  $form_pymnt .= "<input type='hidden' name='submit' id='submit' value='Pay with Molpay'>"."\n";
  $form_pymnt .= "</form>"."\n";
  $form_pymnt .= "<br />"."\n";
  $form_pymnt .= "</body>"."\n";
  $form_pymnt .= "</html>"."\n";
  $paycash = 'n';
  $ispaid = 'y';
echo $form_pymnt;
    }
}


?>
