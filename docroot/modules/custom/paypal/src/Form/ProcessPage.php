<?php
/**
 * @file
 * Contains \Drupal\paypal\Form\ResumeForm.
 */
namespace Drupal\paypal\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ProcessPage extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ipn_notification';
  }

/**
 * {@inheritdoc}
 */
public function buildForm(array $form, FormStateInterface $form_state) {

  $url = 'https://www.paypal.com/cgi-bin/webscr';
$postdata = '';
foreach($_POST as $i => $v) {
	$postdata .= $i.'='.urlencode($v).'&';
}
$postdata .= 'cmd=_notify-validate';

$web = parse_url($url);
if ($web['scheme'] == 'https') { 
	$web['port'] = 443;  
	$ssl = 'ssl://'; 
} else { 
	$web['port'] = 80;
	$ssl = ''; 
}
$fp = @fsockopen($ssl.$web['host'], $web['port'], $errnum, $errstr, 30);

if (!$fp) { 
	echo $errnum.': '.$errstr;
} else {
	fputs($fp, "POST ".$web['path']." HTTP/1.1\r\n");
	fputs($fp, "Host: ".$web['host']."\r\n");
	fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	fputs($fp, "Content-length: ".strlen($postdata)."\r\n");
	fputs($fp, "Connection: close\r\n\r\n");
	fputs($fp, $postdata . "\r\n\r\n");

	while(!feof($fp)) { 
		$info[] = @fgets($fp, 1024); 
	}
	fclose($fp);
	$info = implode(',', $info);
	if (eregi('VERIFIED', $info)) { 
		 \Drupal::database()->insert('paypal_payment_status')
					->fields([
						'user_id',
						'orders_id',
						'before_amount',
						'after_amount',
						'currency_code',
						'custom_id',
						'transaction_id',
						'payment_status',
					])
					->values(array(
						'test',
						'test',
						'test',
						'test',
						'test',
						'test',
						'test',
						'test',
					    
					))
					->execute();
	} else {
		// invalid, log error or something
	}
    }
 
    return $form;
  }

  /**
   * {@inheritdoc}
   */
    public function validateForm(array &$form, FormStateInterface $form_state) {
      
    }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  
   }
}