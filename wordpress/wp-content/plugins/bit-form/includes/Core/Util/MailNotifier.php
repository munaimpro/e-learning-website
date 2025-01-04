<?php

namespace BitCode\BitForm\Core\Util;

use BitCode\BitForm\Core\Database\FormEntryMetaModel;
use BitCode\BitForm\Core\Messages\EmailTemplateHandler;
use BitCode\BitForm\Core\Messages\PdfTemplateHandler;
use BitCode\BitFormPro\Admin\AppSetting\Pdf;

final class MailNotifier
{
  public static function notify($notifyDetails, $formID, $fieldValue, $entryID, $isDblOptin = false, $logId = '')
  {
    $emailTemplateHandler = new EmailTemplateHandler($formID);
    $attachments = [];
    $tempPdfLink = '';
    if (!empty($notifyDetails->pdfId) && is_string($notifyDetails->pdfId)) {
      $pdfTemplateID = json_decode($notifyDetails->pdfId)->id;
      $pdfTemplateHandler = new PdfTemplateHandler($formID);

      $pdfTemplate = $pdfTemplateHandler->getById($pdfTemplateID);
      $pdfBody = FieldValueHandler::replaceFieldWithValue($pdfTemplate[0]->body, $fieldValue);

      $pdfSetting = json_decode($pdfTemplate[0]->setting);

      $path = BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'pdf';
      $fileName = 'bit-form-pdf-' . $formID . '-' . $entryID;

      if (!is_dir($path)) {
        mkdir($path, 0777, true);
      }

      if (class_exists('\BitCode\BitFormPro\Admin\AppSetting\Pdf')) {
        $serverPath = BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR;
        $pdfBody = FieldValueHandler::changeImagePathInHTMLString($pdfBody, $serverPath);
        $generatedPdf = Pdf::getInstance()->generator($pdfSetting, $pdfBody, $path, $entryID, 'F');

        if (!is_wp_error($generatedPdf) && file_exists($generatedPdf)) {
          $attachments[] = $generatedPdf;
          $tempPdfLink = $generatedPdf;
        } else {
          Log::debug_log('Error in generating PDF: ' . $generatedPdf->get_error_message());
        }
      }
    }

    if (is_string($notifyDetails->id)) {
      $mailTemplateID = json_decode($notifyDetails->id)->id;
      $mailTemplate = $emailTemplateHandler->getATemplate($mailTemplateID);
      if (!is_wp_error($mailTemplate)) {
        $mailTo = FieldValueHandler::validateMailArry($notifyDetails->to, $fieldValue);
        if (!empty($mailTo)) {
          $from_name = '';
          if (isset($notifyDetails->from_name) && !empty($notifyDetails->from_name)) {
            $from_name = $notifyDetails->from_name;
          }
          (new MailConfig())->sendMail(['from_name' => $from_name]);
          $mailSubject = FieldValueHandler::replaceFieldWithValue($mailTemplate[0]->sub, $fieldValue);
          $mailBody = FieldValueHandler::replaceFieldWithValue($mailTemplate[0]->body, $fieldValue);
          $webUrl = BITFORMS_UPLOAD_BASE_URL . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
          $mailBody = FieldValueHandler::changeImagePathInHTMLString($mailBody, $webUrl);
          $mailHeaders = [
            // 'Content-Type: text/html; charset=UTF-8',
            // $embeddedMailHeader
          ];
          if (!empty($notifyDetails->replyto)) {
            $mailReplyTo = FieldValueHandler::validateMailArry($notifyDetails->replyto, $fieldValue);
            if (is_array($mailReplyTo)) {
              foreach ($mailReplyTo as $key => $emailAddress) {
                $mailHeaders[] = 'Reply-To: ' . explode('@', $emailAddress)[0] . '<' . sanitize_email($emailAddress) . '>';
              }
            } else {
              $mailHeaders[] = 'Reply-To: ' . explode('@', $mailReplyTo)[0] . '<' . sanitize_email($mailReplyTo) . '>';
            }
          }
          $oldMailBody = $mailBody;
          $data = [];
          if ($isDblOptin && true === has_filter('bf_email_body_text')) {
            $urlParams = $formID . '_' . $entryID . '_' . $logId;
            $data = apply_filters('bf_email_body_text', $mailBody, $urlParams);
            $mailBody = $data['mailbody'];
          }

          if (!empty($notifyDetails->bcc)) {
            $mailBCC = FieldValueHandler::validateMailArry($notifyDetails->bcc, $fieldValue);
            if (is_array($mailBCC)) {
              foreach ($mailBCC as $key => $emailAddress) {
                $mailHeaders[] = 'Bcc: ' . sanitize_email($emailAddress);
              }
            } else {
              $mailHeaders[] = 'Bcc: ' . sanitize_email($mailBCC);
            }
          }
          if (!empty($notifyDetails->cc)) {
            $mailCC = FieldValueHandler::validateMailArry($notifyDetails->cc, $fieldValue);
            if (is_array($mailCC)) {
              foreach ($mailCC as $key => $emailAddress) {
                $mailHeaders[] = 'Cc: ' . sanitize_email($emailAddress);
              }
            } else {
              $mailHeaders[] = 'Cc: ' . sanitize_email($mailCC);
            }
          }
          if (!empty($notifyDetails->from)) {
            $mailFrom = FieldValueHandler::validateMailArry($notifyDetails->from, $fieldValue);
            $fromName = !empty($notifyDetails->fromName) ? $notifyDetails->fromName : explode('@', $mailFrom[0])[0];
            $mailHeaders[] = "FROM: $fromName " . '<' . sanitize_email($mailFrom[0]) . '>';
          }

          if (!empty($notifyDetails->attachment)) {
            $files = $notifyDetails->attachment;
            $fileBasePath = BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formID . DIRECTORY_SEPARATOR . $entryID . DIRECTORY_SEPARATOR;
            if (is_array($files)) {
              foreach ($files as $file) {
                if (isset($fieldValue[$file])) {
                  if (is_array($fieldValue[$file])) {
                    foreach ($fieldValue[$file] as $singleFile) {
                      if (\is_readable("{$fileBasePath}{$singleFile}")) {
                        $attachments[] = "{$fileBasePath}{$singleFile}";
                      }
                    }
                  } elseif (\is_readable("{$fileBasePath}{$fieldValue[$file]}")) {
                    $attachments[] = "{$fileBasePath}{$fieldValue[$file]}";
                  }
                }
              }
            } elseif (isset($fieldValue[$files])) {
              if (is_array($fieldValue[$files])) {
                foreach ($fieldValue[$files] as $singleFile) {
                  if (\is_readable("{$fileBasePath}{$singleFile}")) {
                    $attachments[] = "{$fileBasePath}{$singleFile}";
                  }
                }
              } elseif (\is_readable("{$fileBasePath}{$fieldValue[$files]}")) {
                $attachments[] = "{$fileBasePath}{$fieldValue[$files]}";
              }
            }
          }
          $mailBody = stripcslashes($mailBody);
          $mailSubject = stripcslashes($mailSubject);
          add_filter('wp_mail_content_type', [self::class, 'filterMailContentType']);
          $status = wp_mail($mailTo, $mailSubject, $mailBody, $mailHeaders, $attachments);
          if (!$status) {
            $status = wp_mail($mailTo, $mailSubject, $mailBody, $mailHeaders);
          }
          if ($status && $isDblOptin && false !== strpos($oldMailBody, 'entry_confirmation_url')) {
            $entryMeta = new FormEntryMetaModel();

            $entryMeta->insert(
              [
                'bitforms_form_entry_id' => $entryID,
                'meta_key'               => 'entry_confirm_activation',
                'meta_value'             => $data['token']
              ]
            );
          }
          remove_filter('wp_mail_content_type', [self::class, 'filterMailContentType']);
        }
      }
    }

    if (!empty($tempPdfLink)) {
      wp_delete_file($tempPdfLink);
    }
  }

  public static function filterMailContentType()
  {
    return 'text/html; charset=UTF-8';
  }
}
