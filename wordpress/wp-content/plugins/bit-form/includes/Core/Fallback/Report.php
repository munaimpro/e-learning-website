<?php

namespace BitCode\BitForm\Core\Fallback;

use BitCode\BitForm\Core\Database\FormModel;
use BitCode\BitForm\Core\Database\ReportsModel;
use BitCode\BitForm\Core\Util\IpTool;

class Report
{
  public function report()
  {
    $reportMdl = new ReportsModel();
    global $wpdb;
    $tablename = $wpdb->prefix . 'bitforms_reports';

    // $latestIds = $wpdb->get_results("SELECT  MAX(`id`) as latest_id FROM $tablename GROUP BY `context`");
    $wpdb->query(
      $wpdb->prepare(
        "SELECT  MAX(`id`) as latest_id FROM $tablename GROUP BY `context`"
      )
    );
    $latestIds = $wpdb->last_result;

    $notDeletedIds = array_column($latestIds, 'latest_id');

    if (!is_wp_error($latestIds) && count($notDeletedIds) > 0) {
      $reportMdl->bulkDelete(['id' => $notDeletedIds], 'NOT IN');
    }

    $ipTool = new IpTool();
    $user_details = $ipTool->getUserDetail();

    $formModel = new FormModel();

    $forms = $formModel->get(
      ['id']
    );

    $details = [
      'report_name'   => 'All Entries',
      'hiddenColumns' => [],
      'pageSize'      => 10,
      'sortBy'        => [],
      'filters'       => [],
      'globalFilter'  => '',
      'order'         => [],
    ];

    foreach ($forms as $form) {
      $existReportId = $reportMdl->get(
        [
          'id',
        ],
        [
          'context' => $form->id,
        ]
      );
      if (is_wp_error($existReportId)) {
        $defaultReport = [
          'type'        => 'table',
          'category'    => 'form',
          'context'     => $form->id,
          'details'     => wp_json_encode($details),
          'isDefault'   => (bool) 1,
          'user_id'     => $user_details['id'],
          'user_ip'     => $user_details['ip'],
          'user_device' => $user_details['device'],
          'created_at'  => $user_details['time'],
          'updated_at'  => $user_details['time'],
        ];

        $reportMdl->insert($defaultReport);
      }
    }
  }

  public function addHiddenColumns()
  {
    $reportMdl = new ReportsModel();
    $reports = $reportMdl->get(
      [
        'id',
        'details',
      ],
      [
        'category' => 'form',
        'type'     => 'table',
      ]
    );

    foreach ($reports as $report) {
      $reportDetails = json_decode($report->details);
      if (is_object($reportDetails) && (!property_exists($reportDetails, 'hiddenColumns') || empty($reportDetails->hiddenColumns))) {
        $reportDetails->hiddenColumns = ['__user_id', '__user_ip', '__referer', '__user_device', '__created_at', '__updated_at'];
        $reportMdl->update(
          [
            'details' => wp_json_encode($reportDetails),
          ],
          [
            'id' => $report->id,
          ]
        );
      }
    }
  }
}
