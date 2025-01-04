<?php

namespace BitCode\BitForm\Core\Util;

use BitCode\BitForm\Core\Integration\IntegrationHandler;

/**
 * Class handling plugin activation.
 *
 * @since 1.0.0
 */
final class MailConfig
{
  private $config;

  public function sendMail($config = [])
  {
    $this->config = $config;
    add_action('phpmailer_init', [$this, 'mailConfig'], 10, 1);
  }

  public function mailConfig($phpmailer)
  {
    $integrationHandler = new IntegrationHandler(0);
    $formIntegrations = $integrationHandler->getAllIntegration('mail', 'smtp', 1);
    if (!isset($formIntegrations->errors['result_empty'])) {
      if (1 === (int) $formIntegrations[0]->status) {
        $integration_details = json_decode($formIntegrations[0]->integration_details);
        $phpmailer->Mailer = 'smtp';
        $phpmailer->Host = $integration_details->smtp_host;
        $phpmailer->SMTPAuth = true;
        if (!empty($integration_details->re_email_address)) {
          $phpmailer->addReplyTo($integration_details->re_email_address, 'reply-to');
        }
        $phpmailer->Port = $integration_details->port;
        $phpmailer->Username = $integration_details->smtp_user_name;
        $phpmailer->Password = $integration_details->smtp_password;
        $phpmailer->SMTPSecure = $integration_details->encryption;
        // $phpmailer->SMTP_DEBUG = 1;
        $phpmailer->From = $integration_details->form_email_address;
        $from_name = $integration_details->form_name;
        if (isset($this->config['from_name']) && !empty($this->config['from_name'])) {
          $from_name = $this->config['from_name'];
        }
        $phpmailer->FromName = $from_name;
      }
    }
  }
}
