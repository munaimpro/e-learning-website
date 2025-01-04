<?php
/**
 * ZohoCrm Tag Api Helper
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoCRM;

use BitCode\BitForm\Core\Util\HttpHelper;
use WP_Error;

/**
 * Provide functionality for Tags in Zoho CRM
 */
final class TagApiHelper
{
  private $_defaultHeader;
  private $_apiDomain;
  private $_module;

  /**
   * Constructor function
   *
   * @param object $tokenDetails Api token details
   * @param string $module       Module Name
   */
  public function __construct($tokenDetails, $module)
  {
    $this->_defaultHeader['Authorization'] = "Zoho-oauthtoken {$tokenDetails->access_token}";
    $this->_apiDomain = \urldecode($tokenDetails->api_domain);
    $this->_module = $module;
  }

  /**
   * Helps to get Tags List of zcrm module
   *
   * @return array $tags Tags List
   */
  public function getTagList()
  {
    $getTagsEndpoint = "{$this->_apiDomain}/crm/v2/settings/tags";

    $tagListResponse = HttpHelper::get($getTagsEndpoint, ['module' => $this->_module], $this->_defaultHeader);
    if (is_wp_error($tagListResponse)) {
      return $tagListResponse;
    }

    $tags = [];
    if (!empty($tagListResponse->status) && 'error' === $tagListResponse->status) {
      return new WP_Error($tagListResponse->code, $tagListResponse);
    }
    if (!empty($tagListResponse->tags)) {
      foreach ($tagListResponse->tags as $tagDetails) {
        $tags[] = $tagDetails->name;
      }
    }
    return $tags;
  }

  /**
   * Helps to add Tags to a specific record of a module
   *
   * @param integer $recordID ID of record to add tags
   * @param string  $tagNames urlencoded string of tag names
   *
   * @return string json $addTagsResponse Tags List
   */
  public function addTagsSingleRecord($recordID, $tagNames)
  {
    $addTagsEndpoint = "{$this->_apiDomain}/crm/v2/{$this->_module}/{$recordID}/actions/add_tags";

    $addTagsResponse = HttpHelper::post($addTagsEndpoint, ['tag_names' => $tagNames], $this->_defaultHeader);

    return $addTagsResponse;
  }
}
