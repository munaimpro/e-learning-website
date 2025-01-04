<?php

namespace BitCode\BitForm\Core\Util;

trait PostHelpers
{
  protected function postFieldMapping($postMap, $fieldValues, $postID, $entryID)
  {
    $postData = [];

    $uploadFeatureImg = new WpFileHandler($this->_formID);

    foreach ($postMap as $fieldPair) {
      if (!empty($fieldPair->postField) && !empty($fieldPair->formField)) {
        $formField = $fieldPair->formField;

        $postField = $fieldPair->postField;

        if ('_thumbnail_id' === $fieldPair->postField && !empty($fieldValues[$formField])) {
          $uploadFeatureImg->uploadFeatureImg($fieldValues[$formField], $entryID, $postID);
        } else {
          if ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
            $postData[$postField] = $fieldPair->customValue;
          } else {
            $postData[$postField] = $fieldValues[$formField];
          }
        }
      }
    }
    return $postData;
  }

  protected function setPostData($integrationDetails)
  {
    $postData = [];

    $postData['comment_status'] = isset($integrationDetails->comment_status) ? $integrationDetails->comment_status : '';

    $postData['post_status'] = isset($integrationDetails->post_status) ? $integrationDetails->post_status : '';

    $postData['post_type'] = isset($integrationDetails->post_type) ? $integrationDetails->post_type : '';

    if (isset($integrationDetails->post_author) && 'logged_in_user' !== $integrationDetails->post_author) {
      $postData['post_author'] = $integrationDetails->post_author;
    } else {
      $postData['post_author'] = get_current_user_id();
    }

    return $postData;
  }
}
