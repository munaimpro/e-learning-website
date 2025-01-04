<?php

namespace BitCode\BitForm\Core\Messages;

use BitCode\BitForm\Core\Database\PdfTemplateModel;
use BitCode\BitForm\Core\Util\IpTool;

final class PdfTemplateHandler
{
  private static $_formID;
  private static $_pdfTemplateModel;
  private $_user_details;

  public function __construct($formID)
  {
    static::$_formID = $formID;
    static::$_pdfTemplateModel = new PdfTemplateModel();
    $this->_user_details = (new IpTool())->getUserDetail();
  }

  public function getAll($templateID = null, $userID = null)
  {
    $condition = [
      'form_id' => static::$_formID,
    ];
    if (!is_null($templateID)) {
      $condition = array_merge($condition, ['id' => $templateID]);
    }
    if (!is_null($userID)) {
      $condition = array_merge($condition, ['user_id' => $userID]);
    }
    return  static::$_pdfTemplateModel->get(
      [
        'id',
        'title',
        'setting',
        'body'
      ],
      $condition
    );
  }

  public function getById($templateID)
  {
    return $this->getAll($templateID);
  }

  public function save($templateDetail)
  {
    return static::$_pdfTemplateModel->insert(
      [
        'title'         => $templateDetail->title,
        'setting'       => $templateDetail->setting,
        'body'          => $templateDetail->body,
        'form_id'       => static::$_formID,
        'user_id'       => $this->_user_details['id'],
        'user_ip'       => $this->_user_details['ip'],
        'created_at'    => $this->_user_details['time'],
        'updated_at'    => $this->_user_details['time']
      ]
    );
  }

  public function update($templateDetail)
  {
    return static::$_pdfTemplateModel->update(
      [
        'title'         => $templateDetail->title,
        'setting'       => $templateDetail->setting,
        'body'          => $templateDetail->body,
        'form_id'       => static::$_formID,
        'user_id'       => $this->_user_details['id'],
        'user_ip'       => $this->_user_details['ip'],
        'updated_at'    => $this->_user_details['time']
      ],
      [
        'id' => $templateDetail->id
      ]
    );
  }

  public function saveOrUpdate($templateDetail)
  {
    $templateDetail->setting = wp_json_encode($templateDetail->setting);
    if (isset($templateDetail->id) && !empty($templateDetail->id)) {
      return $this->update($templateDetail);
    }
    return $this->save($templateDetail);
  }

  public function delete($templateID)
  {
    return static::$_pdfTemplateModel->delete(
      [
        'id'      => $templateID,
        'form_id' => static::$_formID,
      ]
    );
  }

  public function duplicateTemplate($templateID)
  {
    $templateDetail = static::$_pdfTemplateModel->get(
      [
        'id'      => $templateID,
        'form_id' => static::$_formID,
      ]
    );

    if (is_wp_error($templateDetail) || empty($templateDetail)) {
      return $templateDetail;
    }

    $title = empty($templateDetail[0]->title)
            ? "Duplicate of Template #{$templateID}"
            : "Duplicate of {$templateDetail[0]->title}";

    $templateDetail[0]->title = $title;
    return $this->save($templateDetail);
  }

  public function duplicateAllTempInAForm($oldFromId)
  {
    $allCols = ['title', 'setting', 'body', 'form_id', 'user_id', 'user_ip', 'created_at', 'updated_at'];
    $dupData = [
      'title',
      'setting',
      'body',
      static::$_formID,
      $this->_user_details['id'],
      $this->_user_details['ip'],
      $this->_user_details['time'],
      $this->_user_details['time']
    ];
    return static::$_pdfTemplateModel->duplicate(
      $allCols,
      $dupData,
      ['form_id' => $oldFromId]
    );
  }
}
